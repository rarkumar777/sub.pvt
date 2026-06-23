<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CustomerLedgerController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get Partners (Agencies/Branches)
        $partnersQuery = User::where('user_group', 'Partners');
        
        if ($request->filled('country')) {
            $partnersQuery->where('country', $request->country);
        }
        if ($request->filled('company')) {
            $partnersQuery->where('company', 'like', '%' . $request->company . '%');
        }
        if ($request->filled('email')) {
            $partnersQuery->where('email', 'like', '%' . $request->email . '%');
        }

        $partners = $partnersQuery->withSum('invoices as total_billed', 'total')
                                  ->withSum('invoices as total_paid', 'total_paid')
                                  ->orderBy('first_name')
                                  ->paginate(20)
                                  ->withQueryString();

        // 2. Get "Miscellaneous" (Direct Customers / 'clients' group) stats
        // Only if we are on the first page or not searching specifically for a partner
        $miscStats = null;
        if ($partners->currentPage() === 1 && !$request->filled('company') && !$request->filled('email')) {
            $miscStats = DB::table('en33_users')
                ->join('en33_invoices', 'en33_users.id', '=', 'en33_invoices.user_id')
                ->where('en33_users.user_group', 'clients')
                ->select(
                    DB::raw('SUM(en33_invoices.total) as total_billed'),
                    DB::raw('SUM(en33_invoices.total_paid) as total_paid'),
                    DB::raw('COUNT(DISTINCT en33_users.id) as total_clients')
                )
                ->first();
        }

        $countries = Country::orderBy('name')->get();

        return view('admin.customers.ledger', compact('partners', 'miscStats', 'countries'));
    }

    public function account(Request $request, $id)
    {
        // id = 'misc' means we load all invoices for 'clients'
        if ($id === 'misc') {
            $query = Invoice::whereHas('user', function($q) {
                $q->where('user_group', 'clients');
            })->with('user')->orderBy('date', 'desc');
            $accountName = 'Direct Customers (Miscellaneous)';
        } else {
            $user = User::findOrFail($id);
            $query = Invoice::where('user_id', $id)->orderBy('date', 'desc');
            $accountName = $user->company ?: $user->first_name . ' ' . $user->last_name;
        }

        // Apply filters
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status != 'all') {
                if ($status == 'paid') {
                    $query->whereColumn('total_paid', '>=', 'total');
                } else if ($status == 'unpaid') {
                    $query->whereColumn('total_paid', '<', 'total');
                }
            }
        }
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->input('to'));
        }

        $invoices = $query->get();
        $totalBilled = $invoices->sum('total');
        $totalPaid = $invoices->sum('total_paid');
        $unpaidBalance = $totalBilled - $totalPaid;

        $html = '<div class="tw-flex tw-flex-col tw-gap-0 tw-bg-slate-50">';
        
        // Header
        $html .= '<div class="tw-px-10 tw-py-14 tw-bg-orange-900 tw-flex tw-justify-between tw-items-center tw-relative tw-overflow-hidden">';
        $html .= '    <div class="tw-absolute tw-top-0 tw-right-0 tw-w-[600px] tw-h-[600px] tw-bg-orange-500/10 tw-rounded-full -tw-mr-64 -tw-mt-64 tw-blur-3xl"></div>';
        $html .= '    <div class="tw-absolute tw-bottom-0 tw-left-0 tw-w-96 tw-h-96 tw-bg-orange-500/5 tw-rounded-full -tw-ml-48 -tw-mb-48 tw-blur-3xl"></div>';
        $html .= '    <div class="tw-relative tw-z-10 tw-flex tw-flex-col tw-gap-4">';
        $html .= '        <div class="tw-flex tw-items-center tw-gap-3 tw-text-[10px] tw-font-black tw-text-orange-500 tw-uppercase tw-tracking-[0.4em]">';
        $html .= '            <div class="tw-w-10 tw-h-px tw-bg-orange-500/50"></div> Customer Financial Ledger';
        $html .= '        </div>';
        $html .= '        <h3 class="tw-text-4xl tw-font-black tw-text-white tw-tracking-tight">' . htmlspecialchars($accountName) . '</h3>';
        $html .= '    </div>';
        $html .= '    <div class="tw-relative tw-z-10 tw-flex tw-items-center tw-gap-6">';
        $html .= '        <div class="tw-flex tw-flex-col tw-items-end">';
        $html .= '            <span class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-1">Total Outstanding</span>';
        $html .= '            <span class="tw-text-3xl tw-font-black ' . ($unpaidBalance > 0 ? 'tw-text-rose-500' : 'tw-text-orange-400') . '">' . number_format($unpaidBalance, 2) . ' JOD</span>';
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '</div>';

        // Filter Bar
        $html .= '<div class="tw-bg-white tw-border-b tw-border-slate-200 tw-px-10 tw-py-4 tw-flex tw-justify-between tw-items-center tw-sticky tw-top-0 tw-z-20 tw-shadow-sm">';
        $html .= '    <form id="customer_account_filter" class="tw-flex tw-items-center tw-gap-4 tw-w-full" onsubmit="event.preventDefault(); applyCustomerAccountFilter(\'' . $id . '\');">';
        $html .= '        <div class="tw-flex tw-items-center tw-gap-2">';
        $html .= '            <i class="fa fa-filter tw-text-slate-300"></i>';
        $html .= '            <select name="status" class="tw-h-10 tw-px-4 tw-text-sm tw-font-bold tw-text-slate-700 tw-bg-slate-50 tw-border tw-border-slate-200 focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-500/20 tw-rounded-xl outline-none">';
        $html .= '                <option value="all" ' . ($request->status == 'all' ? 'selected' : '') . '>All Invoices</option>';
        $html .= '                <option value="unpaid" ' . ($request->status == 'unpaid' ? 'selected' : '') . '>Unpaid / Pending</option>';
        $html .= '                <option value="paid" ' . ($request->status == 'paid' ? 'selected' : '') . '>Paid</option>';
        $html .= '            </select>';
        $html .= '        </div>';
        $html .= '        <div class="tw-flex tw-items-center tw-gap-3 tw-ml-4">';
        $html .= '            <div class="tw-flex tw-items-center tw-gap-2">';
        $html .= '                <span class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-wider">From</span>';
        $html .= '                <input type="date" name="from" value="' . $request->from . '" class="tw-h-10 tw-px-3 tw-text-sm tw-font-bold tw-text-slate-700 tw-bg-slate-50 tw-border tw-border-slate-200 focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-500/20 tw-rounded-xl outline-none">';
        $html .= '            </div>';
        $html .= '            <div class="tw-flex tw-items-center tw-gap-2">';
        $html .= '                <span class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-wider">To</span>';
        $html .= '                <input type="date" name="to" value="' . $request->to . '" class="tw-h-10 tw-px-3 tw-text-sm tw-font-bold tw-text-slate-700 tw-bg-slate-50 tw-border tw-border-slate-200 focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-500/20 tw-rounded-xl outline-none">';
        $html .= '            </div>';
        $html .= '        </div>';
        $html .= '        <div class="tw-ml-auto">';
        $html .= '            <button type="submit" class="tw-h-10 tw-px-6 tw-bg-orange-500 hover:tw-bg-orange-700 tw-text-white tw-text-sm tw-font-black tw-rounded-xl tw-shadow-lg tw-shadow-orange-500/30 tw-transition-all tw-flex tw-items-center tw-gap-2">';
        $html .= '                <i class="fa fa-search"></i> Apply';
        $html .= '            </button>';
        $html .= '        </div>';
        $html .= '    </form>';
        $html .= '</div>';

        // Transactions Table
        $html .= '<div class="tw-p-10 tw-overflow-y-auto tw-max-h-[600px] tw-bg-slate-50">';
        $html .= '    <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-border tw-border-slate-200 tw-overflow-hidden">';
        $html .= '        <table class="tw-w-full tw-text-left">';
        $html .= '            <thead>';
        $html .= '                <tr class="tw-bg-slate-50/80 tw-border-b tw-border-slate-200">';
        $html .= '                    <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Date</th>';
        if ($id === 'misc') {
            $html .= '                <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Client</th>';
        }
        $html .= '                    <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Invoice Details</th>';
        $html .= '                    <th class="tw-px-6 tw-py-4 tw-text-right tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Amount Billed</th>';
        $html .= '                    <th class="tw-px-6 tw-py-4 tw-text-right tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Amount Paid</th>';
        $html .= '                    <th class="tw-px-6 tw-py-4 tw-text-right tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Balance</th>';
        $html .= '                </tr>';
        $html .= '            </thead>';
        $html .= '            <tbody class="tw-divide-y tw-divide-slate-100">';
        
        if ($invoices->isEmpty()) {
            $cols = $id === 'misc' ? 6 : 5;
            $html .= '<tr><td colspan="'.$cols.'" class="tw-py-12 tw-text-center tw-text-slate-500 tw-text-sm tw-font-semibold"><i class="fa fa-folder-open tw-text-4xl tw-text-slate-200 tw-mb-3 tw-block"></i> No invoices found for this criteria.</td></tr>';
        } else {
            foreach ($invoices as $inv) {
                $invBal = $inv->total - $inv->total_paid;
                $html .= '<tr class="hover:tw-bg-slate-50/50 tw-transition-colors">';
                $html .= '    <td class="tw-px-6 tw-py-4">';
                $html .= '        <span class="tw-text-sm tw-font-bold tw-text-slate-700">' . date('M d, Y', strtotime($inv->date)) . '</span>';
                $html .= '    </td>';
                
                if ($id === 'misc') {
                    $clientName = $inv->user ? ($inv->user->first_name . ' ' . $inv->user->last_name) : 'Unknown';
                    $html .= '    <td class="tw-px-6 tw-py-4"><span class="tw-text-[13px] tw-font-bold tw-text-slate-800">' . htmlspecialchars($clientName) . '</span></td>';
                }

                $html .= '    <td class="tw-px-6 tw-py-4">';
                $html .= '        <div class="tw-flex tw-flex-col">';
                $html .= '            <a href="'.route('admin.invoices.show', $inv->id).'" target="_blank" class="tw-text-[13px] tw-font-black tw-text-orange-500 hover:tw-underline">#' . str_pad($inv->id, 5, '0', STR_PAD_LEFT) . '</a>';
                $html .= '            <span class="tw-text-[11px] tw-font-semibold tw-text-slate-500 tw-mt-0.5">' . htmlspecialchars($inv->desc) . '</span>';
                $html .= '        </div>';
                $html .= '    </td>';
                $html .= '    <td class="tw-px-6 tw-py-4 tw-text-right tw-text-[13px] tw-font-black tw-text-slate-700">' . number_format($inv->total, 2) . '</td>';
                $html .= '    <td class="tw-px-6 tw-py-4 tw-text-right tw-text-[13px] tw-font-black tw-text-orange-500">' . number_format($inv->total_paid, 2) . '</td>';
                $html .= '    <td class="tw-px-6 tw-py-4 tw-text-right">';
                if ($invBal > 0) {
                    $html .= '        <span class="tw-text-[13px] tw-font-black tw-text-rose-500">' . number_format($invBal, 2) . '</span>';
                } else {
                    $html .= '        <span class="tw-text-[13px] tw-font-black tw-text-slate-400">0.00</span>';
                }
                $html .= '    </td>';
                $html .= '</tr>';
            }
        }

        $html .= '            </tbody>';
        $html .= '        </table>';
        $html .= '    </div>';
        $html .= '</div>';
        $html .= '</div>';

        return response()->json(['html' => $html]);
    }
}
