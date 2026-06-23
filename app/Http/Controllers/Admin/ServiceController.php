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
            'countries', 'countryId', 'categoryId', 'tree', 'allCategories'
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
        $venderList = Service::whereIn('category', $allCategoryIds)
            ->whereNotNull('vender')
            ->with('venderUser')
            ->get()
            ->pluck('venderUser')
            ->filter()
            ->unique('id')
            ->mapWithKeys(function ($u) {
                $name = !empty($u->company) ? $u->company : $u->email;
                return [$u->id => $name];
            })
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

        $html = view('admin.services._services_panel', compact(
            'category', 'breadcrumb', 'services', 'venderList', 'venderId', 'countryId', 'categoryId'
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
        if ($request->has('notes')) { $data['notes'] = $request->input('notes'); }
        if ($request->has('acc_type')) { $data['acc_type'] = $request->input('acc_type'); }
        if ($request->has('acc_category')) { $data['acc_category'] = $request->input('acc_category'); }
        if ($request->has('website')) { $data['website'] = $request->input('website'); }
        if ($request->has('arrival')) { $data['arrival'] = $request->input('arrival'); }

        // Handle multi-image upload (hotel/activity from library)
        $allImages = [];
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/services'), $filename);
                $allImages[] = 'uploads/services/' . $filename;
            }
        } elseif ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/services'), $filename);
            $allImages[] = 'uploads/services/' . $filename;
        }
        if (!empty($allImages)) {
            $data['image'] = count($allImages) === 1 ? $allImages[0] : json_encode($allImages);
        }

        if ($request->input('service_type') === 'transport') {
            if ($request->has('method')) { $data['transport_method'] = $request->input('method'); }
            if ($request->has('departure')) { $data['departure_location'] = $request->input('departure'); }
            if ($request->has('arrival')) { $data['arrival_destination'] = $request->input('arrival'); }
            if ($request->has('distance')) { $data['distance_km'] = $request->input('distance'); }
            // Extract length which was not named in the form
            if ($request->has('length_time')) { $data['length_time'] = $request->input('length_time'); }
            \App\Models\Transport::create($data);
        } elseif ($request->input('service_type') === 'restaurant') {
            \App\Models\Restaurant::create($data);
        } else {
            Service::create($data);
        }

        if ($request->ajax()) {
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
                        if (stripos($cn, 'accommod') !== false) { $isAccommodation = true; break; }
                        if (stripos($cn, 'transport') !== false || stripos($cn, 'tranport') !== false) { $isTransport = true; break; }
                        if (stripos($cn, 'activit') !== false || stripos($cn, 'pvt') !== false || $checkCat->id == 93) { $isActivity = true; break; }
                        if (stripos($cn, 'guide') !== false || $checkCat->id == 527) { $isGuide = true; break; }
                        $checkCat = $checkCat->parent_id ? ServiceCategory::find($checkCat->parent_id) : null;
                    }
                }
            }
            if ($isAccommodation) return $this->editAccommodationModal($service);
            if ($isTransport)     return $this->editTransportModal($service);
            if ($isActivity)      return $this->editActivityModal($service);
            if ($isGuide)         return $this->editGuideModal($service);
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

        $imgPath   = $service->image ?? '';
        $desc      = htmlspecialchars($service->description ?? '');
        $sid       = $service->id;
        $countryId = $service->country ?? 123;

        if (!$service->relationLoaded('serviceCategory')) {
            $service->load('serviceCategory.parent.parent.parent');
        }

        $arrival     = $service->arrival;
        $accType     = $service->acc_type;
        $accCategory = $service->acc_category;

        if ($service->serviceCategory) {
            $cat   = $service->serviceCategory;
            $chain = [];
            $walker = $cat->parent ?? null;
            while ($walker) { $chain[] = $walker; $walker = $walker->parent ?? null; }
            if (!$arrival && isset($chain[0])) { $arrival = $chain[0]->name; }
            $typeMap = ['Hotels'=>'Hotel','Camps'=>'Camp','Homestay'=>'Guesthouse','Homestays'=>'Guesthouse','Mobile Camp'=>'Camp','Wild Jordan RSCN'=>'Eco-lodge'];
            $starMap = ['1 Star'=>'1 ★','2 Star'=>'2 ★★','3 Star'=>'3 ★★★','4 Stars'=>'4 ★★★★','5 Stars'=>'5 ★★★★★'];
            foreach ($chain as $node) {
                if (!$accType && isset($typeMap[$node->name])) { $accType = $typeMap[$node->name]; }
                if (!$accCategory && isset($starMap[$node->name])) { $accCategory = $starMap[$node->name]; }
            }
        }

        // Header
        $html  = '<script>';
        $html .= 'document.getElementById("libModalHead").innerHTML=\'';
        $html .= '<h3>Modify Activity</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editActSecForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';';
        $html .= '</script>';

        $html .= '<form id="editActSecForm" onsubmit="submitEditAccSection(' . $sid . '); return false;" enctype="multipart/form-data">';
        $html .= csrf_field();

        // Language flags + vendor bar
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg     = $active ? '#ea580c' : 'transparent';
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
        if ($imgPath) { $d = @json_decode($imgPath, true); $existingImages = is_array($d) ? $d : [$imgPath]; }
        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editActSecImageInput" accept="image/*" multiple style="display:none" onchange="addActSecImages(this)">';
        $html .= '<div id="actSecPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach ($existingImages as $img) {
            $imgUrl = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $html .= '<div class="acc-photo-wrap" style="position:relative;flex-shrink:0;height:104px;">';
            $html .= '<img src="' . $imgUrl . '" style="height:100%;border-radius:4px;object-fit:cover;">';
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

        $accTypes = ['Hotel','Guesthouse','Hostel','Resort','Apartment','Camp','Eco-lodge','Riad','Villa'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Accommodation Type</legend>';
        $html .= '<select name="acc_type" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a type</option>';
        foreach ($accTypes as $t) { $sel = ($accType === $t) ? ' selected' : ''; $html .= '<option value="' . $t . '"' . $sel . '>' . $t . '</option>'; }
        $html .= '</select></fieldset>';

        $cats = ['1 ★','2 ★★','3 ★★★','4 ★★★★','5 ★★★★★','Standard','Superior','Luxury'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Category</legend>';
        $html .= '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a category</option>';
        foreach ($cats as $c) { $sel = ($accCategory === $c) ? ' selected' : ''; $html .= '<option value="' . $c . '"' . $sel . '>' . $c . '</option>'; }
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
        foreach (['Entrance','Excursion','Adventure','Cultural','Cooking','Water Sport','Desert Safari','Hiking','Religious','Other'] as $at) {
            $html .= '<option value="' . $at . '">' . $at . '</option>';
        }
        $html .= '</select></div>';
        $html .= '<div style="flex:1;min-width:120px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Activity Category</label>';
        $html .= '<select id="newActCat" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 8px;font-size:12px;background:#fff;color:#555;">';
        $html .= '<option value="">-- Category --</option>';
        foreach (['Standard','Premium','VIP','Group','Private','Family'] as $ac) {
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
        $arrival    = $service->arrival;
        $accType    = $service->acc_type;
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
                'Hotels' => 'Hotel', 'Camps' => 'Camp', 'Homestay' => 'Guesthouse',
                'Homestays' => 'Guesthouse', 'Mobile Camp' => 'Camp',
                'Wild Jordan RSCN' => 'Eco-lodge',
            ];
            $starMap = [
                '1 Star' => '1 ★', '2 Star' => '2 ★★', '3 Star' => '3 ★★★',
                '4 Stars' => '4 ★★★★', '5 Stars' => '5 ★★★★★',
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
        $html .= 'document.getElementById("libModalHead").innerHTML=\'';
        $html .= '<h3>Modify accommodation</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editAccForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';';
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
        $vendorName = $service->venderUser
            ? (!empty($service->venderUser->company) ? strtoupper($service->venderUser->company) : strtoupper($service->venderUser->first_name . ' ' . $service->venderUser->last_name))
            : strtoupper($service->description ?? '');
        $html .= '<div style="margin-left:auto;display:flex;gap:16px;align-items:center;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:6px 14px;font-size:12px;">';
        $html .= '<span><strong>Vendor Name:</strong> ' . htmlspecialchars($vendorName) . '</span>';
        $html .= '<span style="color:#ccc;">|</span>';
        $html .= '<span><strong>Vendor Price:</strong> <span style="color:#ea580c;font-weight:700;">' . number_format($service->cost ?? 0, 2) . ' JOD</span></span>';
        $html .= '</div>';
        $html .= '</div>';

        // Photos section - multi-image support
        $existingImages = [];
        if ($imgPath) {
            $decoded = @json_decode($imgPath, true);
            if (is_array($decoded)) {
                $existingImages = $decoded;
            } else {
                $existingImages = [$imgPath];
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
            $html .= '<div class="acc-photo-wrap" style="position:relative;flex-shrink:0;height:104px;">';
            $html .= '<img src="' . $imgUrl . '" style="height:100%;border-radius:4px;object-fit:cover;">';
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

        // Place of interest - with autocomplete
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend>';
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="' . htmlspecialchars($arrival ?? '') . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        // Accommodation type
        $accTypes = ['Hotel','Guesthouse','Hostel','Resort','Apartment','Camp','Eco-lodge','Riad','Villa'];
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
        $cats = ['1 ★','2 ★★','3 ★★★','4 ★★★★','5 ★★★★★','Standard','Superior','Luxury'];
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
        $hotelServices = \App\Models\Service::whereIn('category', $hotelCatIds)->with('venderUser')->orderBy('description')->get();
        $html .= '<div style="margin-top:20px;">';
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
        // Detect default vendor — use accommodation's own vender, or most common from existing services
        $defaultVender = $service->vender ?? null;
        if (!$defaultVender && $hotelServices->isNotEmpty()) {
            $defaultVender = $hotelServices->filter(fn($s) => $s->vender)->groupBy('vender')->sortByDesc(fn($g) => $g->count())->keys()->first();
        }
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
            $html .= '<button onclick="editSvc(' . $svc->id . ')" style="background:#f0f4ff;border:none;color:#3b82f6;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;margin-right:4px;"><i class="fa fa-pencil"></i></button>';
            $html .= '<button onclick="delSvc(' . $svc->id . ',\'' . addslashes($svc->description) . '\')" style="background:#fff5f5;border:none;color:#e53e3e;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;"><i class="fa fa-trash"></i></button>';
            $html .= '</td></tr>';
        }
        if ($hotelServices->isEmpty()) {
            $html .= '<tr><td colspan="4" style="padding:16px;text-align:center;color:#a0aec0;font-size:12px;">No services found for this hotel.</td></tr>';
        }
        $html .= '</tbody></table></div>';
        $csrf = csrf_token();
        $html .= '<script>
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
function editSvc(id){
    var old=document.getElementById("svcEditForm_"+id);if(old){old.remove();return;}
    var row=document.querySelector("button[onclick*=\\"editSvc("+id+")\\"]").closest("tr");
    var desc=row.querySelector("td:first-child").textContent.trim();
    var costText=row.querySelector("td:nth-child(2)").textContent.trim();
    var cost=parseFloat(costText.replace(/[^0-9.]/g,""))||0;
    var editRow=document.createElement("tr");editRow.id="svcEditForm_"+id;
    editRow.innerHTML=\'<td colspan="4" style="padding:10px 8px;background:#f8fafc;"><div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;"><div style="flex:2;min-width:160px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label><input type="text" id="editSvcDesc_\'+id+\'" value="\'+desc.replace(/\\\'/g,"&#39;")+\'" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="flex:1;min-width:90px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label><input type="number" id="editSvcCost_\'+id+\'" value="\'+cost+\'" step="0.01" style="width:100%;height:34px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;"></div><div style="display:flex;gap:6px;"><button type="button" onclick="saveEditSvc(\'+id+\')" style="height:34px;background:#ea580c;border:none;color:#fff;border-radius:6px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;">Save</button><button type="button" onclick="cancelEditSvc(\'+id+\')" style="height:34px;background:#f1f5f9;border:none;color:#64748b;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;">Cancel</button></div></div></td>\';
    row.parentNode.insertBefore(editRow,row.nextSibling);
}
function saveEditSvc(id){
    var newDesc=document.getElementById("editSvcDesc_"+id).value.trim();
    var newCost=document.getElementById("editSvcCost_"+id).value;
    if(!newDesc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $csrf . '",_method:"PUT",description:newDesc,cost:newCost,service_type:"service"},
        success:function(){var row=document.getElementById("svcEditForm_"+id);if(row){var prev=row.previousElementSibling;if(prev){prev.querySelector("td:first-child").textContent=newDesc;prev.querySelector("td:nth-child(2)").innerHTML=parseFloat(newCost||0).toFixed(2)+" JOD";}}cancelEditSvc(id);showToast("Service updated!","success");},
        error:function(){showToast("Error updating service","error");}
    });
}
function cancelEditSvc(id){var f=document.getElementById("svcEditForm_"+id);if(f)f.remove();}
function delSvc(id,desc){
    if(!confirm("Delete service: "+desc+"?"))return;
    $.ajax({url:"/admin/services/"+id,type:"POST",
        data:{_token:"' . $csrf . '",_method:"DELETE",service_type:"service"},
        success:function(){var btn=document.querySelector("button[onclick*=\\"delSvc("+id+",\\"]");if(btn){var row=btn.closest("tr");if(row)row.remove();}showToast("Service deleted!","success");},
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

        $imgPath   = $service->image ?? '';
        $desc      = htmlspecialchars($service->description ?? '');
        $sid       = $service->id;
        $countryId = $service->country ?? 123;

        if (!$service->relationLoaded('serviceCategory')) {
            $service->load('serviceCategory.parent.parent.parent');
        }

        $arrival     = $service->arrival;
        $accType     = $service->acc_type;
        $accCategory = $service->acc_category;

        if ($service->serviceCategory) {
            $cat   = $service->serviceCategory;
            $chain = [];
            $walker = $cat->parent ?? null;
            while ($walker) { $chain[] = $walker; $walker = $walker->parent ?? null; }
            if (!$arrival && isset($chain[0])) { $arrival = $chain[0]->name; }
            $typeMap = ['Hotels'=>'Hotel','Camps'=>'Camp','Homestay'=>'Guesthouse','Homestays'=>'Guesthouse','Mobile Camp'=>'Camp','Wild Jordan RSCN'=>'Eco-lodge'];
            $starMap = ['1 Star'=>'1 ★','2 Star'=>'2 ★★','3 Star'=>'3 ★★★','4 Stars'=>'4 ★★★★','5 Stars'=>'5 ★★★★★'];
            foreach ($chain as $node) {
                if (!$accType && isset($typeMap[$node->name])) { $accType = $typeMap[$node->name]; }
                if (!$accCategory && isset($starMap[$node->name])) { $accCategory = $starMap[$node->name]; }
            }
        }

        // Header
        $html  = '<script>';
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
            $bg     = $active ? '#ea580c' : 'transparent';
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
        if ($imgPath) { $d = @json_decode($imgPath, true); $existingImages = is_array($d) ? $d : [$imgPath]; }
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
        $accTypes = ['Hotel','Guesthouse','Hostel','Resort','Apartment','Camp','Eco-lodge','Riad','Villa'];
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
        $cats = ['1 ★','2 ★★','3 ★★★','4 ★★★★','5 ★★★★★','Standard','Superior','Luxury'];
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

        $imgPath   = $service->image ?? '';
        $desc      = htmlspecialchars($service->description ?? '');
        $sid       = $service->id;
        $countryId = $service->country ?? 123;

        if (!$service->relationLoaded('serviceCategory')) {
            $service->load('serviceCategory.parent.parent.parent');
        }

        $arrival     = $service->arrival;
        $accType     = $service->acc_type;
        $accCategory = $service->acc_category;

        if ($service->serviceCategory) {
            $cat   = $service->serviceCategory;
            $chain = [];
            $walker = $cat->parent ?? null;
            while ($walker) { $chain[] = $walker; $walker = $walker->parent ?? null; }
            if (!$arrival && isset($chain[0])) { $arrival = $chain[0]->name; }
            $typeMap = ['Hotels'=>'Hotel','Camps'=>'Camp','Homestay'=>'Guesthouse','Homestays'=>'Guesthouse','Mobile Camp'=>'Camp','Wild Jordan RSCN'=>'Eco-lodge'];
            $starMap = ['1 Star'=>'1 ★','2 Star'=>'2 ★★','3 Star'=>'3 ★★★','4 Stars'=>'4 ★★★★','5 Stars'=>'5 ★★★★★'];
            foreach ($chain as $node) {
                if (!$accType && isset($typeMap[$node->name])) { $accType = $typeMap[$node->name]; }
                if (!$accCategory && isset($starMap[$node->name])) { $accCategory = $starMap[$node->name]; }
            }
        }

        // Header
        $html  = '<script>';
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
            $bg     = $active ? '#ea580c' : 'transparent';
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
        if ($imgPath) { $d = @json_decode($imgPath, true); $existingImages = is_array($d) ? $d : [$imgPath]; }
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

        $accTypes = ['Hotel','Guesthouse','Hostel','Resort','Apartment','Camp','Eco-lodge','Riad','Villa'];
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Accommodation type</legend>';
        $html .= '<select name="acc_type" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;">';
        $html .= '<option value="">Select a type of accommodation</option>';
        foreach ($accTypes as $t) {
            $sel = ($accType === $t) ? ' selected' : '';
            $html .= '<option value="' . $t . '"' . $sel . '>' . $t . '</option>';
        }
        $html .= '</select></fieldset>';

        $cats = ['1 ★','2 ★★','3 ★★★','4 ★★★★','5 ★★★★★','Standard','Superior','Luxury'];
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
        $html .= 'document.getElementById("libModalHead").innerHTML=\'';
        $html .= '<h3>Modify restaurant</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editRestForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';';
        $html .= '</script>';

        $html .= '<form id="editRestForm" onsubmit="submitEditRestaurant(' . $sid . '); return false;">';
        $html .= csrf_field();
        $html .= '<input type="hidden" name="service_type" value="restaurant">';

        // Flags
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        $html .= '</div>';

        // Photos
        $existingImages = [];
        if ($imgPath) { $d = @json_decode($imgPath, true); $existingImages = is_array($d) ? $d : [$imgPath]; }
        $html .= '<div style="margin-bottom:16px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#555;">Photos:</span>';
        $html .= '<a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editRestImageInput" accept="image/*" multiple style="display:none" onchange="addActSecImages(this)">';
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
        $html .= '<input type="text" name="description" required style="width:100%;height:32px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="'.$desc.'">';
        $html .= '</fieldset>';

        $html .= '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend>';
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:32px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="'.$arrival.'" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        $html .= '<textarea name="notes" style="width:100%;min-height:250px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description">'.$notes.'</textarea>';
        $html .= '</fieldset>';

        $html .= '<input type="hidden" name="cost" value="' . ($service->cost ?? 0) . '">';
        $html .= '<input type="hidden" name="category" value="' . ($service->category ?? '') . '">';
        $html .= '</form>';

        return response()->json(['html' => $html]);
    }

private function editTransportModal($service)
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

        $desc = htmlspecialchars($service->description ?? '');
        $sid = $service->id;
        $method = $service->transport_method ?? '';
        $depLoc = htmlspecialchars($service->departure_location ?? '');
        $arrDest = htmlspecialchars($service->arrival_destination ?? '');
        $lengthTime = htmlspecialchars($service->length_time ?? '');
        $distKm = htmlspecialchars($service->distance_km ?? '');
        $notes = htmlspecialchars($service->notes ?? '');

        // Auto-derive from title if empty
        if (empty($depLoc) && empty($arrDest) && !empty($service->description)) {
            $parts = explode('/', $service->description);
            if (count($parts) >= 2) {
                $depLoc = htmlspecialchars(trim($parts[0]));
                $arrDest = htmlspecialchars(trim(end($parts)));
            } else {
                $parts = explode('-', $service->description);
                if (count($parts) >= 2) {
                    $depLoc = htmlspecialchars(trim($parts[0]));
                    $arrDest = htmlspecialchars(trim(end($parts)));
                }
            }
        }

        // Auto-derive method if empty
        if (empty($method)) {
            $lowerDesc = strtolower($service->description ?? '');
            if (str_contains($lowerDesc, 'bus')) {
                $method = 'Bus';
            } elseif (str_contains($lowerDesc, 'plane') || str_contains($lowerDesc, 'flight')) {
                $method = 'Airplane';
            } elseif (str_contains($lowerDesc, 'boat') || str_contains($lowerDesc, 'ship') || str_contains($lowerDesc, 'ferry')) {
                $method = 'Boat';
            } elseif (str_contains($lowerDesc, 'train')) {
                $method = 'Train';
            } else {
                $method = 'Car';
            }
        }

        // Header: Modify transport + Cancel/Save
        $html = '<script>';
        $html .= 'document.getElementById("libModalHead").innerHTML=\'';
        $html .= '<h3>Modify transport</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editTransForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';';
        $html .= '</script>';

        $html .= '<form id="editTransForm" onsubmit="submitEditTransport(' . $sid . '); return false;">';
        $html .= csrf_field();

        // Language flags
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        $html .= '</div>';

        // Two-column: Method of transport + Place of interest
        $html .= '<div style="display:flex;gap:24px;margin-bottom:20px;">';

        // LEFT: Method of transport
        $methods = [
            ['key' => 'Bus', 'icon' => 'fa-bus'],
            ['key' => 'Airplane', 'icon' => 'fa-plane'],
            ['key' => 'Car', 'icon' => 'fa-car'],
            ['key' => 'Boat', 'icon' => 'fa-ship'],
            ['key' => 'Train', 'icon' => 'fa-train'],
        ];
        $html .= '<div style="flex:1;">';
        $html .= '<div style="font-size:12px;font-weight:700;color:#555;margin-bottom:10px;">Method of transport</div>';
        $html .= '<div style="display:flex;gap:8px;">';
        foreach ($methods as $m) {
            $isActive = ($method === $m['key']);
            $bdr = $isActive ? '2px solid #ea580c' : '1px solid #ddd';
            $bgc = $isActive ? '#ffedd5' : '#fff';
            $clr = $isActive ? '#ea580c' : '#888';
            $html .= '<label style="display:flex;flex-direction:column;align-items:center;gap:4px;cursor:pointer;padding:10px 12px;border-radius:8px;border:' . $bdr . ';background:' . $bgc . ';min-width:55px;">';
            $html .= '<input type="radio" name="transport_method" value="' . $m['key'] . '"' . ($isActive ? ' checked' : '') . ' style="display:none" onchange="selectTransMethod(this)">';
            $html .= '<i class="fa ' . $m['icon'] . '" style="font-size:22px;color:' . $clr . '"></i>';
            $html .= '<span style="font-size:10px;font-weight:600;color:' . $clr . '">' . $m['key'] . '</span>';
            $html .= '</label>';
        }
        $html .= '</div></div>';

        // RIGHT: Place of interest
        $html .= '<div style="flex:1;">';
        $html .= '<div style="font-size:12px;font-weight:700;color:#555;margin-bottom:10px;">Place of interest</div>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 12px 0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Departure location</legend>';
        $html .= '<input type="text" name="departure_location" style="width:100%;height:36px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $depLoc . '">';
        $html .= '</fieldset>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Arrival destination</legend>';
        $html .= '<input type="text" name="arrival_destination" style="width:100%;height:36px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $arrDest . '">';
        $html .= '</fieldset>';
        $html .= '</div>';

        $html .= '</div>';

        // Transport title
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 4px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Transport title</legend>';
        $html .= '<input type="text" name="description" required maxlength="255" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $desc . '" oninput="document.getElementById(\'transTitleCount\').textContent=\'(\'+this.value.length+\'/255)\'">';
        $html .= '<div id="transTitleCount" style="position:absolute;right:4px;bottom:-16px;font-size:10px;color:#bbb;">(' . strlen($service->description ?? '') . '/255)</div>';
        $html .= '</fieldset>';

        // Length + Distance side by side
        $html .= '<div style="display:flex;gap:16px;margin:16px 0;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;flex:1;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Length</legend>';
        $html .= '<input type="text" name="length_time" placeholder="00:00" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $lengthTime . '">';
        $html .= '</fieldset>';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;flex:1;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Distance (km)</legend>';
        $html .= '<input type="text" name="distance_km" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . $distKm . '">';
        $html .= '</fieldset>';
        $html .= '</div>';

        // Description
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        $html .= '<textarea name="notes" style="width:100%;min-height:140px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description">' . $notes . '</textarea>';
        $html .= '</fieldset>';

        $html .= '<input type="hidden" name="cost" value="' . $service->cost . '">';
        $html .= '<input type="hidden" name="category" value="' . $service->category . '">';
        $html .= '</form>';

        return response()->json(['html' => $html]);
    }

    private function editActivityModal($service)
    {
        $flags = [['emoji'=>'🇫🇷','code'=>'fr'],['emoji'=>'🇬🇧','code'=>'en'],['emoji'=>'🇮🇹','code'=>'it'],['emoji'=>'🇪🇸','code'=>'es'],['emoji'=>'🇩🇪','code'=>'de'],['emoji'=>'🇸🇪','code'=>'se'],['emoji'=>'🇳🇱','code'=>'nl']];
        $imgPath = $service->image ?? '';
        $desc = htmlspecialchars($service->description ?? '');
        $sid = $service->id;
        $arrival = htmlspecialchars($service->arrival ?? '');
        $notes = htmlspecialchars($service->notes ?? '');

        $html = '<script>document.getElementById("libModalHead").innerHTML=\'<h3>Modify activity</h3><div style="display:flex;gap:10px;align-items:center"><a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a><button form="editActForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button></div>\';</script>';
        $html .= '<form id="editActForm" onsubmit="submitEditActivity(' . $sid . '); return false;" enctype="multipart/form-data">' . csrf_field();

        // Flags
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) { $a=($f['code']==='en'); $html .= '<div style="width:40px;height:32px;border-radius:6px;border:'.($a?'2px solid #ea580c':'2px solid transparent').';background:'.($a?'#ea580c':'transparent').';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">'.$f['emoji'].'</div>'; }
        $html .= '</div>';

        // Photos
        $existingImages = [];
        if ($imgPath) { $d = @json_decode($imgPath, true); $existingImages = is_array($d) ? $d : [$imgPath]; }
        $html .= '<div style="margin-bottom:16px;"><div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;"><span style="font-size:11px;font-weight:700;color:#555;">Photos:</span><a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a></div>';
        $html .= '<input type="file" name="new_images[]" id="editActImageInput" accept="image/*" multiple style="display:none" onchange="addActImages(this)">';
        $html .= '<div id="actPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">';
        foreach ($existingImages as $img) {
            $u = (str_starts_with($img,'http')) ? $img : '/'.ltrim($img,'/');
            $html .= '<div style="position:relative;flex-shrink:0;height:104px;"><img src="'.$u.'" style="height:100%;border-radius:4px;object-fit:cover;"><input type="hidden" name="existing_images[]" value="'.htmlspecialchars($img).'"><button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button></div>';
        }
        $html .= '<div onclick="document.getElementById(\'editActImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">📷</div>';
        $html .= '</div></div>';

        // Activity name + Place of interest
        $html .= '<div style="display:flex;gap:16px;margin-bottom:4px;">';
        $html .= '<div style="flex:1;"><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;position:relative;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Activity name</legend><input type="text" name="description" required maxlength="255" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="'.$desc.'" oninput="document.getElementById(\'actNameCount\').textContent=\'(\'+this.value.length+\'/255)\'"><div id="actNameCount" style="position:absolute;right:4px;bottom:-16px;font-size:10px;color:#bbb;">('.strlen($service->description ?? '').'/255)</div></fieldset></div>';
        $html .= '<div style="flex:1;"><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;position:relative;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend><div style="display:flex;align-items:center;padding:0 12px;"><i class="fa fa-map-marker" style="color:#aaa;margin-right:8px;"></i><input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;font-size:13px;background:transparent;" placeholder="Add a destination" value="'.$arrival.'" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)"></div><div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div></fieldset></div>';
        $html .= '</div>';

        // Description
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:16px 0 0 0;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend><textarea name="notes" style="width:100%;min-height:160px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description">'.$notes.'</textarea></fieldset>';
        $html .= '<input type="hidden" name="cost" value="'.$service->cost.'"><input type="hidden" name="category" value="'.$service->category.'">';
        $html .= '</form>';

        // ACTIVITIES LIST at bottom — filtered by same vendor as this accommodation
        $catId = $service->category;
        $activityQuery = Activity::where('category', $catId)->with('venderUser')->orderByDesc('id');
        if ($service->vender) {
            $activityQuery->where('vender', $service->vender);
        }
        $activityItems = $activityQuery->get();
        if ($activityItems->count() > 0) {
            $html .= '<div style="margin-top:24px;border-top:2px solid #eee;padding-top:16px;">';
            $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">';
            $html .= '<span style="font-size:10px;font-weight:800;color:#dc2626;letter-spacing:1px;">🏃 ACTIVITIES LIST</span>';
            $html .= '</div>';
            $html .= '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
            $html .= '<thead><tr style="border-bottom:1px solid #eee;">';
            $html .= '<th style="text-align:left;padding:8px 6px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">DESCRIPTION</th>';
            $html .= '<th style="text-align:left;padding:8px 6px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">COST</th>';
            $html .= '<th style="text-align:left;padding:8px 6px;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">VENDOR</th>';
            $html .= '</tr></thead><tbody>';
            foreach ($activityItems as $act) {
                $vendorName = '-';
                if ($act->venderUser) {
                    $vendorName = !empty($act->venderUser->company)
                        ? strtoupper($act->venderUser->company)
                        : strtoupper($act->venderUser->first_name . ' ' . $act->venderUser->last_name);
                }
                $actDesc = htmlspecialchars($act->description ?? '-');
                $actCost = number_format($act->cost ?? 0, 2);
                $html .= '<tr style="border-bottom:1px solid #f5f5f5;">';
                $html .= '<td style="padding:10px 6px;color:#1e293b;font-weight:600;">' . $actDesc . '</td>';
                $html .= '<td style="padding:10px 6px;font-weight:700;color:#ea580c;white-space:nowrap;">' . $actCost . ' JOD</td>';
                $html .= '<td style="padding:10px 6px;color:#1e293b;">' . $vendorName . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table></div>';
        }

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

        $sid       = $service->id;
        $countryId = $service->country ?? 0;
        $desc      = htmlspecialchars($service->description ?? '');
        $imgPath   = $service->image ?? '';
        $arrival   = $service->arrival ?? '';
        $accType   = $service->acc_type ?? '';
        $accCat    = $service->acc_category ?? '';

        // Auto-detect Place from category chain (same as Transport Hotel)
        if (!$service->relationLoaded('serviceCategory')) {
            $service->load('serviceCategory.parent.parent.parent');
        }
        if (!$arrival && $service->serviceCategory) {
            $chain = [];
            $walker = $service->serviceCategory->parent ?? null;
            while ($walker) { $chain[] = $walker; $walker = $walker->parent ?? null; }
            if (isset($chain[0])) { $arrival = $chain[0]->name; }
        }

        if (!$service->relationLoaded('venderUser')) $service->load('venderUser');
        $vendorName = $service->venderUser
            ? (!empty($service->venderUser->company) ? strtoupper($service->venderUser->company) : strtoupper($service->venderUser->first_name . ' ' . $service->venderUser->last_name))
            : strtoupper($service->description ?? '');

        // Header
        $html  = '<script>';
        $html .= 'document.getElementById("libModalHead").innerHTML=\'';
        $html .= '<h3>Modify Guides</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a>';
        $html .= '<button form="editGuideSecForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';';
        $html .= '</script>';

        $html .= '<form id="editGuideSecForm" onsubmit="submitEditGuideSection(' . $sid . '); return false;" enctype="multipart/form-data">';
        $html .= csrf_field();

        // Flags + vendor bar
        $html .= '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">';
        foreach ($flags as $f) {
            $active = ($f['code'] === 'en');
            $bg     = $active ? '#ea580c' : 'transparent';
            $border = $active ? '2px solid #ea580c' : '2px solid transparent';
            $html .= '<div style="width:40px;height:32px;border-radius:6px;border:' . $border . ';background:' . $bg . ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">' . $f['emoji'] . '</div>';
        }
        $html .= '<div style="margin-left:auto;display:flex;gap:16px;align-items:center;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:6px 14px;font-size:12px;">';
        $html .= '<span><strong>Vendor Name:</strong> ' . htmlspecialchars($vendorName) . '</span>';
        $html .= '<span style="color:#ccc;">|</span>';
        $html .= '<span><strong>Vendor Price:</strong> <span style="color:#ea580c;font-weight:700;">' . number_format($service->cost ?? 0, 2) . ' JOD</span></span>';
        $html .= '</div>';
        $html .= '</div>';

        // Photos section
        $existingImages = [];
        if ($imgPath) { $d = @json_decode($imgPath, true); $existingImages = is_array($d) ? $d : [$imgPath]; }
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
        if ($service->vender) { $guideQuery->where('vender', $service->vender); }
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
        foreach (['Day Guide','Half Day Guide','Full Day Guide','Multi-Day Guide','City Tour Guide','Driver Guide','Local Guide'] as $gt) {
            $html .= '<option value="' . $gt . '">' . $gt . '</option>';
        }
        $html .= '</select></div>';
        $html .= '<div style="flex:1;min-width:120px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Guide Category</label>';
        $html .= '<select id="newGuideCat" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 8px;font-size:12px;background:#fff;color:#555;">';
        $html .= '<option value="">-- Category --</option>';
        foreach (['Licensed','Local','Expert','Senior','Specialist','General'] as $gc) {
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
        $html .= '</tr></thead><tbody>';
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
        success:function(){closeModal();showToast("Guide saved!","success");},
        error:function(){showToast("Error saving guide","error");}
    });
}
function toggleGuideAddForm(){var f=document.getElementById("guideAddSvcForm");f.style.display=(f.style.display==="none"?"": "none");}
function quickAddGuide(sid,vender,category,country,token){
    var desc=document.getElementById("newGuideDesc").value.trim();
    var cost=document.getElementById("newGuideCost").value||0;
    var gtype=document.getElementById("newGuideType").value;
    var gcat=document.getElementById("newGuideCat").value;
    if(!desc){alert("Please enter a description.");return;}
    $.ajax({url:"/admin/guides/quick-add",type:"POST",
        data:{_token:token,description:desc,cost:cost,vender:vender,category:category,country:country,acc_type:gtype,acc_category:gcat},
        success:function(r){if(r.success){document.getElementById("newGuideDesc").value="";document.getElementById("newGuideCost").value="0.00";document.getElementById("newGuideType").value="";document.getElementById("newGuideCat").value="";toggleGuideAddForm();showToast("Guide added!","success");}},
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
        if ($request->has('notes')) { $data['notes'] = $request->input('notes'); }
        if ($request->has('acc_type')) { $data['acc_type'] = $request->input('acc_type'); }
        if ($request->has('acc_category')) { $data['acc_category'] = $request->input('acc_category'); }
        if ($request->has('website')) { $data['website'] = $request->input('website'); }
        if ($request->has('arrival')) { $data['arrival'] = $request->input('arrival'); }
        if ($request->has('transport_method')) { $data['transport_method'] = $request->input('transport_method'); }
        if ($request->has('departure_location')) { $data['departure_location'] = $request->input('departure_location'); }
        if ($request->has('arrival_destination')) { $data['arrival_destination'] = $request->input('arrival_destination'); }
        if ($request->has('length_time')) { $data['length_time'] = $request->input('length_time'); }
        if ($request->has('distance_km')) { $data['distance_km'] = $request->input('distance_km'); }

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

        $country  = $service->country  ?? null;
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
            ->when($request->filled('country'), function($q) use($request) {
                return $q->where('country', $request->country);
            })
            ->when($request->filled('email'), function($q) use($request) {
                return $q->where('email', 'like', '%' . $request->email . '%');
            })
            ->when($request->filled('first_name'), function($q) use($request) {
                return $q->where('first_name', 'like', '%' . $request->first_name . '%');
            })
            ->when($request->filled('last_name'), function($q) use($request) {
                return $q->where('last_name', 'like', '%' . $request->last_name . '%');
            })
            ->when($request->filled('company'), function($q) use($request) {
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
                    $vendorPrice = number_format((float)$balanceRow->balance, 2) . ' JOD';
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
        $html .= '<textarea name="notes" style="width:100%;min-height:160px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description">' . htmlspecialchars((string)($category->description ?? '')) . '</textarea>';
        $html .= '</fieldset>';

        $html .= '</div>';

        // RIGHT column
        $html .= '<div style="flex:1;">';
        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;width:auto;border-bottom:none;margin-bottom:0;line-height:1;">Place Of Interest</legend>';
        $arrivalValue = $category->arrival ?: ($category->parent ? $category->parent->name : '');
        $html .= '<input type="text" id="editAccArrivalInput" name="arrival" autocomplete="off" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Add a destination" value="' . htmlspecialchars((string)($arrivalValue)) . '" oninput="libAccAutocomplete(this.value)" onkeydown="libAccInputKey(event)">';
        $html .= '<div id="editAccArrivalDropdown" style="display:none;position:absolute;left:0;right:0;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;"></div>';
        $html .= '</fieldset>';

        $html .= '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;">';
        $html .= '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;width:auto;border-bottom:none;margin-bottom:0;line-height:1;">Accommodation Type</legend>';
        
        $accType = 'Hotel';
        if ($category->parent && $category->parent->parent && $category->parent->parent->parent) {
            $rootName = $category->parent->parent->parent->name;
            if (stripos($rootName, 'camp') !== false) $accType = 'Camp';
            elseif (stripos($rootName, 'homestay') !== false) $accType = 'Homestay';
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
                for($i=0; $i<$m[1]; $i++) $starRating .= '★';
            }
        }
        $cats = ['1 ★','2 ★★','3 ★★★','4 ★★★★','5 ★★★★★'];
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
        $html .= '<input type="text" name="website" placeholder="e.g. https://www.example.com" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" value="' . htmlspecialchars((string)($category->website ?? '')) . '">';
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
        $newArrival = trim((string)$request->input('arrival', ''));
        
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
        $season->date_to   = $request->input('date_to');
        $season->cost      = floatval($request->input('cost', 0));
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
            if($e->invoice) {
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
            $cannedDays = \App\Models\TourCannedDay::with(['contents' => function($q) {
                $q->where('lang', 'en');
            }])->orderByDesc('id')->limit(5)->get();

            // Only show these specific categories (404 Hotels is now a sub-category of 403 Accommodations)
            $allowedCatIds = [403, 93, 715, 456, 527];
            $rootCategories = ServiceCategory::where('country_id', $countryId)
                ->whereIn('id', $allowedCatIds)
                ->get();

            // Clean display names
            $displayNames = [
                403 => 'Accommodations',
                93  => 'Activities',
                715 => 'Transportation',
                456 => 'Restaurants',
            ];

            // Display order
            $rootCategories = $rootCategories->sortBy(function($cat) {
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
                        'category'      => $rootCat,
                        'services'      => $services,
                        'total'         => $totalCount,
                        'subCategories' => $subCategories,
                        'type'          => $typeMap[$rootCat->id] ?? 'service',
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
                if (str_contains($starLabel, '4 Star')) $starLabel = '4 Star';
                if (str_contains($starLabel, '5 Star')) $starLabel = '5 Star';

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
                usort($hotelsInStar, function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });

                $hotelsByStar[$starLabel] = $hotelsInStar;
            }

        }

        $totalDays = \App\Models\TourCannedDay::count();

        return view('admin.services.library', compact(
            'countries', 'countryId', 'groupedServices', 'rootCategories', 'cannedDays', 'totalDays', 'hotelsByStar'
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
            ->where(function($q) use ($pattern1, $pattern2) {
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

        return response()->json(['services' => $vendors->map(function($v) {
            return ['id' => $v->id, 'name' => $v->name];
        })]);
    }

    private function getLeafNodes($parentId, &$leafNodes, &$visited = []) {
        if (in_array($parentId, $visited)) return;
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
            'cost'        => 'nullable|numeric|min:0',
            'category'    => 'required|integer',
            'country'     => 'nullable|integer',
            'vender'      => 'nullable|integer',
        ]);

        $svc = new \App\Models\Service();
        $svc->description = $request->input('description');
        $svc->cost        = $request->input('cost', 0);
        $svc->category    = $request->input('category');
        $svc->country     = $request->input('country', 123);
        $svc->vender      = $request->input('vender') ?: null;
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
            'cost'        => 'nullable|numeric|min:0',
            'country'     => 'nullable|integer',
            'vender'      => 'nullable|integer',
        ]);

        $tr = new \App\Models\Transport();
        $tr->description = $request->input('description');
        $tr->cost        = $request->input('cost', 0);
        $tr->country     = $request->input('country', 123);
        $tr->vender      = $request->input('vender') ?: null;
        $tr->save();

        return response()->json(['success' => true, 'id' => $tr->id]);
    }

    public function quickAddGuide(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'cost'        => 'nullable|numeric|min:0',
            'country'     => 'nullable|integer',
            'vender'      => 'nullable|integer',
            'category'    => 'nullable|integer',
        ]);

        $svc = new Service();
        $svc->description = $request->input('description');
        $svc->cost        = $request->input('cost', 0);
        $svc->country     = $request->input('country', 123);
        $svc->vender      = $request->input('vender') ?: null;
        $svc->category    = $request->input('category') ?: null;
        $svc->acc_type    = $request->input('acc_type') ?: null;
        $svc->acc_category = $request->input('acc_category') ?: null;
        $svc->save();

        return response()->json(['success' => true, 'id' => $svc->id]);
    }

    public function quickAddActivity(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'cost'        => 'nullable|numeric|min:0',
            'country'     => 'nullable|integer',
            'vender'      => 'nullable|integer',
            'category'    => 'nullable|integer',
        ]);

        $act = new Activity();
        $act->description  = $request->input('description');
        $act->cost         = $request->input('cost', 0);
        $act->country      = $request->input('country', 123);
        $act->vender       = $request->input('vender') ?: null;
        $act->category     = $request->input('category') ?: null;
        $act->acc_type     = $request->input('acc_type') ?: null;
        $act->acc_category = $request->input('acc_category') ?: null;
        $act->save();

        return response()->json(['success' => true, 'id' => $act->id]);
    }
    public function quickAddRestaurant(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'cost'        => 'nullable|numeric|min:0',
            'country'     => 'nullable|integer',
            'vender'      => 'nullable|integer',
            'category'    => 'nullable|integer',
        ]);

        $rest = new \App\Models\Restaurant();
        $rest->description = $request->input('description');
        $rest->cost        = $request->input('cost', 0);
        $rest->country     = $request->input('country', 123);
        $rest->vender      = $request->input('vender') ?: null;
        $rest->category    = $request->input('category') ?: null;
        $rest->save();

        return response()->json(['success' => true, 'id' => $rest->id]);
    }

    /**
     * AJAX: Return all canned days as HTML table for the Days library tab
     */
    public function libraryDays(Request $request)
    {
        $search = trim($request->input('search', ''));
        $days   = \App\Models\TourCannedDay::with('contents')->get();

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
            $title     = $enContent && trim($enContent->title) !== '' ? trim($enContent->title) : '(No title)';

            if ($search && stripos($title, $search) === false) { $index++; continue; }

            // Build background
            $images = @unserialize($day->images);
            if (!is_array($images)) $images = [];
            $firstImage = collect($images)->filter()->first();
            $imageUrl   = '';
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
                : (str_contains($tl, 'amman')   ? 'Amman'
                : (str_contains($tl, 'dead sea') ? 'Dead Sea'
                : (str_contains($tl, 'wadi rum') ? 'Wadi Rum'
                : (str_contains($tl, 'aqaba')    ? 'Aqaba' : 'Jordan'))));

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
                    if (!$walker->parent_id) break;
                    $walker = ServiceCategory::find($walker->parent_id);
                    if ($walker) $serviceTypeRootId = $walker->id;
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
                        'category'      => $rootCat,
                        'services'      => $services,
                        'total'         => $totalCount,
                        'subCategories' => $subCategories,
                        'type'          => $svcType,
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
                        'category'      => $rootCat,
                        'services'      => $services,
                        'total'         => $totalCount,
                        'subCategories' => $subCategories,
                        'type'          => $svcTypeMap[$rootCat->id] ?? 'service',
                    ];
                }
            }
        }
        // Canned days (show when no category filter or 'days' filter)
        $cannedDays = collect();
        $totalDays = 0;
        if (!$categoryFilter || $categoryFilter === 'days') {
            $daysQuery = \App\Models\TourCannedDay::with(['contents' => function($q) {
                $q->where('lang', 'en');
            }]);
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
        if (in_array($parentId, $visited)) return $ids;
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
                ->where(function($q) use ($pattern1, $pattern2) {
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
            if (!$id || !$category) return response()->json(['html' => '']);
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
}
