<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Accommodation;
use App\Models\Activity;
use App\Models\ServiceCategory;
use App\Models\ServiceSeason;
use App\Models\Country;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceExpense;
use App\Models\VenderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        // Get specific countries as shown in the reference site dropdown
        $targetCountryNames = ['Egypt', 'Jordan', 'Lebanon', 'Libya', 'Morocco', 'Oman', 'Palestine', 'Qatar', 'Saudi Arabia'];
        $countries = Country::where('lang', 'en')
            ->whereIn('name', $targetCountryNames)
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->pluck('name', 'id')
            ->toArray();

        $countryId = $request->input('country');
        $categoryId = $request->input('category');

        // If only one country, auto-select it
        if (count($countries) === 1 && !$countryId) {
            $countryId = array_key_first($countries);
        }

        $tree = '';
        $categories = collect();
        $allCategories = collect();

        if ($countryId && isset($countries[$countryId])) {
            // Build categories array for this country
            $allCategories = ServiceCategory::where('country_id', $countryId)
                ->orderBy('parent_id')
                ->orderBy('name')
                ->get();

            $categoriesArray = [];
            foreach ($allCategories as $cat) {
                $categoriesArray[$cat->id] = [
                    'name' => $cat->name,
                    'parent_id' => $cat->parent_id,
                ];
            }

            // Generate tree HTML
            $tree = $this->buildTree($categoriesArray, 0);
        }

        return view('admin.services.index', compact(
            'countries',
            'countryId',
            'categoryId',
            'tree',
            'allCategories'
        ));
    }

    /**
     * AJAX endpoint: get services for a category
     */
    public function getServices(Request $request)
    {
        $categoryId = intval($request->input('c'));
        $venderId = intval($request->input('vender', 0));
        $countryId = intval($request->input('country', 0));

        // Get category info and breadcrumb
        $category = ServiceCategory::find($categoryId);
        if (!$category) {
            return response()->json(['html' => '<div class="pad align-center">Category not found.</div>']);
        }

        // Build parent breadcrumb
        $breadcrumb = $this->getParentBreadcrumb($categoryId);

        // Collect category IDs: include all descendant categories if this is a parent
        $allCategoryIds = [$categoryId];
        $childIds = ServiceCategory::where('parent_id', $categoryId)->pluck('id')->toArray();
        if (!empty($childIds)) {
            // This is a parent category - get all leaf descendant IDs
            $leafIds = [];
            $visited = [];
            $this->getLeafNodes($categoryId, $leafIds, $visited);
            $allCategoryIds = array_merge($allCategoryIds, $leafIds);
            $allCategoryIds = array_unique($allCategoryIds);
        }

        // Include duplicates of the selected category (e.g., RAMA Hotels in Library vs Manage Services)
        if ($category && $category->parent_id > 0) {
            $duplicates = ServiceCategory::where('name', $category->name)
                ->where('country_id', $category->country_id)
                ->pluck('id')->toArray();
            $allCategoryIds = array_merge($allCategoryIds, $duplicates);
            $allCategoryIds = array_unique($allCategoryIds);
        }

        // Get vender list for these categories
        $categoryNameCache = [];
        $venderList = Service::whereIn('category', $allCategoryIds)
            ->whereNotNull('vender')
            ->with('venderUser')
            ->get()
            ->groupBy('vender')
            ->map(function ($group) use (&$categoryNameCache) {
                $u = $group->first()->venderUser;
                if (!$u) return null;
                // For Transportation services, the "vendor" concept is the transport
                // company category (e.g. "Al Raha bus"), not the linked user account's
                // profile fields, which are managed separately and can drift out of sync.
                $transportName = $this->resolveTransportCompanyName($group->first()->category, $categoryNameCache);
                $name = $transportName ?? (!empty($u->company) ? $u->company : $u->email);
                return ['id' => $u->id, 'name' => $name];
            })
            ->filter()
            ->unique('id')
            ->pluck('name', 'id')
            ->toArray();

        // Auto-select vendor if not manually specified and there is a primary vendor
        if ($venderId === 0 && !empty($venderList)) {
            // Get the most-used vendor in these categories
            $topVender = Service::whereIn('category', $allCategoryIds)
                ->where('vender', '>', 0)
                ->groupBy('vender')
                ->orderByRaw('COUNT(*) DESC')
                ->value('vender');
            if ($topVender) {
                $venderId = $topVender;
            }
        }

        // Query services
        $query = Service::whereIn('category', $allCategoryIds)->with('venderUser');
        if ($venderId > 0) {
            $query->where('vender', $venderId);
        }
        $services = $query->orderByDesc('id')->paginate(20);
        $services->appends($request->query());

        // Attach a display-ready vendor label to each service. For Transportation
        // services this is the transport company category name (e.g. "Al Raha bus"),
        // which is the vendor concept used everywhere else in the Transport module.
        foreach ($services as $svc) {
            $transportName = $this->resolveTransportCompanyName($svc->category, $categoryNameCache);
            if ($transportName) {
                $svc->display_vendor_name = $transportName;
            } elseif ($svc->venderUser) {
                $svc->display_vendor_name = !empty($svc->venderUser->company) ? $svc->venderUser->company : $svc->venderUser->email;
            } else {
                $svc->display_vendor_name = null;
            }
        }

        $html = view('admin.services._services_panel', compact(
            'category',
            'breadcrumb',
            'services',
            'venderList',
            'venderId',
            'countryId',
            'categoryId'
        ))->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Build an HTML <li> tree from categories array
     */
    private function buildTree($elements, $parentId = 0)
    {
        $branch = '';
        foreach ($elements as $id => $v) {
            if ($v['parent_id'] == $parentId) {
                $branch .= '<li id="category_' . $id . '"><a href="#" class="get_category" data-id="' . $id . '">' . htmlspecialchars_decode($v['name']) . '</a>';
                $children = $this->buildTree($elements, $id);
                if ($children) {
                    $branch .= '<ul>' . $children . '</ul>';
                }
                $branch .= '</li>';
            }
        }
        return $branch;
    }

    /**
     * Get parent breadcrumb chain for a category
     */
    private function getParentBreadcrumb($categoryId)
    {
        $parents = [];
        $cat = ServiceCategory::find($categoryId);
        while ($cat && $cat->parent_id > 0) {
            $parent = ServiceCategory::find($cat->parent_id);
            if ($parent) {
                array_unshift($parents, $parent->name);
                $cat = $parent;
            } else {
                break;
            }
        }
        return implode(' > ', $parents);
    }

    /**
     * Robustly decode a service's stored `image` value into a flat list of paths.
     * Historically this field has been written in inconsistent formats across
     * different code paths (a single plain path, a PHP serialize()'d array, a
     * JSON-encoded array, or - due to a past bug - a JSON array wrapping a
     * serialized string). This handles all of them, including one level of
     * nested serialization, so existing corrupted records self-heal on display.
     */
    private function decodeServiceImages($imgPath)
    {
        $images = [];
        if (!$imgPath) return $images;

        $decoded = @unserialize($imgPath);
        if ($decoded === false && $imgPath !== 'b:0;') {
            $decoded = @json_decode($imgPath, true);
        }
        $items = is_array($decoded) ? $decoded : [$imgPath];

        foreach ($items as $item) {
            if (!is_string($item)) continue;
            $item = trim($item);
            if ($item === '') continue;
            // Unwrap one level of nested serialization (from the double-encoding bug)
            $inner = @unserialize($item);
            if (is_array($inner)) {
                foreach ($inner as $p) {
                    if (is_string($p) && trim($p) !== '') $images[] = trim($p);
                }
            } else {
                $images[] = $item;
            }
        }
        return $images;
    }

    /**
     * For a given category id, resolve the name of its transport "company" category
     * (the direct child of the Transportation root, e.g. "Al Raha bus"), if this
     * category belongs to the Transportation tree. Returns null otherwise.
     */
    private function resolveTransportCompanyName($categoryId, array &$cache)
    {
        $transportRootId = 715;
        $current = $categoryId;
        for ($i = 0; $current && $i < 10; $i++) {
            if (!array_key_exists($current, $cache)) {
                $cache[$current] = ServiceCategory::find($current);
            }
            $cat = $cache[$current];
            if (!$cat) return null;
            if ($cat->parent_id == $transportRootId) return $cat->name;
            if ($cat->id == $transportRootId) return null; // the root itself, no company
            if ($cat->parent_id == 0) return null; // belongs to a different tree entirely
            $current = $cat->parent_id;
        }
        return null;
    }

    public function create(Request $request)
    {
        // If AJAX request, return modal HTML
        if ($request->ajax()) {
            return $this->addServiceModal($request);
        }
        $categories = ServiceCategory::orderBy('name')->get();
        $venders = User::where('user_group', 'supplier')->orderBy('first_name')->get();
        return view('admin.services.create', compact('categories', 'venders'));
    }

    /**
     * AJAX: Return the Add Service modal content (matching reference site)
     */
    public function addServiceModal(Request $request)
    {
        $countryId = intval($request->input('country', 0));
        $categoryId = intval($request->input('category', 0));

        // Get venders (stored as 'supplier' group)
        $venders = User::where('user_group', 'supplier')
            ->orderBy('first_name')
            ->get();

        // Build category tree for this country
        $allCategories = ServiceCategory::where('country_id', $countryId)
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        $categoriesArray = [];
        foreach ($allCategories as $cat) {
            $categoriesArray[$cat->id] = [
                'name' => $cat->name,
                'parent_id' => $cat->parent_id,
            ];
        }

        // Build category radio tree
        $catTree = $this->buildRadioTree($categoriesArray, 0, 'category_parent');

        // Auto-detect the vendor from existing services in this category (and duplicates)
        $defaultVenderId = 0;
        $currentCat = ServiceCategory::find($categoryId);
        if ($currentCat) {
            // Find all duplicate categories (same name + country)
            $dupIds = ServiceCategory::where('name', $currentCat->name)
                ->where('country_id', $currentCat->country_id)
                ->pluck('id')
                ->toArray();
            // Get the most-used vendor from existing services in these categories
            $topVender = \App\Models\Service::whereIn('category', $dupIds)
                ->where('vender', '>', 0)
                ->groupBy('vender')
                ->orderByRaw('COUNT(*) DESC')
                ->value('vender');
            if ($topVender) {
                $defaultVenderId = $topVender;
            }
        }

        // Build vender options (pre-select defaultVenderId)
        $venderOptions = '<option value="">Select</option>';
        foreach ($venders as $v) {
            $vName = !empty($v->company) ? $v->company : $v->email;
            $selected = ($v->id == $defaultVenderId) ? ' selected' : '';
            $venderOptions .= '<option value="' . $v->id . '"' . $selected . '>' . htmlspecialchars($vName) . '</option>';
        }

        $html = '<div style="font-size:12px; font-weight:700; color:#64748b; margin-bottom:20px; padding:0 25px;"><i class="fa fa-plus-circle" style="color:#ea580c; margin-right:6px;"></i> Add > Service</div>';
        $html .= '<form id="add_service_form" onsubmit="addServiceSubmit(); return false;">';
        $html .= csrf_field();
        $html .= '<input type="hidden" name="country" value="' . $countryId . '">';

        $html .= '<div style="display:flex; flex-wrap:nowrap; gap:20px; padding:0 25px; margin-bottom:20px; align-items:flex-end;">';

        // Vender (read-only display - no dropdown icon)
        $defaultVenderName = 'Select';
        foreach ($venders as $v) {
            if ($v->id == $defaultVenderId) {
                $defaultVenderName = !empty($v->company) ? $v->company : $v->email;
                break;
            }
        }
        $html .= '<div style="flex: 2;"><label style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; display:block;">Venders</label>';
        $html .= '<input type="hidden" name="vender" value="' . $defaultVenderId . '">';
        $html .= '<input type="text" readonly value="' . htmlspecialchars($defaultVenderName) . '" style="width:100%; border-radius:8px; border:1px solid #e2e8f0; padding:10px; font-size:13px; background:#f1f5f9; color:#64748b; cursor:default; outline:none;"></div>';

        // Title/Description
        $html .= '<div style="flex: 3;"><label style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; display:block;">Title</label>';
        $html .= '<input type="text" name="description" style="width:100%; border-radius:8px; border:1px solid #e2e8f0; padding:10px; font-size:13px; background:#f8fafc;" placeholder="Enter service title" required></div>';

        // Cost
        $html .= '<div style="flex: 1;"><label style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; display:block;">Cost (JOD)</label>';
        $html .= '<input type="number" name="cost" step="0.01" style="width:100%; border-radius:8px; border:1px solid #e2e8f0; padding:10px; font-size:13px; background:#f8fafc;" value="0" required></div>';

        // Restricted
        $html .= '<div style="flex: 0.8; padding-bottom:12px; display:flex; justify-content:center;">';
        $html .= '<label style="margin:0; display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color:#475569; cursor:pointer;"><input type="checkbox" name="restricted" value="1" style="width:16px; height:16px; accent-color:#ea580c;"> Restricted</label></div>';

        $html .= '</div>';

        // Auto-assign current category via hidden field
        $html .= '<input type="hidden" name="category_parent" value="' . $categoryId . '">';

        // Submit
        $html .= '<div style="text-align:center; padding:15px 0;">';
        $html .= '<button type="submit" style="background:#ea580c; color:white; border:none; padding:10px 30px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition:all 0.2s;"><i class="fa fa-check"></i> SAVE</button>';
        $html .= '</div></form>';

        // Init SlimSelect for vender dropdown
        $html .= '<script>';
        $html .= 'if (typeof SlimSelect !== "undefined") { try { new SlimSelect({ select: ".new_vender", showSearch: true, hideSelectedOption: true }); } catch(e){} }';
        $html .= '</script>';

        return response()->json(['html' => $html]);
    }

    /**
     * Build radio button tree for category selection
     */
    private function buildRadioTree($elements, $parentId = 0, $inputName = 'category_parent')
    {
        $branch = '';
        foreach ($elements as $id => $v) {
            if ($v['parent_id'] == $parentId) {
                $branch .= '<li><label><input type="radio" name="' . $inputName . '" value="' . $id . '"> ' . htmlspecialchars_decode($v['name']) . '</label>';
                $children = $this->buildRadioTree($elements, $id, $inputName);
                if ($children) {
                    $branch .= '<ul>' . $children . '</ul>';
                }
                $branch .= '</li>';
            }
        }
        return $branch;
    }

    public function store(Request $request)
    {
        $data = $request->only(['description', 'cost', 'country']);
        $data['category'] = intval($request->input('category_parent') ?? $request->input('category') ?? 0);
        $data['restricted'] = $request->input('restricted', 0);
        $data['cost'] = $data['cost'] ?? 0;
        $data['vender'] = $request->input('vender', 0) ?: 0;
        if ($request->has('notes')) {
            $data['notes'] = $request->input('notes');
        }
        if ($request->has('acc_type')) {
            $data['acc_type'] = $request->input('acc_type');
        }
        if ($request->has('acc_category')) {
            $data['acc_category'] = $request->input('acc_category');
        }
        if ($request->has('website')) {
            $data['website'] = $request->input('website');
        }
        if ($request->has('arrival')) {
            $data['arrival'] = $request->input('arrival');
        }

        $serviceType = $request->input('service_type', '');

        // Ensure upload directory exists
        if (!is_dir(public_path('uploads/services'))) {
            mkdir(public_path('uploads/services'), 0755, true);
        }

        // Handle multi-image upload
        $allImages = [];
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/services'), $filename);
                $allImages[] = 'uploads/services/' . $filename;
            }
        } elseif ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/services'), $filename);
            $allImages[] = 'uploads/services/' . $filename;
        }

        if ($serviceType === 'transport') {
            if (!empty($allImages)) {
                $data['image'] = count($allImages) === 1 ? $allImages[0] : serialize($allImages);
            }
            if ($request->has('transport_method')) {
                $data['transport_method'] = $request->input('transport_method');
            } elseif ($request->has('method')) {
                $data['transport_method'] = $request->input('method');
            }
            if ($request->has('departure_location')) {
                $data['departure_location'] = $request->input('departure_location');
            } elseif ($request->has('departure')) {
                $data['departure_location'] = $request->input('departure');
            }
            if ($request->has('arrival_destination')) {
                $data['arrival_destination'] = $request->input('arrival_destination');
            } elseif ($request->has('arrival')) {
                $data['arrival_destination'] = $request->input('arrival');
            }
            if ($request->has('length_time')) {
                $data['length_time'] = $request->input('length_time');
            }
            if ($request->has('distance_km')) {
                $data['distance_km'] = $request->input('distance_km');
            }
            \App\Models\Transport::create($data);

            // Handle sub_services rows from the Create Transport form
            if ($request->has('sub_services') && is_array($request->input('sub_services'))) {
                foreach ($request->input('sub_services') as $subSvc) {
                    if (!empty(trim($subSvc['description'] ?? ''))) {
                        Service::create([
                            'description' => trim($subSvc['description']),
                            'cost' => $subSvc['cost'] ?? 0,
                            'category' => $data['category'],
                            'country' => $data['country'] ?? 0,
                            'vender' => $data['vender'] ?? 0,
                            'transport_method' => $subSvc['transport_method'] ?? '',
                            'departure_location' => $subSvc['departure_location'] ?? '',
                            'arrival_destination' => $subSvc['arrival_destination'] ?? '',
                            'length_time' => $subSvc['length_time'] ?? '',
                            'distance_km' => $subSvc['distance_km'] ?? '',
                        ]);
                    }
                }
            }

        } elseif ($serviceType === 'accommodation') {
            // Accommodation uses 'descriptionL' column instead of 'description'
            if (!empty($allImages)) {
                $data['image'] = serialize($allImages);
            }
            $accomData = $data;
            $accomData['descriptionL'] = $accomData['description'] ?? '';
            unset($accomData['description']);
            \App\Models\Accommodation::create($accomData);

            // Handle property services (sub-activities)
            if ($request->has('prop_desc') && is_array($request->input('prop_desc'))) {
                $propDescs = $request->input('prop_desc');
                $propCosts = $request->input('prop_cost');
                $propTypes = $request->input('prop_type');
                $propCats = $request->input('prop_cat');
                $propIds = $request->input('prop_id', []);

                foreach ($propDescs as $index => $desc) {
                    if (!empty(trim($desc))) {
                        $activityData = [
                            'description' => trim($desc),
                            'cost' => $propCosts[$index] ?? 0,
                            'acc_type' => $propTypes[$index] ?? '',
                            'acc_category' => $propCats[$index] ?? '',
                            'country' => $data['country'] ?? 0,
                            'category' => $data['category'] ?? 0,
                            'vender' => $data['vender'] ?? 0,
                        ];

                        if (!empty($propIds[$index])) {
                            \App\Models\Activity::where('id', $propIds[$index])->update($activityData);
                        } else {
                            \App\Models\Activity::create($activityData);
                        }
                    }
                }
            }

        } elseif ($serviceType === 'restaurant') {
            if (!empty($allImages)) {
                $data['image'] = count($allImages) === 1 ? $allImages[0] : serialize($allImages);
            }
            \App\Models\Restaurant::create($data);

            // Handle sub_services rows from the Create Restaurant form
            if ($request->has('sub_services') && is_array($request->input('sub_services'))) {
                foreach ($request->input('sub_services') as $subSvc) {
                    if (!empty(trim($subSvc['description'] ?? ''))) {
                        Service::create([
                            'description' => trim($subSvc['description']),
                            'cost' => $subSvc['cost'] ?? 0,
                            'category' => $data['category'],
                            'country' => $data['country'] ?? 0,
                            'vender' => $data['vender'] ?? 0,
                        ]);
                    }
                }
            }

        } elseif ($serviceType === 'activity') {
            if (!empty($allImages)) {
                // Use JSON encoding to match the format expected/written by the
                // update() endpoint and the Modify activity photo display, so
                // multi-photo activities don't end up with unreadable images.
                $data['image'] = json_encode(array_values($allImages));
            }
            if ($request->has('acc_category')) {
                $data['acc_category'] = $request->input('acc_category');
            }
            \App\Models\Activity::create($data);

            if ($request->has('sub_services') && is_array($request->input('sub_services'))) {
                foreach ($request->input('sub_services') as $subSvc) {
                    if (!empty(trim($subSvc['description'] ?? ''))) {
                        // Real activity services must live under a specific activity-type
                        // category (Jeep Tour, Lunch, Horse Ride, etc), never the generic
                        // container category ($data['category']) - otherwise they save
                        // successfully but never show up in the vendor's services list.
                        // The frontend supplies the correct category per-row (taken from
                        // an existing sibling service of the selected vendor); fall back
                        // to the container category only if none was supplied.
                        Service::create([
                            'description' => trim($subSvc['description']),
                            'cost' => $subSvc['cost'] ?? 0,
                            'category' => !empty($subSvc['category']) ? intval($subSvc['category']) : $data['category'],
                            'country' => $data['country'] ?? 0,
                            'vender' => $data['vender'] ?? 0,
                        ]);
                    }
                }
            }

        } else {
            // guide or generic
            if (!empty($allImages)) {
                $data['image'] = count($allImages) === 1 ? $allImages[0] : json_encode($allImages);
            }
            Service::create($data);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('admin.services.index', ['country' => $request->input('country'), 'category' => $data['category']])->with('success', 'Service created');
    }


    public function edit(Request $request, $id)
    {
        // Check if this is an activity record from en33_activities
        if ($request->input('service_type') === 'activity') {
            $service = Activity::find($id);
            if ($service) {
                if ($request->ajax() || $request->input('ajax')) {
                    return $this->editActivityModal($service);
                }
            }
        }

        // Check if this is an activity-section hotel (from Activity tab, shows hotel + activities list)
        if ($request->input('service_type') === 'activity_section') {
            $service = Accommodation::find($id);
            if ($service) {
                if ($request->ajax() || $request->input('ajax')) {
                    return $this->editActivitySectionModal($service);
                }
            }
        }

        // Check if this is a transport record from en33_transports
        if ($request->input('service_type') === 'transport') {
            $service = \App\Models\Transport::find($id);
            if ($service) {
                if ($request->ajax() || $request->input('ajax')) {
                    return $this->editTransportModal($service);
                }
            }
        }

        // Check if this is a transport-section hotel (from Transport tab, shows hotel + transports list)
        if ($request->input('service_type') === 'transport_section') {
            $service = Accommodation::find($id);
            if ($service) {
                if ($request->ajax() || $request->input('ajax')) {
                    return $this->editTransportSectionModal($service);
                }
            }
        }

        // Check if this is a restaurant record from en33_restaurants
        if ($request->input('service_type') === 'restaurant') {
            $service = \App\Models\Restaurant::find($id);
            if ($service) {
                if ($request->ajax() || $request->input('ajax')) {
                    return $this->editRestaurantModal($service);
                }
            }
        }

        // Check if this is a restaurant-section hotel (from Restaurant tab, shows hotel + restaurants list)
        if ($request->input('service_type') === 'restaurant_section') {
            $service = Accommodation::find($id);
            if ($service) {
                if ($request->ajax() || $request->input('ajax')) {
                    return $this->editRestaurantSectionModal($service);
                }
            }
        }

        // Check if this is an accommodation record from en33_accommodations
        if ($request->input('service_type') === 'accommodation') {
            $service = Accommodation::find($id);
            if ($service) {
                if ($request->ajax() || $request->input('ajax')) {
                    return $this->editAccommodationModal($service);
                }
                $categories = ServiceCategory::orderBy('name')->get();
                $venders = User::where('user_group', 'supplier')->orderBy('first_name')->get();
                return view('admin.services.edit', compact('service', 'categories', 'venders'));
            }
        }

        $service = Service::with('seasons')->findOrFail($id);

        // AJAX request - return modal HTML
        if ($request->ajax() || $request->input('ajax')) {
            return $this->editServiceModal($service, $request);
        }

        $categories = ServiceCategory::orderBy('name')->get();
        $venders = User::where('user_group', 'supplier')->orderBy('first_name')->get();
        return view('admin.services.edit', compact('service', 'categories', 'venders'));
    }

    /**
     * AJAX: Return the Edit Service modal content
     */
    private function editServiceModal(Service $service, ?Request $request = null)
    {
        // If called from Manage Services, skip type detection and use simple form
        $fromManageServices = $request && $request->input('source') === 'manage_services';

        if (!$fromManageServices) {
            // Detect service type by walking category tree
            $isAccommodation = false;
            $isTransport = false;
            $isActivity = false;
            $isGuide = false;
            if ($service->category) {
                $cat = ServiceCategory::find($service->category);
                if ($cat) {
                    $checkCat = $cat;
                    while ($checkCat) {
                        $cn = strtolower($checkCat->name);
                        if (stripos($cn, 'accommod') !== false) {
                            $isAccommodation = true;
                            break;
                        }
                        if (stripos($cn, 'transport') !== false || stripos($cn, 'tranport') !== false) {
                            $isTransport = true;
                            break;
                        }
                        if (stripos($cn, 'activit') !== false || stripos($cn, 'pvt') !== false || $checkCat->id == 93) {
                            $isActivity = true;
                            break;
                        }
                        if (stripos($cn, 'guide') !== false || $checkCat->id == 527) {
                            $isGuide = true;
                            break;
                        }
                        $checkCat = $checkCat->parent_id ? ServiceCategory::find($checkCat->parent_id) : null;
                    }
                }
            }
            if ($isAccommodation)
                return $this->editAccommodationModal($service);
            if ($isTransport)
                return $this->editTransportModal($service);
            if ($isActivity)
                return $this->editActivityModal($service);
            if ($isGuide)
                return $this->editGuideModal($service);
        }

        // Default edit form for non-accommodation services
        $venders = User::where('user_group', 'supplier')
            ->orderBy('first_name')
            ->get();

        $countryId = $service->country ?: 0;
        if (!$countryId && $service->category) {
            $cat = ServiceCategory::find($service->category);
            if ($cat) {
                $countryId = $cat->country_id;
            }
        }

        $allCategories = ServiceCategory::where('country_id', $countryId)
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        $categoriesArray = [];
        foreach ($allCategories as $cat) {
            $categoriesArray[$cat->id] = [
                'name' => $cat->name,
                'parent_id' => $cat->parent_id,
            ];
        }

        $catTree = $this->buildRadioTree($categoriesArray, 0, 'category');

        $venderOptions = '<option value="">Select</option>';
        foreach ($venders as $v) {
            $vName = !empty($v->company) ? $v->company : $v->email;
            $sel = ($service->vender == $v->id) ? ' selected' : '';
            $venderOptions .= '<option value="' . $v->id . '"' . $sel . '>' . htmlspecialchars($vName) . '</option>';
        }

        $html = '<div style="font-size:12px; font-weight:700; color:#64748b; margin-bottom:20px; padding:0 25px;"><i class="fa fa-edit" style="color:#ea580c; margin-right:6px;"></i> Edit > ' . htmlspecialchars($service->description) . '</div>';
        $html .= '<form id="edit_service_form" onsubmit="editServiceSubmit(' . $service->id . '); return false;">';
        $html .= csrf_field();
        $html .= '<input type="hidden" name="category" value="' . intval($service->category) . '">';

        $html .= '<div style="display:flex; flex-wrap:nowrap; gap:20px; padding:0 25px; margin-bottom:20px; align-items:flex-end;">';

        // Vender (read-only display - no dropdown icon)
        $currentVenderName = 'Select';
        foreach ($venders as $v) {
            if ($service->vender == $v->id) {
                $currentVenderName = !empty($v->company) ? $v->company : $v->email;
                break;
            }
        }
        $html .= '<div style="flex: 2;"><label style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; display:block;">Venders</label>';
        $html .= '<input type="hidden" name="vender" value="' . intval($service->vender) . '">';
        $html .= '<input type="text" readonly value="' . htmlspecialchars($currentVenderName) . '" style="width:100%; border-radius:8px; border:1px solid #e2e8f0; padding:10px; font-size:13px; background:#f1f5f9; color:#64748b; cursor:default; outline:none;"></div>';

        // Title
        $html .= '<div style="flex: 3;"><label style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; display:block;">Title</label>';
        $html .= '<input type="text" name="description" style="width:100%; border-radius:8px; border:1px solid #e2e8f0; padding:10px; font-size:13px; background:#f8fafc;" value="' . htmlspecialchars($service->description) . '" required></div>';

        // Cost
        $html .= '<div style="flex: 1;"><label style="font-size:11px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; display:block;">Cost (JOD)</label>';
        $html .= '<input type="number" name="cost" step="0.01" style="width:100%; border-radius:8px; border:1px solid #e2e8f0; padding:10px; font-size:13px; background:#f8fafc;" value="' . $service->cost . '" required></div>';

        // Restricted
        $checked = $service->restricted ? ' checked' : '';
        $html .= '<div style="flex: 0.8; padding-bottom:12px; display:flex; justify-content:center;">';
        $html .= '<label style="margin:0; display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600; color:#475569; cursor:pointer;"><input type="checkbox" name="restricted" value="1"' . $checked . ' style="width:16px; height:16px; accent-color:#ea580c;"> Restricted</label></div>';

        $html .= '</div>';

        // Submit
        $html .= '<div style="text-align:center; padding:15px 0;">';
        $html .= '<button type="submit" style="background:#ea580c; color:white; border:none; padding:10px 30px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); transition:all 0.2s;"><i class="fa fa-check"></i> UPDATE</button>';
        $html .= '</div></form>';

        return response()->json(['html' => $html]);
    }

    /**
     * Evaneos-style "Modify accommodation" modal
     */
    private function editActivitySectionModal($service)
    {
        $flags = [
            ['emoji' => '🇫🇷', 'code' => 'fr'],
            ['emoji' => '🇬🇧', 'code' => 'en'],
            ['emoji' => '🇮🇹', 'code' => 'it'],
            ['emoji' => '🇪🇸', 'code' => 'es'],
            ['emoji' => '🇩🇪', 'code' => 'de'],
            ['emoji' => '🇸🇪', 'code' => 'se'],
            ['emoji' => '🇳🇱', 'code' => 'nl'],
        ];

        $imgPath = $service->image ?? '';
        $desc = htmlspecialchars($service->description ?? '');
        $sid = $service->id;
        $countryId = $service->country ?? 123;

        if (!$service->relationLoaded('serviceCategory')) {
            $service->load('serviceCategory.parent.parent.parent');
        }

        $arrival = $service->arrival;
        $accType = $service->acc_type;
        $accCategory = $service->acc_category;

        if ($service->serviceCategory) {
            $cat = $service->serviceCategory;
            $chain = [];
            $walker = $cat->parent ?? null;
            while ($walker) {
                $chain[] = $walker;
                $walker = $walker->parent ?? null;
            }
            if (!$arrival && isset($chain[0])) {
                $arrival = $chain[0]->name;
            }
            $typeMap = ['Hotels' => 'Hotel', 'Camps' => 'Camp', 'Homestay' => 'Guesthouse', 'Homestays' => 'Guesthouse', 'Mobile Camp' => 'Camp', 'Wild Jordan RSCN' => 'Eco-lodge'];
            $starMap = ['1 Star' => '1 ★', '2 Star' => '2 ★★', '3 Star' => '3 ★★★', '4 Stars' => '4 ★★★★', '5 Stars' => '5 ★★★★★'];
            foreach ($chain as $node) {
                if (!$accType && isset($typeMap[$node->name])) {
                    $accType = $typeMap[$node->name];
                }
                if (!$accCategory && isset($starMap[$node->name])) {
                    $accCategory = $starMap[$node->name];
                }
            }
        }

        // Header
        $html = '<script>';
        $html .= 'var head = document.getElementById("libModalHead") || document.getElementById("catModalHead");';
        $html .= 'if(head) { head.innerHTML=\'';
        $html .= '<h3>Modify Activity</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="(typeof closeCatModal === \\\'function\\\' ? closeCatModal : closeModal)()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editActSecForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\'; }';

        $html .= '
        window.actSecEditDt = new DataTransfer();

        window.addActSecImages = function(input) {
            if(input.files && input.files.length > 0){
                for(var i=0; i<input.files.length; i++){
                    window.actSecEditDt.items.add(input.files[i]);
                }
            }
            input.value = "";
            window.renderActSecImages();
        };

        window.renderActSecImages = function() {
            var row = document.getElementById("actSecPhotosRow");
            if(!row) return;
            var addBtn = row.lastElementChild;
            var exisitingNew = row.querySelectorAll(".new-act-sec-photo-wrap");
            exisitingNew.forEach(function(e) { e.remove(); });

            for(let i=0; i<window.actSecEditDt.files.length; i++){
                (function(idx) {
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var div = document.createElement("div");
                        div.className = "acc-photo-wrap new-act-sec-photo-wrap";
                        div.style.cssText = "position:relative;flex-shrink:0;height:104px;min-width:104px;background:#f1f5f9;border-radius:4px;";
                        div.innerHTML = "<img src=\'" + e.target.result + "\' style=\'width:100%;height:100%;border-radius:4px;object-fit:cover;\'>" +
                                        "<button type=\'button\' onclick=\'removeActSecNewImg(" + idx + ")\' style=\'position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;\'>✕</button>";
                        row.insertBefore(div, addBtn);
                    };
                    reader.readAsDataURL(window.actSecEditDt.files[idx]);
                })(i);
            }
        };

        window.removeActSecNewImg = function(idx) {
            var newDt = new DataTransfer();
            for(var i=0; i<window.actSecEditDt.files.length; i++){
                if(i !== idx) newDt.items.add(window.actSecEditDt.files[i]);
            }
            window.actSecEditDt = newDt;
            window.renderActSecImages();
        };

        window.submitEditAccSection = function(id) {
            var form = document.getElementById("editActSecForm");
            var fd = new FormData(form);
            fd.append("_method","PUT");
            fd.append("_token","' . csrf_token() . '");
            fd.append("service_type","accommodation"); // Ensure this matches exactly what the backend expects

            fd.delete("new_images[]");
            for(var i=0; i<window.actSecEditDt.files.length; i++){
                fd.append("new_images[]", window.actSecEditDt.files[i]);
            }

            var btn = form.querySelector("button[type=submit]");
            if(btn) { btn.disabled = true; btn.innerText = "Saving..."; }

            $.ajax({
                url: "/admin/services/" + id,
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (typeof closeCatModal === "function") closeCatModal();
                    else if (typeof closeModal === "function") closeModal();

                    if (typeof reloadCatList === "function") reloadCatList();
                    else if (typeof loadLib === "function") loadLib();
                    else location.reload();

                    if (typeof showToast === "function") showToast("Service updated!", "success");
                },
                error: function(x) {
                    if(btn) { btn.disabled = false; btn.innerText = "Save"; }
                    if (typeof showToast === "function") showToast("Error: " + (x.responseJSON && x.responseJSON.message ? x.responseJSON.message : "Could not update"), "error");
                }
            });
        };
        ';
        $html .= '</script>';

        $html .= '<form id="editActSecForm" onsubmit="submitEditAccSection(' . $sid . '); return false;" enctype="multipart/form-data">';
        $html .= csrf_field();

        // Language flags + vendor bar
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        $vendorName = $service->venderUser
            ? (!empty($service->venderUser->company) ? strtoupper($service->venderUser->company) : strtoupper($service->venderUser->first_name . ' ' . $service->venderUser->last_name))
            : strtoupper($service->description ?? '');
        $html .= '<div style="margin-left:auto;display:flex;gap:16px;align-items:center;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:6px 14px;font-size:12px;">';
        $html .= '<span><strong>Vendor Name:</strong> ' . htmlspecialchars($vendorName) . '</span>';
        $html .= '<span style="color:#ccc;">|</span>';
        $html .= '<span><strong>Vendor Price:</strong> <span style="color:#ea580c;font-weight:700;">' . number_format($service->cost ?? 0, 2) . ' JOD</span></span>';
        $html .= '</div>';
        $html .= '</div>';

        // Photos section
        $existingImages = [];
        if ($imgPath) {
            $d = @unserialize($imgPath);
            if ($d === false && $imgPath !== 'b:0;') {
                $d = @json_decode($imgPath, true);
            }
            if (is_array($d)) {
                foreach ($d as $p) {
                    if (trim($p) !== '')
                        $existingImages[] = $p;
                }
            } else {
                if (trim($imgPath) !== '')
                    $existingImages[] = $imgPath;
            }
        }
        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editActSecImageInput" accept="image/*" multiple style="display:none" onchange="addActSecImages(this)">';
        $html .= '<div id="actSecPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach ($existingImages as $img) {
            $imgUrl = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $imgUrl = str_replace('/public/', '/', $imgUrl);
            $html .= '<div class="acc-photo-wrap" style="position:relative;flex-shrink:0;height:104px;min-width:104px;background:#f1f5f9;border-radius:4px;">';
            $html .= '<img src="' . $imgUrl . '" style="width:100%;height:100%;border-radius:4px;object-fit:cover;" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/104?text=Photo\';">';
            $html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '">';
            $html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            $html .= '</div>';
        }
        $html .= '<div onclick="document.getElementById(\'editActSecImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">+</div>';
        $html .= '</div></div>';

        // Two-column layout
        $html .= '<div style="display:flex;gap:16px;margin-bottom:16px;">';

        // LEFT column
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Name of accommodation</legend>';
        $html .= '<input type="text" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $desc . '">';
        $html .= '<div style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(' . strlen($service->description) . '/255)</div>';
        $html .= '</fieldset>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        $html .= '<div id="svcQuillEditor" style="min-height:140px;background:#fff;font-size:13px;line-height:1.6;"></div>';
        $html .= '<textarea name="notes" id="svcQuillHidden" style="display:none">' . htmlspecialchars($service->notes ?? '') . '</textarea>';
        $html .= '</fieldset>';
        $html .= '</div>';

        // RIGHT column
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend>';
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="' . htmlspecialchars($arrival ?? '') . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        $accTypes = ['Hotel', 'Guesthouse', 'Hostel', 'Resort', 'Apartment', 'Camp', 'Eco-lodge', 'Riad', 'Villa'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Accommodation Type</legend>';
        $html .= '<select name="acc_type" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a type</option>';
        foreach ($accTypes as $t) {
            $sel = ($accType === $t) ? ' selected' : '';
            $html .= '<option value="' . $t . '"' . $sel . '>' . $t . '</option>';
        }
        $html .= '</select></fieldset>';

        $cats = ['1 ★', '2 ★★', '3 ★★★', '4 ★★★★', '5 ★★★★★', 'Standard', 'Superior', 'Luxury'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Category</legend>';
        $html .= '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a category</option>';
        foreach ($cats as $c) {
            $sel = ($accCategory === $c) ? ' selected' : '';
            $html .= '<option value="' . $c . '"' . $sel . '>' . $c . '</option>';
        }
        $html .= '</select></fieldset>';

        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Website</legend>';
        $html .= '<input type="text" name="website" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . htmlspecialchars($service->website ?? '') . '">';
        $html .= '</fieldset>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<input type="hidden" name="cost" value="' . ($service->cost ?? 0) . '">';
        $html .= '<input type="hidden" name="category" value="' . ($service->category ?? '') . '">';
        $html .= '</form>';

        // ACTIVITIES LIST from en33_activities
        $activityItems = Activity::where('country', $countryId)->with('venderUser')->orderBy('description')->get();
        $actCsrf = csrf_token();
        $html .= '<div style="margin-top:20px;">';
        $html .= '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">';
        $html .= '<span style="color:#e53e3e;font-size:11px;font-weight:800;letter-spacing:1px;">🏃 ACTIVITIES LIST</span>';
        $html .= '<button type="button" onclick="toggleActivityAddForm()" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Activity</button>';
        $html .= '</div>';
        // Inline Add Activity Form (hidden by default)
        $html .= '<div id="activityAddSvcForm" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        $html .= '<div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">';
        $html .= '<div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label>';
        $html .= '<input type="text" id="newActDesc" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;" placeholder="e.g. City Tour"></div>';
        $html .= '<div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label>';
        $html .= '<input type="number" id="newActCost" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;" placeholder="0.00" step="0.01" value="0.00"></div>';
        $html .= '<div style="flex:1;min-width:120px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Activity Type</label>';
        $html .= '<select id="newActType" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 8px;font-size:12px;background:#fff;color:#555;">';
        $html .= '<option value="">-- Type --</option>';
        foreach (['Entrance', 'Excursion', 'Adventure', 'Cultural', 'Cooking', 'Water Sport', 'Desert Safari', 'Hiking', 'Religious', 'Other'] as $at) {
            $html .= '<option value="' . $at . '">' . $at . '</option>';
        }
        $html .= '</select></div>';
        $html .= '<div style="flex:1;min-width:120px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Activity Category</label>';
        $html .= '<select id="newActCat" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 8px;font-size:12px;background:#fff;color:#555;">';
        $html .= '<option value="">-- Category --</option>';
        foreach (['Standard', 'Premium', 'VIP', 'Group', 'Private', 'Family'] as $ac) {
            $html .= '<option value="' . $ac . '">' . $ac . '</option>';
        }
        $html .= '</select></div>';
        $html .= '<div style="display:flex;gap:6px;padding-bottom:0;">';
        $html .= '<button type="button" onclick="quickAddActivity(' . $sid . ',' . ($service->vender ?? 'null') . ',' . ($service->category ?? 'null') . ',' . $countryId . ',\'' . $actCsrf . '\')" style="height:36px;background:#7c3aed;border:none;color:#fff;border-radius:6px;padding:0 16px;font-size:12px;font-weight:700;cursor:pointer;">Save</button>';
        $html .= '<button type="button" onclick="toggleActivityAddForm()" style="height:36px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button>';
        $html .= '</div></div></div>';
        $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        $html .= '<thead><tr style="border-bottom:1px solid #e2e8f0;">';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">DESCRIPTION</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">COST</th>';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">VENDOR</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">ACTIONS</th>';
        $html .= '</tr></thead><tbody>';
        foreach ($activityItems as $act) {
            $html .= '<tr id="actRow_' . $act->id . '" style="border-bottom:1px solid #f7fafc;">';
            $html .= '<td style="padding:7px 8px;"><span id="actDesc_' . $act->id . '">' . htmlspecialchars($act->description ?? '-') . '</span></td>';
            $html .= '<td style="padding:7px 8px;text-align:right;color:#ea580c;font-weight:700;"><span id="actCost_' . $act->id . '">' . number_format($act->cost ?? 0, 2) . '</span> JOD</td>';
            $html .= '<td style="padding:7px 8px;">' . htmlspecialchars($vendorName) . '</td>';
            $html .= '<td style="padding:7px 8px;text-align:right;white-space:nowrap;">';
            $html .= '<button type="button" onclick="editActRow(' . $act->id . ',\'' . addslashes(htmlspecialchars($act->description ?? '')) . '\',' . ($act->cost ?? 0) . ')" style="background:#f0f4ff;border:none;color:#7c3aed;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;margin-right:4px;"><i class="fa fa-pencil"></i></button>';
            $html .= '<button type="button" onclick="deleteActivityRow(' . $act->id . ',' . $sid . ')" style="background:#fff5f5;border:none;color:#e53e3e;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;"><i class="fa fa-trash"></i></button>';
            $html .= '</td></tr>';
        }
        if ($activityItems->isEmpty()) {
            $html .= '<tr><td colspan="4" style="padding:16px;text-align:center;color:#a0aec0;font-size:12px;">No activities found.</td></tr>';
        }
        $html .= '</tbody></table></div>';

        $html .= '<script>
function toggleActivityAddForm(){var f=document.getElementById("activityAddSvcForm");f.style.display=(f.style.display==="none"?"":"none");}
function quickAddActivity(sid,vender,category,country,token){
    var desc=document.getElementById("newActDesc").value.trim();
    var cost=document.getElementById("newActCost").value||0;
    var atype=document.getElementById("newActType").value;
    var acat=document.getElementById("newActCat").value;
    if(!desc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/activities/quick-add",type:"POST",
        data:{_token:token,description:desc,cost:cost,vender:vender,category:category,country:country,acc_type:atype,acc_category:acat},
        success:function(r){if(r.success){document.getElementById("newActDesc").value="";document.getElementById("newActCost").value="0.00";document.getElementById("newActType").value="";document.getElementById("newActCat").value="";toggleActivityAddForm();showToast("Activity added!","success");}},
        error:function(){showToast("Error adding activity","error");}
    });
}
function editActRow(id,desc,cost){
    var old=document.getElementById("actEditForm_"+id);if(old){old.remove();return;}
    var row=document.getElementById("actRow_"+id);
    var editRow=document.createElement("tr");editRow.id="actEditForm_"+id;
    editRow.innerHTML=\'<td colspan="4" style="padding:10px 8px;background:#f8fafc;"><div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;"><div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="editActDesc_\'+id+\'" value="\'+desc+\'" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="editActCost_\'+id+\'" value="\'+cost+\'" step="0.01" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="display:flex;gap:6px;"><button type="button" onclick="saveEditAct(\'+id+\')" style="height:34px;background:#7c3aed;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button><button type="button" onclick="cancelEditAct(\'+id+\')" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button></div></div></td>\';
    row.parentNode.insertBefore(editRow,row.nextSibling);
}
function saveEditAct(id){
    var newDesc=document.getElementById("editActDesc_"+id).value.trim();
    var newCost=document.getElementById("editActCost_"+id).value;
    if(!newDesc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $actCsrf . '",_method:"PUT",description:newDesc,cost:newCost,service_type:"activity"},
        success:function(){document.getElementById("actDesc_"+id).textContent=newDesc;document.getElementById("actCost_"+id).textContent=parseFloat(newCost||0).toFixed(2);cancelEditAct(id);showToast("Activity updated!","success");},
        error:function(){showToast("Error updating activity","error");}
    });
}
function cancelEditAct(id){var f=document.getElementById("actEditForm_"+id);if(f)f.remove();}
function _initSvcQuill(){
    if(typeof Quill==="undefined"){setTimeout(_initSvcQuill,200);return;}
    var el=document.getElementById("svcQuillEditor");if(!el||el.dataset.init)return;el.dataset.init="1";
    var q=new Quill(el,{theme:"snow",modules:{toolbar:[["bold","italic","underline"],[{list:"ordered"},{list:"bullet"}],["link"],["clean"]]}});
    var h=document.getElementById("svcQuillHidden");
    if(h&&h.value)q.root.innerHTML=h.value;
    q.on("text-change",function(){if(h)h.value=q.root.innerHTML;});
    window._svcQuill=q;
}
if(!document.getElementById("quill-css")){var l=document.createElement("link");l.id="quill-css";l.rel="stylesheet";l.href="https://cdn.quilljs.com/1.3.7/quill.snow.css";document.head.appendChild(l);}
if(!window.Quill&&!document.getElementById("quill-js")){var s=document.createElement("script");s.id="quill-js";s.src="https://cdn.quilljs.com/1.3.7/quill.min.js";s.onload=function(){_initSvcQuill();};document.head.appendChild(s);}else{_initSvcQuill();}
</script>';

        return response()->json(['html' => $html]);
    }

    private function editAccommodationModal($service)
    {
        $venders = \App\Models\User::where('user_group', 'supplier')->orderBy('first_name')->get();
        $flags = [
            ['emoji' => '🇫🇷', 'code' => 'fr'],
            ['emoji' => '🇬🇧', 'code' => 'en'],
            ['emoji' => '🇮🇹', 'code' => 'it'],
            ['emoji' => '🇪🇸', 'code' => 'es'],
            ['emoji' => '🇩🇪', 'code' => 'de'],
            ['emoji' => '🇸🇪', 'code' => 'se'],
            ['emoji' => '🇳🇱', 'code' => 'nl'],
        ];

        $imgPath = $service->image ?? '';
        $desc = htmlspecialchars($service->description ?? '');
        $sid = $service->id;

        // Load service category chain if not loaded
        if (!$service->relationLoaded('serviceCategory')) {
            $service->load('serviceCategory.parent.parent.parent');
        }

        // Auto-derive fields from category hierarchy if NULL in DB
        $arrival = $service->arrival;
        $accType = $service->acc_type;
        $accCategory = $service->acc_category;

        if ($service->serviceCategory) {
            $cat = $service->serviceCategory;

            // Walk up chain to find city, star, type
            $chain = [];
            $walker = $cat->parent ?? null;
            while ($walker) {
                $chain[] = $walker;
                $walker = $walker->parent ?? null;
            }
            // chain[0]=city, chain[1]=star, chain[2]=type (for hotels)
            // chain[0]=star, chain[1]=type (for camps)

            if (!$arrival && isset($chain[0])) {
                $arrival = $chain[0]->name; // city or star level
            }

            $typeMap = [
                'Hotels' => 'Hotel',
                'Camps' => 'Camp',
                'Homestay' => 'Guesthouse',
                'Homestays' => 'Guesthouse',
                'Mobile Camp' => 'Camp',
                'Wild Jordan RSCN' => 'Eco-lodge',
            ];
            $starMap = [
                '1 Star' => '1 ★',
                '2 Star' => '2 ★★',
                '3 Star' => '3 ★★★',
                '4 Stars' => '4 ★★★★',
                '5 Stars' => '5 ★★★★★',
            ];

            foreach ($chain as $node) {
                if (!$accType && isset($typeMap[$node->name])) {
                    $accType = $typeMap[$node->name];
                }
                if (!$accCategory && isset($starMap[$node->name])) {
                    $accCategory = $starMap[$node->name];
                }
            }
        }

        $html = '<script>';
        $html .= 'var head = document.getElementById("libModalHead") || document.getElementById("catModalHead");';
        $html .= 'if(head) { head.innerHTML=\'';
        $html .= '<h3>Modify accommodation</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="(typeof closeCatModal === \\\'function\\\' ? closeCatModal : closeModal)()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editAccForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\'; }';

        $html .= '
        window.accEditDt = new DataTransfer();

        window.addAccImages = function(input) {
            if(input.files && input.files.length > 0){
                for(var i=0; i<input.files.length; i++){
                    window.accEditDt.items.add(input.files[i]);
                }
            }
            input.value = "";
            window.renderAccImages();
        };

        window.renderAccImages = function() {
            var row = document.getElementById("catPhotosRow") || document.getElementById("accPhotosRow");
            if(!row) return;
            var addBtn = row.lastElementChild;
            var exisitingNew = row.querySelectorAll(".new-acc-photo-wrap");
            exisitingNew.forEach(function(e) { e.remove(); });

            for(let i=0; i<window.accEditDt.files.length; i++){
                (function(idx) {
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var div = document.createElement("div");
                        div.className = "acc-photo-wrap new-acc-photo-wrap";
                        div.style.cssText = "position:relative;flex-shrink:0;height:104px;min-width:104px;background:#f1f5f9;border-radius:4px;";
                        div.innerHTML = "<img src=\'" + e.target.result + "\' style=\'width:100%;height:100%;border-radius:4px;object-fit:cover;\'>" +
                                        "<button type=\'button\' onclick=\'removeAccNewImg(" + idx + ")\' style=\'position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;\'>✕</button>";
                        row.insertBefore(div, addBtn);
                    };
                    reader.readAsDataURL(window.accEditDt.files[idx]);
                })(i);
            }
        };

        window.removeAccNewImg = function(idx) {
            var newDt = new DataTransfer();
            for(var i=0; i<window.accEditDt.files.length; i++){
                if(i !== idx) newDt.items.add(window.accEditDt.files[i]);
            }
            window.accEditDt = newDt;
            window.renderAccImages();
        };

        window.submitEditAcc = function(id) {
            var form = document.getElementById("editAccForm");
            var fd = new FormData(form);
            fd.append("_method","PUT");
            fd.append("_token","' . csrf_token() . '");
            fd.append("service_type","accommodation");

            fd.delete("new_images[]");
            for(var i=0; i<window.accEditDt.files.length; i++){
                fd.append("new_images[]", window.accEditDt.files[i]);
            }

            var btn = form.querySelector("button[type=submit]");
            if(btn) { btn.disabled = true; btn.innerText = "Saving..."; }

            $.ajax({
                url: "/admin/services/" + id,
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (typeof closeCatModal === "function") closeCatModal();
                    else if (typeof closeModal === "function") closeModal();

                    if (typeof reloadCatList === "function") reloadCatList();
                    else if (typeof loadLib === "function") loadLib();
                    else location.reload();

                    if (typeof showToast === "function") showToast("Accommodation updated!", "success");
                },
                error: function(x) {
                    if(btn) { btn.disabled = false; btn.innerText = "Save"; }
                    if (typeof showToast === "function") showToast("Error: " + (x.responseJSON && x.responseJSON.message ? x.responseJSON.message : "Could not update"), "error");
                }
            });
        };
        ';
        $html .= '</script>';


        $html .= '<form id="editAccForm" onsubmit="submitEditAcc(' . $sid . '); return false;" enctype="multipart/form-data">';
        $html .= csrf_field();

        // Language flags
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        // Vendor info bar
        $venderOpts = '<option value="">Select an owner/vender account...</option>';
        foreach ($venders as $v) {
            $vName = !empty($v->company) ? $v->company : trim($v->first_name . ' ' . ($v->last_name ?? ''));
            if (!$vName)
                $vName = $v->email;
            $selected = ($service->vender == $v->id) ? ' selected' : '';
            $venderOpts .= '<option value="' . $v->id . '"' . $selected . '>' . htmlspecialchars($vName) . '</option>';
        }

        $html .= '<div style="margin-left:auto;display:flex;gap:16px;align-items:center;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:6px 14px;font-size:12px;width:75%;">';
        $html .= '<div style="flex:1;"><select id="edit_modal_vender_select" name="vender" style="width:100%;height:30px;border:1px solid #ddd;border-radius:4px;outline:none;">' . $venderOpts . '</select></div>';
        $html .= '<span style="color:#ccc;">|</span>';
        $html .= '<span style="white-space:nowrap;"><strong>Vendor Price:</strong> <span style="color:#ea580c;font-weight:700;"><input type="number" name="cost" value="' . ($service->cost ?? 0) . '" step="0.01" style="width:70px;height:24px;border:1px solid #ddd;border-radius:4px;padding:2px 6px;outline:none;"> JOD</span></span>';
        $html .= '</div>';
        $html .= '</div>';

        // Photos section - multi-image support
        $existingImages = [];
        if ($imgPath) {
            $d = @unserialize($imgPath);
            if ($d === false && $imgPath !== 'b:0;') {
                $d = @json_decode($imgPath, true);
            }
            if (is_array($d)) {
                foreach ($d as $p) {
                    if (trim($p) !== '')
                        $existingImages[] = $p;
                }
            } else {
                if (trim($imgPath) !== '')
                    $existingImages[] = $imgPath;
            }
        }

        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editAccImageInput" accept="image/*" multiple style="display:none" onchange="addAccImages(this)">';

        $html .= '<div id="accPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';

        foreach ($existingImages as $idx => $img) {
            $imgUrl = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $imgUrl = str_replace('/public/', '/', $imgUrl);
            $html .= '<div class="acc-photo-wrap" style="position:relative;flex-shrink:0;height:104px;min-width:104px;background:#f1f5f9;border-radius:4px;">';
            $html .= '<img src="' . $imgUrl . '" style="width:100%;height:100%;border-radius:4px;object-fit:cover;" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/104?text=Photo\';">';
            $html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '">';
            $html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            $html .= '</div>';
        }

        // Add photo button
        $html .= '<div onclick="document.getElementById(\'editAccImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">+</div>';

        $html .= '</div>';
        $html .= '</div>';

        // Two-column layout
        $html .= '<div style="display:flex;gap:16px;margin-bottom:16px;">';

        // LEFT column
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Name of accommodation</legend>';
        $html .= '<input type="text" id="editAccDescInput" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $desc . '">';
        $html .= '<div style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(' . strlen($service->description) . '/255)</div>';
        $html .= '</fieldset>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        $html .= '<div id="svcQuillEditor" style="min-height:140px;background:#fff;font-size:13px;line-height:1.6;"></div>';
        $html .= '<textarea name="notes" id="svcQuillHidden" style="display:none">' . htmlspecialchars($service->notes ?? '') . '</textarea>';
        $html .= '</fieldset>';
        $html .= '</div>';

        // RIGHT column
        $html .= '<div style="flex:1;">';

        // Place of interest - with autocomplete
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend>';
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="' . htmlspecialchars($arrival ?? '') . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        // Accommodation type
        $accTypes = ['Hotel', 'Guesthouse', 'Hostel', 'Resort', 'Apartment', 'Camp', 'Eco-lodge', 'Riad', 'Villa'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Accommodation type</legend>';
        $html .= '<select name="acc_type" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a type of accommodation</option>';
        foreach ($accTypes as $t) {
            $sel = ($accType === $t) ? ' selected' : '';
            $html .= '<option value="' . $t . '"' . $sel . '>' . $t . '</option>';
        }
        $html .= '</select></fieldset>';

        // Category
        $cats = ['1 ★', '2 ★★', '3 ★★★', '4 ★★★★', '5 ★★★★★', 'Standard', 'Superior', 'Luxury'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Category</legend>';
        $html .= '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a category</option>';
        foreach ($cats as $c) {
            $sel = ($accCategory === $c) ? ' selected' : '';
            $html .= '<option value="' . $c . '"' . $sel . '>' . $c . '</option>';
        }
        $html .= '</select></fieldset>';

        // Website
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Website</legend>';
        $html .= '<input type="text" name="website" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="" value="' . htmlspecialchars($service->website ?? '') . '">';
        $html .= '</fieldset>';

        $html .= '</div>';
        $html .= '</div>';

        $html .= '<input type="hidden" name="cost" value="' . $service->cost . '">';
        $html .= '<input type="hidden" name="category" value="' . $service->category . '">';
        $html .= '</form>';

        // SERVICES LIST — from en33_services WHERE category IN (hotel category + all descendants)
        $hotelCatIds = $this->getAllDescendantIds($service->category, $service->country ?? 123);
        $hotelCatIds[] = $service->category;

        $allHotelServices = \App\Models\Service::whereIn('category', $hotelCatIds)->with('venderUser')->orderBy('description')->get();

        $defaultVender = $service->vender ?? null;
        if (!$defaultVender && $allHotelServices->isNotEmpty()) {
            $defaultVender = $allHotelServices->filter(fn($s) => $s->vender)->groupBy('vender')->sortByDesc(fn($g) => $g->count())->keys()->first();
        }

        $hotelServices = $defaultVender
            ? $allHotelServices->where('vender', $defaultVender)
            : $allHotelServices;

        $html .= '<div id="editModalServicesList" style="margin-top:20px;">';
        $html .= '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">';
        $html .= '<span style="color:#e53e3e;font-size:11px;font-weight:800;letter-spacing:1px;">🇯🇴 SERVICES LIST</span>';
        $html .= '<button type="button" onclick="toggleAccomAddForm()" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Service</button>';
        $html .= '</div>';
        // Load suppliers for vendor dropdown
        $suppliers = \App\Models\User::where('user_group', 'supplier')->orderBy('company')->orderBy('first_name')->get();
        // Inline Add Service Form (hidden by default)
        $html .= '<div id="accomAddSvcForm" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        $html .= '<div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">';
        $html .= '<div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="newAccomSvcDesc" placeholder="e.g. Double room HB" style="width:100%;height:34px;border:1px solid #ddd;border-radius:6px;padding:0 10px;font-size:12px;outline:none;"></div>';
        $html .= '<div style="flex:1;min-width:100px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="newAccomSvcCost" step="0.01" placeholder="0.00" style="width:100%;height:34px;border:1px solid #ddd;border-radius:6px;padding:0 10px;font-size:12px;outline:none;"></div>';
        $html .= '<div style="flex:2;min-width:140px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Vendor</label><select id="newAccomSvcVender" style="width:100%;height:34px;border:1px solid #ddd;border-radius:6px;padding:0 8px;font-size:12px;outline:none;"><option value="">-- Select vendor --</option>';
        foreach ($suppliers as $sup) {
            $supName = !empty($sup->company) ? $sup->company : ($sup->first_name . ' ' . $sup->last_name);
            $selected = ($defaultVender && $sup->id == $defaultVender) ? ' selected' : '';
            $html .= '<option value="' . $sup->id . '"' . $selected . '>' . htmlspecialchars($supName) . '</option>';
        }
        $html .= '</select></div>';
        $html .= '<button type="button" onclick="saveAccomSvc(' . $service->category . ',' . ($service->country ?? 123) . ')" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:6px 16px;font-size:12px;font-weight:700;cursor:pointer;height:34px;">Save</button>';
        $html .= '<button type="button" onclick="toggleAccomAddForm()" style="background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:700;cursor:pointer;height:34px;">Cancel</button>';
        $html .= '</div></div>';
        $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        $html .= '<thead><tr style="border-bottom:1px solid #e2e8f0;">';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">DESCRIPTION</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">COST</th>';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">VENDOR</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">ACTIONS</th>';
        $html .= '</tr></thead><tbody>';
        foreach ($hotelServices as $svc) {
            // Use company name first, fallback to first+last name
            $vName = '-';
            if ($svc->venderUser) {
                $vName = !empty($svc->venderUser->company)
                    ? strtoupper($svc->venderUser->company)
                    : strtoupper($svc->venderUser->first_name . ' ' . $svc->venderUser->last_name);
            }
            $html .= '<tr style="border-bottom:1px solid #f7fafc;">';
            $html .= '<td style="padding:7px 8px;">' . htmlspecialchars($svc->description) . '</td>';
            $html .= '<td style="padding:7px 8px;text-align:right;color:#ea580c;font-weight:700;">' . number_format($svc->cost, 2) . ' JOD</td>';
            $html .= '<td style="padding:7px 8px;">' . htmlspecialchars($vName) . '</td>';
            $html .= '<td style="padding:7px 8px;text-align:right;white-space:nowrap;">';
            $html .= '<button onclick="openSeasons(' . $svc->id . ')" style="background:#ffedd5;border:none;color:#ea580c;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;margin-right:4px;">🗓 Seasons</button>';
            $html .= '<button onclick="editAccomRowSvc(' . $svc->id . ')" style="background:#f0f4ff;border:none;color:#3b82f6;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;margin-right:4px;"><i class="fa fa-pencil"></i></button>';
            $html .= '<button onclick="delAccomRowSvc(' . $svc->id . ',\'' . addslashes($svc->description) . '\')" style="background:#fff5f5;border:none;color:#e53e3e;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;"><i class="fa fa-trash"></i></button>';
            $html .= '</td></tr>';
        }
        if ($hotelServices->isEmpty()) {
            $html .= '<tr><td colspan="4" style="padding:16px;text-align:center;color:#a0aec0;font-size:12px;">No services found for this hotel.</td></tr>';
        }
        $html .= '</tbody></table></div>';
        $csrf = csrf_token();
        $html .= '<script>
setTimeout(function() {
    if (typeof SlimSelect !== "undefined") {
        try {
            new SlimSelect({
                select: "#edit_modal_vender_select",
                searchPlaceholder: "Search vendors...",
                placeholder: "Select an owner/vender account...",
                onChange: function(info) {
                    var val = info && info.value ? info.value : (Array.isArray(info) && info[0] ? info[0].value : null);
                    var text = info && info.text ? info.text : (Array.isArray(info) && info[0] ? info[0].text : "");
                    if (!val) return;

                    var descInp = document.getElementById("editAccDescInput");
                    if (descInp && text) descInp.value = text;

                    var container = document.getElementById("editModalServicesList");
                    if (!container) return;
                    container.innerHTML = "<div style=\'text-align:center;padding:20px;\'><i class=\'fa fa-spinner fa-spin\' style=\'color:#ea580c\'></i> Loading services...</div>";
                    $.get("/admin/vendor/" + val + "/services", function(res) {
                        if (!res || !res.html) { container.innerHTML = "<p style=\'padding:16px;color:#999;font-size:12px;text-align:center;\'>No services found for this vendor.</p>"; return; }
                        container.innerHTML = res.html;
                    }).fail(function() {
                        container.innerHTML = "<p style=\'color:red;font-size:12px;padding:10px;\'>Failed to load services.</p>";
                    });
                }
            });
        } catch (e) {}
    }
}, 100);
function toggleAccomAddForm(){var f=document.getElementById("accomAddSvcForm");if(f)f.style.display=f.style.display==="none"?"block":"none";}
function saveAccomSvc(catId,countryId){
    var desc=document.getElementById("newAccomSvcDesc").value.trim();
    var cost=document.getElementById("newAccomSvcCost").value.trim();
    var vender=document.getElementById("newAccomSvcVender").value;
    if(!desc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/services/quick-add",type:"POST",
        data:{_token:"' . $csrf . '",description:desc,cost:cost||0,category:catId,country:countryId,vender:vender},
        success:function(){toggleAccomAddForm();showToast("Service added!","success");},
        error:function(x){showToast("Error: "+(x.responseJSON&&x.responseJSON.message?x.responseJSON.message:"Could not add service"),"error");}
    });
}
function editAccomRowSvc(id){
    var old=document.getElementById("svcEditForm_"+id);if(old){old.remove();return;}
    var row=document.querySelector("button[onclick*=\\"editAccomRowSvc("+id+")\\"]").closest("tr");
    var desc=row.querySelector("td:first-child").textContent.trim();
    var costText=row.querySelector("td:nth-child(2)").textContent.trim();
    var cost=parseFloat(costText.replace(/[^0-9.]/g,""))||0;
    var editRow=document.createElement("tr");editRow.id="svcEditForm_"+id;
    editRow.innerHTML=\'<td colspan="4" style="padding:10px 8px;background:#f8fafc;"><div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;"><div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="editSvcDesc_\'+id+\'" value="\'+desc.replace(/\\\'/g,"&#39;")+\'" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="editSvcCost_\'+id+\'" value="\'+cost+\'" step="0.01" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="display:flex;gap:6px;"><button type="button" onclick="saveAccomRowSvc(\'+id+\')" style="height:34px;background:#ea580c;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button><button type="button" onclick="cancelAccomRowSvc(\'+id+\')" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button></div></div></td>\';
    row.parentNode.insertBefore(editRow,row.nextSibling);
}
function saveAccomRowSvc(id){
    var newDesc=document.getElementById("editSvcDesc_"+id).value.trim();
    var newCost=document.getElementById("editSvcCost_"+id).value;
    if(!newDesc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $csrf . '",_method:"PUT",description:newDesc,cost:newCost,service_type:"service"},
        success:function(){var row=document.getElementById("svcEditForm_"+id);if(row){var prev=row.previousElementSibling;if(prev){prev.querySelector("td:first-child").textContent=newDesc;prev.querySelector("td:nth-child(2)").innerHTML=parseFloat(newCost||0).toFixed(2)+" JOD";}}cancelAccomRowSvc(id);showToast("Service updated!","success");},
        error:function(){showToast("Error updating service","error");}
    });
}
function cancelAccomRowSvc(id){var f=document.getElementById("svcEditForm_"+id);if(f)f.remove();}
function delAccomRowSvc(id,desc){
    if(!confirm("Delete service: "+desc+"?"))return;
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $csrf . '",_method:"DELETE",service_type:"service"},
        success:function(){var btn=document.querySelector("button[onclick*=\\"delAccomRowSvc("+id+",\\"]");if(btn){var row=btn.closest("tr");if(row)row.remove();}showToast("Service deleted!","success");},
        error:function(){showToast("Error deleting service","error");}
    });
}
function _initSvcQuill(){
    if(typeof Quill==="undefined"){setTimeout(_initSvcQuill,200);return;}
    var el=document.getElementById("svcQuillEditor");if(!el||el.dataset.init)return;el.dataset.init="1";
    var q=new Quill(el,{theme:"snow",modules:{toolbar:[["bold","italic","underline"],[{list:"ordered"},{list:"bullet"}],["link"],["clean"]]}});
    var h=document.getElementById("svcQuillHidden");
    if(h&&h.value)q.root.innerHTML=h.value;
    q.on("text-change",function(){if(h)h.value=q.root.innerHTML;});
    window._svcQuill=q;
}
if(!document.getElementById("quill-css")){var l=document.createElement("link");l.id="quill-css";l.rel="stylesheet";l.href="https://cdn.quilljs.com/1.3.7/quill.snow.css";document.head.appendChild(l);}
if(!window.Quill&&!document.getElementById("quill-js")){var s=document.createElement("script");s.id="quill-js";s.src="https://cdn.quilljs.com/1.3.7/quill.min.js";s.onload=function(){_initSvcQuill();};document.head.appendChild(s);}else{_initSvcQuill();}
</script>';

        return response()->json(['html' => $html]);
    }

    /**
     * Transport-section hotel modal: shows hotel form + transports list from en33_transports
     */
    private function editTransportSectionModal($service)
    {
        $flags = [
            ['emoji' => '🇫🇷', 'code' => 'fr'],
            ['emoji' => '🇬🇧', 'code' => 'en'],
            ['emoji' => '🇮🇹', 'code' => 'it'],
            ['emoji' => '🇪🇸', 'code' => 'es'],
            ['emoji' => '🇩🇪', 'code' => 'de'],
            ['emoji' => '🇸🇪', 'code' => 'se'],
            ['emoji' => '🇳🇱', 'code' => 'nl'],
        ];

        $imgPath = $service->image ?? '';
        $desc = htmlspecialchars($service->description ?? '');
        $sid = $service->id;
        $countryId = $service->country ?? 123;

        if (!$service->relationLoaded('serviceCategory')) {
            $service->load('serviceCategory.parent.parent.parent');
        }

        $arrival = $service->arrival;
        $accType = $service->acc_type;
        $accCategory = $service->acc_category;

        if ($service->serviceCategory) {
            $cat = $service->serviceCategory;
            $chain = [];
            $walker = $cat->parent ?? null;
            while ($walker) {
                $chain[] = $walker;
                $walker = $walker->parent ?? null;
            }
            if (!$arrival && isset($chain[0])) {
                $arrival = $chain[0]->name;
            }
            $typeMap = ['Hotels' => 'Hotel', 'Camps' => 'Camp', 'Homestay' => 'Guesthouse', 'Homestays' => 'Guesthouse', 'Mobile Camp' => 'Camp', 'Wild Jordan RSCN' => 'Eco-lodge'];
            $starMap = ['1 Star' => '1 ★', '2 Star' => '2 ★★', '3 Star' => '3 ★★★', '4 Stars' => '4 ★★★★', '5 Stars' => '5 ★★★★★'];
            foreach ($chain as $node) {
                if (!$accType && isset($typeMap[$node->name])) {
                    $accType = $typeMap[$node->name];
                }
                if (!$accCategory && isset($starMap[$node->name])) {
                    $accCategory = $starMap[$node->name];
                }
            }
        }

        // Header
        $html = '<script>';
        $html .= 'document.getElementById("libModalHead").innerHTML=\'';
        $html .= '<h3>Modify Transport</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editTransSecForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';';
        $html .= '</script>';

        $html .= '<form id="editTransSecForm" onsubmit="submitEditTransSection(' . $sid . '); return false;" enctype="multipart/form-data">';
        $html .= csrf_field();

        // Language flags + vendor bar
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        $vendorName = $service->venderUser
            ? (!empty($service->venderUser->company) ? strtoupper($service->venderUser->company) : strtoupper($service->venderUser->first_name . ' ' . $service->venderUser->last_name))
            : strtoupper($service->description ?? '');
        $html .= '<div style="margin-left:auto;display:flex;gap:16px;align-items:center;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:6px 14px;font-size:12px;">';
        $html .= '<span><strong>Vendor Name:</strong> ' . htmlspecialchars($vendorName) . '</span>';
        $html .= '<span style="color:#ccc;">|</span>';
        $html .= '<span><strong>Vendor Price:</strong> <span style="color:#ea580c;font-weight:700;">' . number_format($service->cost ?? 0, 2) . ' JOD</span></span>';
        $html .= '</div>';
        $html .= '</div>';

        // Photos section
        $existingImages = [];
        if ($imgPath) {
            $d = @json_decode($imgPath, true);
            $existingImages = is_array($d) ? $d : [$imgPath];
        }
        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editTransSecImageInput" accept="image/*" multiple style="display:none" onchange="addTransSecImages(this)">';
        $html .= '<div id="transSecPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach ($existingImages as $img) {
            $imgUrl = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $html .= '<div class="acc-photo-wrap" style="position:relative;flex-shrink:0;height:104px;">';
            $html .= '<img src="' . $imgUrl . '" style="height:100%;border-radius:4px;object-fit:cover;">';
            $html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '">';
            $html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            $html .= '</div>';
        }
        $html .= '<div onclick="document.getElementById(\'editTransSecImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">+</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Two-column layout
        $html .= '<div style="display:flex;gap:16px;margin-bottom:16px;">';

        // LEFT column
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Name of accommodation</legend>';
        $html .= '<input type="text" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $desc . '">';
        $html .= '<div style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(' . strlen($service->description) . '/255)</div>';
        $html .= '</fieldset>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        $html .= '<div id="svcQuillEditor" style="min-height:140px;background:#fff;font-size:13px;line-height:1.6;"></div>';
        $html .= '<textarea name="notes" id="svcQuillHidden" style="display:none">' . htmlspecialchars($service->notes ?? '') . '</textarea>';
        $html .= '</fieldset>';
        $html .= '</div>';

        // RIGHT column
        $html .= '<div style="flex:1;">';

        // Place of interest
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend>';
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="' . htmlspecialchars($arrival ?? '') . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        // Accommodation type
        $accTypes = ['Hotel', 'Guesthouse', 'Hostel', 'Resort', 'Apartment', 'Camp', 'Eco-lodge', 'Riad', 'Villa'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Accommodation type</legend>';
        $html .= '<select name="acc_type" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a type of accommodation</option>';
        foreach ($accTypes as $t) {
            $sel = ($accType === $t) ? ' selected' : '';
            $html .= '<option value="' . $t . '"' . $sel . '>' . $t . '</option>';
        }
        $html .= '</select></fieldset>';

        // Category
        $cats = ['1 ★', '2 ★★', '3 ★★★', '4 ★★★★', '5 ★★★★★', 'Standard', 'Superior', 'Luxury'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Category</legend>';
        $html .= '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a category</option>';
        foreach ($cats as $c) {
            $sel = ($accCategory === $c) ? ' selected' : '';
            $html .= '<option value="' . $c . '"' . $sel . '>' . $c . '</option>';
        }
        $html .= '</select></fieldset>';

        // Website
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Website</legend>';
        $html .= '<input type="text" name="website" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . htmlspecialchars($service->website ?? '') . '">';
        $html .= '</fieldset>';

        $html .= '</div>';
        $html .= '</div>';

        $html .= '<input type="hidden" name="cost" value="' . $service->cost . '">';
        $html .= '<input type="hidden" name="category" value="' . $service->category . '">';
        $html .= '</form>';


        // TRANSPORTS LIST — from en33_transports for the same country
        $transportQuery = \App\Models\Transport::where('country', $countryId)->with('venderUser')->orderBy('description');
        if ($service->vender) {
            $transportQuery->where('vender', $service->vender);
        }
        $transportItems = $transportQuery->get();
        $transSuppliers = \App\Models\User::where('user_group', 'supplier')->orderBy('company')->orderBy('first_name')->get();

        // Detect default vendor from existing transports
        $defaultTransVender = null;
        if ($transportItems->isNotEmpty()) {
            $defaultTransVender = $transportItems->filter(fn($t) => $t->vender)->groupBy('vender')->sortByDesc(fn($g) => $g->count())->keys()->first();
        }

        $html .= '<div style="margin-top:20px;">';
        $html .= '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">';
        $html .= '<span style="color:#7c3aed;font-size:11px;font-weight:800;letter-spacing:1px;">🚗 TRANSPORTS LIST</span>';
        $html .= '<button type="button" onclick="toggleTransAddForm()" style="background:#7c3aed;border:none;color:#fff;border-radius:6px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Transport</button>';
        $html .= '</div>';

        // Inline Add Transport Form (hidden by default)
        $transcsrf = csrf_token();
        $html .= '<div id="transAddSvcForm" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        $html .= '<div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">';
        $html .= '<div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="newTransSvcDesc" placeholder="e.g. Amman/Petra" style="width:100%;height:34px;border:1px solid #ddd;border-radius:6px;padding:0 10px;font-size:12px;outline:none;"></div>';
        $html .= '<div style="flex:1;min-width:100px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="newTransSvcCost" step="0.01" placeholder="0.00" style="width:100%;height:34px;border:1px solid #ddd;border-radius:6px;padding:0 10px;font-size:12px;outline:none;"></div>';
        $html .= '<div style="flex:1;min-width:120px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Transport Type</label><select id="newTransSvcType" style="width:100%;height:34px;border:1px solid #ddd;border-radius:6px;padding:0 8px;font-size:12px;outline:none;"><option value="">-- Type --</option><option value="Car">Car</option><option value="Bus">Bus</option><option value="Airplane">Airplane</option><option value="Boat">Boat</option><option value="Train">Train</option></select></div>';
        $html .= '<div style="flex:1;min-width:120px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Transport Category</label><select id="newTransSvcCat" style="width:100%;height:34px;border:1px solid #ddd;border-radius:6px;padding:0 8px;font-size:12px;outline:none;"><option value="">-- Category --</option><option value="Private">Private</option><option value="Shared">Shared</option><option value="Group">Group</option><option value="VIP">VIP</option><option value="Economy">Economy</option></select></div>';
        $html .= '<button type="button" onclick="saveTransSvc(' . $countryId . ',' . ($service->vender ?? 0) . ')" style="background:#7c3aed;border:none;color:#fff;border-radius:6px;padding:6px 16px;font-size:12px;font-weight:700;cursor:pointer;height:34px;">Save</button>';
        $html .= '<button type="button" onclick="toggleTransAddForm()" style="background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:700;cursor:pointer;height:34px;">Cancel</button>';
        $html .= '</div></div>';

        $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        $html .= '<thead><tr style="border-bottom:1px solid #e2e8f0;">';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">DESCRIPTION</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">COST</th>';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">VENDOR</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">ACTIONS</th>';
        $html .= '</tr></thead><tbody>';
        // Use the same vendor name as the header
        $hotelVendorName = $vendorName;
        foreach ($transportItems as $tr) {
            $html .= '<tr id="transRow_' . $tr->id . '" style="border-bottom:1px solid #f7fafc;">';
            $html .= '<td style="padding:7px 8px;"><span id="transDesc_' . $tr->id . '">' . htmlspecialchars($tr->description ?? '-') . '</span></td>';
            $html .= '<td style="padding:7px 8px;text-align:right;color:#7c3aed;font-weight:700;"><span id="transCost_' . $tr->id . '">' . number_format($tr->cost ?? 0, 2) . '</span> JOD</td>';
            $html .= '<td style="padding:7px 8px;">' . htmlspecialchars($hotelVendorName) . '</td>';
            $html .= '<td style="padding:7px 8px;text-align:right;white-space:nowrap;">';
            $html .= '<button type="button" onclick="editTransRow(' . $tr->id . ',\'' . addslashes(htmlspecialchars($tr->description ?? '')) . '\',' . ($tr->cost ?? 0) . ')" style="background:#f0f4ff;border:none;color:#7c3aed;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;margin-right:4px;"><i class="fa fa-pencil"></i></button>';
            $html .= '<button type="button" onclick="deleteTransRow(' . $tr->id . ')" style="background:#fff5f5;border:none;color:#e53e3e;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;"><i class="fa fa-trash"></i></button>';
            $html .= '</td></tr>';
        }
        if ($transportItems->isEmpty()) {
            $html .= '<tr><td colspan="4" style="padding:16px;text-align:center;color:#a0aec0;font-size:12px;">No transports found for this country.</td></tr>';
        }
        $html .= '</tbody></table></div>';

        $html .= '<script>
function toggleTransAddForm(){var f=document.getElementById("transAddSvcForm");if(f)f.style.display=f.style.display==="none"?"block":"none";}
function saveTransSvc(countryId,venderId){
    var desc=document.getElementById("newTransSvcDesc").value.trim();
    var cost=document.getElementById("newTransSvcCost").value.trim();
    var type=document.getElementById("newTransSvcType").value;
    var cat=document.getElementById("newTransSvcCat").value;
    if(!desc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/transports/quick-add",type:"POST",
        data:{_token:"' . $transcsrf . '",description:desc,cost:cost||0,country:countryId,vender:venderId,transport_method:type,acc_category:cat},
        success:function(){toggleTransAddForm();showToast("Transport added!","success");},
        error:function(x){showToast("Error: "+(x.responseJSON&&x.responseJSON.message?x.responseJSON.message:"Could not add transport"),"error");}
    });
}
function editTransRow(id,desc,cost){
    var old=document.getElementById("transEditForm_"+id);if(old)old.remove();
    var row=document.getElementById("transRow_"+id);
    var editRow=document.createElement("tr");editRow.id="transEditForm_"+id;
    editRow.innerHTML=\'<td colspan="4" style="padding:10px 8px;background:#f8fafc;"><div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;"><div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="editTransDesc_\'+id+\'" value="\'+desc+\'" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="editTransCost_\'+id+\'" value="\'+cost+\'" step="0.01" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="display:flex;gap:6px;"><button type="button" onclick="saveEditTrans(\'+id+\')" style="height:34px;background:#7c3aed;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button><button type="button" onclick="cancelEditTrans(\'+id+\')" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button></div></div></td>\';
    row.parentNode.insertBefore(editRow,row.nextSibling);
}
function saveEditTrans(id){
    var newDesc=document.getElementById("editTransDesc_"+id).value.trim();
    var newCost=document.getElementById("editTransCost_"+id).value;
    if(!newDesc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $transcsrf . '",_method:"PUT",description:newDesc,cost:newCost,service_type:"transport"},
        success:function(){document.getElementById("transDesc_"+id).textContent=newDesc;document.getElementById("transCost_"+id).textContent=parseFloat(newCost||0).toFixed(2);cancelEditTrans(id);showToast("Transport updated!","success");},
        error:function(){showToast("Error updating transport","error");}
    });
}
function cancelEditTrans(id){var f=document.getElementById("transEditForm_"+id);if(f)f.remove();}
function deleteTransRow(id){
    if(!confirm("Delete this transport?"))return;
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $transcsrf . '",_method:"DELETE",service_type:"transport"},
        success:function(){var r=document.getElementById("transRow_"+id);if(r)r.remove();showToast("Transport deleted!","success");},
        error:function(){showToast("Error deleting transport","error");}
    });
}
function _initSvcQuill(){
    if(typeof Quill==="undefined"){setTimeout(_initSvcQuill,200);return;}
    var el=document.getElementById("svcQuillEditor");if(!el||el.dataset.init)return;el.dataset.init="1";
    var q=new Quill(el,{theme:"snow",modules:{toolbar:[["bold","italic","underline"],[{list:"ordered"},{list:"bullet"}],["link"],["clean"]]}});
    var h=document.getElementById("svcQuillHidden");
    if(h&&h.value)q.root.innerHTML=h.value;
    q.on("text-change",function(){if(h)h.value=q.root.innerHTML;});
    window._svcQuill=q;
}
if(!document.getElementById("quill-css")){var l=document.createElement("link");l.id="quill-css";l.rel="stylesheet";l.href="https://cdn.quilljs.com/1.3.7/quill.snow.css";document.head.appendChild(l);}
if(!window.Quill&&!document.getElementById("quill-js")){var s=document.createElement("script");s.id="quill-js";s.src="https://cdn.quilljs.com/1.3.7/quill.min.js";s.onload=function(){_initSvcQuill();};document.head.appendChild(s);}else{_initSvcQuill();}
</script>';

        return response()->json(['html' => $html]);
    }

    /**
     * Restaurant-section hotel modal: shows hotel form + restaurants list from en33_restaurants
     */
    private function editRestaurantSectionModal($service)
    {
        $flags = [
            ['emoji' => '🇫🇷', 'code' => 'fr'],
            ['emoji' => '🇬🇧', 'code' => 'en'],
            ['emoji' => '🇮🇹', 'code' => 'it'],
            ['emoji' => '🇪🇸', 'code' => 'es'],
            ['emoji' => '🇩🇪', 'code' => 'de'],
            ['emoji' => '🇸🇪', 'code' => 'se'],
            ['emoji' => '🇳🇱', 'code' => 'nl'],
        ];

        $imgPath = $service->image ?? '';
        $desc = htmlspecialchars($service->description ?? '');
        $sid = $service->id;
        $countryId = $service->country ?? 123;

        if (!$service->relationLoaded('serviceCategory')) {
            $service->load('serviceCategory.parent.parent.parent');
        }

        $arrival = $service->arrival;
        $accType = $service->acc_type;
        $accCategory = $service->acc_category;

        if ($service->serviceCategory) {
            $cat = $service->serviceCategory;
            $chain = [];
            $walker = $cat->parent ?? null;
            while ($walker) {
                $chain[] = $walker;
                $walker = $walker->parent ?? null;
            }
            if (!$arrival && isset($chain[0])) {
                $arrival = $chain[0]->name;
            }
            $typeMap = ['Hotels' => 'Hotel', 'Camps' => 'Camp', 'Homestay' => 'Guesthouse', 'Homestays' => 'Guesthouse', 'Mobile Camp' => 'Camp', 'Wild Jordan RSCN' => 'Eco-lodge'];
            $starMap = ['1 Star' => '1 ★', '2 Star' => '2 ★★', '3 Star' => '3 ★★★', '4 Stars' => '4 ★★★★', '5 Stars' => '5 ★★★★★'];
            foreach ($chain as $node) {
                if (!$accType && isset($typeMap[$node->name])) {
                    $accType = $typeMap[$node->name];
                }
                if (!$accCategory && isset($starMap[$node->name])) {
                    $accCategory = $starMap[$node->name];
                }
            }
        }

        // Header
        $html = '<script>';
        $html .= 'document.getElementById("libModalHead").innerHTML=\'';
        $html .= '<h3>Modify Restaurant</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editRestSecForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';';
        $html .= '</script>';

        $html .= '<form id="editRestSecForm" onsubmit="submitEditRestSection(' . $sid . '); return false;" enctype="multipart/form-data">';
        $html .= csrf_field();

        // Language flags + vendor bar
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        $vendorName = $service->venderUser
            ? (!empty($service->venderUser->company) ? strtoupper($service->venderUser->company) : strtoupper($service->venderUser->first_name . ' ' . $service->venderUser->last_name))
            : strtoupper($service->description ?? '');
        $html .= '<div style="margin-left:auto;display:flex;gap:16px;align-items:center;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:6px 14px;font-size:12px;">';
        $html .= '<span><strong>Vendor Name:</strong> ' . htmlspecialchars($vendorName) . '</span>';
        $html .= '<span style="color:#ccc;">|</span>';
        $html .= '<span><strong>Vendor Price:</strong> <span style="color:#ea580c;font-weight:700;">' . number_format($service->cost ?? 0, 2) . ' JOD</span></span>';
        $html .= '</div>';
        $html .= '</div>';

        // Photos
        $existingImages = [];
        if ($imgPath) {
            $d = @json_decode($imgPath, true);
            $existingImages = is_array($d) ? $d : [$imgPath];
        }
        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editRestSecImageInput" accept="image/*" multiple style="display:none" onchange="addRestSecImages(this)">';
        $html .= '<div id="restSecPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach ($existingImages as $img) {
            $imgUrl = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $html .= '<div class="acc-photo-wrap" style="position:relative;flex-shrink:0;height:104px;">';
            $html .= '<img src="' . $imgUrl . '" style="height:100%;border-radius:4px;object-fit:cover;">';
            $html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '">';
            $html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            $html .= '</div>';
        }
        $html .= '<div onclick="document.getElementById(\'editRestSecImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">+</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Two-column layout
        $html .= '<div style="display:flex;gap:16px;margin-bottom:16px;">';

        // LEFT
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Name of accommodation</legend>';
        $html .= '<input type="text" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $desc . '">';
        $html .= '<div style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(' . strlen($service->description) . '/255)</div>';
        $html .= '</fieldset>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        $html .= '<div id="svcQuillEditor" style="min-height:140px;background:#fff;font-size:13px;line-height:1.6;"></div>';
        $html .= '<textarea name="notes" id="svcQuillHidden" style="display:none">' . htmlspecialchars($service->notes ?? '') . '</textarea>';
        $html .= '</fieldset>';
        $html .= '</div>';

        // RIGHT
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend>';
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="' . htmlspecialchars($arrival ?? '') . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        $accTypes = ['Hotel', 'Guesthouse', 'Hostel', 'Resort', 'Apartment', 'Camp', 'Eco-lodge', 'Riad', 'Villa'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Accommodation type</legend>';
        $html .= '<select name="acc_type" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a type of accommodation</option>';
        foreach ($accTypes as $t) {
            $sel = ($accType === $t) ? ' selected' : '';
            $html .= '<option value="' . $t . '"' . $sel . '>' . $t . '</option>';
        }
        $html .= '</select></fieldset>';

        $cats = ['1 ★', '2 ★★', '3 ★★★', '4 ★★★★', '5 ★★★★★', 'Standard', 'Superior', 'Luxury'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Category</legend>';
        $html .= '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a category</option>';
        foreach ($cats as $c) {
            $sel = ($accCategory === $c) ? ' selected' : '';
            $html .= '<option value="' . $c . '"' . $sel . '>' . $c . '</option>';
        }
        $html .= '</select></fieldset>';

        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Website</legend>';
        $html .= '<input type="text" name="website" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . htmlspecialchars($service->website ?? '') . '">';
        $html .= '</fieldset>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<input type="hidden" name="cost" value="' . $service->cost . '">';
        $html .= '<input type="hidden" name="category" value="' . $service->category . '">';
        $html .= '</form>';

        // RESTAURANTS LIST — from en33_restaurants for the same country
        $restaurantItems = \App\Models\Restaurant::where('country', $countryId)->with('venderUser')->orderBy('description')->get();
        $restCsrf = csrf_token();
        $html .= '<div style="margin-top:20px;">';
        $html .= '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">';
        $html .= '<span style="color:#dc2626;font-size:11px;font-weight:800;letter-spacing:1px;">🍽️ RESTAURANTS LIST</span>';
        $html .= '<button type="button" onclick="toggleRestAddForm()" style="background:#dc2626;border:none;color:#fff;border-radius:6px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Restaurant</button>';
        $html .= '</div>';
        // Inline Add Restaurant Form
        $html .= '<div id="restAddSvcForm" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        $html .= '<div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">';
        $html .= '<div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="newRestDesc" placeholder="e.g. Dinner" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="newRestCost" step="0.01" placeholder="0.00" value="0.00" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="display:flex;gap:6px;">';
        $html .= '<button type="button" onclick="quickAddRest(' . $sid . ',' . ($service->vender ?? 'null') . ',' . ($service->category ?? 'null') . ',' . $countryId . ',\'' . $restCsrf . '\')" style="height:34px;background:#dc2626;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button>';
        $html .= '<button type="button" onclick="toggleRestAddForm()" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button>';
        $html .= '</div></div></div>';
        $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        $html .= '<thead><tr style="border-bottom:1px solid #e2e8f0;">';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">DESCRIPTION</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">COST</th>';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">VENDOR</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">ACTIONS</th>';
        $html .= '</tr></thead><tbody>';
        foreach ($restaurantItems as $rest) {
            $html .= '<tr id="restRow_' . $rest->id . '" style="border-bottom:1px solid #f7fafc;">';
            $html .= '<td style="padding:7px 8px;"><span id="restDesc_' . $rest->id . '">' . htmlspecialchars($rest->description ?? '-') . '</span></td>';
            $html .= '<td style="padding:7px 8px;text-align:right;color:#dc2626;font-weight:700;"><span id="restCost_' . $rest->id . '">' . number_format($rest->cost ?? 0, 2) . '</span> JOD</td>';
            $html .= '<td style="padding:7px 8px;">' . htmlspecialchars($vendorName) . '</td>';
            $html .= '<td style="padding:7px 8px;text-align:right;white-space:nowrap;">';
            $html .= '<button type="button" onclick="editRestRow(' . $rest->id . ',\'' . addslashes(htmlspecialchars($rest->description ?? '')) . '\',' . ($rest->cost ?? 0) . ')" style="background:#f0f4ff;border:none;color:#dc2626;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;margin-right:4px;"><i class="fa fa-pencil"></i></button>';
            $html .= '<button type="button" onclick="deleteRestRow(' . $rest->id . ')" style="background:#fff5f5;border:none;color:#e53e3e;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;"><i class="fa fa-trash"></i></button>';
            $html .= '</td></tr>';
        }
        if ($restaurantItems->isEmpty()) {
            $html .= '<tr><td colspan="4" style="padding:16px;text-align:center;color:#a0aec0;font-size:12px;">No restaurants found.</td></tr>';
        }
        $html .= '</tbody></table></div>';

        $html .= '<script>
function toggleRestAddForm(){var f=document.getElementById("restAddSvcForm");f.style.display=(f.style.display==="none"?"":"none");}
function quickAddRest(sid,vender,category,country,token){
    var desc=document.getElementById("newRestDesc").value.trim();
    var cost=document.getElementById("newRestCost").value||0;
    if(!desc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/restaurants/quick-add",type:"POST",
        data:{_token:token,description:desc,cost:cost,vender:vender,category:category,country:country},
        success:function(r){if(r.success){document.getElementById("newRestDesc").value="";document.getElementById("newRestCost").value="0.00";toggleRestAddForm();showToast("Restaurant added!","success");}},
        error:function(){showToast("Error adding restaurant","error");}
    });
}
function editRestRow(id,desc,cost){
    var old=document.getElementById("restEditForm_"+id);if(old){old.remove();return;}
    var row=document.getElementById("restRow_"+id);
    var editRow=document.createElement("tr");editRow.id="restEditForm_"+id;
    editRow.innerHTML=\'<td colspan="4" style="padding:10px 8px;background:#f8fafc;"><div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;"><div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="editRestDesc_\'+id+\'" value="\'+desc+\'" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="editRestCost_\'+id+\'" value="\'+cost+\'" step="0.01" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="display:flex;gap:6px;"><button type="button" onclick="saveEditRest(\'+id+\')" style="height:34px;background:#dc2626;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button><button type="button" onclick="cancelEditRest(\'+id+\')" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button></div></div></td>\';
    row.parentNode.insertBefore(editRow,row.nextSibling);
}
function saveEditRest(id){
    var newDesc=document.getElementById("editRestDesc_"+id).value.trim();
    var newCost=document.getElementById("editRestCost_"+id).value;
    if(!newDesc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $restCsrf . '",_method:"PUT",description:newDesc,cost:newCost,service_type:"restaurant"},
        success:function(){document.getElementById("restDesc_"+id).textContent=newDesc;document.getElementById("restCost_"+id).textContent=parseFloat(newCost||0).toFixed(2);cancelEditRest(id);showToast("Restaurant updated!","success");},
        error:function(){showToast("Error updating restaurant","error");}
    });
}
function cancelEditRest(id){var f=document.getElementById("restEditForm_"+id);if(f)f.remove();}
function deleteRestRow(id){
    if(!confirm("Delete this restaurant item?"))return;
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $restCsrf . '",_method:"DELETE",service_type:"restaurant"},
        success:function(){var r=document.getElementById("restRow_"+id);if(r)r.remove();showToast("Restaurant deleted!","success");},
        error:function(){showToast("Error deleting restaurant","error");}
    });
}
function _initSvcQuill(){
    if(typeof Quill==="undefined"){setTimeout(_initSvcQuill,200);return;}
    var el=document.getElementById("svcQuillEditor");if(!el||el.dataset.init)return;el.dataset.init="1";
    var q=new Quill(el,{theme:"snow",modules:{toolbar:[["bold","italic","underline"],[{list:"ordered"},{list:"bullet"}],["link"],["clean"]]}});
    var h=document.getElementById("svcQuillHidden");
    if(h&&h.value)q.root.innerHTML=h.value;
    q.on("text-change",function(){if(h)h.value=q.root.innerHTML;});
    window._svcQuill=q;
}
if(!document.getElementById("quill-css")){var l=document.createElement("link");l.id="quill-css";l.rel="stylesheet";l.href="https://cdn.quilljs.com/1.3.7/quill.snow.css";document.head.appendChild(l);}
if(!window.Quill&&!document.getElementById("quill-js")){var s=document.createElement("script");s.id="quill-js";s.src="https://cdn.quilljs.com/1.3.7/quill.min.js";s.onload=function(){_initSvcQuill();};document.head.appendChild(s);}else{_initSvcQuill();}
</script>';

        return response()->json(['html' => $html]);
    }

    /**
     * Evaneos-style "Modify transport" modal
     */
    private function editRestaurantModal($service)
    {
        $flags = [
            ['emoji' => '🇫🇷', 'code' => 'fr'],
            ['emoji' => '🇬🇧', 'code' => 'en'],
            ['emoji' => '🇮🇹', 'code' => 'it'],
            ['emoji' => '🇪🇸', 'code' => 'es'],
            ['emoji' => '🇩🇪', 'code' => 'de'],
            ['emoji' => '🇸🇪', 'code' => 'se'],
            ['emoji' => '🇳🇱', 'code' => 'nl'],
        ];

        $imgPath = $service->image ?? '';
        $desc = htmlspecialchars($service->description ?? '');
        $sid = $service->id;
        $notes = htmlspecialchars($service->notes ?? '');
        $arrival = htmlspecialchars($service->arrival ?? '');

        $html = '<script>';
        $html .= 'var head = document.getElementById("libModalHead") || document.getElementById("catModalHead");';
        $html .= 'if(head) { head.innerHTML=\'';
        $html .= '<h3>Modify restaurant</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="(typeof closeCatModal === \\\'function\\\' ? closeCatModal : closeModal)()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editRestForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\'; }';
        $html .= '</script>';

        $html .= '<form id="editRestForm" onsubmit="submitEditRestaurant(' . $sid . '); return false;">';
        $html .= csrf_field();
        $html .= '<input type="hidden" name="service_type" value="restaurant">';

        // Flags & Select Restaurant Top Row
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }

        $html .= '<div style="margin-left:auto;display:flex;gap:16px;align-items:center;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:6px 14px;font-size:12px;width:75%;">';
        $html .= '<div style="flex:1;"><label style="font-size:10px;font-weight:700;color:#555;margin-bottom:2px;display:block">Select Vendor</label>';
        $html .= '<select id="edit_modal_vender_select" name="vender" onchange="if(typeof processRestEditChange === \'function\') processRestEditChange(this.value);" style="width:100%;height:30px;border:1px solid #ddd;border-radius:4px;outline:none;" required>';
        $html .= '<option value="">Select a vendor...</option>';
        $html .= '</select></div></div>';
        $currentVender = intval($service->vender ?? 0);
        $html .= '<script>var gCurrentVender = ' . $currentVender . ';</script>';
        $html .= '</div>';

        // Photos
        $existingImages = [];
        if ($imgPath) {
            $d = @unserialize($imgPath);
            if ($d === false && $imgPath !== 'b:0;') {
                $d = @json_decode($imgPath, true);
            }
            if (is_array($d)) {
                foreach ($d as $p) {
                    if (trim($p) !== '') $existingImages[] = $p;
                }
            } else {
                if (trim($imgPath) !== '') $existingImages[] = $imgPath;
            }
        }
        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editRestImageInput" accept="image/*" multiple style="display:none" onchange="addRestNewImages(this)">';
        $html .= '<div id="restPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach ($existingImages as $img) {
            $imgUrl = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $html .= '<div class="acc-photo-wrap" style="position:relative;flex-shrink:0;height:104px;">';
            $html .= '<img src="' . $imgUrl . '" style="height:100%;border-radius:4px;object-fit:cover;">';
            $html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '">';
            $html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            $html .= '</div>';
        }
        $html .= '<div onclick="document.getElementById(\'editRestImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">+</div>';
        $html .= '</div></div>';




        $html .= '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Restaurant name</legend>';
        $html .= '<input type="text" name="description" required style="width:100%;height:32px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $desc . '">';
        $html .= '</fieldset>';

        $html .= '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend>';
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:32px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="' . $arrival . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        $html .= '<textarea name="notes" style="width:100%;min-height:250px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description">' . $notes . '</textarea>';
        $html .= '</fieldset>';

        $html .= '<input type="hidden" name="cost" value="' . ($service->cost ?? 0) . '">';
        $html .= '</form>';

        // Sub-Services Form for Edit Restaurant Modal
        $restCsrf = csrf_token();
        $html .= '<div style="margin-top:20px;">';
        $html .= '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">';
        $html .= '<span style="color:#ea580c;font-size:11px;font-weight:800;letter-spacing:1px;text-transform:uppercase;">🍽️ EXISTING SERVICES LIST</span>';
        $html .= '<button type="button" onclick="toggleRestSubAddForm()" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Service Row</button>';
        $html .= '</div>';

        $html .= '<div id="restSubAddSvcForm" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        $html .= '<div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">';
        $html .= '<div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="newRestDescEdit" placeholder="e.g. Breakfast" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="newRestCostEdit" step="0.01" value="0.00" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="display:flex;gap:6px;">';
        $html .= '<button type="button" onclick="quickAddRestSubEdit(' . ($service->category ?? 0) . ',\'' . $restCsrf . '\', ' . ($service->country ?? 123) . ', ' . ($service->vender ?? 0) . ')" style="height:34px;background:#ea580c;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button>';
        $html .= '<button type="button" onclick="toggleRestSubAddForm()" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button>';
        $html .= '</div></div></div>';

        $html .= '<div id="restServicesContainer" style="margin-top:0px;"></div>';
        $html .= '</div>';

        // Load existing sub-services
        $restaurantSvcs = \App\Models\Service::where('category', $service->category)->get(['id', 'description', 'cost']);

        $html .= '<script>
        window.currentRestServices = ' . json_encode($restaurantSvcs) . ';

        // Setup toggle
        window.toggleRestSubAddForm = function() {
            var f = document.getElementById("restSubAddSvcForm");
            if (f) {
                f.style.display = (f.style.display === "none") ? "block" : "none";
            }
        };



        window.processRestEditChange = function(catId) {
            var cCon = null;
            var editModal = document.getElementById("edit_service_content");
            if (editModal) { cCon = editModal.querySelector("#restServicesContainer"); }
            if (!cCon) {
                var mb = document.getElementById("catModalBody");
                if (mb) { cCon = mb.querySelector("#restServicesContainer"); }
            }
            if (!cCon) { cCon = document.getElementById("restServicesContainer"); }
            if (!catId) {
                window.currentRestServices = [];
                if (cCon) cCon.innerHTML = "";
                return;
            }
            if (typeof restCategoriesData !== "undefined") {
                var el = null;
                for (var gi = 0; gi < restCategoriesData.length; gi++) {
                    if (String(restCategoriesData[gi].id) === String(catId)) {
                        el = restCategoriesData[gi];
                        break;
                    }
                }
                // No longer overwriting the restaurant name input based on vendor selection.
            }
            if (typeof restSubServicesData !== "undefined") {
                window.currentRestServices = restSubServicesData.filter(function(s) {
                    return String(s.vender) === String(catId);
                });
                if (!cCon) return;
                var svcs = window.currentRestServices;
                if (svcs.length > 0) {
                    var h = "<table style=\"width:100%;border-collapse:collapse;font-size:12px;\"><thead><tr><th style=\"padding:8px 6px;text-align:left;color:#64748b;font-size:10px;\">DESCRIPTION</th><th style=\"padding:8px 6px;text-align:left;color:#64748b;font-size:10px;\">COST</th><th style=\"padding:8px 6px;text-align:right;color:#64748b;font-size:10px;\">ACTIONS</th></tr></thead><tbody>";
                    svcs.forEach(function(act) {
                        h += "<tr><td style=\"padding:10px 6px;color:#1e293b;font-weight:600;\">" + (act.description || "-") + "</td><td style=\"padding:10px 6px;font-weight:700;color:#dc2626;\">" + parseFloat(act.cost || 0).toFixed(2) + " JOD</td><td style=\"padding:10px 6px;text-align:right;\"><a href=\"javascript:void(0)\" onclick=\"editRestSubSvc(" + act.id + ")\" style=\"margin-right:12px;color:#3b82f6;\">Edit</a> <a href=\"javascript:void(0)\" onclick=\"delRestSubSvc(" + act.id + ")\" style=\"color:#ef4444;\">Delete</a></td></tr>";
                    });
                    h += "</tbody></table>";
                    cCon.innerHTML = h;
                } else {
                    cCon.innerHTML = "<div style=\"font-size:12px;color:#718096;text-align:center;padding:10px;margin-top:16px;\">No existing services found for this restaurant.</div>";
                }
            }
        };

        // Auto-populate dropdown and trigger fetch on load
        setTimeout(function() {
            var sel = document.getElementById("edit_modal_vender_select");
            if (sel && typeof restCategoriesData !== "undefined") {
                restCategoriesData.forEach(function(v){
                    var o = document.createElement("option"); o.value = v.id; o.textContent = v.name;
                    if(String(v.id) === String(window.gCurrentVender)) { o.selected = true; }
                    sel.appendChild(o);
                });
                if(sel.value && typeof processRestEditChange === "function") {
                    processRestEditChange(sel.value);
                }
            }
        }, 50);

        window.quickAddRestSubEdit = function(cat, token, country, vender) {
            var sel = document.getElementById("edit_modal_vender_select");
            var actCat = sel ? sel.value : cat;
            if (!actCat) actCat = cat;
            var desc = document.getElementById("newRestDescEdit").value;
            var cost = document.getElementById("newRestCostEdit").value || 0;
            if(!desc) { alert("Please enter description"); return; }
            var btn = event.target;
            btn.innerHTML = "Saving...";
            btn.disabled = true;
            $.ajax({
                url: "/admin/services",
                type: "POST",
                data: {
                    _token: token,
                    service_type: "service",
                    description: desc,
                    cost: cost,
                    category: actCat,
                    country: country,
                    vender: vender
                },
                success: function(res) {
                    btn.innerHTML = "Save"; btn.disabled = false;
                    document.getElementById("newRestDescEdit").value = "";
                    document.getElementById("newRestCostEdit").value = "0";
                    window.currentRestServices.unshift(res.data);
                    if (typeof window.processRestEditChange === "function") window.processRestEditChange(actCat);
                    if (typeof showToast==="function") showToast("Service added!", "success");
                },
                error: function(x) {
                    btn.innerHTML = "Save"; btn.disabled = false;
                    alert("Error saving service");
                }
            });
        };

        window.restEditDt = new DataTransfer();

        window.addRestNewImages = function(input) {
            if(input.files && input.files.length > 0){
                for(var i=0; i<input.files.length; i++){
                    window.restEditDt.items.add(input.files[i]);
                }
            }
            input.value = "";
            window.renderRestNewImages();
        };

        window.renderRestNewImages = function() {
            var row = document.getElementById("restPhotosRow");
            if(!row) return;
            var addBtn = row.lastElementChild;
            var existingNew = row.querySelectorAll(".new-rest-photo-wrap");
            existingNew.forEach(function(e) { e.remove(); });

            for(let i=0; i<window.restEditDt.files.length; i++){
                (function(idx) {
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var div = document.createElement("div");
                        div.className = "acc-photo-wrap new-rest-photo-wrap";
                        div.style.cssText = "position:relative;flex-shrink:0;height:104px;min-width:104px;background:#f1f5f9;border-radius:4px;";
                        div.innerHTML = "<img src=\'" + e.target.result + "\' style=\'width:100%;height:100%;border-radius:4px;object-fit:cover;\'>" +
                                        "<button type=\'button\' onclick=\'removeRestNewImg(" + idx + ")\' style=\'position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;\'>✕</button>";
                        row.insertBefore(div, addBtn);
                    };
                    reader.readAsDataURL(window.restEditDt.files[idx]);
                })(i);
            }
        };

        window.removeRestNewImg = function(idx) {
            var newDt = new DataTransfer();
            for(var i=0; i<window.restEditDt.files.length; i++){
                if(i !== idx) newDt.items.add(window.restEditDt.files[i]);
            }
            window.restEditDt = newDt;
            window.renderRestNewImages();
        };

        window.submitEditRestaurant = function(id) {
            var form = document.getElementById("editRestForm");
            var fd = new FormData(form);
            fd.append("_method","PUT");
            fd.append("_token","' . csrf_token() . '");
            fd.append("service_type","restaurant");

            fd.delete("new_images[]");
            if (window.restEditDt) {
                for(var i=0; i<window.restEditDt.files.length; i++){
                    fd.append("new_images[]", window.restEditDt.files[i]);
                }
            }

            var btn = form.querySelector("button[type=submit]");
            if(btn) { btn.disabled = true; btn.innerText = "Saving..."; }

            $.ajax({
                url: "/admin/services/" + id,
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (typeof closeCatModal === "function") closeCatModal();
                    else if (typeof closeModal === "function") closeModal();
                    if (typeof showToast === "function") showToast("Restaurant updated", "success");
                    if (typeof refreshData === "function") refreshData(); // Or reload window
                    else window.location.reload();
                },
                error: function(x) {
                    if(btn) { btn.disabled = false; btn.innerText = "Save"; }
                    var msg = "Error updating restaurant";
                    if(x.responseJSON && x.responseJSON.message) msg = x.responseJSON.message;
                    alert(msg);
                }
            });
        };

        setTimeout(function(){
            var sel = document.getElementById("edit_modal_vender_select");
            if (sel && typeof window.processRestEditChange === "function") {
                window.processRestEditChange(sel.value);
            }
        }, 100);
        </script>';

        return response()->json(['html' => $html]);
    }

    private function editTransportModal($service)
    {
        // Get the root-level parent of this service's category (Transportation root)
        $catId = $service->category;
        $rootId = $catId;
        $walker = \App\Models\ServiceCategory::find($catId);
        while ($walker && $walker->parent_id != 0) {
            $walker = \App\Models\ServiceCategory::find($walker->parent_id);
            if ($walker) $rootId = $walker->id;
        }
        // Collect all category IDs under this Transportation root
        $transCategories = \App\Models\ServiceCategory::where('parent_id', $rootId)->pluck('id')->toArray();
        $transCategories[] = $rootId;
        // Also get sub-sub categories
        $subIds = \App\Models\ServiceCategory::whereIn('parent_id', $transCategories)->pluck('id')->toArray();
        $allTransCatIds = array_unique(array_merge($transCategories, $subIds));
        // Get only vendor IDs who have services under these transport categories
        $transVenderIds = \App\Models\Activity::whereIn('category', $allTransCatIds)->pluck('vender')->unique()->toArray();
        $venders = \App\Models\User::where('user_group', 'supplier')
            ->whereIn('id', $transVenderIds)
            ->orderBy('first_name')
            ->get();
        $flags = [
            ['emoji' => '🇫🇷', 'code' => 'fr'],
            ['emoji' => '🇬🇧', 'code' => 'en'],
            ['emoji' => '🇮🇹', 'code' => 'it'],
            ['emoji' => '🇪🇸', 'code' => 'es'],
            ['emoji' => '🇩🇪', 'code' => 'de'],
            ['emoji' => '🇸🇪', 'code' => 'se'],
            ['emoji' => '🇳🇱', 'code' => 'nl'],
        ];

        $imgPath = $service->image ?? '';
        $desc = htmlspecialchars($service->description ?? '');
        $sid = $service->id;
        $method = $service->transport_method ?? '';
        $depLoc = htmlspecialchars($service->departure_location ?? '');
        $arrDest = htmlspecialchars($service->arrival_destination ?? '');
        $lengthTime = htmlspecialchars($service->length_time ?? '');
        $distKm = htmlspecialchars($service->distance_km ?? '');
        $notes = htmlspecialchars($service->notes ?? '');

        if (empty($depLoc) && empty($arrDest) && !empty($service->description)) {
            $parts = explode('/', $service->description);
            if (count($parts) >= 2) {
                $depLoc = trim($parts[0]);
                $arrDest = trim(end($parts));
            } else {
                $parts = explode('-', $service->description);
                if (count($parts) >= 2) {
                    $depLoc = trim($parts[0]);
                    $arrDest = trim(end($parts));
                }
            }
        }
        if (empty($method)) {
            $lowerDesc = strtolower($service->description ?? '');
            if (str_contains($lowerDesc, 'bus')) $method = 'Bus';
            elseif (str_contains($lowerDesc, 'plane') || str_contains($lowerDesc, 'flight')) $method = 'Airplane';
            elseif (str_contains($lowerDesc, 'boat') || str_contains($lowerDesc, 'ship') || str_contains($lowerDesc, 'ferry')) $method = 'Boat';
            elseif (str_contains($lowerDesc, 'train')) $method = 'Train';
            else $method = 'Car';
        }

        $html = '<script>';
        $html .= 'var head = document.getElementById("libModalHead") || document.getElementById("catModalHead");';
        $html .= 'if(head) { head.innerHTML=\'';
        $html .= '<h3>Modify transport</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="(typeof closeCatModal === \\\'function\\\' ? closeCatModal : closeModal)()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editTransForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\'; }';

        $html .= '
        window.transEditDt = new DataTransfer();

        window.addTransImages = function(input) {
            if(input.files && input.files.length > 0){
                for(var i=0; i<input.files.length; i++){
                    window.transEditDt.items.add(input.files[i]);
                }
            }
            input.value = "";
            window.renderTransImages();
        };

        window.renderTransImages = function() {
            var row = document.getElementById("transPhotosRow");
            if(!row) return;
            var addBtn = row.lastElementChild;
            var exisitingNew = row.querySelectorAll(".new-trans-photo-wrap");
            exisitingNew.forEach(function(e) { e.remove(); });

            for(let i=0; i<window.transEditDt.files.length; i++){
                (function(idx) {
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var div = document.createElement("div");
                        div.className = "trans-photo-wrap new-trans-photo-wrap";
                        div.style.cssText = "position:relative;flex-shrink:0;height:104px;min-width:104px;background:#f1f5f9;border-radius:4px;";
                        div.innerHTML = "<img src=\'" + e.target.result + "\' style=\'width:100%;height:100%;border-radius:4px;object-fit:cover;\'>" +
                                        "<button type=\'button\' onclick=\'removeTransNewImg(" + idx + ")\' style=\'position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;\'>✕</button>";
                        row.insertBefore(div, addBtn);
                    };
                    reader.readAsDataURL(window.transEditDt.files[idx]);
                })(i);
            }
        };

        window.removeTransNewImg = function(idx) {
            var newDt = new DataTransfer();
            for(var i=0; i<window.transEditDt.files.length; i++){
                if(i !== idx) newDt.items.add(window.transEditDt.files[i]);
            }
            window.transEditDt = newDt;
            window.renderTransImages();
        };

        window.submitEditTrans = function(id) {
            var form = document.getElementById("editTransForm");
            var fd = new FormData(form);
            fd.append("_method","PUT");
            fd.append("_token","' . csrf_token() . '");
            fd.append("service_type","transport");

            fd.delete("new_images[]");
            for(var i=0; i<window.transEditDt.files.length; i++){
                fd.append("new_images[]", window.transEditDt.files[i]);
            }

            var btn = form.querySelector("button[type=submit]");
            if(btn) { btn.disabled = true; btn.innerText = "Saving..."; }

            $.ajax({
                url: "/admin/services/" + id,
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (typeof closeCatModal === "function") closeCatModal();
                    else if (typeof closeModal === "function") closeModal();

                    if (typeof reloadCatList === "function") reloadCatList();
                    else if (typeof loadLib === "function") loadLib();
                    else location.reload();

                    if (typeof showToast === "function") showToast("Transport updated!", "success");
                },
                error: function(x) {
                    if(btn) { btn.disabled = false; btn.innerText = "Save"; }
                    if (typeof showToast === "function") showToast("Error: " + (x.responseJSON && x.responseJSON.message ? x.responseJSON.message : "Could not update"), "error");
                }
            });
        };
        ';
        $html .= '</script>';

        $html .= '<form id="editTransForm" onsubmit="submitEditTrans(' . $sid . '); return false;" enctype="multipart/form-data">';
        $html .= csrf_field();

        // Top Row: Flags & Vendor Price info
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }

        // Build dropdown from transport company category names (Ismael cars, Al Raha bus etc.)
        // Value = vendor user ID for proper data relationship
        $transCompanyCategories = \App\Models\ServiceCategory::where('parent_id', $rootId)->orderBy('name')->get();
        $venderOpts = '<option value="">Select a vendor...</option>';
        $companyMethodData = [];

        // Determine the ACTUAL vendor (company) this service belongs to, based purely on
        // its real category relationship. This is the single source of truth. A
        // description-text fallback is only used when no real category match exists at
        // all — it must never override a real match, otherwise two options can end up
        // marked "selected" at once and the dropdown won't match the service's real data.
        $companyCatId = 0;
        $companyCatIdsMap = [];
        foreach ($transCompanyCategories as $companyCat) {
            $childCatIds = \App\Models\ServiceCategory::where('parent_id', $companyCat->id)->pluck('id')->toArray();
            $companyCatIdsMap[$companyCat->id] = array_merge([$companyCat->id], $childCatIds);
            if (in_array($service->category, $companyCatIdsMap[$companyCat->id])) {
                $companyCatId = $companyCat->id;
            }
        }
        if ($companyCatId === 0) {
            foreach ($transCompanyCategories as $companyCat) {
                if (strcasecmp(trim($service->description ?? ''), trim($companyCat->name)) === 0) {
                    $companyCatId = $companyCat->id;
                    break;
                }
            }
        }

        foreach ($transCompanyCategories as $companyCat) {
            $selected = ($companyCat->id === $companyCatId) ? ' selected' : '';
            // Use category ID as unique option value so select works correctly
            $venderOpts .= '<option value="' . $companyCat->id . '" data-catid="' . $companyCat->id . '"' . $selected . '>' . htmlspecialchars($companyCat->name) . '</option>';

            $directSvcs = \App\Models\Service::where('category', $companyCat->id)->get(['id', 'description', 'cost', 'vender', 'departure_location', 'arrival_destination', 'length_time', 'distance_km', 'transport_method']);
            $methodsList = [];
            $methodCats = \App\Models\ServiceCategory::where('parent_id', $companyCat->id)->orderBy('name')->get();
            foreach ($methodCats as $mc) {
                $subSvcs = \App\Models\Service::where('category', $mc->id)->get(['id', 'description', 'cost', 'vender', 'departure_location', 'arrival_destination', 'length_time', 'distance_km', 'transport_method']);
                $methodsList[] = ['id' => $mc->id, 'name' => $mc->name, 'services' => $subSvcs->values()->toArray()];
            }
            // Key = category ID (string) — always available, never null
            $companyMethodData[strval($companyCat->id)] = [
                'catId'          => $companyCat->id,
                'vendorId'       => '',
                'name'           => $companyCat->name,
                'methods'        => $methodsList,
                'directServices' => $directSvcs->values()->toArray()
            ];
        }
        $companyMethodDataJson = json_encode($companyMethodData);

        $html .= '<div style="margin-left:auto;display:flex;gap:16px;align-items:center;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:6px 14px;font-size:12px;width:75%;">';
        $html .= '<div style="flex:1;"><label style="font-size:10px;font-weight:700;color:#555;margin-bottom:2px;display:block">Select Vendor</label><select id="edit_modal_vender_select" name="category" onchange="if(typeof processTransEditChange === \'function\') processTransEditChange(this.value);" style="width:100%;height:30px;border:1px solid #ddd;border-radius:4px;outline:none;">' . $venderOpts . '</select></div>';
        $html .= '<span style="color:#ccc;">|</span>';
        $html .= '<span style="white-space:nowrap;"><strong>Vendor Price:</strong> <span style="color:#ea580c;font-weight:700;"><input type="number" name="cost" value="' . ($service->cost ?? 0) . '" step="0.01" style="width:70px;height:24px;border:1px solid #ddd;border-radius:4px;padding:2px 6px;outline:none;"> JOD</span></span>';
        $html .= '</div>';
        $html .= '</div>';

        // Photos section
        $existingImages = [];
        if ($imgPath) {
            $d = @unserialize($imgPath);
            if ($d === false && $imgPath !== 'b:0;') $d = @json_decode($imgPath, true);
            if (is_array($d)) { foreach ($d as $p) { if (trim($p) !== '') $existingImages[] = $p; } }
            else { if (trim($imgPath) !== '') $existingImages[] = $imgPath; }
        }

        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editTransImageInput" accept="image/*" multiple style="display:none" onchange="addTransImages(this)">';
        $html .= '<div id="transPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach ($existingImages as $idx => $img) {
            $imgUrl = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $imgUrl = str_replace('/public/', '/', $imgUrl);
            $html .= '<div class="trans-photo-wrap" style="position:relative;flex-shrink:0;height:104px;min-width:104px;background:#f1f5f9;border-radius:4px;">';
            $html .= '<img src="' . $imgUrl . '" style="width:100%;height:100%;border-radius:4px;object-fit:cover;" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/104?text=Photo\';">';
            $html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '">';
            $html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            $html .= '</div>';
        }
        $html .= '<div onclick="document.getElementById(\'editTransImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">+</div>';
        $html .= '</div></div>';

        // Layout
        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Transport Title</legend>';
        $html .= '<input type="text" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $desc . '" oninput="document.getElementById(\'transTitleCount\').textContent=\'(\'+this.value.length+\'/255)\'">';
        $html .= '<div id="transTitleCount" style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(' . strlen($desc) . '/255)</div>';
        $html .= '</fieldset>';

        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        $html .= '<div id="transQuillEditor" style="min-height:140px;background:#fff;font-size:13px;line-height:1.6;"></div>';
        $html .= '<textarea name="notes" id="transQuillHidden" style="display:none">' . $notes . '</textarea>';
        $html .= '</fieldset>';
        // Hidden fields column for JS
        $html .= '<div style="display:none;">';
        // The top form should not have departure, arrival, length, distance or method fields visually.
        // We keep a hidden select for JS logic to populate the row edit options.
        $html .= '<div style="display:none;">';
        $html .= '<select id="editTransMethodSelect" name="transport_method">';
        if ($companyCatId == 0 && $service->category) {
            $svcCat = \App\Models\ServiceCategory::find($service->category);
            if ($svcCat && $svcCat->parent_id > 0) {
                $companyCatId = $svcCat->parent_id;
            } elseif ($svcCat && $svcCat->parent_id == 0) {
                $companyCatId = $svcCat->id;
            }
        }
        $currentCompanyChildCats = ($companyCatId > 0)
            ? \App\Models\ServiceCategory::where('parent_id', $companyCatId)->get()
            : collect();
        if ($currentCompanyChildCats->isNotEmpty()) {
            $html .= '<option value="">Select method...</option>';
            foreach ($currentCompanyChildCats as $childCat) {
                $sel = ($service->category == $childCat->id) ? ' selected' : '';
                $html .= '<option value="' . $childCat->id . '"' . $sel . '>' . htmlspecialchars($childCat->name) . '</option>';
            }
        } else {
            $html .= '<option value="">Select method...</option>';
        }
        $html .= '</select>';
        $html .= '</div>';

        $html .= '</div>';

        $html .= '<input type="hidden" name="category" value="' . ($service->category ?? '') . '">';
        $html .= '</form>';

        // Add Sub Service Form
        $transCsrf = csrf_token();
        $html .= '<div style="margin-top:20px;">';
        $html .= '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">';
        $html .= '<span style="color:#10b981;font-size:11px;font-weight:800;letter-spacing:1px;">🚗 SUB-SERVICES LIST</span>';
        $html .= '<button type="button" onclick="toggleTransSubAddForm()" style="background:#10b981;border:none;color:#fff;border-radius:6px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Service Row</button>';
        $html .= '</div>';
        $html .= '<div id="transSubAddSvcForm" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        $html .= '<div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">';
        $html .= '<div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="newTsDesc" placeholder="e.g. Airport Transfer" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="flex:1;min-width:140px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Method</label><select id="newTsMethod" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 8px;font-size:12px;"></select></div>';
        $html .= '<div style="flex:1;min-width:130px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Departure</label><input type="text" id="newTsDep" autocomplete="off" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;" oninput="if(typeof transPlaceAutocomplete!==\'undefined\') transPlaceAutocomplete(this.value, \'newTsDep_dd\', \'newTsDep\')"><div id="newTsDep_dd" style="display:none;position:absolute;z-index:9999;background:#fff;border:1px solid #e2e8f0;max-height:150px;overflow-y:auto;border-radius:4px;"></div></div>';
        $html .= '<div style="flex:1;min-width:130px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Arrival</label><input type="text" id="newTsArr" autocomplete="off" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;" oninput="if(typeof transPlaceAutocomplete!==\'undefined\') transPlaceAutocomplete(this.value, \'newTsArr_dd\', \'newTsArr\')"><div id="newTsArr_dd" style="display:none;position:absolute;z-index:9999;background:#fff;border:1px solid #e2e8f0;max-height:150px;overflow-y:auto;border-radius:4px;"></div></div>';
        $html .= '<div style="flex:1;min-width:80px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Length</label><input type="text" id="newTsLen" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="flex:1;min-width:80px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Dist(km)</label><input type="text" id="newTsDist" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="newTsCost" step="0.01" value="0.00" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="display:flex;gap:6px;">';
        $html .= '<button type="button" onclick="quickAddTransSub(' . ($service->country ?? 123) . ',\'' . $transCsrf . '\')" style="height:34px;background:#10b981;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button>';
        $html .= '<button type="button" onclick="toggleTransSubAddForm()" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button>';
        $html .= '</div></div></div>';
        $html .= '</div>';

        // Services list container (loads on vendor select change)
        $html .= '<div id="transServicesContainer" style="margin-top:0px;"></div>';

        $html .= '<script>
        var companyMethodData = ' . $companyMethodDataJson . ';


        window.transEditRowId = null;
        function renderTransSvcTable(services) {
            window.currentTransServices = services;
            var con = document.getElementById("transServicesContainer");
            if (!con) return;
            if (!services || services.length === 0) {
                con.innerHTML = "<div style=\"font-size:12px;color:#718096;text-align:center;padding:10px;border-radius:6px;background:#f8fafc\">No services found.</div>";
                return;
            }
            var th = "text-align:left;padding:5px 6px;font-size:10px;font-weight:700;color:#718096;white-space:nowrap;";
            var thR = "text-align:right;padding:5px 6px;font-size:10px;font-weight:700;color:#718096;white-space:nowrap;";
            var tbl = "<div style=\"margin-top:6px;overflow-x:auto;\"><table style=\"width:100%;border-collapse:collapse;font-size:11px;\">"
                + "<thead><tr style=\"border-bottom:1px solid #e2e8f0;background:#f8fafc;\">"
                + "<th style=\"" + th + "\">DESCRIPTION</th>"
                + "<th style=\"" + th + "\">Method of transport</th>"
                + "<th style=\"" + th + "\">Departure location</th>"
                + "<th style=\"" + th + "\">Arrival location</th>"
                + "<th style=\"" + th + "\">Length</th>"
                + "<th style=\"" + th + "\">Distance km</th>"
                + "<th style=\"" + thR + "\">COST</th>"
                + "<th style=\"" + thR + "\">ACTIONS</th>"
                + "</tr></thead><tbody>";
            services.forEach(function(s) {
                var td = "padding:5px 6px;border-bottom:1px solid #f0f0f0;";
                if (window.transEditRowId === s.id) {
                    var inpStyle = "width:100%;border:1px solid #cbd5e1;border-radius:4px;padding:4px;font-size:11px;outline:none;";

                    var curVal = String(s.transport_method || "");
                    var methodSelectHTML = "<select id=\"ts_meth_" + s.id + "\" style=\"" + inpStyle + "\">";
                    var topSel = document.getElementById("editTransMethodSelect");
                    if (topSel) {
                        for (var i = 0; i < topSel.options.length; i++) {
                            var opt = topSel.options[i];
                            if (!opt.text) continue;
                            if (opt.text.toLowerCase().indexOf("select method") > -1 && i === 0) continue; // Skip placeholder
                            var isSelected = (curVal == opt.text || curVal == opt.value) ? " selected" : "";
                            methodSelectHTML += "<option value=\"" + opt.text.replace(/\"/g, "&quot;") + "\"" + isSelected + ">" + opt.text + "</option>";
                        }
                    } else {
                        methodSelectHTML += "<option value=\"" + curVal.replace(/\"/g, "&quot;") + "\">" + curVal + "</option>";
                    }
                    methodSelectHTML += "</select>";

                    var tdDepArr = "padding:5px 6px;border-bottom:1px solid #f0f0f0;position:relative;";
                    tbl += "<tr>"
                        + "<td style=\"" + td + "\"><input type=\"text\" id=\"ts_desc_" + s.id + "\" value=\"" + String(s.description || "").replace(/\'/g, "&apos;").replace(/\"/g, "&quot;") + "\" style=\"" + inpStyle + "\"></td>"
                        + "<td style=\"" + td + "\">" + methodSelectHTML + "</td>"
                        + "<td style=\"" + tdDepArr + "\"><input autocomplete=\"off\" type=\"text\" id=\"ts_dep_" + s.id + "\" value=\"" + String(s.departure_location || "").replace(/\'/g, "&apos;").replace(/\"/g, "&quot;") + "\" style=\"" + inpStyle + "\" oninput=\"if(typeof transPlaceAutocomplete !== \'undefined\') transPlaceAutocomplete(this.value, \'ts_dep_dd_" + s.id + "\', \'ts_dep_" + s.id + "\')\"><div id=\"ts_dep_dd_" + s.id + "\" style=\"display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.15);max-height:220px;overflow-y:auto;text-align:left;\"></div></td>"
                        + "<td style=\"" + tdDepArr + "\"><input autocomplete=\"off\" type=\"text\" id=\"ts_arr_" + s.id + "\" value=\"" + String(s.arrival_destination || "").replace(/\'/g, "&apos;").replace(/\"/g, "&quot;") + "\" style=\"" + inpStyle + "\" oninput=\"if(typeof transPlaceAutocomplete !== \'undefined\') transPlaceAutocomplete(this.value, \'ts_arr_dd_" + s.id + "\', \'ts_arr_" + s.id + "\')\"><div id=\"ts_arr_dd_" + s.id + "\" style=\"display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.15);max-height:220px;overflow-y:auto;text-align:left;\"></div></td>"
                        + "<td style=\"" + td + "\"><input type=\"text\" id=\"ts_len_" + s.id + "\" value=\"" + String(s.length_time || "").replace(/\'/g, "&apos;").replace(/\"/g, "&quot;") + "\" style=\"" + inpStyle + "\"></td>"
                        + "<td style=\"" + td + "\"><input type=\"text\" id=\"ts_dist_" + s.id + "\" value=\"" + String(s.distance_km || "").replace(/\'/g, "&apos;").replace(/\"/g, "&quot;") + "\" style=\"" + inpStyle + "\"></td>"
                        + "<td style=\"" + td + "\"><input type=\"number\" step=\"0.01\" id=\"ts_cost_" + s.id + "\" value=\"" + (s.cost || 0) + "\" style=\"" + inpStyle + "text-align:right;\"></td>"
                        + "<td style=\"" + td + "text-align:right;white-space:nowrap;\">"
                        + "<a href=\"javascript:void(0)\" onclick=\"saveTransSubSvc(" + s.id + ")\" style=\"margin-right:12px;color:#10b981;text-decoration:none;font-weight:700;\"><i class=\"fa fa-save\"></i> Save</a>"
                        + "<a href=\"javascript:void(0)\" onclick=\"cancelTransEdit()\" style=\"color:#64748b;text-decoration:none;font-weight:600;\"><i class=\"fa fa-times\"></i> Cancel</a>"
                        + "</td>"
                        + "</tr>";
                } else {
                    tbl += "<tr>"
                        + "<td style=\"" + td + "\">" + (s.description || "-") + "</td>"
                        + "<td style=\"" + td + "\">" + (s.transport_method || "-") + "</td>"
                        + "<td style=\"" + td + "\">" + (s.departure_location || "-") + "</td>"
                        + "<td style=\"" + td + "\">" + (s.arrival_destination || "-") + "</td>"
                        + "<td style=\"" + td + "\">" + (s.length_time || "-") + "</td>"
                        + "<td style=\"" + td + "\">" + (s.distance_km || "-") + "</td>"
                        + "<td style=\"" + td + "text-align:right;color:#ea580c;font-weight:700;\">" + parseFloat(s.cost || 0).toFixed(2) + " JOD</td>"
                        + "<td style=\"" + td + "text-align:right;white-space:nowrap;\">"
                        + "<a href=\"javascript:void(0)\" onclick=\"editTransSubSvc(" + s.id + ")\" style=\"margin-right:12px;color:#3b82f6;text-decoration:none;\"><i class=\"fa fa-pencil\"></i> Edit</a>"
                        + "<a href=\"javascript:void(0)\" onclick=\"delTransSubSvc(" + s.id + ")\" style=\"color:#ef4444;text-decoration:none;\"><i class=\"fa fa-trash\"></i> Delete</a>"
                        + "</td>"
                        + "</tr>";
                }
            });
            tbl += "</tbody></table></div>";
            con.innerHTML = tbl;
        }

        window.editTransSubSvc = function(id) {
            window.transEditRowId = id;
            renderTransSvcTable(window.currentTransServices);
            var s = window.currentTransServices.find(function(x) { return x.id == id; });
            if (!s) return;
            var depInp = document.getElementById("editTransDepInp");
            if (depInp) depInp.value = s.departure_location || "";
            var arrInp = document.getElementById("editTransArrInp");
            if (arrInp) arrInp.value = s.arrival_destination || "";
            var lenInp = document.getElementById("editTransLenInp");
            if (lenInp) lenInp.value = s.length_time || "";
            var distInp = document.getElementById("editTransDistInp");
            if (distInp) distInp.value = s.distance_km || "";
        };

        window.cancelTransEdit = function() {
            window.transEditRowId = null;
            renderTransSvcTable(window.currentTransServices);
        };

        window.saveTransSubSvc = function(id) {
            var s = window.currentTransServices.find(function(x) { return x.id == id; });
            if (!s) return;
            var newDesc = document.getElementById("ts_desc_" + id).value;
            var newMethod = document.getElementById("ts_meth_" + id).value;
            var depInp = document.getElementById("editTransDepInp");
            var newDep = depInp ? depInp.value : (document.getElementById("ts_dep_" + id) ? document.getElementById("ts_dep_" + id).value : "");
            var arrInp = document.getElementById("editTransArrInp");
            var newArr = arrInp ? arrInp.value : (document.getElementById("ts_arr_" + id) ? document.getElementById("ts_arr_" + id).value : "");
            var lenInp = document.getElementById("editTransLenInp");
            var newLen = lenInp ? lenInp.value : (document.getElementById("ts_len_" + id) ? document.getElementById("ts_len_" + id).value : "");
            var distInp = document.getElementById("editTransDistInp");
            var newDist = distInp ? distInp.value : (document.getElementById("ts_dist_" + id) ? document.getElementById("ts_dist_" + id).value : "");
            var newCost = document.getElementById("ts_cost_" + id).value;

            $.ajax({
                url: "/admin/services/" + id,
                type: "POST",
                data: {
                    _method: "PUT",
                    _token: "' . csrf_token() . '",
                    description: newDesc,
                    transport_method: newMethod,
                    departure_location: newDep,
                    arrival_destination: newArr,
                    length_time: newLen,
                    distance_km: newDist,
                    cost: parseFloat(newCost) || 0,
                    service_type: "basic"
                },
                success: function() {
                    s.description = newDesc;
                    s.transport_method = newMethod;
                    s.departure_location = newDep;
                    s.arrival_destination = newArr;
                    s.length_time = newLen;
                    s.distance_km = newDist;
                    s.cost = parseFloat(newCost) || 0;
                    window.transEditRowId = null;
                    renderTransSvcTable(window.currentTransServices);
                    if(typeof showToast === "function") showToast("Updated successfully", "success");
                    else alert("Updated successfully");
                },
                error: function(x) {
                    var msg = "Error updating service";
                    if (x.responseJSON && x.responseJSON.message) msg = x.responseJSON.message;
                    alert(msg);
                }
            });
        };

        window.delTransSubSvc = function(id) {
            if(!confirm("Are you sure you want to delete this service?")) return;
            $.ajax({
                url: "/admin/services/" + id,
                type: "POST",
                data: {
                    _method: "DELETE",
                    _token: "' . csrf_token() . '",
                    service_type: "service"
                },
                success: function() {
                    window.currentTransServices = window.currentTransServices.filter(function(x) { return x.id != id; });
                    renderTransSvcTable(window.currentTransServices);
                    if(typeof showToast === "function") showToast("Deleted successfully", "success");
                    else alert("Deleted successfully");
                },
                error: function(x) {
                    alert("Error deleting service");
                }
            });
        };

        function updateTransMethodDropdown(vendorKey, selectedMethodId) {
            var company = companyMethodData[vendorKey];
            var sel = document.getElementById("editTransMethodSelect");
            if (!sel) return;
            sel.innerHTML = "";
            if (!company || !company.methods || company.methods.length === 0) {
                sel.innerHTML = "<option value=\'\'>No sub-methods available</option>";
                if (company && company.directServices) renderTransSvcTable(company.directServices);
                return;
            }
            sel.innerHTML = "<option value=\'\'>Select method...</option>";
            var firstSvcs = null;
            company.methods.forEach(function(m, idx) {
                var opt = document.createElement("option");
                opt.value = m.id;
                opt.text = m.name;
                if (selectedMethodId && m.id == selectedMethodId) { opt.selected = true; firstSvcs = m.services; }
                else if (!selectedMethodId && idx === 0) { firstSvcs = m.services; }
                sel.appendChild(opt);
            });
            if (firstSvcs) renderTransSvcTable(firstSvcs);
        }

        window.toggleTransSubAddForm = function() {
            var f = document.getElementById("transSubAddSvcForm");
            if(f) f.style.display = (f.style.display === "none" ? "block" : "none");
            if(f && f.style.display !== "none") {
                // Populate method dropdown from the top method dropdown
                var mSel = document.getElementById("newTsMethod");
                if (mSel) {
                    mSel.innerHTML = "";
                    var methodOpts = ["Car with driver","Van with driver","Bus with driver","Car","Van","Bus","Airplane","Boat","Train"];
                    for(var i=0; i<methodOpts.length; i++) {
                        mSel.add(new Option(methodOpts[i], methodOpts[i]));
                    }
                }
            }
        };

        window.quickAddTransSub = function(country, token) {
            var desc = document.getElementById("newTsDesc").value.trim();
            var methSel = document.getElementById("newTsMethod");
            var methName = methSel && methSel.selectedIndex >= 0 ? methSel.value : "";
            var dep = document.getElementById("newTsDep").value.trim();
            var arr = document.getElementById("newTsArr").value.trim();
            var len = document.getElementById("newTsLen").value.trim();
            var dist = document.getElementById("newTsDist").value.trim();
            var cost = document.getElementById("newTsCost").value.trim() || 0;

            if(!desc) { alert("Please enter a description."); return; }
            var catId = "";
            var vendDD = document.getElementById("edit_modal_vender_select");
            if (vendDD && vendDD.selectedIndex >= 0) {
               catId = vendDD.options[vendDD.selectedIndex].getAttribute("data-catid");
            }
            if(!catId) { alert("Please select a transport company."); return; }

            $.ajax({
                url: "/admin/services/quick-add",
                type: "POST",
                data: {
                    _token: token,
                    description: desc,
                    cost: cost,
                    category: catId,
                    country: country,
                    transport_method: methName,
                    departure_location: dep,
                    arrival_destination: arr,
                    length_time: len,
                    distance_km: dist
                },
                success: function(resp) {
                    if (resp.success) {
                        var newObj = {
                            id: resp.id,
                            description: desc,
                            transport_method: methName,
                            departure_location: dep,
                            arrival_destination: arr,
                            length_time: len,
                            distance_km: dist,
                            cost: parseFloat(cost)
                        };
                        if (!window.currentTransServices) { window.currentTransServices = []; }
                        window.currentTransServices.unshift(newObj);
                        renderTransSvcTable(window.currentTransServices);
                        document.getElementById("newTsDesc").value = "";
                        document.getElementById("newTsDep").value = "";
                        document.getElementById("newTsArr").value = "";
                        document.getElementById("newTsLen").value = "";
                        document.getElementById("newTsDist").value = "";
                        document.getElementById("newTsCost").value = "0.00";
                        toggleTransSubAddForm();

                        var vendorId = null;
                        var vendDD = document.getElementById("edit_modal_vender_select");
                        if (vendDD && vendDD.options[vendDD.selectedIndex]) {
                            vendorId = vendDD.options[vendDD.selectedIndex].getAttribute("data-catid");
                        }

                        if (vendorId && companyMethodData[String(vendorId)]) {
                            var methodsArr = companyMethodData[String(vendorId)].methods;
                            var f = methodsArr ? methodsArr.find(function(m){ return String(m.id) === String(catId); }) : null;
                            if (f) {
                                f.services.unshift(newObj);
                            } else {
                                companyMethodData[String(vendorId)].directServices.unshift(newObj);
                            }
                        }

                        if(typeof showToast === "function") showToast("Service added!", "success");
                    }
                },
                error: function(x) {
                    var msg = "Error adding service";
                    if (x.responseJSON && x.responseJSON.message) msg = x.responseJSON.message;
                    if(typeof showToast === "function") showToast(msg, "error"); else alert(msg);
                }
            });
        };

        setTimeout(function(){
            var ssMethod = null;

            // Helper: get selected vendor option data-catid
            window.getVendorCatId = function() {
                var sel = document.getElementById("edit_modal_vender_select");
                if (!sel) return null;
                var opt = sel.options[sel.selectedIndex];
                return opt ? (opt.getAttribute("data-catid") || "") : "";
            };

            function rebuildMethodDropdown(catId, selectedMethodId) {
                var company = companyMethodData[String(catId)];
                var allSvcs = [];
                var nativeOpts = "";

                if (company && company.methods && company.methods.length > 0) {
                    nativeOpts = "<option value=\"\">Select method...</option>";
                    company.methods.forEach(function(m, idx) {
                        var isSel = (selectedMethodId && String(m.id) === String(selectedMethodId));
                        nativeOpts += "<option value=\"" + m.id + "\"" + (isSel ? " selected" : "") + ">" + m.name + "</option>";
                        if (m.services) allSvcs = allSvcs.concat(m.services);
                    });
                    if (company.directServices && company.directServices.length > 0) {
                        allSvcs = allSvcs.concat(company.directServices);
                    }
                } else {
                    nativeOpts = "<option value=\"\">Select method...</option>";
                    if (company && company.directServices) allSvcs = company.directServices;
                }

                var sel = document.getElementById("editTransMethodSelect");
                if (sel) sel.innerHTML = nativeOpts;

                if (typeof SlimSelect !== "undefined") {
                    try { if (ssMethod && ssMethod.slim) ssMethod.destroy(); } catch(ex) {}
                    try {
                        ssMethod = new SlimSelect({
                            select: "#editTransMethodSelect",
                            showSearch: false,
                            onChange: function(info) {
                                var methodId = info && info.value ? info.value : (Array.isArray(info) && info[0] ? info[0].value : null);
                                if (!methodId) return;
                                var cId = getVendorCatId();
                                var comp = cId ? companyMethodData[String(cId)] : null;
                                if (comp && comp.methods) {
                                    var f = comp.methods.find(function(m) { return String(m.id) === String(methodId); });
                                    if (f) renderTransSvcTable(f.services);
                                }
                            }
                        });
                    } catch(ex) {}
                }
                if (allSvcs.length > 0) renderTransSvcTable(allSvcs);
                else renderTransSvcTable([]);
            }

            window.processTransEditChange = function(val) {
                // Auto-populate Transport Title (description)
                var selEl = document.getElementById("edit_modal_vender_select");
                if (selEl && selEl.options[selEl.selectedIndex]) {
                    var vName = selEl.options[selEl.selectedIndex].text;
                    var formNode = document.getElementById("editTransForm");
                    var descInp = null;
                    if (formNode) {
                        descInp = formNode.querySelector("input[name=\'description\']");
                    }
                    if (!descInp) {
                         descInp = document.querySelector("#catModalBody input[name=\'description\']");
                    }
                    if (!descInp) {
                         descInp = document.querySelector("input[name=\'description\']");
                    }
                    if (descInp && vName && vName !== "Select a vendor...") {
                        descInp.value = vName;
                        // Update character count if the counter script exists
                        var countEl = document.getElementById("transTitleCount");
                        if(countEl) countEl.textContent = "(" + vName.length + "/255)";
                    }
                }

                // Use data-catid — not vendor ID — for companyMethodData lookup
                setTimeout(function() {
                    var cId = getVendorCatId();
                    if (cId) rebuildMethodDropdown(cId, null);
                    else { var c = document.getElementById("transServicesContainer"); if(c) c.innerHTML = ""; }
                }, 10);
            };

            if(typeof SlimSelect !== "undefined"){
                try{
                    ssMethod = new SlimSelect({
                        select: "#editTransMethodSelect",
                        showSearch: false,
                        onChange: function(info){
                            var methodId = info && info.value ? info.value : (Array.isArray(info) && info[0] ? info[0].value : null);
                            if (!methodId) return;
                            var cId = getVendorCatId();
                            var comp = cId ? companyMethodData[String(cId)] : null;
                            if (comp && comp.methods) {
                                var found = comp.methods.find(function(m){ return String(m.id) === String(methodId); });
                                if (found) renderTransSvcTable(found.services);
                            }
                        }
                    });
                }catch(e){}
            }
            if(typeof Quill !== "undefined") {
                var q = new Quill("#transQuillEditor", {theme: "snow"});
                q.root.innerHTML = document.getElementById("transQuillHidden").value;
                q.on("text-change", function() {
                    document.getElementById("transQuillHidden").value = q.root.innerHTML;
                });
            }
            // On modal open: load services for pre-selected company + method
            var initCatId = getVendorCatId();
            var initMethodEl = document.getElementById("editTransMethodSelect");
            var initMethodId = initMethodEl ? initMethodEl.value : null;
            if (initCatId) {
                rebuildMethodDropdown(initCatId, initMethodId || null);
            }
        }, 200);
        </script>';

        return response()->json(['html' => $html]);
    }

    private function editActivityModal($service)
    {
        $flags = [['emoji' => '🇫🇷', 'code' => 'fr'], ['emoji' => '🇬🇧', 'code' => 'en'], ['emoji' => '🇮🇹', 'code' => 'it'], ['emoji' => '🇪🇸', 'code' => 'es'], ['emoji' => '🇩🇪', 'code' => 'de'], ['emoji' => '🇸🇪', 'code' => 'se'], ['emoji' => '🇳🇱', 'code' => 'nl']];
        $imgPath = $service->image ?? '';
        $desc = htmlspecialchars($service->description ?? '');
        $sid = $service->id;
        $arrival = htmlspecialchars($service->arrival ?? '');
        $notes = htmlspecialchars($service->notes ?? '');

        $html = '<script>var hd = document.getElementById("libModalHead") || document.getElementById("catModalHead"); if(hd){hd.innerHTML=\'<h3>Modify activity</h3><div style="display:flex;gap:10px;align-items:center"><a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a><button form="editActForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button></div>\';}</script>';
        $html .= '<form id="editActForm" onsubmit="submitEditActivity(' . $sid . '); return false;" enctype="multipart/form-data">' . csrf_field();

        // ------------------ Select Vendor Dropdown ------------------
        $html .= '<div style="display:flex;gap:16px;margin-bottom:16px;">';
        $html .= '<div style="flex:1;"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Select Vendor</label>';
        $html .= '<select name="vender" id="editActCategorySelect" onchange="if(typeof updateEditActivityServices===\'function\'){updateEditActivityServices(this.value);}" required style="width:100%;height:40px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px;outline:none;background:#fff">';
        $html .= '<option value="">Select a vendor...</option>';
        $html .= '</select></div></div>';
        $currentVender = intval($service->vender ?? 0);
        $html .= '<script>(function(){ var sel=document.getElementById("editActCategorySelect"); if(!sel||typeof actCategoriesData==="undefined") return; actCategoriesData.forEach(function(v){ var o=document.createElement("option"); o.value=v.id; o.textContent=v.name; if(String(v.id)==="' . $currentVender . '") o.selected=true; sel.appendChild(o); }); if(sel.value && typeof updateEditActivityServices==="function") updateEditActivityServices(sel.value); })();</script>';
        // -------------------------------------------------------------------------

        // Flags
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $a = ($f['code'] === 'en');
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . ($a ? '2px solid #ea580c' : '2px solid transparent') . ';background:' . ($a ? '#ea580c' : 'transparent') . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        $html .= '</div>';

        // Photos
        $existingImages = $this->decodeServiceImages($imgPath);
        $html .= '<div style="margin-bottom:16px;"><div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;"><span style="font-size:11px;font-weight:700;color:#555;">Photos:</span><a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a></div>';
        $html .= '<input type="file" name="new_images[]" id="editActImageInput" accept="image/*" multiple style="display:none" onchange="addActImages(this)">';
        $html .= '<div id="actPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach ($existingImages as $img) {
            $u = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $html .= '<div style="position:relative;flex-shrink:0;height:104px;"><img src="' . $u . '" style="height:100%;border-radius:4px;object-fit:cover;"><input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '"><button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button></div>';
        }
        $html .= '<div onclick="document.getElementById(\'editActImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">📷</div>';
        $html .= '</div></div>';

        // Activity name + Place of interest
        $html .= '<div style="display:flex;gap:16px;margin-bottom:4px;">';
        $html .= '<div style="flex:1;"><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;position:relative;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Activity name</legend><input type="text" id="editActNameInput" name="description" required maxlength="255" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $desc . '" oninput="document.getElementById(\'actNameCount\').textContent=\'(\'+this.value.length+\'/255)\'"><div id="actNameCount" style="position:absolute;right:4px;bottom:-16px;font-size:10px;color:#bbb;">(' . strlen($service->description ?? '') . '/255)</div></fieldset></div>';
        $html .= '<div style="flex:1;"><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;position:relative;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend><div style="display:flex;align-items:center;padding:0 12px;"><i class="fa fa-map-marker" style="color:#aaa;margin-right:8px;"></i><input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;font-size:13px;background:transparent;" placeholder="Add a destination" value="' . $arrival . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)"></div><div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div></fieldset></div>';
        $html .= '</div>';

        // Description
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:16px 0 0 0;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend><textarea name="notes" style="width:100%;min-height:160px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description">' . $notes . '</textarea></fieldset>';
        $html .= '<input type="hidden" name="cost" value="' . $service->cost . '">';
        $html .= '</form>';

        $html .= '<div style="margin-top:24px;border-top:2px solid #eee;padding-top:16px;">';
        $html .= '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">';
        $html .= '<span style="font-size:10px;font-weight:800;color:#dc2626;letter-spacing:1px;">?? ACTIVITIES SERVICES LIST</span>';
        $html .= '<button type="button" onclick="toggleActSubAddForm()" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:6px 14px;font-size:11px;font-weight:700;cursor:pointer"><i class="fa fa-plus"></i> Add Service Row</button>';
        $html .= '</div>';

        $actCsrf = csrf_token();
        $html .= '<div id="actSubAddSvcForm" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        $html .= '<div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">';
        $html .= '<div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="newActDescEdit" placeholder="e.g. Local Guide" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="newActCostEdit" step="0.01" value="0.00" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div>';
        $html .= '<div style="display:flex;gap:6px;">';
        $html .= '<button type="button" onclick="quickAddActSubEdit(' . ($service->category ?? 0) . ',\'' . $actCsrf . '\', ' . ($service->country ?? 123) . ', ' . ($service->vender ?? 0) . ')" style="height:34px;background:#ea580c;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button>';
        $html .= '<button type="button" onclick="toggleActSubAddForm()" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button>';
        $html .= '</div></div></div>';
        $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        $html .= '<thead><tr style="border-bottom:1px solid #eee;">';
        $html .= '<th style="text-align:left;padding:8px 6px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">DESCRIPTION</th>';
        $html .= '<th style="text-align:left;padding:8px 6px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">COST</th>';
        $html .= '</tr></thead><tbody id="editActServicesTbody">';
        $html .= '</tbody></table></div>';
        $html .= '<script>setTimeout(function(){ if(typeof updateEditActivityServices === "function"){ var el = document.getElementById("editActCategorySelect"); if(el) updateEditActivityServices(el.value); } }, 100);</script>';

        return response()->json(['html' => $html]);
    }

    private function editGuideModal($service)
    {
        $flags = [
            ['emoji' => '🇫🇷', 'code' => 'fr'],
            ['emoji' => '🇬🇧', 'code' => 'en'],
            ['emoji' => '🇮🇹', 'code' => 'it'],
            ['emoji' => '🇪🇸', 'code' => 'es'],
            ['emoji' => '🇩🇪', 'code' => 'de'],
            ['emoji' => '🇸🇪', 'code' => 'se'],
            ['emoji' => '🇳🇱', 'code' => 'nl'],
        ];

        $sid = $service->id;
        $countryId = $service->country ?? 0;
        $desc = htmlspecialchars($service->description ?? '');
        $imgPath = $service->arrival_destination ?? '';
        $arrival = $service->arrival ?? '';
        $accType = $service->acc_type ?? ($service->transport_method ?? '');
        $accCat = $service->acc_category ?? ($service->departure_location ?? '');

        // Auto-detect Place from category chain (same as Transport Hotel)
        if (!$service->relationLoaded('serviceCategory')) {
            $service->load('serviceCategory.parent.parent.parent');
        }
        if (!$arrival && $service->serviceCategory) {
            $chain = [];
            $walker = $service->serviceCategory->parent ?? null;
            while ($walker) {
                $chain[] = $walker;
                $walker = $walker->parent ?? null;
            }
            if (isset($chain[0])) {
                $arrival = $chain[0]->name;
            }
        }

        if (!$service->relationLoaded('venderUser'))
            $service->load('venderUser');
        $vendorName = $service->venderUser
            ? (!empty($service->venderUser->company) ? strtoupper($service->venderUser->company) : strtoupper($service->venderUser->first_name . ' ' . $service->venderUser->last_name))
            : strtoupper($service->description ?? '');

        // Header
        $html = '<script>';
        $html .= 'var hd = document.getElementById("libModalHead") || document.getElementById("catModalHead"); if(hd){hd.innerHTML=\'';
        $html .= '<h3>Modify Guides</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="(typeof closeCatModal === \\\'function\\\' ? closeCatModal : closeModal)()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editGuideSecForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';}';
        $html .= '</script>';

        $html .= '<form id="editGuideSecForm" onsubmit="submitEditGuideSection(' . $sid . '); return false;" enctype="multipart/form-data">';
        $html .= csrf_field();

        // ------------------ Select Vendor Dropdown ------------------
        $venders = \App\Models\User::whereIn('user_group', ['vender', 'supplier'])->orderBy('first_name')->get();
        $html .= '<div style="display:flex;gap:16px;margin-bottom:16px;">';
        $html .= '<div style="flex:1;"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Select Vendor</label>';
        $html .= '<select name="vender" id="editGuideVenderSelect" onchange="if(typeof updateEditGuideServices===\'function\'){updateEditGuideServices(this.value);}" required style="width:100%;height:40px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px;outline:none;background:#fff">';
        $html .= '<option value="">Select a vendor...</option>';
        $currentGuideVender = intval($service->vender ?? 0);
        foreach ($venders as $v) {
            $vName = !empty($v->company) ? $v->company : trim($v->first_name . ' ' . ($v->last_name ?? ''));
            if (!$vName) $vName = $v->email;
            $selected = ($currentGuideVender === $v->id) ? ' selected' : '';
            $html .= '<option value="' . $v->id . '"' . $selected . '>' . htmlspecialchars($vName) . '</option>';
        }
        $html .= '</select></div>';
        $html .= '<div style="white-space:nowrap;padding-top:26px;"><strong>Vendor Price:</strong> <span style="color:#ea580c;font-weight:700;">' . number_format($service->cost ?? 0, 2) . ' JOD</span></div>';
        $html .= '</div>';
        $html .= '<script>(function(){ setTimeout(function(){ if(typeof SlimSelect !== "undefined") { new SlimSelect({ select: "#editGuideVenderSelect", onChange: function(info) { var val = info && info.value ? info.value : (Array.isArray(info)&&info[0]?info[0].value:""); if(val && typeof updateEditGuideServices==="function"){ updateEditGuideServices(val); } else { var tb = document.getElementById("editGuideListTbody"); if(tb) tb.innerHTML = ""; } } }); } var sel=document.getElementById("editGuideVenderSelect"); if(sel && sel.value && typeof updateEditGuideServices==="function") updateEditGuideServices(sel.value); }, 200); })();</script>';
        // -------------------------------------------------------------------------

        // Flags
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        $html .= '</div>';

        // Photos section
        $existingImages = [];
        if ($imgPath) {
            $d = @json_decode($imgPath, true);
            $existingImages = is_array($d) ? $d : [$imgPath];
        }
        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editGuideSecImageInput" accept="image/*" multiple style="display:none" onchange="addGuideSecImages(this)">';
        $html .= '<div id="guideSecPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach ($existingImages as $img) {
            $imgUrl = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $html .= '<div style="position:relative;flex-shrink:0;height:104px;">';
            $html .= '<img src="' . $imgUrl . '" style="height:100%;border-radius:4px;object-fit:cover;">';
            $html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '">';
            $html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            $html .= '</div>';
        }
        $html .= '<div onclick="document.getElementById(\'editGuideSecImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">+</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Two-column layout
        $html .= '<div style="display:flex;gap:16px;margin-bottom:16px;">';

        // LEFT: Guide Name + Description
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Name of Guide</legend>';
        $html .= '<input type="text" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $desc . '">';
        $html .= '<div style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(' . strlen($service->description ?? '') . '/255)</div>';
        $html .= '</fieldset>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        $html .= '<div id="svcQuillEditor" style="min-height:140px;background:#fff;font-size:13px;line-height:1.6;"></div>';
        $html .= '<textarea name="notes" id="svcQuillHidden" style="display:none">' . htmlspecialchars($service->notes ?? '') . '</textarea>';
        $html .= '</fieldset>';
        $html .= '</div>';

        // RIGHT: Place + Guide Type + Category + Website
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of Interest</legend>';
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="e.g. Petra, Wadi Rum" value="' . htmlspecialchars($arrival) . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        $guideTypes = ['Day Guide', 'Half Day Guide', 'Full Day Guide', 'Multi-Day Guide', 'City Tour Guide', 'Driver Guide', 'Local Guide'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Guide Type</legend>';
        $html .= '<select name="acc_type" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select guide type</option>';
        foreach ($guideTypes as $t) {
            $sel = ($accType === $t) ? ' selected' : '';
            $html .= '<option value="' . $t . '"' . $sel . '>' . $t . '</option>';
        }
        $html .= '</select></fieldset>';

        $guideCats = ['Licensed', 'Local', 'Expert', 'Senior', 'Specialist', 'General'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Guide Category</legend>';
        $html .= '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a category</option>';
        foreach ($guideCats as $c) {
            $sel = ($accCat === $c) ? ' selected' : '';
            $html .= '<option value="' . $c . '"' . $sel . '>' . $c . '</option>';
        }
        $html .= '</select></fieldset>';

        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Website</legend>';
        $html .= '<input type="text" name="website" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . htmlspecialchars($service->website ?? '') . '">';
        $html .= '</fieldset>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<input type="hidden" name="cost" value="' . ($service->cost ?? 0) . '">';
        $html .= '<input type="hidden" name="category" value="' . $service->category . '">';
        $html .= '</form>';

        // GUIDES LIST
        $guideQuery = Service::where('category', $service->category)->with('venderUser')->orderBy('description');
        if ($service->vender) {
            $guideQuery->where('vender', $service->vender);
        }
        $guideItems = $guideQuery->get();

        $guidecsrf = csrf_token();
        $html .= '<div style="margin-top:20px;">';
        $html .= '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">';
        $html .= '<span style="color:#ea580c;font-size:11px;font-weight:800;letter-spacing:1px;">🧭 GUIDES LIST</span>';
        $html .= '<button type="button" onclick="toggleGuideAddForm()" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Guide</button>';
        $html .= '</div>';
        // Inline Add Guide Form (hidden by default)
        $html .= '<div id="guideAddSvcForm" style="display:none;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:12px;">';
        $html .= '<div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">';
        $html .= '<div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label>';
        $html .= '<input type="text" id="newGuideDesc" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;" placeholder="e.g. Amman/Petra"></div>';
        $html .= '<div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label>';
        $html .= '<input type="number" id="newGuideCost" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;" placeholder="0.00" step="0.01" value="0.00"></div>';
        $html .= '<div style="flex:1;min-width:120px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Guide Type</label>';
        $html .= '<select id="newGuideType" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 8px;font-size:12px;background:#fff;color:#555;">';
        $html .= '<option value="">-- Type --</option>';
        foreach (['Day Guide', 'Half Day Guide', 'Full Day Guide', 'Multi-Day Guide', 'City Tour Guide', 'Driver Guide', 'Local Guide'] as $gt) {
            $html .= '<option value="' . $gt . '">' . $gt . '</option>';
        }
        $html .= '</select></div>';
        $html .= '<div style="flex:1;min-width:120px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Guide Category</label>';
        $html .= '<select id="newGuideCat" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 8px;font-size:12px;background:#fff;color:#555;">';
        $html .= '<option value="">-- Category --</option>';
        foreach (['Licensed', 'Local', 'Expert', 'Senior', 'Specialist', 'General'] as $gc) {
            $html .= '<option value="' . $gc . '">' . $gc . '</option>';
        }
        $html .= '</select></div>';
        $html .= '<div style="display:flex;gap:6px;padding-bottom:0;">';
        $html .= '<button type="button" onclick="quickAddGuide(' . $sid . ',' . ($service->vender ?? 'null') . ',' . ($service->category ?? 'null') . ',' . $countryId . ',\'' . $guidecsrf . '\')" style="height:36px;background:#7c3aed;border:none;color:#fff;border-radius:6px;padding:0 16px;font-size:12px;font-weight:700;cursor:pointer;">Save</button>';
        $html .= '<button type="button" onclick="toggleGuideAddForm()" style="height:36px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button>';
        $html .= '</div></div></div>';
        $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        $html .= '<thead><tr style="border-bottom:1px solid #e2e8f0;">';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">DESCRIPTION</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">COST</th>';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">VENDOR</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">ACTIONS</th>';
        $html .= '</tr></thead><tbody id="editGuideListTbody">';
        foreach ($guideItems as $g) {
            $html .= '<tr id="guideRow_' . $g->id . '" style="border-bottom:1px solid #f7fafc;">';
            $html .= '<td style="padding:7px 8px;"><span id="guideDesc_' . $g->id . '">' . htmlspecialchars($g->description ?? '-') . '</span></td>';
            $html .= '<td style="padding:7px 8px;text-align:right;color:#ea580c;font-weight:700;"><span id="guideCost_' . $g->id . '">' . number_format($g->cost ?? 0, 2) . '</span> JOD</td>';
            $html .= '<td style="padding:7px 8px;">' . htmlspecialchars($vendorName) . '</td>';
            $html .= '<td style="padding:7px 8px;text-align:right;white-space:nowrap;">';
            $html .= '<button type="button" onclick="editGuideRow(' . $g->id . ',\'' . addslashes(htmlspecialchars($g->description ?? '')) . '\',' . ($g->cost ?? 0) . ')" style="background:#f0f4ff;border:none;color:#ea580c;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;margin-right:4px;"><i class="fa fa-pencil"></i></button>';
            $html .= '<button type="button" onclick="deleteGuideRow(' . $g->id . ')" style="background:#fff5f5;border:none;color:#e53e3e;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;"><i class="fa fa-trash"></i></button>';
            $html .= '</td></tr>';
        }
        if ($guideItems->isEmpty()) {
            $html .= '<tr><td colspan="4" style="padding:16px;text-align:center;color:#a0aec0;font-size:12px;">No guides found.</td></tr>';
        }
        $html .= '</tbody></table></div>';

        $csrf = csrf_token();
        $html .= '<script>
function addGuideSecImages(input){var row=document.getElementById("guideSecPhotosRow");Array.from(input.files).forEach(function(f){var url=URL.createObjectURL(f);var w=document.createElement("div");w.style.cssText="position:relative;flex-shrink:0;height:104px;";w.innerHTML=\'<img src="\'+url+\'" style="height:100%;border-radius:4px;object-fit:cover;"><button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;">✕</button>\';row.insertBefore(w,row.lastElementChild);});}
function submitEditGuideSection(id){
    var form=document.getElementById("editGuideSecForm");
    var fd=new FormData(form);
    fd.append("_method","PUT");fd.append("service_type","guide");
    $.ajax({url:"/admin/services/"+id,type:"POST",data:fd,processData:false,contentType:false,
        success:function(){closeModal();showToast("Guide saved!","success");setTimeout(function(){window.location.reload();},800);},
        error:function(){showToast("Error saving guide","error");}
    });
}
function toggleGuideAddForm(){var f=document.getElementById("guideAddSvcForm");f.style.display=(f.style.display==="none"?"": "none");}

// Re-render the Guides List for the selected vendor from the page-level
// guideCategoriesData (mirrors updateEditActivityServices for Activities).
function updateEditGuideServices(vendorId){
    var tbody = document.getElementById("editGuideListTbody");
    if (!tbody) return;
    tbody.innerHTML = "";
    if (!vendorId || typeof guideCategoriesData === "undefined") return;
    var found = null;
    guideCategoriesData.forEach(function(v){ if(String(v.id) === String(vendorId)) found = v; });
    if (!found || !found.services || found.services.length === 0) {
        tbody.innerHTML = "<tr><td colspan=4 style=padding:16px;text-align:center;color:#a0aec0;font-size:12px;>No guides found.</td></tr>";
        return;
    }
    var vName = (found.name || "").toUpperCase();
    var rowsHtml = "";
    found.services.forEach(function(g){
        var descEsc = String(g.description || "-").replace(/</g,"&lt;").replace(/>/g,"&gt;");
        var descAttr = String(g.description || "").replace(/\'/g,"\\\\\'").replace(/\"/g,"&quot;");
        rowsHtml += "<tr id=guideRow_" + g.id + " style=border-bottom:1px solid #f7fafc;>" +
            "<td style=padding:7px 8px;><span id=guideDesc_" + g.id + ">" + descEsc + "</span></td>" +
            "<td style=padding:7px 8px;text-align:right;color:#ea580c;font-weight:700;><span id=guideCost_" + g.id + ">" + parseFloat(g.cost || 0).toFixed(2) + "</span> JOD</td>" +
            "<td style=padding:7px 8px;>" + vName + "</td>" +
            "<td style=padding:7px 8px;text-align:right;white-space:nowrap;>" +
            "<button type=button onclick=\"editGuideRow(" + g.id + ",\'" + descAttr + "\'," + (g.cost || 0) + ")\" style=background:#f0f4ff;border:none;color:#ea580c;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;margin-right:4px;><i class=fa fa-pencil></i></button>" +
            "<button type=button onclick=\"deleteGuideRow(" + g.id + ")\" style=background:#fff5f5;border:none;color:#e53e3e;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;><i class=fa fa-trash></i></button>" +
            "</td></tr>";
    });
    tbody.innerHTML = rowsHtml;
}

function quickAddGuide(sid,vender,category,country,token){
    var sel = document.getElementById("editGuideVenderSelect");
    var selectedVendor = sel && sel.value ? sel.value : vender;
    var desc=document.getElementById("newGuideDesc").value.trim();
    var cost=document.getElementById("newGuideCost").value||0;
    var gtype=document.getElementById("newGuideType").value;
    var gcat=document.getElementById("newGuideCat").value;
    if(!desc){alert("Please enter a description.");return;}

    // Reuse an existing sibling service\'s category for this vendor (guaranteed
    // valid), falling back to this guide\'s own category if the vendor has none yet.
    var targetCategory = category;
    var found = null;
    if (typeof guideCategoriesData !== "undefined") {
        guideCategoriesData.forEach(function(v){ if(String(v.id) === String(selectedVendor)) found = v; });
        if (found && found.services && found.services.length > 0 && found.services[0].category) {
            targetCategory = found.services[0].category;
        }
    }

    $.ajax({url:"/admin/guides/quick-add",type:"POST",
        data:{_token:token,description:desc,cost:cost,vender:selectedVendor,category:targetCategory,country:country,acc_type:gtype,acc_category:gcat},
        success:function(r){if(r.success){
            if (!found) {
                var selEl = document.getElementById("editGuideVenderSelect");
                var vName = selEl && selEl.options[selEl.selectedIndex] ? selEl.options[selEl.selectedIndex].text : "No Vendor";
                found = { id: selectedVendor, name: vName, services: [] };
                if (typeof guideCategoriesData !== "undefined") guideCategoriesData.push(found);
            }
            if(!found.services) found.services = [];
            found.services.unshift({ id: r.id || ("temp_" + Date.now()), description: desc, cost: parseFloat(cost) || 0, category: targetCategory, vender: selectedVendor });
            document.getElementById("newGuideDesc").value="";document.getElementById("newGuideCost").value="0.00";document.getElementById("newGuideType").value="";document.getElementById("newGuideCat").value="";toggleGuideAddForm();
            if (typeof updateEditGuideServices === "function") updateEditGuideServices(selectedVendor);
            showToast("Guide added!","success");
        }},
        error:function(){showToast("Error adding guide","error");}
    });
}
function editGuideRow(id,desc,cost){
    var old=document.getElementById("guideEditForm_"+id);if(old){old.remove();return;}
    var row=document.getElementById("guideRow_"+id);
    var editRow=document.createElement("tr");editRow.id="guideEditForm_"+id;
    editRow.innerHTML=\'<td colspan="4" style="padding:10px 8px;background:#f8fafc;"><div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;"><div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="editGuideDesc_\'+id+\'" value="\'+desc+\'" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="editGuideCost_\'+id+\'" value="\'+cost+\'" step="0.01" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="display:flex;gap:6px;"><button type="button" onclick="saveEditGuide(\'+id+\')" style="height:34px;background:#ea580c;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button><button type="button" onclick="cancelEditGuide(\'+id+\')" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button></div></div></td>\';
    row.parentNode.insertBefore(editRow,row.nextSibling);
}
function saveEditGuide(id){
    var newDesc=document.getElementById("editGuideDesc_"+id).value.trim();
    var newCost=document.getElementById("editGuideCost_"+id).value;
    if(!newDesc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $guidecsrf . '",_method:"PUT",description:newDesc,cost:newCost,service_type:"guide"},
        success:function(){document.getElementById("guideDesc_"+id).textContent=newDesc;document.getElementById("guideCost_"+id).textContent=parseFloat(newCost||0).toFixed(2);cancelEditGuide(id);showToast("Guide updated!","success");},
        error:function(){showToast("Error updating guide","error");}
    });
}
function cancelEditGuide(id){var f=document.getElementById("guideEditForm_"+id);if(f)f.remove();}
function deleteGuideRow(id){
    if(!confirm("Delete this guide?"))return;
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $guidecsrf . '",_method:"DELETE",service_type:"guide"},
        success:function(){var r=document.getElementById("guideRow_"+id);if(r)r.remove();showToast("Guide deleted!","success");},
        error:function(){showToast("Error deleting guide","error");}
    });
}
function _initSvcQuill(){
    if(typeof Quill==="undefined"){setTimeout(_initSvcQuill,200);return;}
    var el=document.getElementById("svcQuillEditor");if(!el||el.dataset.init)return;el.dataset.init="1";
    var q=new Quill(el,{theme:"snow",modules:{toolbar:[["bold","italic","underline"],[{list:"ordered"},{list:"bullet"}],["link"],["clean"]]}});
    var h=document.getElementById("svcQuillHidden");
    if(h&&h.value)q.root.innerHTML=h.value;
    q.on("text-change",function(){if(h)h.value=q.root.innerHTML;});
    window._svcQuill=q;
}
if(!document.getElementById("quill-css")){var l=document.createElement("link");l.id="quill-css";l.rel="stylesheet";l.href="https://cdn.quilljs.com/1.3.7/quill.snow.css";document.head.appendChild(l);}
if(!window.Quill&&!document.getElementById("quill-js")){var s=document.createElement("script");s.id="quill-js";s.src="https://cdn.quilljs.com/1.3.7/quill.min.js";s.onload=function(){_initSvcQuill();};document.head.appendChild(s);}else{_initSvcQuill();}
</script>';

        return response()->json(['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        // Check if this is an activity record
        if ($request->input('service_type') === 'activity') {
            $service = Activity::find($id);
        }
        // Check if this is an accommodation record
        if (empty($service) && $request->input('service_type') === 'accommodation') {
            $service = Accommodation::find($id);
        }
        // Check if this is a transport record
        if (empty($service) && $request->input('service_type') === 'transport') {
            $service = \App\Models\Transport::find($id);
        }
        // Check if this is a restaurant record
        if (empty($service) && $request->input('service_type') === 'restaurant') {
            $service = \App\Models\Restaurant::find($id);
        }
        if (empty($service)) {
            $service = Service::findOrFail($id);
        }

        $data = [];
        if ($request->input('service_type') === 'accommodation') {
            // en33_accommodations uses 'descriptionL' column
            $data['descriptionL'] = $request->input('description', $service->description);
        } else {
            $data['description'] = $request->input('description', $service->description);
        }
        $data['cost'] = $request->input('cost', $service->cost) ?? 0;
        $data['category'] = $request->input('category', $service->category);
        $data['restricted'] = $request->input('restricted', 0);
        if ($request->has('vender')) {
            $data['vender'] = $request->input('vender') ?: 0;
        }
        
        $isGuide = ($request->input('service_type') === 'guide');

        if ($request->has('notes') && !$isGuide) {
            $data['notes'] = $request->input('notes');
        }
        if ($request->has('acc_type') && !$isGuide) {
            $data['acc_type'] = $request->input('acc_type');
        }
        if ($request->has('acc_category') && !$isGuide) {
            $data['acc_category'] = $request->input('acc_category');
        }
        if ($request->has('website') && !$isGuide) {
            $data['website'] = $request->input('website');
        }
        if ($request->has('arrival') && !$isGuide) {
            $data['arrival'] = $request->input('arrival');
        }
        
        // Map non-existing columns to unused existing columns for Guide
        if ($isGuide) {
            if ($request->has('acc_type')) {
                $data['transport_method'] = $request->input('acc_type');
            }
            if ($request->has('acc_category')) {
                $data['departure_location'] = $request->input('acc_category');
            }
        }
        if (!$isGuide && $request->has('transport_method')) {
            $data['transport_method'] = $request->input('transport_method');
        }
        if (!$isGuide && $request->has('departure_location')) {
            $data['departure_location'] = $request->input('departure_location');
        }
        if ($request->has('arrival_destination')) {
            $data['arrival_destination'] = $request->input('arrival_destination');
        }
        if ($request->has('length_time')) {
            $data['length_time'] = $request->input('length_time');
        }
        if ($request->has('distance_km')) {
            $data['distance_km'] = $request->input('distance_km');
        }

        // Handle multi-image: keep existing + add new
        if ($request->has('existing_images') || $request->hasFile('new_images')) {
            $allImages = $request->input('existing_images', []);
            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $file) {
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                    $file->move(public_path('uploads/services'), $filename);
                    $allImages[] = 'uploads/services/' . $filename;
                }
            }
            $data['image'] = json_encode(array_values($allImages));
        } elseif ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/services'), $filename);
            $data['image'] = 'uploads/services/' . $filename;
        }

        if ($isGuide && isset($data['image'])) {
            $data['arrival_destination'] = $data['image'];
            unset($data['image']);
        }

        $service->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('admin.services.edit', $id)->with('success', 'Service updated');
    }

    public function destroy(Request $request, $id)
    {
        $type = $request->input('service_type', '');

        if ($type === 'activity') {
            $service = Activity::find($id);
            if (!$service) {
                return $request->ajax()
                    ? response()->json(['message' => 'Activity not found'], 404)
                    : abort(404);
            }
        } elseif ($type === 'accommodation') {
            $service = Accommodation::find($id);
            if (!$service) {
                return $request->ajax()
                    ? response()->json(['message' => 'Accommodation not found'], 404)
                    : abort(404);
            }
        } elseif ($type === 'transport') {
            $service = \App\Models\Transport::find($id);
            if (!$service) {
                return $request->ajax()
                    ? response()->json(['message' => 'Transport not found'], 404)
                    : abort(404);
            }
        } elseif ($type === 'restaurant') {
            $service = \App\Models\Restaurant::find($id);
            if (!$service) {
                return $request->ajax()
                    ? response()->json(['message' => 'Restaurant not found'], 404)
                    : abort(404);
            }
        } else {
            $service = Service::findOrFail($id);
        }

        $country = $service->country ?? null;
        $category = $service->category ?? null;
        $service->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('admin.services.index', ['country' => $country, 'category' => $category])->with('success', 'Service deleted');
    }

    public function show($id)
    {
        return $this->edit(request(), $id);
    }

    public function venders(Request $request)
    {
        $venders = User::whereIn('user_group', ['vender', 'supplier'])
            ->when($request->filled('country'), function ($q) use ($request) {
                return $q->where('country', $request->country);
            })
            ->when($request->filled('email'), function ($q) use ($request) {
                return $q->where('email', 'like', '%' . $request->email . '%');
            })
            ->when($request->filled('first_name'), function ($q) use ($request) {
                return $q->where('first_name', 'like', '%' . $request->first_name . '%');
            })
            ->when($request->filled('last_name'), function ($q) use ($request) {
                return $q->where('last_name', 'like', '%' . $request->last_name . '%');
            })
            ->when($request->filled('company'), function ($q) use ($request) {
                return $q->where('company', 'like', '%' . $request->company . '%');
            })
            ->with(['venderDetail', 'venderBalance'])
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        $countries = Country::orderBy('name')->get();

        return view('admin.services.venders', compact('venders', 'countries'));
    }

    public function settings()
    {
        // 1. Get User Groups
        $ugPath = base_path('../pvt.jo/config/users/user_groups.php');
        $userGroups = [];
        if (file_exists($ugPath)) {
            $content = file_get_contents($ugPath);
            if (preg_match_all("/\$GOGIES\['user_groups'\]\['([^']+)'\]/", $content, $matches)) {
                $userGroups = $matches[1];
            }
        }

        // 2. Get Service Settings (Countries and Venders Group)
        $settPath = base_path('../pvt.jo/config/services/settings.php');
        $vendersGroup = 'supplier'; // default
        $selectedCountries = [];
        if (file_exists($settPath)) {
            $content = file_get_contents($settPath);
            if (preg_match("/\[ 'venders_group' \]\s*=\s*'([^']+)'/", $content, $m) || preg_match("/\['venders_group'\]='([^']+)'/", $content, $m)) {
                $vendersGroup = $m[1];
            }
            if (preg_match_all("/\['countries'\]\[\s*(\d+)\s*\]/", $content, $m)) {
                $selectedCountries = array_map('intval', $m[1]);
            }
        }

        // 3. Get Email Template
        $mailPath = base_path('../pvt.jo/admin/services/service_mail.php');
        $emailTemplate = '';
        if (file_exists($mailPath)) {
            $emailTemplate = file_get_contents($mailPath);
        }

        // 4. Get All Countries
        $countries = \App\Models\Country::where('lang', 'en')->orderBy('name')->get();

        return view('admin.services.settings', compact('userGroups', 'vendersGroup', 'selectedCountries', 'emailTemplate', 'countries'));
    }

    public function updateSettings(Request $request)
    {
        $vendersGroup = $request->input('venders_group', 'supplier');
        $emailTemplate = $request->input('email_template', '');
        $countries = $request->input('countries', []);

        // 1. Save Settings PHP file
        $settPath = base_path('../pvt.jo/config/services/settings.php');
        $data = "<?php if (!defined('gogies')){ exit;} \$GOGIES['services']['countries']=[];\$GOGIES['services']['venders_group']='" . $vendersGroup . "'; ";
        foreach ($countries as $cid) {
            $data .= "\$GOGIES['services']['countries'][" . intval($cid) . "] =\$GOGIES['countries'][ " . intval($cid) . "]; ";
        }
        $data .= "?>";
        file_put_contents($settPath, $data);

        // 2. Save Email Template
        $mailPath = base_path('../pvt.jo/admin/services/service_mail.php');
        file_put_contents($mailPath, $emailTemplate);

        return redirect()->route('admin.services.settings')->with('success', 'Settings updated successfully');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'categ_name' => 'required|string|max:255',
            'category_parent' => 'required|integer',
            'country_id' => 'required|integer',
        ]);

        ServiceCategory::create([
            'name' => $request->input('categ_name'),
            'parent_id' => intval($request->input('category_parent')),
            'country_id' => intval($request->input('country_id')),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Return the Add Category modal content
     */
    public function addCategoryModal(Request $request)
    {
        $countryId = intval($request->input('country', 0));
        $country = Country::find($countryId);
        $countryName = $country ? $country->name : '';

        // Build category tree
        $allCategories = ServiceCategory::where('country_id', $countryId)
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        $categoriesArray = [];
        foreach ($allCategories as $cat) {
            $categoriesArray[$cat->id] = [
                'name' => $cat->name,
                'parent_id' => $cat->parent_id,
            ];
        }

        $catTree = $this->buildRadioTree($categoriesArray, 0, 'category_parent');

        $html = '<h3><i class="fa-plus-circle"></i> ' . ($countryName ? $countryName . ' -> ' : '') . ' Add -> category</h3>';
        $html .= '<form id="add_category_form" onsubmit="categorySubmit(); return false;">';
        $html .= csrf_field();
        $html .= '<input type="hidden" name="country_id" value="' . $countryId . '">';

        // Name
        $html .= '<div class="sd-12 h-pad pad-b"><label>Name</label>';
        $html .= '<input type="text" name="categ_name" id="categ_name" class="sd-12" required></div>';

        // Parent tree
        $html .= '<div class="sd-12 h-pad text-capitalize">Parent</div>';
        $html .= '<div class="sd-12">';
        $html .= '<div class="bordered" style="overflow-x:auto; max-width:100%; max-height:300px; overflow-y:auto;">';
        $html .= '<div class="grey bordered-b sd-12 h-pad">';
        $html .= '<div class="pull-left"><input type="text" placeholder="Search..." class="pull-left btn" id="search_cat_parents" autocomplete="off" /></div>';
        $html .= '<div id="cat_sidetree" class="pull-right h-pad-t">';
        $html .= '<a class="grey h-pad-r" href="javascript:void(0);" title="Collapse" onclick="$(\'#cat_parent_tree\').find(\'ul\').hide();"><i class="fa-minus-circle medium"></i></a>';
        $html .= '<a class="grey h-pad-r" id="cat-open-all" href="javascript:void(0);" title="Expand All" onclick="$(\'#cat_parent_tree\').find(\'ul\').show();"><i class="fa-plus-circle medium"></i></a>';
        $html .= '</div></div>';
        $html .= '<ul id="cat_parent_tree">';
        $html .= '<li><label><input type="radio" name="category_parent" value="0" checked> Root (No parent)</label></li>';
        $html .= $catTree;
        $html .= '</ul></div></div>';

        // Submit
        $html .= '<div class="d-pad align-center sd-12">';
        $html .= '<button type="submit" class="btn blue"><i class="fa-check"></i> Save</button>';
        $html .= '</div></form>';

        // JS for tree and search
        $html .= '<script>';
        $html .= 'if (typeof $.fn.treeview !== "undefined") { $("#cat_parent_tree").treeview({ collapsed: true, animated: "medium", control: "#cat_sidetree", persist: "location" }); }';
        $html .= '$("#search_cat_parents").keyup(function() {';
        $html .= '  var t = $(this).val().toLowerCase();';
        $html .= '  if (t === "") { $("#cat_parent_tree").find("ul").hide(); } else { $("#cat_parent_tree").find("ul").show(); }';
        $html .= '  $("#cat_parent_tree li").each(function() {';
        $html .= '    var txt = $(this).children("label").text().toLowerCase();';
        $html .= '    if (txt.indexOf(t) !== -1) { $(this).removeClass("hide"); } else { $(this).addClass("hide"); }';
        $html .= '  });';
        $html .= '});';
        $html .= '</script>';

        return response()->json(['html' => $html]);
    }

    /**
     * AJAX: Return the Edit Category modal content
     */
    public function editCategoryModal(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);
        $countryId = $category->country_id;

        $flags = [
            ['emoji' => '🇫🇷', 'code' => 'fr'],
            ['emoji' => '🇬🇧', 'code' => 'en'],
            ['emoji' => '🇮🇹', 'code' => 'it'],
            ['emoji' => '🇪🇸', 'code' => 'es'],
            ['emoji' => '🇩🇪', 'code' => 'de'],
            ['emoji' => '🇸🇪', 'code' => 'se'],
            ['emoji' => '🇳🇱', 'code' => 'nl'],
        ];

        $html = '<script>';
        $html .= 'var mb = document.querySelector("#libModal .lib-modal-box"); if(mb) { mb.style.maxWidth = "960px"; mb.style.transition = "max-width 0.3s ease"; }';
        $html .= 'document.getElementById("libModalHead").innerHTML=\'';
        $html .= '<h3>Modify accommodation</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="edit_category_form" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';';
        $html .= '</script>';

        $html .= '<form id="edit_category_form" onsubmit="event.preventDefault(); if(document.getElementById(\'hotelSelect\') && document.getElementById(\'hotelSelect\').value !== \'\') { document.getElementById(\'hidden_parent\').value = document.getElementById(\'hotelSelect\').value; } var fd = new FormData(this); $.ajax({ url: \'/admin/services-category/' . $id . '/update\', type: \'POST\', data: fd, processData: false, contentType: false, success: function(r){ if(typeof closeModal === \'function\') { closeModal(); loadLib(); } else { location.reload(); } }, error: function(){ alert(\'Error updating\'); } }); return false;" enctype="multipart/form-data">';
        $html .= csrf_field();
        $html .= '<input type="hidden" name="country_id" value="' . $countryId . '">';
        $html .= '<input type="hidden" name="category_parent" id="hidden_parent" value="' . $category->parent_id . '">';

        // Fetch Vendor logic
        $vendorName = 'N/A';
        $vendorPrice = '0.00 JOD';

        if (!empty($category->name)) {
            $vendorMatch = \App\Models\User::whereIn('user_group', ['vender', 'supplier'])
                ->where('company', 'like', '%' . trim($category->name) . '%')
                ->first();

            if ($vendorMatch) {
                $vendorName = $vendorMatch->company ?: ($vendorMatch->first_name . ' ' . $vendorMatch->last_name);
                $balanceRow = \Illuminate\Support\Facades\DB::table('en33_services_vender_balance')
                    ->where('vender_id', $vendorMatch->id)
                    ->first();
                if ($balanceRow) {
                    $vendorPrice = number_format((float) $balanceRow->balance, 2) . ' JOD';
                }
            }
        }

        // Language flags & Vendor Details row
        $html .= '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:22px;">';

        $html .= '<div style="display:flex;gap:8px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        $html .= '</div>';

        // Horizontal Vendor Details
        $html .= '<div style="display:flex; gap:20px; align-items:center; background:#f8f9fa; border:1px solid #ddd; border-radius:8px; padding:14px 28px;">';
        $html .= '<div><span style="font-size:11px; color:#555;">Vendor Name:</span> <strong style="font-size:13px; color:#2c3e50; margin-left:6px;">' . htmlspecialchars($vendorName) . '</strong></div>';
        $html .= '<div style="width:1px; height:20px; background:#ddd;"></div>';
        $html .= '<div><span style="font-size:11px; color:#555;">Vendor Price:</span> <strong style="font-size:13px; color:#2ecc71; margin-left:6px;">' . $vendorPrice . '</strong></div>';
        $html .= '</div>';

        $html .= '</div>';

        // Photos section
        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editCatImageInput" accept="image/*" multiple style="display:none" onchange="addAccImages(this)">';
        $html .= '<div id="catPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';

        // Display existing images if any
        $existingImages = [];
        if (!empty($category->image)) {
            $decoded = @json_decode($category->image, true);
            if (is_array($decoded)) {
                $existingImages = $decoded;
            } else {
                $existingImages = [$category->image];
            }
        }

        foreach ($existingImages as $img) {
            $imgUrl = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $html .= '<div class="acc-photo-wrap" style="position:relative;flex-shrink:0;height:104px;">';
            $html .= '<img src="' . $imgUrl . '" style="height:100%;border-radius:4px;object-fit:cover;">';
            $html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '">';
            $html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            $html .= '</div>';
        }

        $html .= '<div onclick="document.getElementById(\'editCatImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">+</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Two-column layout
        $html .= '<div style="display:flex;gap:16px;margin-bottom:16px;">';

        // LEFT column
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;width:auto;border-bottom:none;margin-bottom:0;line-height:1;">Name Of Accommodation</legend>';
        $html .= '<input type="text" name="categ_name" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . htmlspecialchars($category->name) . '">';
        $html .= '<div style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(' . strlen($category->name) . '/255)</div>';
        $html .= '</fieldset>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;width:auto;border-bottom:none;margin-bottom:0;line-height:1;">Description</legend>';
        $html .= '<textarea name="notes" style="width:100%;min-height:160px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description">' . htmlspecialchars((string) ($category->description ?? '')) . '</textarea>';
        $html .= '</fieldset>';

        $html .= '</div>';

        // RIGHT column
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;width:auto;border-bottom:none;margin-bottom:0;line-height:1;">Place Of Interest</legend>';
        $arrivalValue = $category->arrival ?: ($category->parent ? $category->parent->name : '');
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="' . htmlspecialchars((string) ($arrivalValue)) . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;width:auto;border-bottom:none;margin-bottom:0;line-height:1;">Accommodation Type</legend>';

        $accType = 'Hotel';
        if ($category->parent && $category->parent->parent && $category->parent->parent->parent) {
            $rootName = $category->parent->parent->parent->name;
            if (stripos($rootName, 'camp') !== false)
                $accType = 'Camp';
            elseif (stripos($rootName, 'homestay') !== false)
                $accType = 'Homestay';
        }
        $types = ['Hotel', 'Camp', 'Homestay', 'Mobile Camp', 'Wild Jordan RSCN'];
        $html .= '<select name="acc_type" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        foreach ($types as $t) {
            $selected = ($t === $accType) ? 'selected' : '';
            $html .= '<option value="' . $t . '" ' . $selected . '>' . $t . '</option>';
        }
        $html .= '</select></fieldset>';

        $starRating = '';
        if ($category->parent && $category->parent->parent) {
            $gpName = $category->parent->parent->name;
            if (preg_match('/^(\d)\s*(★|Star)/i', $gpName, $m)) {
                $starRating = $m[1] . ' ';
                for ($i = 0; $i < $m[1]; $i++)
                    $starRating .= '★';
            }
        }
        $cats = ['1 ★', '2 ★★', '3 ★★★', '4 ★★★★', '5 ★★★★★'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;width:auto;border-bottom:none;margin-bottom:0;line-height:1;">Category</legend>';
        $html .= '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a category</option>';
        foreach ($cats as $c) {
            $selected = ($c === $starRating) ? 'selected' : '';
            $html .= '<option value="' . $c . '" ' . $selected . '>' . $c . '</option>';
        }
        $html .= '</select></fieldset>';

        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;width:auto;border-bottom:none;margin-bottom:0;line-height:1;">Website</legend>';
        $html .= '<input type="text" name="website" placeholder="e.g. https://www.example.com" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . htmlspecialchars((string) ($category->website ?? '')) . '">';
        $html .= '</fieldset>';

        $html .= '</div>';
        $html .= '</div>';

        // Add services table for this category
        $html .= '<div id="vendorServicesTableWrap">';
        $resp = $this->getVendorServicesTable($category->id, new \Illuminate\Http\Request());
        $respData = json_decode($resp->getContent(), true);
        if ($respData && isset($respData['html'])) {
            $html .= $respData['html'];
        }
        $html .= '</div>';

        $html .= '</form>';

        return response()->json(['html' => $html]);
    }

    /**
     * AJAX: Update a category
     */
    public function updateCategory(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);

        $existingImages = [];
        if (!empty($category->image)) {
            $decoded = @json_decode($category->image, true);
            if (is_array($decoded)) {
                $existingImages = $decoded;
            } else {
                $existingImages = [$category->image];
            }
        }

        $keptImages = $request->input('existing_images', []);
        $finalImages = array_intersect($existingImages, $keptImages);

        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                $path = $file->store('categories', 'public');
                $finalImages[] = '/storage/' . $path;
            }
        }

        $imageJson = count($finalImages) > 0 ? json_encode(array_values($finalImages)) : null;

        $baseName = $request->input('categ_name');
        $newArrival = trim((string) $request->input('arrival', ''));

        $duplicates = ServiceCategory::where('name', $category->name)
            ->where('country_id', $category->country_id)
            ->get();

        foreach ($duplicates as $dup) {
            $dup->name = $baseName;
            $dup->description = $request->input('notes');
            $dup->arrival = $newArrival;
            $dup->website = $request->input('website');
            $dup->image = $imageJson;

            // Only update parent_id if it's the exact one they are editing
            if ($dup->id == $category->id) {
                $dup->parent_id = intval($request->input('category_parent'));
            }

            // Auto-move logic ONLY for items inside the "Hotels" tree
            if (!empty($newArrival)) {
                $currentParent = ServiceCategory::find($dup->parent_id);
                // If it's in a city folder (which has a star folder parent)
                if ($currentParent && $currentParent->parent_id > 0 && $currentParent->parent_id != 403) {
                    $starFolderId = $currentParent->parent_id;
                    if (strtolower(trim($currentParent->name)) !== strtolower($newArrival)) {
                        $newCityFolder = ServiceCategory::where('parent_id', $starFolderId)
                            ->where('name', $newArrival)
                            ->first();
                        if (!$newCityFolder) {
                            $newCityFolder = ServiceCategory::create([
                                'name' => $newArrival,
                                'parent_id' => $starFolderId,
                                'country_id' => $dup->country_id
                            ]);
                        }
                        $dup->parent_id = $newCityFolder->id;
                    }
                }
            }
            $dup->save();
        }

        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Delete a category
     */
    public function destroyCategory($id)
    {
        $category = ServiceCategory::findOrFail($id);

        // Check if it has children or services
        if ($category->children()->count() > 0 || $category->services()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete category that has subcategories or services.']);
        }

        $category->delete();
        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Get seasons for a service (popup content)
     */
    public function getSeasons($serviceId)
    {
        $service = Service::with('seasons')->findOrFail($serviceId);
        $breadcrumb = $this->getParentBreadcrumb($service->category);
        $cat = ServiceCategory::find($service->category);
        $catName = $cat ? $cat->name : '';

        $html = '<div style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:15px;">' . htmlspecialchars($catName) . ' > ' . htmlspecialchars($service->description) . ' > Seasons</div>';
        $html .= '<div id="season_error_msg" style="display:none; color:#ef4444; font-size:12px; font-weight:600; margin-bottom:10px; padding:8px 12px; background:#fef2f2; border:1px solid #fecaca; border-radius:6px;"><i class="fa fa-warning" style="margin-right:5px;"></i> <span></span></div>';

        // Add new form - above the table
        $html .= '<div style="display:flex; gap:10px; align-items:center; margin-bottom:16px;">';
        $html .= '<input type="date" id="season_from" style="flex:1; padding:8px; border:1px solid #e2e8f0; border-radius:6px;" placeholder="From">';
        $html .= '<input type="date" id="season_to" style="flex:1; padding:8px; border:1px solid #e2e8f0; border-radius:6px;" placeholder="To">';
        $html .= '<input type="number" step="0.01" id="season_cost" style="flex:1; padding:8px; border:1px solid #e2e8f0; border-radius:6px;" placeholder="Cost">';
        $html .= '<button id="add_season_btn" style="white-space:nowrap; background:#f97316; color:#fff; border:none; padding:9px 18px; border-radius:6px; font-weight:600; cursor:pointer;">+ Add New</button>';
        $html .= '</div>';

        // Table - full width
        $html .= '<table style="width:100%; border-collapse:collapse;"><tr class="grey"><th class="pad" style="text-align:left;">From</th><th class="pad" style="text-align:left;">To</th><th class="pad" style="text-align:left;">Cost</th><th style="width:110px;"></th></tr>';

        // Existing seasons
        foreach ($service->seasons as $s) {
            $html .= '<tr class="cell" data-season-id="' . $s->id . '">';
            $html .= '<td class="pad"><span class="season-val">' . $s->date_from . '</span><input type="date" class="season-edit-input" value="' . $s->date_from . '" style="display:none; padding:6px; border:1px solid #e2e8f0; border-radius:4px; width:100%;"></td>';
            $html .= '<td class="pad"><span class="season-val">' . $s->date_to . '</span><input type="date" class="season-edit-input" value="' . $s->date_to . '" style="display:none; padding:6px; border:1px solid #e2e8f0; border-radius:4px; width:100%;"></td>';
            $html .= '<td class="pad"><span class="season-val">' . number_format($s->cost, 2) . '</span><input type="number" step="0.01" class="season-edit-input" value="' . $s->cost . '" style="display:none; padding:6px; border:1px solid #e2e8f0; border-radius:4px; width:100%;"></td>';
            $html .= '<td class="pad" style="white-space:nowrap; text-align:right;">';
            $html .= '<button class="btn small edit-season-btn" data-id="' . $s->id . '" title="Edit" style="background:#f97316;color:#fff;border:none;"><i class="fa fa-pencil"></i></button> ';
            $html .= '<button class="btn small save-season-btn" data-id="' . $s->id . '" title="Save" style="display:none;background:#f97316;color:#fff;border:none;"><i class="fa fa-check"></i></button> ';
            $html .= '<button class="btn red small del-season-btn" data-id="' . $s->id . '" title="Delete"><i class="fa fa-trash"></i></button>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';


        return response()->json([
            'html' => $html,
            'service_id' => $serviceId
        ]);
    }

    /**
     * AJAX: Add a season for a service
     */
    public function addSeason(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $newFrom = $request->input('date_from');
        $newTo = $request->input('date_to');

        // Check for overlapping dates
        $overlap = ServiceSeason::where('service_id', $service->id)
            ->where(function ($query) use ($newFrom, $newTo) {
                $query->where('date_from', '<=', $newTo)
                    ->where('date_to', '>=', $newFrom);
            })->exists();

        if ($overlap) {
            return response()->json(['success' => false, 'error' => 'The selected date range overlaps with an existing season!']);
        }

        ServiceSeason::create([
            'service_id' => $service->id,
            'date_from' => $newFrom,
            'date_to' => $newTo,
            'cost' => floatval($request->input('cost', 0)),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Delete a season
     */
    public function deleteSeason($seasonId)
    {
        $season = ServiceSeason::findOrFail($seasonId);
        $season->delete();
        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Update a season
     */
    public function updateSeason(Request $request, $seasonId)
    {
        $season = ServiceSeason::findOrFail($seasonId);
        $season->date_from = $request->input('date_from');
        $season->date_to = $request->input('date_to');
        $season->cost = floatval($request->input('cost', 0));
        $season->save();
        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Get vendor account details (expenses/invoices) for modal
     */
    public function venderAccount(Request $request, $id)
    {
        $vender = User::with('venderBalance')->findOrFail($id);

        $query = InvoiceExpense::where('vender', $id)
            ->with(['invoice', 'addedByUser', 'service.serviceCategory.country'])
            ->orderBy('service_date', 'desc');

        // Apply filters
        if ($request->filled('payment_status')) {
            $paymentStatus = $request->input('payment_status');
            if ($paymentStatus != 'all') {
                $query->where('payment_status', $paymentStatus);
            }
        }
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status != 'all') {
                if ($status == 'con') {
                    $query->whereIn('status', ['con', 'com']);
                } else {
                    $query->where('status', $status);
                }
            }
        }
        if ($request->filled('from')) {
            $query->where('service_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('service_date', '<=', $request->input('to'));
        }

        $expenses = $query->get();
        $unpaidBalance = $expenses->where('payment_status', 'u')->sum('cost');

        $unpaidBalance = $expenses->where('payment_status', 'u')->sum('cost');

        $html = '<div class="tw-flex tw-flex-col tw-gap-0 tw-bg-slate-50">';

        // Header
        $html .= '<div class="tw-px-10 tw-py-14 tw-bg-orange-900 tw-flex tw-justify-between tw-items-center tw-relative tw-overflow-hidden">';
        $html .= '    <div class="tw-absolute tw-top-0 tw-right-0 tw-w-[600px] tw-h-[600px] tw-bg-orange-500/10 tw-rounded-full -tw-mr-64 -tw-mt-64 tw-blur-3xl"></div>';
        $html .= '    <div class="tw-absolute tw-bottom-0 tw-left-0 tw-w-96 tw-h-96 tw-bg-orange-500/5 tw-rounded-full -tw-ml-48 -tw-mb-48 tw-blur-3xl"></div>';
        $html .= '    <div class="tw-relative tw-z-10 tw-flex tw-flex-col tw-gap-4">';
        $html .= '        <div class="tw-flex tw-items-center tw-gap-3 tw-text-[10px] tw-font-black tw-text-orange-500 tw-uppercase tw-tracking-[0.4em]">';
        $html .= '            <div class="tw-w-10 tw-h-px tw-bg-orange-500/50"></div> Global Financial Ledger';
        $html .= '        </div>';
        $html .= '        <h3 class="tw-text-4xl tw-font-black tw-text-white tw-tracking-tight">' . htmlspecialchars($vender->first_name . ' ' . $vender->last_name);
        if ($vender->company) {
            $html .= ' <span class="tw-text-white/20 tw-font-light tw-mx-3">/</span> <span class="tw-text-white/60">' . htmlspecialchars($vender->company) . '</span>';
        }
        $html .= '</h3>';
        $html .= '        <div class="tw-flex tw-items-center tw-gap-8">';
        $html .= '            <div class="tw-text-xs tw-text-slate-400 tw-font-bold tw-flex tw-items-center tw-gap-2.5"><i class="fa fa-envelope-o tw-text-orange-500"></i> ' . $vender->email . '</div>';
        if ($vender->phone) {
            $html .= '            <div class="tw-text-xs tw-text-slate-400 tw-font-bold tw-flex tw-items-center tw-gap-2.5"><i class="fa fa-phone tw-text-orange-500"></i> ' . $vender->phone . '</div>';
        }
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '    <div class="tw-relative tw-z-10 tw-bg-white/5 tw-backdrop-blur-3xl tw-border tw-border-white/10 tw-p-10 tw-rounded-[3rem] tw-flex tw-flex-col tw-items-end tw-gap-2 tw-shadow-[0_20px_50px_rgba(0,0,0,0.3)]">';
        $html .= '        <span class="tw-text-[10px] tw-font-black tw-text-rose-400 tw-uppercase tw-tracking-[0.2em]">Outstanding Exposure</span>';
        $html .= '        <div class="tw-flex tw-items-baseline tw-gap-2.5">';
        $html .= '            <span class="tw-text-5xl tw-font-black tw-text-white tw-tracking-tighter">' . number_format($unpaidBalance, 2) . '</span>';
        $html .= '            <span class="tw-text-xs tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">JOD</span>';
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '</div>';

        // Filter Bar
        $html .= '<div class="tw-px-10 tw-py-8 tw-bg-white tw-border-b tw-border-slate-100 tw-flex tw-flex-wrap tw-items-center tw-gap-10">';
        $html .= '    <div class="tw-flex-1 tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-gap-8">';
        $html .= '        <div class="tw-flex tw-flex-col tw-gap-2">';
        $html .= '            <label class="tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Settlement Status</label>';
        $html .= '            <select class="vender-acc-filter !tw-h-12 !tw-bg-slate-50 !tw-border-slate-100 focus:!tw-border-orange-500 focus:!tw-ring-4 focus:!tw-ring-orange-500/5 tw-transition-all tw-rounded-xl text-xs font-bold"><option value="all">Consolidated View</option><option value="p" ' . ($request->input('payment_status') == 'p' ? 'selected' : '') . '>Settled Invoices</option><option value="u" ' . ($request->input('payment_status') == 'u' ? 'selected' : '') . '>Pending Payments</option></select>';
        $html .= '        </div>';
        $html .= '        <div class="tw-flex tw-flex-col tw-gap-2">';
        $html .= '            <label class="tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Transaction Stream</label>';
        $html .= '            <select class="vender-acc-filter !tw-h-12 !tw-bg-slate-50 !tw-border-slate-100 focus:!tw-border-orange-500 focus:!tw-ring-4 focus:!tw-ring-orange-500/5 tw-transition-all tw-rounded-xl text-xs font-bold"><option value="all">All Operations</option><option value="con" ' . ($request->input('status') == 'con' ? 'selected' : '') . '>Completed</option><option value="pen" ' . ($request->input('status') == 'pen' ? 'selected' : '') . '>Active Queue</option><option value="inp" ' . ($request->input('status') == 'inp' ? 'selected' : '') . '>In Execution</option><option value="can" ' . ($request->input('status') == 'can' ? 'selected' : '') . '>Terminated</option></select>';
        $html .= '        </div>';
        $html .= '        <div class="tw-flex tw-flex-col tw-gap-2">';
        $html .= '            <label class="tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Timeline Start</label>';
        $html .= '            <div class="tw-relative"><i class="fa fa-calendar tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400 tw-text-xs"></i><input type="text" placeholder="Start Date" class="datepicker vender-acc-filter !tw-h-12 !tw-pl-11 !tw-bg-slate-50 !tw-border-slate-100 tw-rounded-xl text-xs font-bold" value="' . ($request->input('from')) . '"></div>';
        $html .= '        </div>';
        $html .= '        <div class="tw-flex tw-flex-col tw-gap-2">';
        $html .= '            <label class="tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Timeline End</label>';
        $html .= '            <div class="tw-relative"><i class="fa fa-calendar tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400 tw-text-xs"></i><input type="text" placeholder="End Date" class="datepicker vender-acc-filter !tw-h-12 !tw-pl-11 !tw-bg-slate-50 !tw-border-slate-100 tw-rounded-xl text-xs font-bold" value="' . ($request->input('to')) . '"></div>';
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '    <div class="tw-flex tw-items-end tw-h-full tw-pt-6">';
        $html .= '        <div class="dropdown tw-relative">';
        $html .= '            <button class="btn orange !tw-px-10 !tw-py-4 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-flex tw-items-center tw-gap-3 dropdown-toggle tw-shadow-2xl tw-shadow-orange-500/20" data-toggle="dropdown">Execution <i class="fa fa-chevron-down tw-text-[10px] tw-opacity-50"></i></button>';
        $html .= '            <ul class="dropdown-menu tw-rounded-2xl tw-border-none tw-shadow-2xl tw-p-2"><li><a href="#" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-rounded-xl hover:tw-bg-orange-50 tw-text-xs tw-font-bold tw-text-slate-700 tw-transition-all"><i class="fa fa-check-circle tw-text-orange-500"></i> Bulk Approve</a></li><li><a href="#" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-rounded-xl hover:tw-bg-rose-50 tw-text-xs tw-font-bold tw-text-slate-700 tw-transition-all"><i class="fa fa-times-circle tw-text-rose-500"></i> Void Selection</a></li></ul>';
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '</div>';

        // Table
        $html .= '<div class="tw-bg-white">';
        $html .= '    <div class="tw-overflow-hidden">';
        $html .= '        <table class="tw-w-full tw-table-auto">';
        $html .= '            <thead>';
        $html .= '                <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">';
        $html .= '                    <th class="tw-px-10 tw-py-6 tw-text-left tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Transaction Vector</th>';
        $html .= '                    <th class="tw-px-10 tw-py-6 tw-text-left tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Financial Performance</th>';
        $html .= '                    <th class="tw-px-10 tw-py-6 tw-text-center tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Operational Status</th>';
        $html .= '                    <th class="tw-px-10 tw-py-6 tw-text-left tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Project Timeline</th>';
        $html .= '                    <th class="tw-px-10 tw-py-6 tw-text-center tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest" style="width:60px"></th>';
        $html .= '                </tr>';
        $html .= '            </thead>';
        $html .= '            <tbody class="tw-divide-y tw-divide-slate-50">';

        foreach ($expenses as $e) {
            $html .= '<tr class="group hover:tw-bg-orange-500/[0.02] tw-transition-all tw-duration-300">';

            // Description
            $html .= '<td class="tw-px-10 tw-py-8">';
            if ($e->invoice) {
                $html .= '<span class="tw-text-xs tw-font-black tw-text-orange-900 tw-block tw-mb-2.5 tw-tracking-tight">' . htmlspecialchars($e->invoice->desc) . '</span>';
            }
            $html .= '<div class="tw-flex tw-items-center tw-gap-3">';
            if ($e->qty > 0) {
                $html .= '<div class="tw-bg-orange-900 tw-text-white tw-text-[8px] tw-font-black tw-px-2 tw-py-1 tw-rounded-md tw-uppercase tw-tracking-wider">' . $e->qty . ' PAX</div>';
            }
            $html .= '<span class="tw-text-[11px] tw-text-slate-500 tw-font-bold tw-line-clamp-1">' . htmlspecialchars($e->desc) . '</span>';
            $html .= '</div>';
            if ($e->confirmation_number) {
                $html .= '<div class="tw-mt-4 tw-flex tw-items-center tw-gap-2 tw-text-orange-500 tw-text-[9px] tw-font-black tw-uppercase tw-tracking-[0.2em]"><div class="tw-w-4 tw-h-px tw-bg-orange-500/30"></div> REF: ' . htmlspecialchars($e->confirmation_number) . '</div>';
            }
            $html .= '</td>';

            // Financials
            $html .= '<td class="tw-px-10 tw-py-8">';
            $html .= '    <div class="tw-flex tw-items-baseline tw-gap-2 tw-mb-2.5">';
            $html .= '        <span class="tw-text-xl tw-font-black tw-text-orange-900 tw-tracking-tighter">' . number_format($e->cost, 2) . '</span>';
            $html .= '        <span class="tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase">JOD</span>';
            $html .= '    </div>';
            if ($e->payment_status == 'p') {
                $html .= '<div class="tw-flex tw-items-center tw-gap-2 tw-text-orange-500 tw-text-[9px] tw-font-black tw-uppercase tw-tracking-widest"><i class="fa fa-check-circle"></i> Settled</div>';
            } else {
                $html .= '<div class="tw-flex tw-items-center tw-gap-2 tw-text-rose-500 tw-text-[9px] tw-font-black tw-uppercase tw-tracking-widest"><i class="fa fa-clock-o"></i> Outstanding</div>';
            }
            $html .= '</td>';

            // Status
            $html .= '<td class="tw-px-10 tw-py-8 tw-text-center">';
            if ($e->status == 'con' || $e->status == 'com') {
                $html .= '<span class="tw-bg-orange-500 tw-text-white tw-text-[8px] tw-font-black tw-px-3 tw-py-1.5 tw-rounded-lg tw-uppercase tw-shadow-lg tw-shadow-orange-500/20">Archived</span>';
            } elseif ($e->status == 'pen') {
                $html .= '<span class="tw-bg-amber-500 tw-text-white tw-text-[8px] tw-font-black tw-px-3 tw-py-1.5 tw-rounded-lg tw-uppercase tw-shadow-lg tw-shadow-amber-500/20">Waiting</span>';
            } elseif ($e->status == 'inp') {
                $html .= '<span class="tw-bg-orange-500 tw-text-white tw-text-[8px] tw-font-black tw-px-3 tw-py-1.5 tw-rounded-lg tw-uppercase tw-shadow-lg tw-shadow-orange-500/20">Active</span>';
            } elseif ($e->status == 'can') {
                $html .= '<span class="tw-bg-rose-600 tw-text-white tw-text-[8px] tw-font-black tw-px-3 tw-py-1.5 tw-rounded-lg tw-uppercase tw-shadow-lg tw-shadow-rose-600/20">Revoked</span>';
            } else {
                $html .= '<span class="tw-bg-slate-500 tw-text-white tw-text-[8px] tw-font-black tw-px-3 tw-py-1.5 tw-rounded-lg tw-uppercase">' . $e->status . '</span>';
            }
            $html .= '</td>';

            // Dates
            $html .= '<td class="tw-px-10 tw-py-8">';
            $html .= '    <div class="tw-flex tw-flex-col tw-gap-2.5">';
            $html .= '        <div class="tw-flex tw-items-center tw-gap-3"><div class="tw-w-1.5 tw-h-5 tw-bg-orange-500 tw-rounded-full"></div> <span class="tw-text-[9px] tw-font-black tw-text-slate-400 tw-uppercase tw-w-10">Start:</span> <span class="tw-text-xs tw-font-bold tw-text-slate-700">' . ($e->service_date ?: 'TBD') . '</span></div>';
            $html .= '        <div class="tw-flex tw-items-center tw-gap-3"><div class="tw-w-1.5 tw-h-5 tw-bg-slate-100 tw-rounded-full"></div> <span class="tw-text-[9px] tw-font-black tw-text-slate-400 tw-uppercase tw-w-10">End:</span> <span class="tw-text-xs tw-font-bold tw-text-slate-700">' . ($e->service_end_date ?: 'TBD') . '</span></div>';
            $html .= '    </div>';
            $html .= '</td>';

            // Select
            $html .= '<td class="tw-px-10 tw-py-8 tw-text-center">';
            $html .= '<input type="checkbox" name="expense_ids[]" value="' . $e->id . '" class="tw-w-5 tw-h-5 tw-rounded-lg tw-accent-orange-500 tw-cursor-pointer tw-transition-all hover:tw-scale-110">';
            $html .= '</td>';

            $html .= '</tr>';
        }

        $html .= '            </tbody>';
        $html .= '        </table>';
        $html .= '    </div>';
        $html .= '</div>';
        $html .= '</div>';

        // Add Datepicker initialization and filter logic
        $html .= '<script>';
        $html .= 'if (typeof $.fn.datepicker !== "undefined") {';
        $html .= '  $(".datepicker").datepicker({ format: "yyyy-mm-dd", autoHide: true });';
        $html .= '}';
        $html .= 'if (typeof window.venderAccFilterAttached === "undefined") {';
        $html .= '  window.venderAccFilterAttached = true;';
        $html .= '  $(document).on("change pick.datepicker", ".vender-acc-filter", function() {';
        $html .= '    var $modal = $("#vender_account_content");';
        $html .= '    var pStatus = $modal.find("select:eq(0)").val();';
        $html .= '    var vStatus = $modal.find("select:eq(1)").val();';
        $html .= '    var fromDate = $modal.find("input:eq(0)").val();';
        $html .= '    var toDate = $modal.find("input:eq(1)").val();';
        $html .= '    var url = "' . url("admin/services-venders/" . $id . "/account") . '?payment_status=" + pStatus + "&status=" + vStatus + "&from=" + fromDate + "&to=" + toDate;';
        $html .= '    $.get(url, function(res) { $("#vender_account_content").html(res.html); });';
        $html .= '  });';
        $html .= '}';
        $html .= '</script>';

        return response()->json(['html' => $html]);

    }

    /**
     * AJAX: Get vendor description and images for modal
     */
    public function venderDescription(Request $request, $id)
    {
        $vender = User::with('venderDetail')->findOrFail($id);
        $detail = $vender->venderDetail;

        $desc = [];
        $images = [];
        if ($detail) {
            $desc = is_array($detail->description) ? $detail->description : (unserialize($detail->description) ?: []);
            $images = is_array($detail->images) ? $detail->images : (unserialize($detail->images) ?: []);
        }

        $langs = [
            'en' => 'Description (en):',
            'fr' => 'Description (fr):',
            'it' => 'Description (it):',
            'es' => 'Description (es):',
            'Ar' => 'Description (Ar):',
            'ge' => 'Description (ge):',
            'pt' => 'Description (pt):',
        ];

        $html = '<div class="tw-flex tw-flex-col tw-gap-0 tw-bg-slate-50">';

        // Header
        $html .= '<div class="tw-px-10 tw-py-14 tw-bg-orange-900 tw-flex tw-justify-between tw-items-center tw-relative tw-overflow-hidden">';
        $html .= '    <div class="tw-absolute tw-bottom-0 tw-left-0 tw-w-[700px] tw-h-[700px] tw-bg-orange-500/10 tw-rounded-full -tw-ml-64 -tw-mb-64 tw-blur-3xl"></div>';
        $html .= '    <div class="tw-relative tw-z-10 tw-flex tw-flex-col tw-gap-4">';
        $html .= '        <div class="tw-flex tw-items-center tw-gap-3 tw-text-[10px] tw-font-black tw-text-orange-500 tw-uppercase tw-tracking-[0.4em]">';
        $html .= '            <div class="tw-w-10 tw-h-px tw-bg-orange-500/50"></div> Vender Profile & Identity';
        $html .= '        </div>';
        $html .= '        <h3 class="tw-text-4xl tw-font-black tw-text-white tw-tracking-tight">' . htmlspecialchars($vender->first_name . ' ' . $vender->last_name);
        if ($vender->company) {
            $html .= ' <span class="tw-text-white/20 tw-font-light tw-mx-3">/</span> <span class="tw-text-white/60">' . htmlspecialchars($vender->company) . '</span>';
        }
        $html .= '</h3>';
        $html .= '        <div class="tw-flex tw-items-center tw-gap-5">';
        $html .= '            <div class="tw-text-xs tw-text-slate-400 tw-font-bold tw-flex tw-items-center tw-gap-2.5"><i class="fa fa-envelope-o tw-text-orange-500"></i> ' . $vender->email . '</div>';
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '    <div class="tw-relative tw-z-10">';
        $html .= '        <div class="tw-bg-white/5 tw-backdrop-blur-3xl tw-border tw-border-white/10 tw-px-10 tw-py-6 tw-rounded-[2.5rem] tw-flex tw-items-center tw-gap-6 tw-shadow-2xl">';
        $html .= '            <div class="tw-text-right">';
        $html .= '                <div class="tw-text-[10px] tw-font-black tw-text-orange-400 tw-uppercase tw-tracking-widest tw-mb-1">Platform Status</div>';
        $html .= '                <div class="tw-text-sm tw-font-black tw-text-white">Active & Verified</div>';
        $html .= '            </div>';
        $html .= '            <div class="tw-w-12 tw-h-12 tw-rounded-2xl tw-bg-orange-500 tw-text-white tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-orange-500/30"><i class="fa fa-check tw-text-lg"></i></div>';
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '</div>';

        $html .= '<div class="tw-p-10">';
        $html .= '    <form id="vender_desc_form" class="tw-flex tw-flex-col tw-gap-16">';
        $html .= '        <input type="hidden" name="_token" value="' . csrf_token() . '">';

        $html .= '        <div class="tw-flex tw-flex-col tw-gap-10">';
        $html .= '            <div class="tw-flex tw-items-center tw-gap-5">';
        $html .= '                <div class="tw-w-14 tw-h-14 tw-rounded-[1.5rem] tw-bg-orange-500/5 tw-text-orange-500 tw-flex tw-items-center tw-justify-center tw-shadow-sm tw-border tw-border-orange-500/10"><i class="fa fa-commenting tw-text-xl"></i></div>';
        $html .= '                <div>';
        $html .= '                    <h4 class="tw-text-base tw-font-black tw-text-orange-900 tw-uppercase tw-tracking-widest">Operational Narrative</h4>';
        $html .= '                    <p class="tw-text-xs tw-text-slate-400 tw-font-bold">Define the professional core of this service provider across available languages.</p>';
        $html .= '                </div>';
        $html .= '            </div>';
        $html .= '            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-12">';
        foreach ($langs as $code => $label) {
            $val = isset($desc[$code]) ? $desc[$code] : '';
            $html .= '            <div class="tw-flex tw-flex-col tw-gap-4">';
            $html .= '                <div class="tw-flex tw-justify-between tw-items-center">';
            $html .= '                    <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">' . $label . '</label>';
            $html .= '                    <span class="tw-bg-orange-500/5 tw-text-orange-500 tw-text-[10px] tw-font-black tw-px-3 tw-py-1 tw-rounded-lg tw-uppercase tw-border tw-border-orange-500/10">' . strtoupper($code) . '</span>';
            $html .= '                </div>';
            $html .= '                <textarea name="desc[' . $code . ']" class="!tw-h-48 !tw-min-h-[180px] !tw-resize-none !tw-bg-white !tw-border-slate-100 focus:!tw-border-orange-500 focus:!tw-ring-4 focus:!tw-ring-orange-500/5 tw-transition-all tw-rounded-[1.5rem] tw-p-6 tw-text-sm tw-font-medium tw-text-slate-600 placeholder:tw-text-slate-300" placeholder="Type professional details...">' . htmlspecialchars($val) . '</textarea>';
            $html .= '            </div>';
        }
        $html .= '            </div>';
        $html .= '        </div>';

        $html .= '        <div class="tw-pt-16 tw-border-t tw-border-slate-100">';
        $html .= '            <div class="tw-flex tw-justify-between tw-items-center tw-mb-12">';
        $html .= '                <div class="tw-flex tw-items-center tw-gap-5">';
        $html .= '                    <div class="tw-w-14 tw-h-14 tw-rounded-[1.5rem] tw-bg-amber-50 tw-text-amber-600 tw-flex tw-items-center tw-justify-center tw-shadow-sm tw-border tw-border-amber-100"><i class="fa fa-camera-retro tw-text-xl"></i></div>';
        $html .= '                    <div>';
        $html .= '                        <h3 class="tw-text-base tw-font-black tw-text-orange-900 tw-uppercase tw-tracking-widest">Portfolio Discovery</h3>';
        $html .= '                        <p class="tw-text-xs tw-text-slate-400 tw-font-bold">High-impact imagery to showcase service capabilities and quality standards.</p>';
        $html .= '                    </div>';
        $html .= '                </div>';
        $html .= '                <button type="button" class="btn orange !tw-px-10 !tw-py-4 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-flex tw-items-center tw-gap-3 tw-shadow-2xl tw-shadow-orange-500/20 hover:tw-scale-105 tw-transition-transform" onclick="addVenderImage();"><i class="fa fa-folder-open"></i> Assets Manager</button>';
        $html .= '            </div>';

        $html .= '            <div id="vender_images_container" class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-5 tw-gap-10">';
        if (!empty($images)) {
            foreach ($images as $img) {
                $html .= '<div class="tw-relative tw-aspect-[4/3] tw-rounded-[2.5rem] tw-overflow-hidden tw-border-[10px] tw-border-white tw-shadow-[0_20px_40px_rgba(0,0,0,0.08)] group vender-image-item">';
                $html .= '<img src="' . $img . '" class="tw-w-full tw-h-full tw-object-cover tw-transition-transform tw-duration-1000 group-hover:tw-scale-110">';
                $html .= '<input type="hidden" name="images[]" value="' . $img . '">';
                $html .= '<div class="tw-absolute tw-inset-0 tw-bg-orange-900/60 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-all tw-duration-500 tw-flex tw-items-center tw-justify-center tw-backdrop-blur-[2px]">';
                $html .= '    <button type="button" onclick="$(this).closest(\'.vender-image-item\').remove();" class="tw-w-14 tw-h-14 tw-rounded-full tw-bg-rose-500 tw-text-white tw-flex tw-items-center tw-justify-center tw-shadow-2xl tw-transition-transform hover:tw-scale-110"><i class="fa fa-trash-o tw-text-xl"></i></button>';
                $html .= '</div></div>';
            }
        }
        $html .= '            </div>';
        if (empty($images)) {
            $html .= '        <div class="tw-py-24 tw-bg-slate-50 tw-border-2 tw-border-dashed tw-border-slate-200 tw-rounded-[3rem] tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-6">';
            $html .= '            <div class="tw-w-24 tw-h-24 tw-rounded-full tw-bg-white tw-text-slate-200 tw-flex tw-items-center tw-justify-center tw-text-4xl tw-shadow-sm"><i class="fa fa-picture-o"></i></div>';
            $html .= '            <div class="tw-text-center">';
            $html .= '                <p class="tw-text-sm tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Gallery Empty</p>';
            $html .= '                <p class="tw-text-xs tw-text-slate-300 tw-font-bold tw-mt-1">Connect images from the assets manager to showcase this vendor.</p>';
            $html .= '            </div>';
            $html .= '        </div>';
        }
        $html .= '        </div>';

        $html .= '        <div class="tw-pt-16 tw-border-t tw-border-slate-100 tw-flex tw-justify-end">';
        $html .= '            <button type="button" onclick="saveVenderDescription(' . $id . ');" class="btn orange !tw-px-20 !tw-py-5 tw-text-sm tw-font-black tw-uppercase tw-tracking-[0.2em] tw-flex tw-items-center tw-gap-4 tw-shadow-2xl tw-shadow-orange-500/30 hover:tw-scale-[1.02] tw-transition-transform"><i class="fa fa-cloud-upload"></i> Synchronize Profile</button>';
        $html .= '        </div>';

        $html .= '    </form>';
        $html .= '</div>';
        $html .= '</div>';

        return response()->json(['html' => $html]);

    }

    /**
     * AJAX: Save vendor description and images
     */
    public function updateVenderDescription(Request $request, $id)
    {
        $vender = User::findOrFail($id);

        $desc = $request->input('desc', []);
        $images = $request->input('images', []);

        VenderDetail::updateOrCreate(
            ['vender_id' => $id],
            [
                'description' => serialize($desc),
                'images' => serialize($images),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Library page — Evaneos-style grouped service listing
     */
    public function library(Request $request)
    {
        $targetCountryNames = ['Egypt', 'Jordan', 'Lebanon', 'Libya', 'Morocco', 'Oman', 'Palestine', 'Qatar', 'Saudi Arabia'];
        $countries = Country::where('lang', 'en')
            ->whereIn('name', $targetCountryNames)
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->pluck('name', 'id')
            ->toArray();

        $countryId = $request->input('country');
        if (!$countryId) {
            $jordanId = array_search('Jordan', $countries);
            $countryId = $jordanId !== false ? $jordanId : array_key_first($countries);
        }

        $groupedServices = [];
        $rootCategories = [];
        $cannedDays = collect();

        if ($countryId) {
            // Get Canned Days with English content
            $cannedDays = \App\Models\TourCannedDay::with([
                'contents' => function ($q) {
                    $q->where('lang', 'en');
                }
            ])->orderByDesc('id')->limit(5)->get();

            // Only show these specific categories (404 Hotels is now a sub-category of 403 Accommodations)
            $allowedCatIds = [403, 93, 715, 456, 527];
            $rootCategories = ServiceCategory::where('country_id', $countryId)
                ->whereIn('id', $allowedCatIds)
                ->get();

            // Clean display names
            $displayNames = [
                403 => 'Accommodations',
                93 => 'Activities',
                715 => 'Transportation',
                456 => 'Restaurants',
            ];

            // Display order
            $rootCategories = $rootCategories->sortBy(function ($cat) {
                $order = [403 => 1, 93 => 2, 715 => 3, 456 => 4, 527 => 5];
                return $order[$cat->id] ?? 99;
            });

            foreach ($rootCategories as $rootCat) {
                $categoryIds = $this->getAllDescendantIds($rootCat->id, $countryId);
                $categoryIds[] = $rootCat->id;

                // For catId=93 (Activities section): show hotels from en33_accommodations using 403 category tree
                if ($rootCat->id == 93) {
                    $accomCatIds = $this->getAllDescendantIds(403, $countryId);
                    $accomCatIds[] = 403;
                    $services = Accommodation::whereIn('category', $accomCatIds)
                        ->with('venderUser', 'serviceCategory.parent')
                        ->orderByDesc('id')
                        ->limit(4)
                        ->get();
                    $totalCount = Accommodation::whereIn('category', $accomCatIds)->count();
                } elseif ($rootCat->id == 403) {
                    $services = Accommodation::whereIn('category', $categoryIds)
                        ->with('venderUser', 'serviceCategory.parent')
                        ->orderByDesc('id')
                        ->limit(4)
                        ->get();
                    $totalCount = Accommodation::whereIn('category', $categoryIds)->count();
                } elseif ($rootCat->id == 715) {
                    // Transport section: show hotels from en33_accommodations (same as Activities)
                    $accomCatIds = $this->getAllDescendantIds(403, $countryId);
                    $accomCatIds[] = 403;
                    $services = Accommodation::whereIn('category', $accomCatIds)
                        ->with('venderUser', 'serviceCategory.parent')
                        ->orderByDesc('id')
                        ->limit(4)
                        ->get();
                    $totalCount = Accommodation::whereIn('category', $accomCatIds)->count();
                } elseif ($rootCat->id == 456) {
                    // Restaurant section: show actual restaurants from en33_restaurants
                    $restCatIds = $this->getAllDescendantIds(456, $countryId);
                    $restCatIds[] = 456;
                    $services = \App\Models\Restaurant::whereIn('category', $restCatIds)
                        ->with('venderUser', 'serviceCategory.parent')
                        ->orderByDesc('id')
                        ->limit(4)
                        ->get();
                    $totalCount = \App\Models\Restaurant::whereIn('category', $restCatIds)->count();
                } else {
                    $services = Service::whereIn('category', $categoryIds)
                        ->with('venderUser', 'serviceCategory')
                        ->orderByDesc('id')
                        ->limit(4)
                        ->get();
                    $totalCount = Service::whereIn('category', $categoryIds)->count();
                }

                if ($rootCat->id == 403 || $rootCat->id == 93 || $rootCat->id == 715 || $rootCat->id == 456) {
                    // Accommodation, Activity & Transport: no subcategories, show only hotel records
                    $subCategories = collect();
                } else {
                    $subCategories = ServiceCategory::where('country_id', $countryId)
                        ->where('parent_id', $rootCat->id)
                        ->orderBy('name')
                        ->get();
                }

                if ($totalCount > 0 || $subCategories->count() > 0) {
                    // Override display name if available
                    if (isset($displayNames[$rootCat->id])) {
                        $rootCat->display_name = $displayNames[$rootCat->id];
                    }
                    // Map root cat ID to service type
                    $typeMap = [403 => 'accommodation', 93 => 'activity_section', 715 => 'transport_section', 456 => 'restaurant_section', 527 => 'guide'];
                    $groupedServices[] = [
                        'category' => $rootCat,
                        'services' => $services,
                        'total' => $totalCount,
                        'subCategories' => $subCategories,
                        'type' => $typeMap[$rootCat->id] ?? 'service',
                    ];
                }
            }

            // Prepare hotels grouped by star rating for the modal dropdown
            // 404 is Hotels. Its children are Star categories. Their children are Cities. Their children are actual Hotels.
            // Wait, actually, the hierarchy is: 404 (Hotels) -> 428 (5 Stars) -> 429 (Amman) -> 180 (Amman Rotana Hotel).
            $hotelsByStar = [];
            $starCats = ServiceCategory::where('parent_id', 404)->where('country_id', $countryId)->get();
            foreach ($starCats as $star) {
                // $star->name is like "5 Stars" or "5 Star". Let's normalize it to the options in our select
                $starLabel = $star->name;
                // "4 Stars" -> "4 Star", "5 Stars" -> "5 Star" to match dropdown values
                if (str_contains($starLabel, '4 Star'))
                    $starLabel = '4 Star';
                if (str_contains($starLabel, '5 Star'))
                    $starLabel = '5 Star';

                $hotelsInStar = [];
                // Cities under this star
                $cities = ServiceCategory::where('parent_id', $star->id)->where('country_id', $countryId)->get();
                foreach ($cities as $city) {
                    // Hotels under this city
                    $actualHotels = ServiceCategory::where('parent_id', $city->id)->where('country_id', $countryId)->get();
                    foreach ($actualHotels as $hotel) {
                        $hotelsInStar[] = [
                            'id' => $hotel->id,
                            'name' => html_entity_decode($hotel->name) . ' (' . html_entity_decode($city->name) . ')'
                        ];
                    }
                }

                // Sort alphabetically by name
                usort($hotelsInStar, function ($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });

                $hotelsByStar[$starLabel] = $hotelsInStar;
            }

        }

        $totalDays = \App\Models\TourCannedDay::count();

        return view('admin.services.library', compact(
            'countries',
            'countryId',
            'groupedServices',
            'rootCategories',
            'cannedDays',
            'totalDays',
            'hotelsByStar'
        ));
    }

    /**
     * Dedicated page for each library category (Activity, Transport, Accommodation, Restaurant, Guide)
     */
    public function libraryCategory(Request $request)
    {
        // Detect which category from URL segment
        $segment = $request->segment(3); // 'activity', 'transport', 'accommodation', 'restaurant', 'guide'

        $categoryMap = [
            'activity' => ['id' => 93, 'name' => 'Activity', 'icon' => 'fa-binoculars'],
            'transport' => ['id' => 715, 'name' => 'Transport', 'icon' => 'fa-car'],
            'accommodation' => ['id' => 403, 'name' => 'Accommodation', 'icon' => 'fa-bed'],
            'restaurant' => ['id' => 456, 'name' => 'Restaurant', 'icon' => 'fa-cutlery'],
            'guide' => ['id' => 527, 'name' => 'Guide', 'icon' => 'fa-user'],
        ];

        if (!isset($categoryMap[$segment])) {
            abort(404);
        }

        $catInfo = $categoryMap[$segment];
        $catId = $catInfo['id'];
        $catName = $catInfo['name'];
        $catIcon = $catInfo['icon'];

        // Country
        $targetCountryNames = ['Egypt', 'Jordan', 'Lebanon', 'Libya', 'Morocco', 'Oman', 'Palestine', 'Qatar', 'Saudi Arabia'];
        $countries = \App\Models\Country::where('lang', 'en')
            ->whereIn('name', $targetCountryNames)
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->pluck('name', 'id')
            ->toArray();

        $countryId = $request->input('country');
        if (!$countryId) {
            $jordanId = array_search('Jordan', $countries);
            $countryId = $jordanId !== false ? $jordanId : array_key_first($countries);
        }

        $search = trim($request->input('search', ''));
        $services = collect();

        if ($countryId) {
            if ($catId == 403) {
                // Accommodation — en33_accommodations
                $catIds = $this->getAllDescendantIds(403, $countryId);
                $catIds[] = 403;
                $query = \App\Models\Accommodation::whereIn('category', $catIds)
                    ->with('venderUser', 'serviceCategory.parent');
                if ($search)
                    $query->where('descriptionL', 'like', '%' . $search . '%');
                $services = $query->orderByDesc('id')->get();

            } elseif ($catId == 715) {
                // Transport — en33_transports
                $query = \App\Models\Transport::where('country', $countryId)
                    ->with('venderUser', 'serviceCategory');
                if ($search)
                    $query->where('description', 'like', '%' . $search . '%');
                $services = $query->orderByDesc('id')->get();

            } elseif ($catId == 93) {
                // Activity — en33_activities
                $catIds = $this->getAllDescendantIds(93, $countryId);
                $catIds[] = 93;
                $query = \App\Models\Activity::where('country', $countryId)
                    ->with('venderUser', 'serviceCategory');
                if ($search)
                    $query->where('description', 'like', '%' . $search . '%');
                $services = $query->orderByDesc('id')->get();

            } elseif ($catId == 456) {
                // Restaurant — en33_restaurants
                $catIds = $this->getAllDescendantIds(456, $countryId);
                $catIds[] = 456;
                $query = \App\Models\Restaurant::whereIn('category', $catIds)
                    ->with('venderUser', 'serviceCategory.parent');
                if ($search)
                    $query->where('description', 'like', '%' . $search . '%');
                $services = $query->orderByDesc('id')->get();

            } elseif ($catId == 527) {
                // Guide — en33_services
                $catIds = $this->getAllDescendantIds(527, $countryId);
                $catIds[] = 527;
                $query = \App\Models\Service::whereIn('category', $catIds)
                    ->with('venderUser', 'serviceCategory');
                if ($search)
                    $query->where('description', 'like', '%' . $search . '%');
                $services = $query->orderByDesc('id')->get();
            }
        }

        $venders = \App\Models\User::where('user_group', 'supplier')->orderBy('first_name')->get();
        $transCompanies = collect();
        $companyMethodData = [];
        if ($catId == 715) {
            $transCompanies = \App\Models\ServiceCategory::where('parent_id', $catId)->orderBy('name')->get();
            // Build companyMethodData for Create Transport modal (same structure as editTransportModal)
            foreach ($transCompanies as $companyCat) {
                $childCatsInfo = \App\Models\ServiceCategory::where('parent_id', $companyCat->id)->get();
                $methodsList = [];
                foreach ($childCatsInfo as $childCat) {
                    $childSvcs = Service::where('category', $childCat->id)->get(['id', 'description', 'cost', 'vender', 'transport_method', 'departure_location', 'arrival_destination', 'length_time', 'distance_km']);
                    $methodsList[] = ['id' => $childCat->id, 'name' => $childCat->name, 'services' => $childSvcs->values()->toArray()];
                }
                $directSvcs = Service::where('category', $companyCat->id)->get(['id', 'description', 'cost', 'vender', 'transport_method', 'departure_location', 'arrival_destination', 'length_time', 'distance_km']);
                $companyMethodData[strval($companyCat->id)] = [
                    'catId' => $companyCat->id,
                    'name' => $companyCat->name,
                    'methods' => $methodsList,
                    'directServices' => $directSvcs->values()->toArray()
                ];
            }
        }

        $restCategoriesData = [];
        $restSubServicesData = [];
        if ($catId == 456) {
            $cities = \App\Models\ServiceCategory::where('parent_id', 456)->orderBy('name')->get();
            $allRestCatIds = [];
            foreach ($cities as $c) {
                $rests = \App\Models\ServiceCategory::where('parent_id', $c->id)->pluck('id')->toArray();
                $allRestCatIds = array_merge($allRestCatIds, $rests);
            }
            $allRestCatIds = array_unique($allRestCatIds);
            $allRestCatIds[] = 456;

            $subSvcs = Service::whereIn('category', $allRestCatIds)->with('venderUser')->get(['id', 'description', 'cost', 'vender', 'category']);

            $vendorMap = [];
            foreach($subSvcs as $sub) {
                $restSubServicesData[] = $sub;

                $vendorId = $sub->vender ?? 0;
                if (!isset($vendorMap[$vendorId])) {
                    $vendorName = 'No Vendor';
                    if ($sub->venderUser) {
                        $vendorName = !empty($sub->venderUser->company)
                            ? $sub->venderUser->company
                            : trim($sub->venderUser->first_name . ' ' . $sub->venderUser->last_name);
                    }
                    $vendorMap[$vendorId] = [
                        'id' => $vendorId,
                        'name' => $vendorName,
                        'services' => []
                    ];
                }
                $vendorMap[$vendorId]['services'][] = [
                    'id'          => $sub->id,
                    'description' => $sub->description,
                    'cost'        => $sub->cost,
                    'vender'      => $sub->vender,
                    'category'    => $sub->category,
                    'vendor_name' => $vendorMap[$vendorId]['name'],
                ];
            }

            foreach ($vendorMap as $vm) {
                if (count($vm['services']) > 0) {
                    $restCategoriesData[] = $vm;
                }
            }
        }

        $actCategoriesData = [];
        if ($catId == 93) {
            $actSubCats = \App\Models\ServiceCategory::where('parent_id', 204)->orderBy('name')->get(['id', 'name', 'country_id']);
            $allActCatIds = [];
            foreach ($actSubCats as $ac) {
                $duplicateIds = \App\Models\ServiceCategory::where('name', $ac->name)
                    ->where('country_id', $countryId)
                    ->pluck('id')->toArray();
                $allActCatIds = array_unique(array_merge($allActCatIds, [$ac->id], $duplicateIds));
            }

            // Get all services for all activity sub-categories, group by vendor
            $acSvcs = Service::whereIn('category', $allActCatIds)->with('venderUser')->get(['id', 'description', 'cost', 'vender', 'category']);

            $vendorGroups = [];
            foreach ($acSvcs as $svc) {
                $vendorId = $svc->vender ?? 0;
                $vendorName = 'No Vendor';
                if ($svc->venderUser) {
                    $vendorName = !empty($svc->venderUser->company)
                        ? $svc->venderUser->company
                        : trim($svc->venderUser->first_name . ' ' . $svc->venderUser->last_name);
                }
                if (!isset($vendorGroups[$vendorId])) {
                    $vendorGroups[$vendorId] = [
                        'id'       => $vendorId,
                        'name'     => $vendorName,
                        'services' => []
                    ];
                }
                $vendorGroups[$vendorId]['services'][] = [
                    'id'          => $svc->id,
                    'description' => $svc->description,
                    'cost'        => $svc->cost,
                    'vender'      => $svc->vender,
                    'category'    => $svc->category,
                    'vendor_name' => $vendorName,
                ];
            }
            foreach ($vendorGroups as $vg) {
                if (count($vg['services']) > 0) {
                    $actCategoriesData[] = $vg;
                }
            }
        }

        $guideCategoriesData = [];
        if ($catId == 527) {
            $allGuideCatIds = $this->getAllDescendantIds(527, $countryId);
            $allGuideCatIds[] = 527;
            $allGuideCatIds = array_unique($allGuideCatIds);

            // Get all services under the Guides tree, grouped by vendor (same pattern as Activities)
            $guideSvcs = Service::whereIn('category', $allGuideCatIds)->with('venderUser')->get(['id', 'description', 'cost', 'vender', 'category']);

            $guideVendorGroups = [];
            foreach ($guideSvcs as $svc) {
                $vendorId = $svc->vender ?? 0;
                $vendorName = 'No Vendor';
                if ($svc->venderUser) {
                    $vendorName = !empty($svc->venderUser->company)
                        ? $svc->venderUser->company
                        : trim($svc->venderUser->first_name . ' ' . $svc->venderUser->last_name);
                }
                if (!isset($guideVendorGroups[$vendorId])) {
                    $guideVendorGroups[$vendorId] = [
                        'id'       => $vendorId,
                        'name'     => $vendorName,
                        'services' => []
                    ];
                }
                $guideVendorGroups[$vendorId]['services'][] = [
                    'id'          => $svc->id,
                    'description' => $svc->description,
                    'cost'        => $svc->cost,
                    'vender'      => $svc->vender,
                    'category'    => $svc->category,
                    'vendor_name' => $vendorName,
                ];
            }
            foreach ($guideVendorGroups as $vg) {
                if (count($vg['services']) > 0) {
                    $guideCategoriesData[] = $vg;
                }
            }
        }

        return view('admin.services.library_category', compact(
            'catName',
            'catIcon',
            'catId',
            'segment',
            'services',
            'countryId',
            'countries',
            'search',
            'venders',
            'transCompanies',
            'companyMethodData',
            'restCategoriesData',
            'restSubServicesData',
            'actCategoriesData',
            'guideCategoriesData'
        ));
    }

    /**
     * AJAX: Get services by category for the modal dropdown
     */
    public function getServicesByCategory(Request $request)
    {
        $category = $request->input('category', '');
        $countryId = $request->input('country_id', 123);

        if (!$category) {
            return response()->json(['services' => []]);
        }

        if (preg_match('/(\d)/', $category, $m)) {
            $starNum = $m[1];
            $pattern1 = $starNum . ' Star';
            $pattern2 = $starNum . ' Stars';
        } else {
            $pattern1 = $category;
            $pattern2 = $category;
        }

        $starCats = ServiceCategory::where('country_id', $countryId)
            ->where(function ($q) use ($pattern1, $pattern2) {
                $q->where('name', $pattern1)->orWhere('name', $pattern2);
            })->get();

        if ($starCats->isEmpty()) {
            return response()->json(['services' => []]);
        }

        $leafIds = [];
        $visited = [];
        foreach ($starCats as $sc) {
            $this->getLeafNodes($sc->id, $leafIds, $visited);
        }

        if (empty($leafIds)) {
            return response()->json(['services' => []]);
        }

        $vendors = ServiceCategory::whereIn('id', $leafIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'services' => $vendors->map(function ($v) {
                return ['id' => $v->id, 'name' => $v->name];
            })
        ]);
    }

    private function getLeafNodes($parentId, &$leafNodes, &$visited = [])
    {
        if (in_array($parentId, $visited))
            return;
        $visited[] = $parentId;

        $children = ServiceCategory::where('parent_id', $parentId)->pluck('id')->toArray();
        if (empty($children)) {
            $leafNodes[] = $parentId;
        } else {
            foreach ($children as $childId) {
                $this->getLeafNodes($childId, $leafNodes, $visited);
            }
        }
    }

    /**
     * AJAX: Quick-add a service to a category (from accommodation modal)
     */
    public function quickAdd(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'category' => 'required|integer',
            'country' => 'nullable|integer',
            'vender' => 'nullable|integer',
        ]);

        $svc = new \App\Models\Service();
        $svc->description = $request->input('description');
        $svc->cost = $request->input('cost', 0);
        $svc->category = $request->input('category');
        $svc->country = $request->input('country', 123);
        $svc->vender = $request->input('vender', 0) ?: 0;
        if ($request->has('transport_method')) $svc->transport_method = $request->input('transport_method');
        if ($request->has('departure_location')) $svc->departure_location = $request->input('departure_location');
        if ($request->has('arrival_destination')) $svc->arrival_destination = $request->input('arrival_destination');
        if ($request->has('length_time')) $svc->length_time = $request->input('length_time');
        if ($request->has('distance_km')) $svc->distance_km = $request->input('distance_km');
        $svc->save();

        return response()->json(['success' => true, 'id' => $svc->id]);
    }

    /**
     * AJAX: Quick-add a transport (from Transport Hotel modal)
     */
    public function quickAddTransport(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'country' => 'nullable|integer',
            'vender' => 'nullable|integer',
        ]);

        $tr = new \App\Models\Transport();
        $tr->description = $request->input('description');
        $tr->cost = $request->input('cost', 0);
        $tr->country = $request->input('country', 123);
        $tr->vender = $request->input('vender') ?: null;
        $tr->save();

        return response()->json(['success' => true, 'id' => $tr->id]);
    }

    public function quickAddGuide(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'country' => 'nullable|integer',
            'vender' => 'nullable|integer',
            'category' => 'nullable|integer',
        ]);

        $svc = new Service();
        $svc->description = $request->input('description');
        $svc->cost = $request->input('cost', 0);
        $svc->country = $request->input('country', 123);
        $svc->vender = $request->input('vender') ?: null;
        $svc->category = $request->input('category') ?: null;
        $svc->transport_method = $request->input('acc_type') ?: null;
        $svc->departure_location = $request->input('acc_category') ?: null;
        $svc->save();

        return response()->json(['success' => true, 'id' => $svc->id]);
    }

    public function quickAddActivity(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'country' => 'nullable|integer',
            'vender' => 'nullable|integer',
            'category' => 'nullable|integer',
        ]);

        $act = new Activity();
        $act->description = $request->input('description');
        $act->cost = $request->input('cost', 0);
        $act->country = $request->input('country', 123);
        $act->vender = $request->input('vender') ?: null;
        $act->category = $request->input('category') ?: null;
        $act->acc_type = $request->input('acc_type') ?: null;
        $act->acc_category = $request->input('acc_category') ?: null;

        if ($request->has('transport_method')) $act->transport_method = $request->input('transport_method');
        if ($request->has('departure_location')) $act->departure_location = $request->input('departure_location');
        if ($request->has('arrival_destination')) $act->arrival_destination = $request->input('arrival_destination');
        if ($request->has('length_time')) $act->length_time = $request->input('length_time');
        if ($request->has('distance_km')) $act->distance_km = $request->input('distance_km');
        $act->acc_category = $request->input('acc_category') ?: null;
        $act->save();

        return response()->json(['success' => true, 'id' => $act->id]);
    }
    public function quickAddRestaurant(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'country' => 'nullable|integer',
            'vender' => 'nullable|integer',
            'category' => 'nullable|integer',
        ]);

        $rest = new \App\Models\Restaurant();
        $rest->description = $request->input('description');
        $rest->cost = $request->input('cost', 0);
        $rest->country = $request->input('country', 123);
        $rest->vender = $request->input('vender') ?: null;
        $rest->category = $request->input('category') ?: null;
        $rest->save();

        return response()->json(['success' => true, 'id' => $rest->id]);
    }

    /**
     * AJAX: Return all canned days as HTML table for the Days library tab
     */
    public function libraryDays(Request $request)
    {
        $search = trim($request->input('search', ''));
        $days = \App\Models\TourCannedDay::with('contents')->get();

        $fallbacks = [
            'linear-gradient(90deg,#73523e,#2a2230)',
            'linear-gradient(90deg,#777,#555)',
            'linear-gradient(90deg,#6b3d20,#201712)',
            'linear-gradient(90deg,#5b6d73,#1c3336)',
            'linear-gradient(90deg,#725f43,#2a281e)',
        ];

        // Inline CSS (same classes as canned-days/index.blade.php)
        $html = '<style>
.ev-day-card{position:relative;min-height:106px;border-radius:2px;background:#555;box-shadow:0 1px 4px rgba(0,0,0,.18);isolation:isolate;}
.ev-day-card::before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(0,0,0,.55),rgba(0,0,0,.18)),var(--ev-bg);background-size:cover;background-position:center;z-index:-2;}
.ev-day-card::after{content:"";position:absolute;inset:0;background:rgba(0,0,0,.12);z-index:-1;}
.ev-day-body{min-height:106px;padding:28px 58px 22px 18px;color:#fff;display:flex;flex-direction:column;justify-content:center;}
.ev-location{display:flex;align-items:center;gap:8px;margin-bottom:8px;font-size:12px;font-weight:700;text-shadow:0 1px 2px rgba(0,0,0,.35);}
.ev-card-title{margin:0;color:#fff!important;font-size:22px!important;line-height:1.22!important;font-weight:500!important;text-shadow:0 1px 2px rgba(0,0,0,.35);}
.ev-menu-button{position:absolute;top:14px;right:12px;width:28px;height:34px;border:0;background:transparent;color:#fff;cursor:pointer;font-size:22px;line-height:1;padding:0;display:flex;align-items:center;justify-content:center;text-shadow:0 1px 2px rgba(0,0,0,.45);}
.ev-card-menu{position:absolute;top:44px;right:12px;width:142px;display:none;background:#fff;border:1px solid #e3e7e9;box-shadow:0 8px 22px rgba(0,0,0,.18);z-index:4;}
.ev-card-menu.open{display:block;}
.ev-card-menu button,.ev-card-menu a{width:100%;display:flex;align-items:center;gap:8px;padding:10px 12px;border:0;background:#fff;color:#263238;font-size:13px;font-weight:700;text-decoration:none;text-align:left;cursor:pointer;}
.ev-card-menu button:hover,.ev-card-menu a:hover{background:#f3f6f6;color:#ea580c;}
.ev-card-menu .ev-danger{color:#db2447;}
.ev-card-langs{position:absolute;right:12px;bottom:10px;display:flex;gap:4px;flex-wrap:wrap;justify-content:flex-end;max-width:180px;}
.ev-lang-chip{min-width:26px;height:21px;display:inline-flex;align-items:center;justify-content:center;border-radius:2px;background:rgba(255,255,255,.92);color:#132027;font-size:10px;font-weight:800;text-transform:uppercase;}
.ev-card-list{display:flex;flex-direction:column;gap:14px;}
</style>';

        $html .= '<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">';
        $html .= '<span style="font-size:11px;font-weight:800;color:#ea580c;letter-spacing:1px;">📅 CANNED DAYS</span>';
        $html .= '<span style="font-size:11px;color:#aaa;">(' . $days->count() . ' total)</span>';
        $html .= '</div>';
        $html .= '<div class="ev-card-list">';

        $index = 0;
        foreach ($days as $day) {
            $enContent = $day->contents->firstWhere('lang', 'en') ?? $day->contents->first();
            $title = $enContent && trim($enContent->title) !== '' ? trim($enContent->title) : '(No title)';

            if ($search && stripos($title, $search) === false) {
                $index++;
                continue;
            }

            // Build background
            $images = @unserialize($day->images);
            if (!is_array($images))
                $images = [];
            $firstImage = collect($images)->filter()->first();
            $imageUrl = '';
            if ($firstImage) {
                $imageUrl = str_starts_with($firstImage, 'http') ? $firstImage : '/' . ltrim($firstImage, '/');
                $imageUrl = str_replace([' ', "'"], ['%20', '%27'], $imageUrl);
            }
            // Use single quotes inside url() — style attr uses double quotes so inner double quotes break it
            $bgValue = $imageUrl
                ? "url('" . $imageUrl . "')"
                : $fallbacks[$index % count($fallbacks)];

            // Location from title
            $tl = strtolower($title);
            $place = str_contains($tl, 'petra') ? 'Petra'
                : (str_contains($tl, 'amman') ? 'Amman'
                    : (str_contains($tl, 'dead sea') ? 'Dead Sea'
                        : (str_contains($tl, 'wadi rum') ? 'Wadi Rum'
                            : (str_contains($tl, 'aqaba') ? 'Aqaba' : 'Jordan'))));

            // Language chips
            $langs = $day->contents->pluck('lang')->filter()->map(fn($l) => $l === 'Ar' ? 'AR' : strtoupper($l))->unique()->values();

            $titleSafe = addslashes($title);

            $html .= '<article class="ev-day-card" style="--ev-bg:' . $bgValue . ';">';
            $html .= '<div class="ev-day-body">';
            $html .= '<div class="ev-location"><i class="fa fa-map-marker"></i> ' . htmlspecialchars($place) . '</div>';
            $html .= '<h3 class="ev-card-title">' . htmlspecialchars($title) . '</h3>';
            $html .= '</div>';

            // Three-dot menu
            $html .= '<button type="button" class="ev-menu-button" onclick="toggleLibDayMenu(event,' . $day->id . ')">⋮</button>';
            $html .= '<div id="lib_day_menu_' . $day->id . '" class="ev-card-menu">';
            $html .= '<button type="button" onclick="editDay(' . $day->id . ');closeLibDayMenus();"><i class="fa fa-pencil"></i> Edit</button>';
            $html .= '<button type="button" class="ev-danger" onclick="deleteDay(' . $day->id . ',\'' . $titleSafe . '\');closeLibDayMenus();"><i class="fa fa-trash"></i> Delete</button>';
            $html .= '</div>';

            // Language chips
            if ($langs->isNotEmpty()) {
                $html .= '<div class="ev-card-langs">';
                foreach ($langs as $lang) {
                    $html .= '<span class="ev-lang-chip">' . htmlspecialchars($lang) . '</span>';
                }
                $html .= '</div>';
            }

            $html .= '</article>';
            $index++;
        }

        if ($days->isEmpty()) {
            $html .= '<div style="padding:40px;text-align:center;color:#a0aec0;font-size:13px;border:1px dashed #ddd;">No canned days found.</div>';
        }

        $html .= '</div>';

        return response()->json(['html' => $html]);
    }

    /**
     * AJAX: Filter library services by search/category
     */
    public function libraryFilter(Request $request)
    {
        $countryId = intval($request->input('country', 0));
        $search = $request->input('search', '');
        $categoryFilter = $request->input('category', '');

        if (!$countryId) {
            return response()->json(['html' => '<div class="tw-text-center tw-py-20 tw-text-slate-400">Select a country</div>']);
        }

        $groupedServices = [];

        if ($categoryFilter) {
            $rootCat = ServiceCategory::find($categoryFilter);
            if ($rootCat) {
                $categoryIds = $this->getAllDescendantIds($rootCat->id, $countryId);
                $categoryIds[] = $rootCat->id;

                // Walk up ancestor chain to find service type root (403, 93, 715, 456, 527)
                $serviceTypeRootId = $rootCat->id;
                $walker = $rootCat;
                $knownRoots = [403, 93, 715, 456, 527];
                while ($walker && !in_array($walker->id, $knownRoots)) {
                    if (!$walker->parent_id)
                        break;
                    $walker = ServiceCategory::find($walker->parent_id);
                    if ($walker)
                        $serviceTypeRootId = $walker->id;
                }

                // For catId=93 (Activities): use 403 tree; for 403: use own tree
                if ($serviceTypeRootId == 93) {
                    $accomCatIds = $this->getAllDescendantIds(403, $countryId);
                    $accomCatIds[] = 403;
                    $query = Accommodation::whereIn('category', $accomCatIds)
                        ->with('venderUser', 'serviceCategory.parent');
                    if ($search) {
                        $query->where('descriptionL', 'like', '%' . $search . '%');
                    }
                    $svcType = 'activity_section';
                } elseif ($serviceTypeRootId == 403) {
                    $query = Accommodation::whereIn('category', $categoryIds)
                        ->with('venderUser', 'serviceCategory.parent');
                    if ($search) {
                        $query->where('descriptionL', 'like', '%' . $search . '%');
                    }
                    $svcType = 'accommodation';
                } elseif ($serviceTypeRootId == 715) {
                    // Transport section: show hotels from en33_accommodations (same as Activities)
                    $accomCatIds = $this->getAllDescendantIds(403, $countryId);
                    $accomCatIds[] = 403;
                    $query = Accommodation::whereIn('category', $accomCatIds)
                        ->with('venderUser', 'serviceCategory.parent');
                    if ($search) {
                        $query->where('descriptionL', 'like', '%' . $search . '%');
                    }
                    $svcType = 'transport_section';
                } elseif ($serviceTypeRootId == 456) {
                    // Restaurant section: show hotels from en33_accommodations
                    $accomCatIds = $this->getAllDescendantIds(403, $countryId);
                    $accomCatIds[] = 403;
                    $query = Accommodation::whereIn('category', $accomCatIds)
                        ->with('venderUser', 'serviceCategory.parent');
                    if ($search) {
                        $query->where('descriptionL', 'like', '%' . $search . '%');
                    }
                    $svcType = 'restaurant_section';
                } else {
                    $query = Service::whereIn('category', $categoryIds)
                        ->with('venderUser', 'serviceCategory');
                    if ($search) {
                        $query->where('description', 'like', '%' . $search . '%');
                    }
                    $svcType = 'service';
                }

                $services = $query->orderByDesc('id')->get();
                $totalCount = $services->count();

                if ($serviceTypeRootId == 403 || $serviceTypeRootId == 93 || $serviceTypeRootId == 715 || $serviceTypeRootId == 456) {
                    // Accommodation & Activity: no subcategories, only own table records
                    $subCategories = collect();
                } else {
                    $subCategoriesQuery = ServiceCategory::where('country_id', $countryId)
                        ->where('parent_id', $rootCat->id)
                        ->orderBy('name');
                    if ($search) {
                        $subCategoriesQuery->where('name', 'like', '%' . $search . '%');
                    }
                    $subCategories = $subCategoriesQuery->get();
                }

                if ($totalCount > 0 || $subCategories->count() > 0) {
                    $groupedServices[] = [
                        'category' => $rootCat,
                        'services' => $services,
                        'total' => $totalCount,
                        'subCategories' => $subCategories,
                        'type' => $svcType,
                    ];
                }
            }
        } else {
            // Only specific categories (404 Hotels is under 403 Accommodations)
            $allowedCatIds = [403, 93, 715, 456, 527];
            $rootCategories = ServiceCategory::where('country_id', $countryId)
                ->whereIn('id', $allowedCatIds)
                ->get();

            foreach ($rootCategories as $rootCat) {
                $categoryIds = $this->getAllDescendantIds($rootCat->id, $countryId);
                $categoryIds[] = $rootCat->id;

                // For catId=93 (Activities): use 403 tree; for 403: use own tree
                if ($rootCat->id == 93) {
                    $accomCatIds = $this->getAllDescendantIds(403, $countryId);
                    $accomCatIds[] = 403;
                    $query = Accommodation::whereIn('category', $accomCatIds)
                        ->with('venderUser', 'serviceCategory.parent');
                    if ($search) {
                        $query->where('descriptionL', 'like', '%' . $search . '%');
                    }
                    $services = $query->orderByDesc('id')->limit($search ? 50 : 5)->get();
                    $totalCount = Accommodation::whereIn('category', $accomCatIds)
                        ->when($search, fn($q) => $q->where('descriptionL', 'like', '%' . $search . '%'))
                        ->count();
                } elseif ($rootCat->id == 403) {
                    $query = Accommodation::whereIn('category', $categoryIds)
                        ->with('venderUser', 'serviceCategory.parent');
                    if ($search) {
                        $query->where('descriptionL', 'like', '%' . $search . '%');
                    }
                    $services = $query->orderByDesc('id')->limit($search ? 50 : 5)->get();
                    $totalCount = Accommodation::whereIn('category', $categoryIds)
                        ->when($search, fn($q) => $q->where('descriptionL', 'like', '%' . $search . '%'))
                        ->count();
                } elseif ($rootCat->id == 715) {
                    // Transport section: show hotels from en33_accommodations (same as Activities)
                    $accomCatIds = $this->getAllDescendantIds(403, $countryId);
                    $accomCatIds[] = 403;
                    $query = Accommodation::whereIn('category', $accomCatIds)
                        ->with('venderUser', 'serviceCategory.parent');
                    if ($search) {
                        $query->where('descriptionL', 'like', '%' . $search . '%');
                    }
                    $services = $query->orderByDesc('id')->limit($search ? 50 : 5)->get();
                    $totalCount = Accommodation::whereIn('category', $accomCatIds)
                        ->when($search, fn($q) => $q->where('descriptionL', 'like', '%' . $search . '%'))
                        ->count();
                } elseif ($rootCat->id == 456) {
                    // Restaurant section: show actual restaurants from en33_restaurants
                    $restCatIds = $this->getAllDescendantIds(456, $countryId);
                    $restCatIds[] = 456;
                    $query = \App\Models\Restaurant::whereIn('category', $restCatIds)
                        ->with('venderUser', 'serviceCategory.parent');
                    if ($search) {
                        $query->where('description', 'like', '%' . $search . '%');
                    }
                    $services = $query->orderByDesc('id')->limit($search ? 50 : 5)->get();
                    $totalCount = \App\Models\Restaurant::whereIn('category', $restCatIds)
                        ->when($search, fn($q) => $q->where('description', 'like', '%' . $search . '%'))
                        ->count();
                } else {
                    $query = Service::whereIn('category', $categoryIds)
                        ->with('venderUser', 'serviceCategory');
                    if ($search) {
                        $query->where('description', 'like', '%' . $search . '%');
                    }
                    $services = $query->orderByDesc('id')->limit($search ? 50 : 5)->get();
                    $totalCount = Service::whereIn('category', $categoryIds)
                        ->when($search, fn($q) => $q->where('description', 'like', '%' . $search . '%'))
                        ->count();
                }

                if ($rootCat->id == 403 || $rootCat->id == 93 || $rootCat->id == 715 || $rootCat->id == 456) {
                    // Accommodation, Activity & Transport: no subcategories, show only hotel records
                    $subCategories = collect();
                } else {
                    $subCategoriesQuery = ServiceCategory::where('country_id', $countryId)
                        ->where('parent_id', $rootCat->id)
                        ->orderBy('name');
                    if ($search) {
                        $subCategoriesQuery->where('name', 'like', '%' . $search . '%');
                    }
                    $subCategories = $subCategoriesQuery->get();
                }

                if ($totalCount > 0 || $subCategories->count() > 0) {
                    // Detect service type for this root category
                    $svcTypeMap = [403 => 'accommodation', 93 => 'activity_section', 715 => 'transport_section', 456 => 'restaurant_section', 527 => 'guide'];
                    $groupedServices[] = [
                        'category' => $rootCat,
                        'services' => $services,
                        'total' => $totalCount,
                        'subCategories' => $subCategories,
                        'type' => $svcTypeMap[$rootCat->id] ?? 'service',
                    ];
                }
            }
        }
        // Canned days (show when no category filter or 'days' filter)
        $cannedDays = collect();
        $totalDays = 0;
        if (!$categoryFilter || $categoryFilter === 'days') {
            $daysQuery = \App\Models\TourCannedDay::with([
                'contents' => function ($q) {
                    $q->where('lang', 'en');
                }
            ]);
            if ($search) {
                $dayIds = \App\Models\TourCannedDayContent::where('lang', 'en')
                    ->where('title', 'like', '%' . $search . '%')
                    ->pluck('day_id');
                $daysQuery->whereIn('id', $dayIds);
            }
            $cannedDays = $daysQuery->orderByDesc('id')->limit($categoryFilter === 'days' ? 50 : 5)->get();
            $totalDays = \App\Models\TourCannedDay::count();
        }

        // If days-only filter, skip services
        if ($categoryFilter === 'days') {
            $groupedServices = [];
        }

        $catFilterActive = $categoryFilter;

        $html = view('admin.services._library_items', compact('groupedServices', 'cannedDays', 'totalDays', 'catFilterActive'))->render();
        return response()->json(['html' => $html]);
    }

    /**
     * Get all descendant category IDs recursively
     */
    private function getAllDescendantIds($parentId, $countryId, $visited = [])
    {
        $ids = [];
        if (in_array($parentId, $visited))
            return $ids;
        $visited[] = $parentId;

        $children = ServiceCategory::where('parent_id', $parentId)
            ->where('country_id', $countryId)
            ->pluck('id');

        foreach ($children as $childId) {
            if (!in_array($childId, $visited)) {
                $ids[] = $childId;
                $ids = array_merge($ids, $this->getAllDescendantIds($childId, $countryId, $visited));
            }
        }

        return $ids;
    }

    /**
     * Alias with circular reference protection
     */
    private function getAllDescendantCategoryIds($parentId, $countryId, $visited = [])
    {
        return $this->getAllDescendantIds($parentId, $countryId, $visited);
    }

    public function getVendorServicesTable($id, Request $request)
    {
        $star = $request->input('star', '');
        $countryId = $request->input('country_id', 123);

        $categoryIds = [];

        if ($star && $id == 0) {
            // Load ALL services for all hotels under this star category
            if (preg_match('/(\d)/', $star, $m)) {
                $starNum = $m[1];
                $pattern1 = $starNum . ' Star';
                $pattern2 = $starNum . ' Stars';
            } else {
                $pattern1 = $star;
                $pattern2 = $star;
            }

            $starCats = ServiceCategory::where('country_id', $countryId)
                ->where('parent_id', 404) // Only under Hotels
                ->where(function ($q) use ($pattern1, $pattern2) {
                    $q->where('name', $pattern1)->orWhere('name', $pattern2);
                })->get();

            $leafIds = [];
            $visited = [];
            foreach ($starCats as $sc) {
                $this->getLeafNodes($sc->id, $leafIds, $visited);
            }
            $categoryIds = $leafIds;
        } else {
            // Single hotel category
            $category = ServiceCategory::find($id);
            if (!$id || !$category)
                return response()->json(['html' => '']);
            $categoryIds = [$id];
        }

        if (empty($categoryIds)) {
            return response()->json(['html' => '<div style="padding:20px; text-align:center; color:#999; font-size:12px;">No services found.</div>']);
        }

        $services = Service::whereIn('category', $categoryIds)->with('venderUser')->get();

        // Build vendors list for filter dropdown
        $vendorsList = [];
        foreach ($services as $svc) {
            if ($svc->venderUser) {
                $vName = $svc->venderUser->company ?: $svc->venderUser->first_name . ' ' . $svc->venderUser->last_name;
                $vendorsList[$svc->vender] = $vName;
            }
        }

        // Fetch ALL vendors for the Add Service form
        $allVendors = \App\Models\User::whereIn('user_group', ['vender', 'supplier'])->orderBy('company')->get();
        $masterVendorSelect = '<select id="masterVendorList" style="display:none;">';
        foreach ($allVendors as $av) {
            $avName = $av->company ?: $av->first_name . ' ' . $av->last_name;
            $masterVendorSelect .= '<option value="' . $av->id . '">' . htmlspecialchars($avName) . '</option>';
        }
        $masterVendorSelect .= '</select>';

        // Build "SERVICES LIST" header
        $html = '<div style="margin-top:20px;">';
        $html .= $masterVendorSelect;
        $html .= '<input type="hidden" id="currentCategoryId" value="' . (count($categoryIds) == 1 ? $categoryIds[0] : 0) . '">';
        $html .= '<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">';
        $html .= '<div style="display:flex; align-items:center; gap:8px; color:#e74c3c; font-weight:700; font-size:13px;"><i class="fa fa-list"></i> SERVICES LIST</div>';
        $html .= '</div>';

        if ($services->count() == 0) {
            $html .= '<div style="padding:40px 20px; text-align:center; color:#999; font-size:12px; border:1px solid #f0f0f0; border-radius:12px; background:#fcfcfc;">No services found for this vendor/category.</div>';
        } else {
            $html .= '<div style="border-radius:12px; overflow:hidden; border:1px solid #f0f0f0; box-shadow:0 2px 10px rgba(0,0,0,0.02);">';
            $html .= '<table style="width:100%; border-collapse:collapse; font-size:12px; font-family:\'Inter\', sans-serif;">';
            $html .= '<thead style="background:#f8f9fa; border-bottom:1px solid #eee;">';
            $html .= '<tr>';
            $html .= '<th style="text-align:left; padding:15px; color:#999; font-weight:600; text-transform:uppercase; font-size:10px; letter-spacing:0.5px;">Description</th>';
            $html .= '<th style="text-align:left; padding:15px; color:#999; font-weight:600; text-transform:uppercase; font-size:10px; letter-spacing:0.5px;">Cost</th>';
            $html .= '<th style="text-align:left; padding:15px; color:#999; font-weight:600; text-transform:uppercase; font-size:10px; letter-spacing:0.5px;">Vendor</th>';
            $html .= '<th style="text-align:right; padding:15px; color:#999; font-weight:600; text-transform:uppercase; font-size:10px; letter-spacing:0.5px;">Actions</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody style="background:#fff;">';

            foreach ($services as $svc) {
                $venderName = $svc->venderUser ? ($svc->venderUser->company ?: $svc->venderUser->first_name . ' ' . $svc->venderUser->last_name) : 'N/A';
                $html .= '<tr class="svc-row" data-vendor="' . ($svc->vender ?? '') . '" style="border-bottom:1px solid #f8f9fa;">';
                $html .= '<td style="padding:15px; color:#2c3e50; font-weight:600; font-size:13px;">' . htmlspecialchars($svc->description) . '</td>';
                $html .= '<td style="padding:15px; font-weight:700; font-size:13px; color:#2c3e50;">' . number_format($svc->cost, 2) . ' <span style="color:#2ecc71; font-weight:600; font-size:11px;">JOD</span></td>';
                $html .= '<td style="padding:15px; color:#555; font-size:12px;">' . htmlspecialchars($venderName) . '</td>';
                $html .= '<td style="padding:15px; text-align:right;">';
                $html .= '<div style="display:inline-flex; gap:6px; align-items:center;">';
                $html .= '<button type="button" onclick="openSeasons(' . $svc->id . ')" style="background:#fff8ef; color:#f39c12; border:none; padding:6px 12px; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:5px;"><i class="fa fa-calendar"></i> Seasons</button>';
                $html .= '<button type="button" onclick="editSvc(' . $svc->id . ')" style="background:#f0f7ff; color:#3498db; border:none; width:32px; height:32px; border-radius:8px; cursor:pointer;"><i class="fa fa-edit"></i></button>';
                $html .= '<button type="button" onclick="delSvc(' . $svc->id . ', \'' . addslashes($svc->description) . '\')" style="background:#fff5f5; color:#e74c3c; border:none; width:32px; height:32px; border-radius:8px; cursor:pointer;"><i class="fa fa-trash"></i></button>';
                $html .= '</div>';
                $html .= '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }
        $html .= '</div>';

        return response()->json(['html' => $html]);
    }
    public function getVendorActivities($id)
    {
        $activities = \App\Models\Activity::where('vender', $id)->get(['id', 'description', 'cost']);
        $services = \App\Models\Service::where('vender', $id)->get(['id', 'description', 'cost']);
        $combined = $activities->merge($services)->values();
        return response()->json($combined);
    }
    public function getVendorServices($id)
    {
        $services = \App\Models\Service::where('vender', $id)->orderBy('description')->get();
        if ($services->isEmpty()) {
            return response()->json(['html' => '']);
        }
        $csrf = csrf_token();
        $html = '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">';
        $html .= '<span style="color:#e53e3e;font-size:11px;font-weight:800;letter-spacing:1px;">🇯🇴 SERVICES LIST</span>';
        $html .= '<button type="button" onclick="toggleAccomAddForm()" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Service</button>';
        $html .= '</div>';
        $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        $html .= '<thead><tr style="border-bottom:1px solid #e2e8f0;">';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">DESCRIPTION</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">COST</th>';
        $html .= '<th style="text-align:left;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">VENDOR</th>';
        $html .= '<th style="text-align:right;padding:6px 8px;font-size:10px;font-weight:700;color:#718096;letter-spacing:1px;">ACTIONS</th>';
        $html .= '</tr></thead><tbody>';
        foreach ($services as $svc) {
            $vName = '-';
            if ($svc->venderUser) {
                $vName = !empty($svc->venderUser->company) ? strtoupper($svc->venderUser->company) : strtoupper(trim($svc->venderUser->first_name . ' ' . $svc->venderUser->last_name));
            }
            $html .= '<tr style="border-bottom:1px solid #f7fafc;">';
            $html .= '<td style="padding:7px 8px;">' . htmlspecialchars($svc->description) . '</td>';
            $html .= '<td style="padding:7px 8px;text-align:right;color:#ea580c;font-weight:700;">' . number_format($svc->cost, 2) . ' JOD</td>';
            $html .= '<td style="padding:7px 8px;">' . htmlspecialchars($vName) . '</td>';
            $html .= '<td style="padding:7px 8px;text-align:right;white-space:nowrap;">';
            $html .= '<button onclick="editSvc(' . $svc->id . ')" style="background:#f0f4ff;border:none;color:#3b82f6;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;margin-right:4px;"><i class="fa fa-pencil"></i></button>';
            $html .= '<button onclick="delSvc(' . $svc->id . ',\'' . addslashes($svc->description) . '\')" style="background:#fff5f5;border:none;color:#e53e3e;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;"><i class="fa fa-trash"></i></button>';
            $html .= '</td></tr>';
        }
        $html .= '</tbody></table>';
        return response()->json(['html' => $html]);
    }
}
