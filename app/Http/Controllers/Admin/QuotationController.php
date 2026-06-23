<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourQuotation;
use App\Models\TourQuotationDay;
use App\Models\TourQuotationPricing;
use App\Models\TourCannedDay;
use App\Models\TourInclusion;
use App\Models\ServiceCategory;
use App\Models\Invoice;
use App\Models\InvoiceExpense;
use App\Models\TourBooking;
use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = TourQuotation::orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ref_number', 'like', "%{$search}%");
            });
        }

        $quotations = $query->get();
        $user = auth()->user();
        return view('admin.quotations.index', compact('quotations', 'user'));
    }

    /**
     * AJAX: Update quotation status (drag-drop kanban)
     */
    public function updateStatus(Request $request, $id)
    {
        $quotation = TourQuotation::findOrFail($id);
        $newStatus = $request->input('status');
        if (in_array($newStatus, ['draft', 'sent', 'accepted', 'rejected'])) {
            $quotation->status = $newStatus;
            $quotation->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 422);
    }

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

    private function getExpenseCategories()
    {
        // Fast-access categories matching the reference site (pvt.jo)
        // These are the curated categories shown as quick-access buttons
        $fastAccess = [
            ['id' => 404, 'name' => 'Hotels', 'color' => '#4CAF50'],
            ['id' => 204, 'name' => 'Activities in jordan', 'color' => '#2196F3'],
            ['id' => 456, 'name' => 'Restaurants', 'color' => '#009688'],
            ['id' => 715, 'name' => 'Tranportation', 'color' => '#2196F3'],
        ];

        $categories = [];
        foreach ($fastAccess as $fa) {
            $cat = ServiceCategory::find($fa['id']);
            if ($cat) {
                $subCats = ServiceCategory::where('parent_id', $fa['id'])
                    ->orderBy('name')
                    ->get(['id', 'name']);
                $categories[] = [
                    'id' => $fa['id'],
                    'name' => $fa['name'],
                    'color' => $fa['color'],
                    'sub_categories' => $subCats,
                ];
            }
        }

        return $categories;
    }

    private function stripQuotationDayMeta($contents)
    {
        return preg_replace('/^\s*<!--QTP_META:[A-Za-z0-9+\/=]+-->\s*/', '', $contents ?? '');
    }

    private function buildQuotationDayContents(Request $request, $dayNumber, $contents)
    {
        $meta = [
            'title' => $request->input('day_title_' . $dayNumber, ''),
            'sites' => array_values(array_filter($request->input('day_sites_' . $dayNumber, []))),
            'meal_type' => $request->input('meal_type_' . $dayNumber, 'none'),
            'meal_options' => array_values(array_filter($request->input('meal_options_' . $dayNumber, []))),
            'accommodation' => [
                'name'        => $request->input('day_accommodation_name_' . $dayNumber, ''),
                'location'    => $request->input('day_accommodation_location_' . $dayNumber, ''),
                'image'       => $request->input('day_accommodation_image_' . $dayNumber, ''),
                'description' => $request->input('day_accommodation_description_' . $dayNumber, ''),
                'type'        => $request->input('day_accommodation_type_' . $dayNumber, ''),
                'category'    => $request->input('day_accommodation_category_' . $dayNumber, ''),
            ],
            'accommodation_alternatives' => array_values(array_filter($request->input('day_accommodation_alt_' . $dayNumber, []))),
            'services' => array_values(array_filter($request->input('day_services_' . $dayNumber, []))),
        ];

        $cleanContents = $this->stripQuotationDayMeta($contents);
        return '<!--QTP_META:' . base64_encode(json_encode($meta)) . '-->' . $cleanContents;
    }

    public function create(Request $request)
    {
        $pricingBases = TourQuotationPricing::all();
        $cannedDays = TourCannedDay::with(['contents' => function ($q) {
            $q->where('lang', 'en');
        }])->orderByDesc('id')->get();
        $expenseCategories = $this->getExpenseCategories();
        $inclusions = TourInclusion::where('lang', 'en')->orderBy('name')->get();
        $expenseCountries = DB::table('en33_countries')
            ->where('lang', 'en')
            ->whereIn('id', [71, 123, 134, 137, 160, 177, 1565, 190, 203])
            ->orderBy('name')
            ->get(['id', 'name']);
        // Prefill from Request Manager if query params present
        $prefill = [
            'customer_name' => $request->query('customer_name', ''),
            'email' => $request->query('email', ''),
            'phone' => $request->query('phone', ''),
            'description' => $request->query('description', ''),
            'ref_number' => $request->query('ref_number', ''),
            'travel_date' => $request->query('travel_date', date('Y-m-d')),
            'days' => $request->query('days', 1),
            'nights' => $request->query('nights', 0),
            'travelers_number' => $request->query('travelers_number', 1),
            'trip_request_id' => $request->query('trip_request_id', ''),
        ];
        return view('admin.quotations.create', compact('pricingBases', 'expenseCategories', 'inclusions', 'expenseCountries', 'prefill'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'days' => 'required|integer',
            'nights' => 'required|integer',
        ]);

        $data = $request->only([
            'customer_name', 'email', 'phone', 'description',
            'ref_number', 'travel_date', 'days', 'nights',
            'travelers_number', 'pricing_base', 'lang',
        ]);
        // Set defaults for NOT NULL fields that aren't in the form
        $data['email'] = $data['email'] ?? '';
        $data['phone'] = $data['phone'] ?? '';
        $data['description'] = $data['description'] ?? '';
        $data['ref_number'] = $data['ref_number'] ?? '';
        $data['travel_date'] = $data['travel_date'] ?? now()->toDateString();
        $data['travelers_number'] = $data['travelers_number'] ?? 1;
        $data['pricing_base'] = $data['pricing_base'] ?? 0;
        $data['lang'] = $data['lang'] ?? 'en';
        $data['added_by'] = auth()->id();
        $data['last_edited'] = auth()->id();
        $data['views'] = 0;
        $data['total_cost'] = 0;
        $data['total'] = 0;
        $quotation = TourQuotation::create($data);

        // Auto-create a linked Invoice for expense tracking
        $invoice = Invoice::create([
            'items' => '',
            'discount' => 0,
            'tax' => 0,
            'status' => 'un',
            'type' => 'q',
            'module' => 'tours',
            'user_id' => 0,
            'desc' => 'Quotation #' . ($data['ref_number'] ?: $quotation->id) . ' - ' . $data['customer_name'],
            'date' => $data['travel_date'],
            'total' => 0,
            'cost' => 0,
            'added_by' => auth()->id(),
            'paid_by' => 0,
            'partly_payment' => 0,
            'total_paid' => 0,
            'due_to_date' => $data['travel_date'],
            'discount_description' => '',
            'sent_count' => 0,
            'invoices_set' => '',
        ]);
        $quotation->update(['invoice_id' => $invoice->id]);

        // Create days WITH submitted content
        $days = intval($request->days);
        for ($c = 1; $c <= $days; $c++) {
            $contents = $request->input('desc_day_' . $c) ?? '';
            $contents = $this->buildQuotationDayContents($request, $c, $contents);
            $expenseIds = $request->input('expenses_day_' . $c, []);
            $expenseNames = $request->input('expenses_name_' . $c, []);
            $expenseQtys = $request->input('expenses_qty_' . $c, []);
            $expenseCosts = $request->input('expenses_cost_' . $c, []);
            $expenseDates = $request->input('expenses_date_' . $c, []);
            $expensesArr = [];
            if (is_array($expenseIds)) {
                foreach ($expenseIds as $key => $expId) {
                    $expensesArr[] = [
                        'id' => $expId,
                        'desc' => $expenseNames[$key] ?? '',
                        'qty' => $expenseQtys[$key] ?? 1,
                        'cost' => floatval($expenseCosts[$key] ?? 0),
                        'date' => $expenseDates[$key] ?? '',
                    ];
                }
            }
            $included = $request->input('day_inc_' . $c, []);
            $excluded = $request->input('day_exc_' . $c, []);
            $images = $request->input('day_images_' . $c, []);

            TourQuotationDay::create([
                'quotation_id' => $quotation->id,
                'day_number' => $c,
                'contents' => $contents,
                'expenses' => !empty($expensesArr) ? serialize($expensesArr) : '',
                'total_cost' => 0,
                'included' => !empty($included) ? serialize($included) : '',
                'excluded' => !empty($excluded) ? serialize($excluded) : '',
                'images' => !empty($images) ? serialize($images) : '',
            ]);
        }

        return redirect()->route('admin.quotations.edit', $quotation->id)->with('success', 'Quotation created');
    }

    public function edit($id)
    {
        $quotation = TourQuotation::with(['quotationDays', 'pricingBase'])->findOrFail($id);

        // Auto-create invoice if missing (for legacy quotations)
        if (!$quotation->invoice_id) {
            $invoice = Invoice::create([
                'items' => '', 'discount' => 0, 'tax' => 0, 'status' => 'un',
                'type' => 'q', 'module' => 'tours', 'user_id' => 0,
                'desc' => 'Quotation #' . ($quotation->ref_number ?: $quotation->id) . ' - ' . $quotation->customer_name,
                'date' => $quotation->travel_date, 'total' => 0, 'cost' => 0,
                'added_by' => auth()->id(), 'paid_by' => 0, 'partly_payment' => 0,
                'total_paid' => 0, 'due_to_date' => $quotation->travel_date,
                'discount_description' => '', 'sent_count' => 0, 'invoices_set' => '',
            ]);
            $quotation->update(['invoice_id' => $invoice->id]);
            $quotation->refresh();
        }

        // Load expenses from linked invoice
        $invoiceExpenses = InvoiceExpense::with(['service.serviceCategory', 'venderUser'])
            ->where('invoice_id', $quotation->invoice_id)
            ->orderBy('id')
            ->get();

        // Totals
        $totalExpenses = $invoiceExpenses->where('status', '!=', 'can')->sum('cost');
        $profitAmount = $quotation->profit_amount ?? 0;
        $clientTotal = $totalExpenses + $profitAmount;

        $pricingBases = TourQuotationPricing::all();
        $cannedDays = TourCannedDay::with(['contents' => function ($q) {
            $q->where('lang', 'en');
        }])->orderByDesc('id')->get();
        $expenseCategories = $this->getExpenseCategories();
        $inclusions = TourInclusion::where('lang', 'en')->orderBy('name')->get();
        $expenseCountries = DB::table('en33_countries')
            ->where('lang', 'en')
            ->whereIn('id', [71, 123, 134, 137, 160, 177, 1565, 190, 203])
            ->orderBy('name')
            ->get(['id', 'name']);

        // Service categories for add-expense modal
        $categories = ServiceCategory::with('children.children.children')
            ->whereNull('parent_id')
            ->orWhere('parent_id', 0)
            ->orderBy('name')
            ->get();

        // Linked booking (if validated)
        $linkedBooking = TourBooking::where('quotation_id', $quotation->id)->first();

        return view('admin.quotations.edit', compact(
            'quotation', 'pricingBases', 'expenseCategories', 'inclusions', 'expenseCountries',
            'invoiceExpenses', 'totalExpenses', 'profitAmount', 'clientTotal',
            'categories', 'linkedBooking', 'cannedDays'
        ));
    }

    public function update(Request $request, $id)
    {
        $quotation = TourQuotation::findOrFail($id);
        $data = $request->only([
            'customer_name', 'email', 'phone', 'description',
            'ref_number', 'travel_date', 'days', 'nights',
            'travelers_number', 'pricing_base', 'lang'
        ]);
        $data['last_edited'] = auth()->id();
        $quotation->update($data);

        // Update day-by-day content
        $days = intval($request->days);
        for ($c = 1; $c <= $days; $c++) {
            $day = TourQuotationDay::where('quotation_id', $id)
                ->where('day_number', $c)->first();

            if (!$day) {
                $day = TourQuotationDay::create([
                    'quotation_id' => $id,
                    'day_number' => $c,
                    'expenses' => '',
                    'total_cost' => 0,
                    'images' => '',
                    'contents' => '',
                    'included' => '',
                    'excluded' => '',
                ]);
            }

            // Day contents (TinyMCE)
            $contents = $request->input('desc_day_' . $c) ?? '';
            $contents = $this->buildQuotationDayContents($request, $c, $contents);

            // Expenses
            $expenseIds = $request->input('expenses_day_' . $c, []);
            $expenseNames = $request->input('expenses_name_' . $c, []);
            $expenseQtys = $request->input('expenses_qty_' . $c, []);
            $expenseCosts = $request->input('expenses_cost_' . $c, []);
            $expenseDates = $request->input('expenses_date_' . $c, []);
            $expensesArr = [];
            if (is_array($expenseIds)) {
                foreach ($expenseIds as $key => $expId) {
                    $expensesArr[] = [
                        'id' => $expId,
                        'desc' => $expenseNames[$key] ?? '',
                        'qty' => $expenseQtys[$key] ?? 1,
                        'cost' => floatval($expenseCosts[$key] ?? 0),
                        'date' => $expenseDates[$key] ?? '',
                    ];
                }
            }

            // Inclusions
            $included = $request->input('day_inc_' . $c, []);
            $excluded = $request->input('day_exc_' . $c, []);

            // Images
            $images = $request->input('day_images_' . $c, []);

            $day->update([
                'contents' => $contents,
                'expenses' => !empty($expensesArr) ? serialize($expensesArr) : '',
                'included' => !empty($included) ? serialize($included) : '',
                'excluded' => !empty($excluded) ? serialize($excluded) : '',
                'images' => !empty($images) ? serialize($images) : '',
            ]);
        }

        return redirect()->route('admin.quotations.edit', $id)->with('success', 'Quotation updated');
    }

    public function destroy($id)
    {
        $quotation = TourQuotation::findOrFail($id);
        $quotation->quotationDays()->delete();
        $quotation->delete();
        return redirect()->route('admin.quotations.index')->with('success', 'Quotation deleted');
    }

    public function show($id)
    {
        return $this->edit($id);
    }

    public function copy($id)
    {
        $original = TourQuotation::with('quotationDays')->findOrFail($id);
        $copy = $original->replicate();
        $copy->ref_number = $original->ref_number . '-copy';
        $copy->save();

        foreach ($original->quotationDays as $day) {
            $dayData = $day->replicate();
            $dayData->quotation_id = $copy->id;
            $dayData->save();
        }

        return redirect()->route('admin.quotations.edit', $copy->id)->with('success', 'Quotation copied');
    }

    public function sendModal($id)
    {
        $q = TourQuotation::findOrFail($id);
        $viewUrl = url('/' . ($q->lang ?: 'en') . '/tours/quotation/' . $q->id . '/');

        $emailBody = 'Hello <b>' . htmlspecialchars($q->customer_name) . '</b>,
As per your request we have prepare for you estimated tour package cost .
Please note the bellow details :
Description :' . htmlspecialchars($q->description) . '.
Travel Date :' . $q->travel_date . '.
Travelers Number :' . $q->travelers_number . '.
Number of days :' . $q->days . '.
Number of nights :' . $q->nights . '.
to view full details, description and pricing please click the bellow link <a style="color:blue;" href="' . $viewUrl . '"><b>more details</b></a>
Thank you for choosing US !';

        // Company signature
        $logoUrl = url('/uploads/filemanager/PvtLogo1.png');
        $signature = '<div style="padding:10px; margin-top:15px; border-top:1px solid #ccc;">
            <table><tr><td style="padding-right:15px;">
            <img src="' . $logoUrl . '" width="120"></td><td>
            <b>Jordan Branch :</b> P.O. Box 43 Petra 71810 - Jordan<br>
            <b>Oman Branch :</b> P.O. Box 615 Muscat 115 - Oman<br>
            M.W+962779966001 | M:+962777767938<br>
            www.pvt.jo | info@pvt.jo - Resrvation Dept.
            </td></tr></table></div>';

        $html = '<div class="box small nogap">To: ' . htmlspecialchars($q->email) . '(' . htmlspecialchars($q->customer_name) . ')</div>
        <div class="box nogap">
            <div class="row">
                <div class="md-10">
                    <input type="text" id="send_subject" placeholder="Subject..." class="full-width">
                </div>
                <div class="md-2 align-right">
                    <a href="javascript:void(0);" class="btn white full-width" onclick="sendQuotationMail(' . $q->id . ');"><i class="fa-send"></i> Send</a>
                </div>
            </div>
        </div>
        <pre><div contenteditable="true" class="bordered pad" id="mail_msg">' . $emailBody . $signature . '</div></pre>';

        return response()->json(['html' => $html]);
    }

    public function send(Request $request, $id)
    {
        $q = TourQuotation::findOrFail($id);
        $subject = $request->input('subject', 'Quotation');
        $body = $request->input('qmsg', '');

        if (!$q->email) {
            return response()->json(['success' => false, 'message' => 'No email address found']);
        }

        if (empty($subject)) {
            return response()->json(['success' => false, 'message' => 'Subject is required']);
        }

        try {
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . config('mail.from.address', 'res@pvt.jo') . "\r\n";

            $sent = mail($q->email, $subject, $body, $headers);

            if ($sent) {
                return response()->json(['success' => true, 'message' => 'Email sent successfully to ' . $q->email]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to send email. Check mail server configuration.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function days($id)
    {
        $quotation = TourQuotation::with('quotationDays')->findOrFail($id);
        return view('admin.quotations.days', compact('quotation'));
    }

    public function pricing()
    {
        $pricingList = TourQuotationPricing::all();
        return view('admin.quotations.pricing', compact('pricingList'));
    }

    public function storePricing(Request $request)
    {
        $request->validate([
            'description' => 'required|max:150',
            'customer_type' => 'required|in:direct,agency',
            'min_profit' => 'required|numeric',
            'type' => 'required|in:p,f',
            'value' => 'required|numeric',
        ]);

        TourQuotationPricing::create([
            'description' => $request->description,
            'customer_type' => $request->customer_type,
            'min_profit' => floatval($request->min_profit),
            'max_profit' => 0,
            'type' => $request->type,
            'value' => $request->value,
            'commission' => $request->commission ?? 0,
        ]);

        return redirect()->route('admin.quotation-pricing.index')->with('success', 'Pricing added successfully');
    }

    public function editPricing($id)
    {
        $item = TourQuotationPricing::findOrFail($id);
        $directSel = ($item->customer_type == 'direct') ? ' selected' : '';
        $agencySel = ($item->customer_type == 'agency') ? ' selected' : '';
        $pSel = ($item->type == 'p') ? ' selected' : '';
        $fSel = ($item->type == 'f') ? ' selected' : '';
        $html = '<form method="POST" action="' . route('admin.quotation-pricing.update', $item->id) . '" class="tw-flex tw-flex-col tw-gap-5">
            <input type="hidden" name="_token" value="' . csrf_token() . '">
            <input type="hidden" name="_method" value="PUT">
            <div class="tw-flex tw-flex-col tw-gap-2">
                <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">Description</label>
                <input type="text" name="description" value="' . htmlspecialchars($item->description) . '" placeholder="e.g. Evaneos" required>
            </div>
            <div class="tw-flex tw-flex-col tw-gap-2">
                <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">Customer Type</label>
                <select name="customer_type" required>
                    <option value="direct"' . $directSel . '>Direct Customer (simpler)</option>
                    <option value="agency"' . $agencySel . '>Agency / Partner (with commission)</option>
                </select>
            </div>
            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">Pricing Strategy</label>
                    <select name="type" required>
                        <option value="p"' . $pSel . '>Percentage (%)</option>
                        <option value="f"' . $fSel . '>Fixed Amount (JOD)</option>
                    </select>
                </div>
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">Margin / Multiplier Value</label>
                    <input type="number" name="value" step="0.01" value="' . $item->value . '" required>
                </div>
            </div>
            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">Min Profit (per person JOD)</label>
                    <input type="number" name="min_profit" value="' . $item->min_profit . '" step="0.01" required>
                </div>
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">Agent Commission (%)</label>
                    <input type="number" name="commission" step="0.01" value="' . $item->commission . '" placeholder="0.00">
                </div>
            </div>
            <div class="tw-pt-6 tw-border-t tw-border-slate-100">
                <button type="submit" class="btn blue !tw-w-full !tw-py-4">
                    <i class="fa fa-save"></i> Save Changes
                </button>
            </div>
        </form>';
        return response()->json(['html' => $html]);
    }

    public function updatePricing(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|max:150',
            'customer_type' => 'required|in:direct,agency',
            'min_profit' => 'required|numeric',
            'type' => 'required|in:p,f',
            'value' => 'required|numeric',
        ]);

        $item = TourQuotationPricing::findOrFail($id);
        $item->update([
            'description' => $request->description,
            'customer_type' => $request->customer_type,
            'min_profit' => floatval($request->min_profit),
            'type' => $request->type,
            'value' => $request->value,
            'commission' => $request->commission ?? 0,
        ]);

        return redirect()->route('admin.quotation-pricing.index')->with('success', 'Pricing updated successfully');
    }

    public function destroyPricing($id)
    {
        TourQuotationPricing::findOrFail($id)->delete();
        return redirect()->route('admin.quotation-pricing.index')->with('success', 'Pricing deleted successfully');
    }

    public function emailTemplate($id)
    {
        $quotation = TourQuotation::findOrFail($id);
        return view('admin.quotations.email-template', compact('quotation'));
    }

    public function fastAccess(Request $request)
    {
        // Load saved fast access config
        $fastExpenses = [];
        $configPath = base_path('../pvt.jo/config/modules/tours/quotation_fast_access.php');
        if (file_exists($configPath)) {
            $GOGIES = [];
            include $configPath;
            $fastExpenses = $GOGIES['fast_expenses'] ?? [];
        }

        // Handle POST - save selections
        if ($request->isMethod('post')) {
            $configData = '<?php ';
            foreach ($request->all() as $key => $val) {
                if (strpos($key, 'country_') === 0) {
                    // Parse country_COUNTRYID_SERVICEID
                    $parts = explode('_', $key);
                    if (count($parts) >= 3) {
                        $countryId = $parts[1];
                        $serviceId = $parts[2];
                        $configData .= ' $GOGIES[\'fast_expenses\'][' . $countryId . '][' . $serviceId . ']=' . $serviceId . '; ';
                    }
                }
            }
            file_put_contents($configPath, $configData);
            return redirect()->route('admin.quotation-fast-access')->with('success', 'Saved successfully');
        }

        // Get countries and their service categories
        $countries = \Illuminate\Support\Facades\DB::table('en33_countries')
            ->whereIn('id', \App\Models\ServiceCategory::select('country_id')->distinct()->pluck('country_id')->toArray())
            ->get(['id', 'name']);

        $categoriesByCountry = [];
        foreach ($countries as $country) {
            $categoriesByCountry[$country->id] = [
                'name' => $country->name,
                'categories' => \App\Models\ServiceCategory::where('country_id', $country->id)
                    ->orderBy('name')
                    ->get()
                    ->keyBy('id')
                    ->toArray()
            ];
        }

        return view('admin.quotations.fast-access', compact('categoriesByCountry', 'fastExpenses'));
    }

    public function emailTemplates(Request $request)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $basePath = storage_path('app/quotation_mails');

        if (!\Illuminate\Support\Facades\File::exists($basePath)) {
            \Illuminate\Support\Facades\File::makeDirectory($basePath, 0755, true);
        }

        // Handle POST - save templates
        if ($request->isMethod('post')) {
            foreach ($langs as $lang) {
                $content = $request->input('template_' . $lang, '');
                file_put_contents($basePath . '/email_' . $lang . '.php', $content);
            }
            return redirect()->route('admin.quotation-email-templates')->with('success', 'Saved successfully');
        }

        // Load templates
        $templates = [];
        foreach ($langs as $lang) {
            $filePath = $basePath . '/email_' . $lang . '.php';
            $templates[$lang] = file_exists($filePath) ? file_get_contents($filePath) : '';
        }

        return view('admin.quotations.email-templates', compact('templates', 'langs'));
    }

    /**
     * AJAX: Update profit amount and recalculate totals
     */
    public function updateProfit(Request $request, $id)
    {
        $quotation = TourQuotation::findOrFail($id);
        $profit = floatval($request->input('profit_amount', 0));
        $quotation->profit_amount = $profit;

        // Recalculate total from invoice expenses
        $totalExpenses = 0;
        if ($quotation->invoice_id) {
            $totalExpenses = InvoiceExpense::where('invoice_id', $quotation->invoice_id)
                ->where('status', '!=', 'can')
                ->sum('cost');
        }
        $quotation->total_cost = $totalExpenses;
        $quotation->total = $totalExpenses + $profit;
        $quotation->save();

        // Also update the invoice total (client price)
        if ($quotation->invoice_id) {
            Invoice::where('id', $quotation->invoice_id)->update(['total' => $quotation->total, 'cost' => $totalExpenses]);
        }

        return response()->json([
            'success' => true,
            'total_expenses' => number_format($totalExpenses, 2),
            'profit' => number_format($profit, 2),
            'client_total' => number_format($quotation->total, 2),
        ]);
    }

    /**
     * Validate quotation and convert to booking
     */
    public function validateQuotation($id)
    {
        $quotation = TourQuotation::findOrFail($id);

        if ($quotation->status === 'accepted') {
            return redirect()->route('admin.quotations.edit', $id)->with('success', 'Already validated.');
        }

        // Ensure invoice exists
        if (!$quotation->invoice_id) {
            return redirect()->route('admin.quotations.edit', $id)->with('error', 'No invoice linked.');
        }

        // Update invoice status and total
        $invoice = Invoice::findOrFail($quotation->invoice_id);
        $invoice->update([
            'status' => 'un',
            'type' => 'b',
            'total' => $quotation->total,
            'cost' => $quotation->total_cost,
        ]);

        // Find or create user by email
        $userId = 0;
        if ($quotation->email) {
            $user = User::where('email', $quotation->email)->first();
            if (!$user) {
                $user = User::create([
                    'email' => $quotation->email,
                    'first_name' => $quotation->customer_name,
                    'last_name' => '',
                    'password' => bcrypt(str_random(12)),
                    'type' => 'user',
                ]);
            }
            $userId = $user->id;
            $invoice->update(['user_id' => $userId]);
        }

        // Create booking
        $booking = TourBooking::create([
            'user_id' => $userId,
            'invoice_id' => $invoice->id,
            'quotation_id' => $quotation->id,
            'guest_name' => $quotation->customer_name,
            'travel_date' => $quotation->travel_date,
            'days' => $quotation->days,
            'nights' => $quotation->nights,
            'adult' => $quotation->travelers_number,
            'child' => 0,
            'infant' => 0,
            'trip_status' => 'con',
            'added_by' => auth()->id(),
            'booked_in_date' => now()->toDateString(),
            'note' => 'Converted from Quotation #' . ($quotation->ref_number ?: $quotation->id),
            'tour_id' => 0,
            'hotel_grade' => 0,
            'room_single' => 0,
            'rooms_double' => 0,
            'rooms_twin' => 0,
            'rooms_triple' => 0,
            'rooms_quad' => 0,
            'start_country' => 0,
            'paid_by' => 0,
        ]);

        // Mark quotation as accepted
        $quotation->update(['status' => 'accepted']);

        return redirect()->route('admin.quotations.edit', $id)
            ->with('success', 'Quotation validated! Booking #' . $booking->id . ' created successfully.');
    }
}
