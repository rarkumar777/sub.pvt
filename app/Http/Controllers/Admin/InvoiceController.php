<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceExpense;
use App\Models\InvoiceTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('user')->orderByDesc('id');

        if ($request->filled('id')) {
            $invoices->where('id', $request->id);
        }
        if ($request->filled('status')) {
            $invoices->where('status', $request->status);
        }
        if ($request->filled('module')) {
            if ($request->module == 'invoices') {
                $invoices->where('module', '');
            } else {
                $invoices->where('module', $request->module);
            }
        }
        if ($request->filled('title')) {
            $invoices->where('desc', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('from_date')) {
            $invoices->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $invoices->where('date', '<=', $request->to_date);
        }
        if ($request->filled('from_due_to_date')) {
            $invoices->where('due_to_date', '>=', $request->from_due_to_date);
        }
        if ($request->filled('to_due_to_date')) {
            $invoices->where('due_to_date', '<=', $request->to_due_to_date);
        }
        if ($request->filled('user_email')) {
            $invoices->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->user_email . '%');
            });
        }

        $invoices = $invoices->paginate(20);
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $users = User::orderBy('first_name')->get();
        return view('admin.invoices.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['added_by'] = auth()->id();
        
        // Provide defaults for required fields without DB defaults
        $data['items'] = serialize([]);
        $data['discount'] = $data['discount'] ?? '0';
        $data['tax'] = $data['tax'] ?? 0;
        $data['status'] = $data['status'] ?? 'u';
        $data['type'] = $data['type'] ?? '';
        $data['module'] = $data['module'] ?? '';
        $data['total'] = $data['total'] ?? 0;
        $data['cost'] = $data['cost'] ?? 0;
        $data['paid_by'] = $data['paid_by'] ?? '';
        $data['partly_payment'] = $data['partly_payment'] ?? 0;
        $data['total_paid'] = $data['total_paid'] ?? 0;
        $data['discount_description'] = $data['discount_description'] ?? '';
        $data['sent_count'] = $data['sent_count'] ?? 0;
        $data['invoices_set'] = $data['invoices_set'] ?? '';

        $invoice = Invoice::create($data);
        return redirect()->route('admin.invoices.edit', $invoice->id)->with('success', 'Invoice created');
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['expenses.service', 'transactions', 'user'])->findOrFail($id);
        $users = User::orderBy('first_name')->get();
        return view('admin.invoices.edit', compact('invoice', 'users'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Don't allow editing module-linked invoices
        if (!empty($invoice->module)) {
            return redirect()->route('admin.invoices.edit', $id)->with('error', 'This invoice is managed by a module');
        }

        // Find user by email
        $user = User::where('email', $request->input('user'))->first();
        if (!$user) {
            return redirect()->route('admin.invoices.edit', $id)->withErrors(['User email not found']);
        }

        // Build items array
        $items = [];
        $itemTotal = 0;
        $count = intval($request->input('current_count', 0));
        for ($i = 1; $i <= $count; $i++) {
            if ($request->has('item_' . $i)) {
                $name = $request->input('item_' . $i);
                $qty = intval($request->input('item_qty_' . $i, 1));
                $price = floatval($request->input('item_price_' . $i, 0));
                $items[] = ['name' => $name, 'qty' => $qty, 'price' => $price];
                $itemTotal += ($qty * $price);
            }
        }

        // Calculate discount
        $discountAmount = floatval($request->input('discount_amount', 0));
        $discountType = $request->input('discount_type', '');
        if ($discountType == '%') {
            $discountCalc = ($discountAmount / 100) * $itemTotal;
            $discountStr = $discountAmount . '%';
        } else {
            $discountCalc = $discountAmount;
            $discountStr = $discountAmount;
        }

        // Calculate tax
        $tax = floatval($request->input('tax', 0));
        $taxCalc = ($tax / 100) * ($itemTotal - $discountCalc);
        $total = ($itemTotal - $discountCalc) + $taxCalc;

        $invoice->update([
            'user_id' => $user->id,
            'desc' => $request->input('desc', ''),
            'date' => $request->input('date'),
            'due_to_date' => $request->input('due_to_date'),
            'tax' => $tax,
            'discount' => $discountStr,
            'discount_description' => $request->input('discount_description', ''),
            'partly_payment' => floatval($request->input('partly_payment', 0)),
            'items' => serialize($items),
            'total' => $total,
            'paid_by' => $invoice->paid_by ?? '',
            'invoices_set' => $invoice->invoices_set ?? '',
        ]);

        // Send notification if requested
        if ($request->input('notify_user') == '1') {
            // TODO: Send notification email
        }

        return redirect()->route('admin.invoices.edit', $id)->with('success', 'Invoice updated successfully');
    }

    public function destroy($id)
    {
        Invoice::destroy($id);
        return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted');
    }

    public function show($id)
    {
        return $this->edit($id);
    }

    public function expenses($id)
    {
        $invoice = Invoice::with(['expenses.service', 'expenses.venderUser', 'expenses.addedByUser'])->findOrFail($id);
        return view('admin.invoices.expenses', compact('invoice'));
    }

    public function storeExpense(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        InvoiceExpense::create([
            'invoice_id' => $id,
            'remarks' => $request->input('description'),
            'service_date' => $request->input('date'),
            'service_time' => $request->input('time', ''),
            'service_end_date' => $request->input('end_date', '0000-00-00'),
            'cost' => floatval($request->input('cost')),
            'payment_status' => $request->input('payment_status', ''),
            'status' => $request->input('status', ''),
            'added_by' => auth()->id(),
            'time' => time(),
        ]);
        // Update cost on invoice
        $totalCost = InvoiceExpense::where('invoice_id', $id)->sum('cost');
        $invoice->update(['cost' => $totalCost]);

        return redirect()->route('admin.invoices.expenses', $id)->with('success', 'Expense added successfully');
    }

    public function expenseEditForm($invoiceId, $expenseId)
    {
        $exp = InvoiceExpense::findOrFail($expenseId);
        $html = '<div class="modal" id="edit_expense">
            <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[500px] !tw-max-w-[95vw] tw-shadow-2xl">
                <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
                    <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                        <i class="fa fa-edit tw-text-orange-400"></i> Edit Expense
                    </h3>
                    <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
                </div>
                
                <form method="POST" action="' . route('admin.invoices.expenses.update', [$invoiceId, $expenseId]) . '" class="tw-p-8 tw-flex tw-flex-col tw-gap-6">
                    ' . csrf_field() . '
                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700">Description / Remarks</label>
                        <textarea name="description" placeholder="What was this cost for?" required rows="3">' . e($exp->remarks) . '</textarea>
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-sm tw-font-bold tw-text-slate-700">Service Date</label>
                            <input type="text" name="date" class="datepicker" value="' . $exp->service_date . '" required>
                        </div>
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-sm tw-font-bold tw-text-slate-700">Cost (JOD)</label>
                            <input type="number" step="0.01" name="cost" value="' . $exp->cost . '" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700">Time / Schedule</label>
                        <input type="text" name="time" value="' . e($exp->service_time) . '" placeholder="e.g. 10:00 AM">
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-sm tw-font-bold tw-text-slate-700">Payment Status</label>
                            <select name="payment_status">
                                <option value="">Select...</option>
                                <option value="pending" ' . ($exp->payment_status=='pending'?'selected':'') . '>Pending</option>
                                <option value="completed" ' . ($exp->payment_status=='completed'?'selected':'') . '>Completed</option>
                                <option value="confirmed" ' . ($exp->payment_status=='confirmed'?'selected':'') . '>Confirmed</option>
                                <option value="cancelled" ' . ($exp->payment_status=='cancelled'?'selected':'') . '>Cancelled</option>
                            </select>
                        </div>
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-sm tw-font-bold tw-text-slate-700">Overall Status</label>
                            <select name="status">
                                <option value="">Select...</option>
                                <option value="pending" ' . ($exp->status=='pending'?'selected':'') . '>Pending</option>
                                <option value="completed" ' . ($exp->status=='completed'?'selected':'') . '>Completed</option>
                                <option value="confirmed" ' . ($exp->status=='confirmed'?'selected':'') . '>Confirmed</option>
                                <option value="cancelled" ' . ($exp->status=='cancelled'?'selected':'') . '>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="tw-pt-6 tw-border-t tw-border-slate-50">
                        <button type="submit" class="btn amber !tw-w-full !tw-py-4">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script>$(\'.datepicker\').datepicker({autoHide:true,format:\'yyyy-mm-dd\'});</script>';
        return response($html);
    }

    public function updateExpense(Request $request, $invoiceId, $expenseId)
    {
        $exp = InvoiceExpense::findOrFail($expenseId);
        $exp->update([
            'remarks' => $request->input('description'),
            'service_date' => $request->input('date'),
            'service_time' => $request->input('time', ''),
            'service_end_date' => $request->input('end_date', '0000-00-00'),
            'cost' => floatval($request->input('cost')),
            'payment_status' => $request->input('payment_status', ''),
            'status' => $request->input('status', ''),
        ]);
        $totalCost = InvoiceExpense::where('invoice_id', $invoiceId)->sum('cost');
        Invoice::where('id', $invoiceId)->update(['cost' => $totalCost]);

        return redirect()->route('admin.invoices.expenses', $invoiceId)->with('success', 'Expense updated successfully');
    }

    public function deleteExpense($invoiceId, $expenseId)
    {
        $exp = InvoiceExpense::findOrFail($expenseId);
        $exp->delete();
        $totalCost = InvoiceExpense::where('invoice_id', $invoiceId)->sum('cost');
        Invoice::where('id', $invoiceId)->update(['cost' => $totalCost]);

        return redirect()->route('admin.invoices.expenses', $invoiceId)->with('success', 'Expense deleted successfully');
    }

    public function sendInvoice(Request $request, $id)
    {
        // TODO: Implement email sending
        return redirect()->route('admin.invoices.edit', $id)->with('success', 'Invoice sent');
    }

    public function transactionsAjax($id)
    {
        $invoice = Invoice::findOrFail($id);
        $transactions = InvoiceTransaction::where('invoice_id', $id)->get();

        $html = '<div class="modal" id="transations">
            <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[800px] !tw-max-w-[95vw] tw-shadow-2xl">
                <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
                    <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                        <i class="fa fa-refresh tw-text-orange-400"></i> Invoice #' . $id . ' Transactions
                    </h3>
                    <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
                </div>';

        // Header Actions
        $html .= '<div class="tw-px-8 tw-py-4 tw-bg-slate-50/50 tw-border-b tw-border-slate-100 tw-flex tw-justify-between tw-items-center">
            <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase">Payment History</span>
            <button class="tw-text-xs tw-font-black tw-text-orange-600 hover:tw-text-orange-800 tw-flex tw-items-center tw-gap-1.5 tw-transition-colors" onclick="do_ajax(\'#ajax\',\'' . route('admin.invoices.transactions.add-form', $id) . '\',\'#transations\');">
                <i class="fa fa-plus-circle"></i> LOG NEW PAYMENT
            </button>
        </div>';

        if ($transactions->isEmpty()) {
            $html .= '<div class="tw-p-12 tw-text-center">
                <div class="tw-w-14 tw-h-14 tw-rounded-full tw-bg-slate-50 tw-mx-auto tw-flex tw-items-center tw-justify-center tw-text-slate-200 tw-mb-4">
                    <i class="fa fa-money tw-text-2xl"></i>
                </div>
                <p class="tw-text-slate-400 tw-text-sm">No transactions found for this invoice.</p>
            </div>';
        } else {
            $html .= '<div class="tw-overflow-x-auto"><table class="tw-w-full tw-text-left">
                <thead>
                    <tr class="tw-border-b tw-border-slate-50">
                        <th class="tw-py-3 tw-px-8 tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase">Details</th>
                        <th class="tw-py-3 tw-px-4 tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase">Total & Status</th>
                        <th class="tw-py-3 tw-px-4 tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase">Reference</th>
                        <th class="tw-py-3 tw-px-8 tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">';

            foreach ($transactions as $t) {
                $statusLabel = '';
                if ($t->status == 'c') $statusLabel = '<span class="tw-px-1.5 tw-py-0.5 tw-bg-emerald-50 tw-text-emerald-600 tw-text-[8px] tw-font-black tw-rounded tw-uppercase">Paid</span>';
                elseif ($t->status == 'u') $statusLabel = '<span class="tw-px-1.5 tw-py-0.5 tw-bg-rose-50 tw-text-rose-600 tw-text-[8px] tw-font-black tw-rounded tw-uppercase">Unpaid</span>';
                elseif ($t->status == 'pa') $statusLabel = '<span class="tw-px-1.5 tw-py-0.5 tw-bg-amber-50 tw-text-amber-600 tw-text-[8px] tw-font-black tw-rounded tw-uppercase">Partly</span>';
                else $statusLabel = '<span class="tw-px-1.5 tw-py-0.5 tw-bg-slate-100 tw-text-slate-400 tw-text-[8px] tw-font-black tw-rounded tw-uppercase">' . $t->status . '</span>';

                $addedBy = 'System';
                if ($t->added_by) {
                    $u = User::find($t->added_by);
                    if ($u) $addedBy = $u->first_name;
                }

                $html .= '<tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                    <td class="tw-py-4 tw-px-8">
                        <div class="tw-flex tw-flex-col">
                            <span class="tw-text-xs tw-font-bold tw-text-slate-700">' . e($t->description) . '</span>
                            <span class="tw-text-[9px] tw-text-slate-400 tw-mt-1">By: ' . $t->payment_method . ' / Added by ' . $addedBy . '</span>
                        </div>
                    </td>
                    <td class="tw-py-4 tw-px-4">
                        <div class="tw-flex tw-flex-col tw-gap-1">
                            <span class="tw-text-xs tw-font-black tw-text-slate-900">' . number_format($t->total, 2) . ' JOD</span>
                            ' . $statusLabel . '
                        </div>
                    </td>
                    <td class="tw-py-4 tw-px-4">
                        <div class="tw-flex tw-flex-col">
                            <span class="tw-text-xs tw-font-medium tw-text-slate-500">' . e($t->transaction_reference) . '</span>
                            <span class="tw-text-[9px] tw-text-slate-400 tw-mt-1">' . $t->date . '</span>
                        </div>
                    </td>
                    <td class="tw-py-4 tw-px-8 tw-text-right">
                        <div class="tw-flex tw-justify-end tw-items-center tw-gap-1">
                            <button onclick="do_ajax(\'#ajax\',\'' . route('admin.invoices.transactions.edit-form', [$id, $t->id]) . '\',\'#transations\');" class="tw-w-7 tw-h-7 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-text-amber-500 hover:tw-bg-amber-50 tw-transition-all" title="Edit"><i class="fa fa-edit"></i></button>
                            <button onclick="if(confirm(\'Delete this transaction?\')){do_ajax(\'#ajax\',\'' . route('admin.invoices.transactions.delete', [$id, $t->id]) . '\',\'#transations\');}" class="tw-w-7 tw-h-7 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-text-rose-500 hover:tw-bg-rose-50 tw-transition-all" title="Delete"><i class="fa fa-trash"></i></button>
                        </div>
                    </td>
                </tr>';
            }
            $html .= '</tbody></table></div>';
        }

        $html .= '</div></div>';
        return response($html);
    }

    public function transactionAddForm($id)
    {
        $html = '<div class="modal" id="transations">
            <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[500px] !tw-max-w-[95vw] tw-shadow-2xl">
                <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
                    <h3 class="tw-text-base tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                        <i class="fa fa-plus-circle tw-text-orange-400"></i> Log New Transaction
                    </h3>
                    <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
                </div>
                
                <div class="tw-px-8 tw-py-4 tw-bg-slate-50/50 tw-border-b tw-border-slate-100 tw-flex tw-justify-between tw-items-center">
                    <span class="tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase">Invoice #' . $id . '</span>
                    <button class="tw-text-[10px] tw-font-bold tw-text-amber-600 hover:tw-text-amber-700 tw-flex tw-items-center tw-gap-1" onclick="do_ajax(\'#ajax\',\'' . route('admin.invoices.transactions', $id) . '\',\'#transations\');">
                        <i class="fa fa-arrow-left"></i> BACK TO LIST
                    </button>
                </div>

                <form id="edit_form" onsubmit="do_ajax_post(\'#ajax\',\'' . route('admin.invoices.transactions.store', $id) . '\',\'#transations\',\'#edit_form\'); return false;" class="tw-p-8 tw-flex tw-flex-col tw-gap-5">
                    ' . csrf_field() . '
                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <label class="tw-text-xs tw-font-bold tw-text-slate-700">Transaction Description</label>
                        <input type="text" name="description" placeholder="e.g. Bank Transfer" required class="!tw-h-10">
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-xs tw-font-bold tw-text-slate-700">Reference #</label>
                            <input type="text" name="transaction_reference" placeholder="Ref ID" required class="!tw-h-10">
                        </div>
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-xs tw-font-bold tw-text-slate-700">Paid By / Method</label>
                            <input type="text" name="payment_method" placeholder="Method" required class="!tw-h-10">
                        </div>
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-xs tw-font-bold tw-text-slate-700">Amount (JOD)</label>
                            <input type="number" step="0.01" name="total" placeholder="0.00" required class="!tw-h-10">
                        </div>
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-xs tw-font-bold tw-text-slate-700">Transaction Date</label>
                            <input type="text" name="date" class="datepicker" value="' . date('Y-m-d') . '" required class="!tw-h-10">
                        </div>
                    </div>

                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <label class="tw-text-xs tw-font-bold tw-text-slate-700">Payment Status</label>
                        <select name="status" class="!tw-h-10">
                            <option value="c">Paid</option>
                            <option value="u">Unpaid</option>
                            <option value="pa">Partly</option>
                            <option value="ca">Cancelled</option>
                            <option value="r">Refunded</option>
                        </select>
                    </div>

                    <div class="tw-pt-4 tw-border-t tw-border-slate-50">
                        <button type="submit" class="btn amber !tw-w-full !tw-py-4">
                            <i class="fa fa-check-circle"></i> Save Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script>$(\'.datepicker\').datepicker({autoHide:true,format:\'yyyy-mm-dd\'});</script>';
        return response($html);
    }

    public function transactionEditForm($invoiceId, $transId)
    {
        $t = InvoiceTransaction::findOrFail($transId);
        $html = '<div class="modal" id="transations">
            <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[500px] !tw-max-w-[95vw] tw-shadow-2xl">
                <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
                    <h3 class="tw-text-base tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                        <i class="fa fa-edit tw-text-orange-400"></i> Edit Transaction
                    </h3>
                    <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
                </div>
                
                <div class="tw-px-8 tw-py-4 tw-bg-slate-50/50 tw-border-b tw-border-slate-100 tw-flex tw-justify-between tw-items-center">
                    <span class="tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase">Invoice #' . $invoiceId . '</span>
                    <button class="tw-text-[10px] tw-font-bold tw-text-amber-600 hover:tw-text-amber-700 tw-flex tw-items-center tw-gap-1" onclick="do_ajax(\'#ajax\',\'' . route('admin.invoices.transactions', $invoiceId) . '\',\'#transations\');">
                        <i class="fa fa-arrow-left"></i> BACK TO LIST
                    </button>
                </div>

                <form id="edit_form" onsubmit="do_ajax_post(\'#ajax\',\'' . route('admin.invoices.transactions.update', [$invoiceId, $transId]) . '\',\'#transations\',\'#edit_form\'); return false;" class="tw-p-8 tw-flex tw-flex-col tw-gap-5">
                    ' . csrf_field() . '
                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <label class="tw-text-xs tw-font-bold tw-text-slate-700">Transaction Description</label>
                        <input type="text" name="description" value="' . e($t->description) . '" placeholder="e.g. Bank Transfer" required class="!tw-h-10">
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-xs tw-font-bold tw-text-slate-700">Reference #</label>
                            <input type="text" name="transaction_reference" value="' . e($t->transaction_reference) . '" placeholder="Ref ID" required class="!tw-h-10">
                        </div>
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-xs tw-font-bold tw-text-slate-700">Paid By / Method</label>
                            <input type="text" name="payment_method" value="' . e($t->payment_method) . '" placeholder="Method" required class="!tw-h-10">
                        </div>
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-xs tw-font-bold tw-text-slate-700">Amount (JOD)</label>
                            <input type="number" step="0.01" name="total" value="' . $t->total . '" placeholder="0.00" required class="!tw-h-10">
                        </div>
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label class="tw-text-xs tw-font-bold tw-text-slate-700">Transaction Date</label>
                            <input type="text" name="date" class="datepicker" value="' . $t->date . '" required class="!tw-h-10">
                        </div>
                    </div>

                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <label class="tw-text-xs tw-font-bold tw-text-slate-700">Payment Status</label>
                        <select name="status" class="!tw-h-10">
                            <option value="c" ' . ($t->status=='c'?'selected':'') . '>Paid</option>
                            <option value="u" ' . ($t->status=='u'?'selected':'') . '>Unpaid</option>
                            <option value="pa" ' . ($t->status=='pa'?'selected':'') . '>Partly</option>
                            <option value="ca" ' . ($t->status=='ca'?'selected':'') . '>Cancelled</option>
                            <option value="r" ' . ($t->status=='r'?'selected':'') . '>Refunded</option>
                        </select>
                    </div>

                    <div class="tw-pt-4 tw-border-t tw-border-slate-50">
                        <button type="submit" class="btn amber !tw-w-full !tw-py-4">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script>$(\'.datepicker\').datepicker({autoHide:true,format:\'yyyy-mm-dd\'});</script>';
        return response($html);
    }

    public function storeTransaction(Request $request, $id)
    {
        InvoiceTransaction::create([
            'invoice_id' => $id,
            'description' => $request->input('description'),
            'total' => floatval($request->input('total')),
            'status' => $request->input('status'),
            'payment_method' => $request->input('payment_method'),
            'transaction_reference' => $request->input('transaction_reference'),
            'date' => $request->input('date'),
            'added_by' => auth()->id(),
        ]);

        // Update total_paid on invoice
        $totalPaid = InvoiceTransaction::where('invoice_id', $id)->sum('total');
        Invoice::where('id', $id)->update(['total_paid' => $totalPaid]);

        return $this->transactionsAjax($id);
    }

    public function updateTransaction(Request $request, $invoiceId, $transId)
    {
        $t = InvoiceTransaction::findOrFail($transId);
        $t->update([
            'description' => $request->input('description'),
            'total' => floatval($request->input('total')),
            'status' => $request->input('status'),
            'payment_method' => $request->input('payment_method'),
            'transaction_reference' => $request->input('transaction_reference'),
            'date' => $request->input('date'),
        ]);

        $totalPaid = InvoiceTransaction::where('invoice_id', $invoiceId)->sum('total');
        Invoice::where('id', $invoiceId)->update(['total_paid' => $totalPaid]);

        return $this->transactionsAjax($invoiceId);
    }

    public function deleteTransaction($invoiceId, $transId)
    {
        InvoiceTransaction::destroy($transId);
        $totalPaid = InvoiceTransaction::where('invoice_id', $invoiceId)->sum('total');
        Invoice::where('id', $invoiceId)->update(['total_paid' => $totalPaid]);

        return $this->transactionsAjax($invoiceId);
    }
}
