<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceExpense;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Country;
use App\Models\User;
use App\Models\TourBooking;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $invoiceId = $request->input('invoice');

        // If no invoice specified, show all expenses
        if (!$invoiceId) {
            $expensesQuery = InvoiceExpense::with(['invoice', 'venderUser', 'service.serviceCategory', 'addedByUser', 'booking'])
                ->orderByDesc('id');

            // Filters
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $expensesQuery->where(function($q) use ($searchTerm) {
                    $q->where('remarks', 'like', "%{$searchTerm}%")
                      ->orWhere('id', $searchTerm)
                      ->orWhere('confirmation_number', 'like', "%{$searchTerm}%")
                      ->orWhereHas('service', function($sq) use ($searchTerm) {
                          $sq->where('description', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('venderUser', function($vq) use ($searchTerm) {
                          $vq->where('first_name', 'like', "%{$searchTerm}%")
                             ->orWhere('last_name', 'like', "%{$searchTerm}%")
                             ->orWhere('company', 'like', "%{$searchTerm}%");
                      });
                });
            }
            if ($request->filled('status')) {
                $expensesQuery->where('status', $request->status);
            }
            if ($request->filled('vender')) {
                $expensesQuery->where('vender', $request->vender);
            }
            // For country, we might need to join services or categories if not direct
            if ($request->filled('country')) {
                $expensesQuery->whereHas('service.serviceCategory', function($q) use ($request) {
                    $q->where('country_id', intval($request->country));
                });
            }

            // Booking number filter
            if ($request->filled('booking_number')) {
                $filterBooking = TourBooking::find($request->booking_number);
                if ($filterBooking && $filterBooking->invoice_id) {
                    $expensesQuery->where('invoice_id', $filterBooking->invoice_id);
                }
            }

            $expenses = $expensesQuery->paginate(15)->onEachSide(1);
            
            // Data for filters (Restricted to specific countries as per reference)
            $vendorsList = User::whereHas('venderExpenses')->get(['id', 'first_name', 'last_name']);
            $countriesList = Country::where('lang', 'en')
                ->whereIn('name', ['Egypt', 'Jordan', 'Lebanon', 'Libya', 'Morocco', 'Oman', 'Palestine'])
                ->orderBy('name')
                ->get(['lang_id', 'name']);
            $statusesList = [
                'pen' => 'Pending', 
                'inp' => 'In Progress', 
                'com' => 'Completed', 
                'con' => 'Confirmed',
                'can' => 'Cancelled'
            ];

            // Bookings for filter & create dropdowns
            $bookings = TourBooking::orderByDesc('id')->get(['id', 'guest_name', 'invoice_id']);

            // Service providers (vendors who have expenses)
            $serviceProviders = User::whereHas('venderExpenses')->get(['id', 'first_name', 'last_name', 'company']);

            // Country lookup for display
            $countriesLookup = Country::where('lang', 'en')->pluck('name', 'lang_id')->toArray();

            return view('admin.expenses.index', compact('expenses', 'vendorsList', 'countriesList', 'statusesList', 'bookings', 'serviceProviders', 'countriesLookup'));
        }

        // Invoice-based expenses page (from booking)
        $invoice = Invoice::findOrFail($invoiceId);

        // Check for invoices_set (linked invoices)
        $invoiceIds = [$invoiceId];
        if (!empty($invoice->invoices_set)) {
            $linkedInvoices = @unserialize($invoice->invoices_set, ['allowed_classes' => false]);
            if (is_array($linkedInvoices) && count($linkedInvoices) > 0) {
                $invoiceIds = $linkedInvoices;
            }
        }

        // Build expenses query
        $expensesQuery = InvoiceExpense::with(['invoice', 'venderUser', 'service.serviceCategory', 'addedByUser'])
            ->whereIn('invoice_id', $invoiceIds)
            ->orderBy('service_date', 'asc');

        // Vendor filter
        if ($request->filled('vender') && intval($request->vender) > 0) {
            $expensesQuery->where('vender', intval($request->vender));
        }

        $expenses = $expensesQuery->get();

        // Calculate totals
        $totalCost = 0;
        $totalPaid = 0;
        $totalUnpaid = 0;
        foreach ($expenses as $expense) { if ($expense->status == "can") continue;
            $totalCost += $expense->cost;
            if ($expense->payment_status == 'c') {
                $totalPaid += $expense->cost;
            } else {
                $totalUnpaid += $expense->cost;
            }
        }

        // Calculate Revenue and Profit
        $totalRevenue = Invoice::whereIn('id', $invoiceIds)->sum('total');
        $totalProfit = $totalRevenue - $totalCost;

        // Get distinct vendors for filter dropdown
        $vendors = InvoiceExpense::where('invoice_id', $invoiceId)
            ->with('venderUser')
            ->select('vender')
            ->distinct()
            ->get()
            ->mapWithKeys(function ($item) {
                $name = 'Unknown';
                if ($item->venderUser) {
                    $name = !empty($item->venderUser->company) ? $item->venderUser->company : $item->venderUser->email;
                }
                return [$item->vender => $name];
            })->toArray();

        // Countries for reference
        $countries = Country::where('lang', 'en')->pluck('name', 'lang_id')->toArray();

        // Status maps
        $statusList = [
            'pen' => 'Pending', 'can' => 'Cancelled', 'com' => 'Completed',
            'inp' => 'In Process', 'con' => 'Confirmed',
        ];
        $statusColors = [
            'pen' => 'orange', 'can' => 'red', 'com' => 'green',
            'inp' => 'blue', 'con' => 'green',
        ];

        // Get categories for add new modal
        $categories = ServiceCategory::with('children.children.children')
            ->whereNull('parent_id')
            ->orWhere('parent_id', 0)
            ->orderBy('name')
            ->get();

        return view('admin.expenses.invoice', compact(
            'invoice', 'expenses', 'totalCost', 'totalPaid', 'totalUnpaid',
            'totalRevenue', 'totalProfit',
            'vendors', 'invoiceId', 'countries', 'statusList', 'statusColors',
            'categories'
        ));
    }

    /**
     * AJAX: Get services for a selected category
     */
    public function getServices(Request $request)
    {
        $categoryId = intval($request->input('category'));
        $vendorFilter = $request->input('vender');

        $category = ServiceCategory::find($categoryId);
        // Build full category path: walk up all parents
        $categoryName = $category ? $category->name : '';
        $pathParts = [];
        if ($category) {
            $parent = $category->parent;
            while ($parent) {
                array_unshift($pathParts, $parent->name);
                $parent = $parent->parent;
            }
        }
        $parentName = count($pathParts) > 0 ? implode(' > ', $pathParts) . ' > ' : '';

        // Get all descendant category IDs (so clicking "5 Stars" also shows services from child categories like specific hotel names)
        $allCategoryIds = [$categoryId];
        $allCategoryIds = array_merge($allCategoryIds, $this->getAllDescendantCatIds($categoryId));

        $servicesQuery = Service::with('venderUser')
            ->whereIn('category', $allCategoryIds);

        if ($vendorFilter && intval($vendorFilter) > 0) {
            $servicesQuery->where('vender', intval($vendorFilter));
        }

        $services = $servicesQuery->orderBy('description')->get();

        // Get distinct vendors for these categories
        $categoryVendors = Service::whereIn('category', $allCategoryIds)
            ->with('venderUser')
            ->select('vender')
            ->distinct()
            ->get()
            ->mapWithKeys(function ($item) {
                $name = 'Unknown';
                if ($item->venderUser) {
                    $name = !empty($item->venderUser->company) ? $item->venderUser->company : $item->venderUser->email;
                }
                return [$item->vender => $name];
            })->toArray();

        return response()->json([
            'categoryName' => $parentName . $categoryName,
            'services' => $services->map(function ($s) {
                $venderName = 'N/A';
                if ($s->venderUser) {
                    $venderName = !empty($s->venderUser->company) ? $s->venderUser->company : ($s->venderUser->first_name . ' ' . $s->venderUser->last_name);
                }
                return [
                    'id' => $s->id,
                    'description' => $s->description,
                    'notes' => $s->notes,
                    'cost' => number_format($s->cost, 2),
                    'cost_raw' => $s->cost,
                    'vender' => $s->vender,
                    'vender_name' => $venderName,
                ];
            }),
            'vendors' => $categoryVendors,
        ]);
    }

    /**
     * Recursively get all descendant category IDs
     */
    private function getAllDescendantCatIds($parentId)
    {
        $ids = [];
        $children = ServiceCategory::where('parent_id', $parentId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, $this->getAllDescendantCatIds($childId));
        }
        return $ids;
    }

    /**
     * AJAX: Get service detail form data
     */
    public function getServiceDetail(Request $request)
    {
        $serviceId = intval($request->input('service'));
        $service = Service::with('venderUser')->findOrFail($serviceId);

        $venderName = 'N/A';
        if ($service->venderUser) {
            $venderName = !empty($service->venderUser->company)
                ? $service->venderUser->company
                : ($service->venderUser->first_name . ' ' . $service->venderUser->last_name);
        }

        return response()->json([
            'id' => $service->id,
            'description' => $service->description,
            'cost' => $service->cost,
            'vender' => $service->vender,
            'vender_name' => $venderName,
            'duration' => 1,
        ]);
    }

    /**
     * Store a new expense
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|integer',
            'service_id' => 'required|integer',
            'date' => 'required|date',
            'cost' => 'required|numeric',
            'qty' => 'required|integer|min:1',
        ]);

        $service = Service::findOrFail($request->service_id);
        $invoice = Invoice::findOrFail($request->invoice_id);

        $unitCost = floatval($request->cost);
        $qty = intval($request->qty);
        $totalCost = $unitCost * $qty;

        $expense = InvoiceExpense::create([
            'invoice_id' => $request->invoice_id,
            'service_id' => $request->service_id,
            'cost' => $totalCost,
            'service_date' => $request->date,
            'service_end_date' => !empty($request->end_date) ? $request->end_date : $request->date,
            'service_time' => $request->time ?? '',
            'status' => $request->status ?? 'pen',
            'payment_status' => '',
            'vender' => $service->vender,
            'qty' => $qty,
            'duration' => intval($request->duration ?? 1),
            'vender_notify' => 0,
            'remarks' => $request->remarks ?? '',
            'added_by' => auth()->id(),
            'time' => time(),
            'cost_per_unit' => $unitCost,
            'confirmation_number' => '',
            'paid_by' => 0,
        ]);

        // Update invoice cost
        $invoice->update([
            'cost' => $invoice->cost + $totalCost,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense added successfully',
            'totals' => $this->getUpdatedTotals($request->invoice_id)
        ]);
    }

    public function markAllCompleted(Request $request)
    {
        $invoiceId = $request->input('invoice');
        if ($invoiceId) {
            InvoiceExpense::where('invoice_id', $invoiceId)
                ->where('status', '!=', 'can')
                ->update(['status' => 'com']);
        }
        return redirect()->route('admin.expenses.index', ['invoice' => $invoiceId])
            ->with('success', 'All expenses marked as completed');
    }

    public function destroy(Request $request, $id)
    {
        $expense = InvoiceExpense::findOrFail($id);
        $invoiceId = $expense->invoice_id;

        // Update invoice cost
        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $invoice->update(['cost' => $invoice->cost - $expense->cost]);
        }

        $expense->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.expenses.index', ['invoice' => $invoiceId])
            ->with('success', 'Expense deleted successfully');
    }

    /**
     * Get expense data for edit modal (AJAX)
     */
    public function edit($id)
    {
        $expense = InvoiceExpense::with(['service', 'venderUser'])->findOrFail($id);

        $venderName = '-';
        if ($expense->venderUser) {
            $venderName = !empty($expense->venderUser->company) ? $expense->venderUser->company : $expense->venderUser->email;
        }

        $description = $expense->service ? $expense->service->description : 'N/A';

        return response()->json([
            'id' => $expense->id,
            'invoice_id' => $expense->invoice_id,
            'vender_name' => $venderName,
            'description' => $description,
            'service_date' => $expense->service_date,
            'service_end_date' => $expense->service_end_date,
            'service_time' => $expense->service_time,
            'cost' => $expense->cost_per_unit ?? $expense->cost,
            'qty' => $expense->qty,
            'duration' => $expense->duration,
            'status' => $expense->status,
            'remarks' => $expense->remarks,
        ]);
    }

    /**
     * Update an expense (AJAX)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'cost' => 'required|numeric',
            'qty' => 'required|integer|min:1',
        ]);

        $expense = InvoiceExpense::findOrFail($id);
        $oldCost = $expense->cost;

        $unitCost = floatval($request->cost);
        $qty = intval($request->qty);
        $totalCost = $unitCost * $qty;

        $expense->update([
            'service_date' => $request->date,
            'service_end_date' => !empty($request->end_date) ? $request->end_date : $request->date,
            'service_time' => $request->time ?? '',
            'cost' => $totalCost,
            'cost_per_unit' => $unitCost,
            'qty' => $qty,
            'duration' => intval($request->duration ?? 1),
            'status' => $request->status ?? 'pen',
            'remarks' => $request->remarks ?? '',
            'added_by' => auth()->id(),
            'time' => time(),
        ]);

        // Update invoice cost (adjust for difference)
        $invoice = Invoice::find($expense->invoice_id);
        if ($invoice) {
            $invoice->update(['cost' => $invoice->cost - $oldCost + $totalCost]);
        }

        // Write history
        $this->writeHistory($expense->invoice_id, $id, $expense);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'totals' => $this->getUpdatedTotals($expense->invoice_id)
        ]);
    }

    /**
     * Get expense history (AJAX)
     */
    public function history($id)
    {
        $expense = InvoiceExpense::findOrFail($id);
        $invoiceId = $expense->invoice_id;

        $historyHtml = '';

        // Try to read legacy PHP history file from old project
        $legacyPath = base_path('../pvt.jo/config/invoices/expenses/' . $invoiceId . '/' . $id . '.php');
        if (file_exists($legacyPath)) {
            $history = '';
            // Execute the PHP file which sets $history variable
            ob_start();
            include $legacyPath;
            ob_end_clean();
            $historyHtml = $history;
        }

        // Also read new JSON history from storage
        $jsonPath = storage_path('app/expenses_history/' . $invoiceId . '/' . $id . '.json');
        if (file_exists($jsonPath)) {
            $entries = json_decode(file_get_contents($jsonPath), true);
            if (!empty($entries)) {
                // If no legacy history, add header
                if (empty($historyHtml)) {
                    $historyHtml = '<div class="tw-grid tw-grid-cols-12 tw-gap-4 tw-px-4 tw-py-3 tw-bg-slate-50 tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-rounded-xl tw-mb-4">
                        <div class="tw-col-span-4">Cost Description</div>
                        <div class="tw-col-span-2 tw-text-right">Financials</div>
                        <div class="tw-col-span-2">Progress</div>
                        <div class="tw-col-span-2">Performance</div>
                        <div class="tw-col-span-2">Audit Trace</div>
                    </div>';
                }
                foreach ($entries as $entry) {
                    $statusValue = $entry['status'] ?? '';
                    $statusClass = 'tw-bg-slate-100 tw-text-slate-600';
                    if($statusValue == 'com') $statusClass = 'tw-bg-emerald-50 tw-text-emerald-600';
                    if($statusValue == 'con') $statusClass = 'tw-bg-indigo-50 tw-text-indigo-600';
                    if($statusValue == 'can') $statusClass = 'tw-bg-rose-50 tw-text-rose-600';
                    if($statusValue == 'pen') $statusClass = 'tw-bg-amber-50 tw-text-amber-600';

                    $statusLabel = $this->getStatusLabel($statusValue);

                    $historyHtml .= '<div class="tw-grid tw-grid-cols-12 tw-gap-4 tw-px-4 tw-py-4 tw-border-b tw-border-slate-50 hover:tw-bg-slate-50/50 tw-transition-colors">
                        <div class="tw-col-span-4">
                            <div class="tw-text-sm tw-font-bold tw-text-slate-700">' . htmlspecialchars($entry['description'] ?? 'Operational Entry') . '</div>
                            ' . (!empty($entry['remarks']) ? '<div class="tw-mt-1.5 tw-p-2 tw-bg-white tw-border tw-border-slate-100 tw-rounded-lg tw-text-xs tw-text-slate-500 tw-italic">"' . htmlspecialchars($entry['remarks']) . '"</div>' : '') . '
                        </div>
                        <div class="tw-col-span-2 tw-text-right">
                            <div class="tw-text-sm tw-font-black tw-text-indigo-600">JOD ' . number_format($entry['cost'] ?? 0, 2) . '</div>
                            <div class="tw-text-[10px] tw-text-slate-400 tw-mt-1">QTY: ' . intval($entry['qty'] ?? 1) . ' | DUR: ' . intval($entry['duration'] ?? 1) . '</div>
                        </div>
                        <div class="tw-col-span-2">
                            <span class="tw-inline-flex tw-px-2.5 tw-py-1 tw-rounded-lg tw-text-[10px] tw-font-black tw-uppercase ' . $statusClass . '">' . $statusLabel . '</span>
                        </div>
                        <div class="tw-col-span-2">
                            <div class="tw-text-[11px] tw-font-bold tw-text-slate-600">' . ($entry['service_date'] ?? 'N/A') . '</div>
                            <div class="tw-text-[10px] tw-text-slate-400 tw-mt-1">' . ($entry['service_end_date'] ?? '') . ' ' . ($entry['service_time'] ?? '') . '</div>
                        </div>
                        <div class="tw-col-span-2">
                            <div class="tw-text-[11px] tw-font-black tw-text-slate-900">' . htmlspecialchars($entry['edited_by_name'] ?? 'System') . '</div>
                            <div class="tw-text-[9px] tw-text-slate-400 tw-mt-1">' . ($entry['edited_at'] ?? '') . '</div>
                        </div>
                    </div>';
                }
            }
        }

        if (empty($historyHtml)) {
            $historyHtml = '<div class="box error pad">No data found</div>';
        }

        return response()->json([
            'html' => $historyHtml,
        ]);
    }

    /**
     * Write history entry to JSON file
     */
    private function writeHistory($invoiceId, $expenseId, $expense)
    {
        $dir = storage_path('app/expenses_history/' . $invoiceId);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . '/' . $expenseId . '.json';
        $entries = [];
        if (file_exists($filePath)) {
            $entries = json_decode(file_get_contents($filePath), true) ?: [];
        }

        $user = auth()->user();
        $service = $expense->service;

        $entries[] = [
            'description' => $service ? $service->description : 'N/A',
            'cost' => $expense->cost,
            'qty' => $expense->qty,
            'duration' => $expense->duration,
            'status' => $expense->status,
            'remarks' => $expense->remarks,
            'service_date' => $expense->service_date,
            'service_end_date' => $expense->service_end_date,
            'service_time' => $expense->service_time,
            'edited_by_name' => $user ? ($user->first_name . ' ' . $user->last_name) : '',
            'edited_by_email' => $user ? $user->email : '',
            'edited_at' => date('Y-m-d h:i a'),
        ];

        file_put_contents($filePath, json_encode($entries, JSON_PRETTY_PRINT));
    }

    /**
     * Get status label HTML
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pen' => '<span class="small label orange">Pending</span>',
            'con' => '<span class="small label green">confirmed</span>',
            'com' => '<span class="small label green">completed</span>',
            'can' => '<span class="small label red">Cancelled</span>',
            'inp' => '<span class="small label blue">In Process</span>',
        ];
        return $labels[$status] ?? '<span class="small label grey">' . htmlspecialchars($status) . '</span>';
    }

    /**
     * Get updated totals for financial AJAX synchronization
     */
    private function getUpdatedTotals($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $invoiceIds = [$invoiceId];
        if (!empty($invoice->invoices_set)) {
            $linkedInvoices = @unserialize($invoice->invoices_set, ['allowed_classes' => false]);
            if (is_array($linkedInvoices) && count($linkedInvoices) > 0) {
                $invoiceIds = $linkedInvoices;
            }
        }

        $expenses = InvoiceExpense::whereIn('invoice_id', $invoiceIds)->get();

        $totalCost = 0;
        $totalPaid = 0;
        $totalUnpaid = 0;
        foreach ($expenses as $expense) { if ($expense->status == "can") continue;
            $totalCost += $expense->cost;
            if ($expense->payment_status == 'c') {
                $totalPaid += $expense->cost;
            } else {
                $totalUnpaid += $expense->cost;
            }
        }

        $totalRevenue = Invoice::whereIn('id', $invoiceIds)->sum('total');
        $totalProfit = $totalRevenue - $totalCost;

        return [
            'totalCost' => number_format($totalCost, 2, '.', ''),
            'totalPaid' => number_format($totalPaid, 2, '.', ''),
            'totalUnpaid' => number_format($totalUnpaid, 2, '.', ''),
            'totalRevenue' => number_format($totalRevenue, 2, '.', ''),
            'totalProfit' => number_format($totalProfit, 2, '.', ''),
        ];
    }
}
