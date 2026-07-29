<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourCannedDay;
use App\Models\TourCannedDayContent;
use App\Models\TripItineraryDay;
use Illuminate\Http\Request;

class CannedDayController extends Controller
{
    public function index()
    {
        $cannedDays = TourCannedDay::with('contents')->get();
        return view('admin.canned-days.index', compact('cannedDays'));
    }

    public function create()
    {
        return view('admin.canned-days.create');
    }

    public function store(Request $request)
    {
        $langs = ['en','fr','it','es','Ar','ge','pt'];

        // Images
        $images = [];
        if ($request->has('dimages')) {
            foreach ($request->dimages as $k => $v) {
                $images[$k] = $v;
            }
        }

        // Expenses
        $expenses = [];
        if ($request->has('expenses') && is_array($request->expenses)) {
            foreach ($request->expenses as $k => $v) {
                $expenses[] = ['id' => intval($v), 'desc' => $request->expenses_name[$k] ?? ''];
            }
        }

        // Inclusions
        $included = [];
        if ($request->has('day_inc_0') && is_array($request->day_inc_0)) {
            foreach ($request->day_inc_0 as $k => $v) {
                $included[intval($k)] = $v;
            }
        }
        $excluded = [];
        if ($request->has('day_exc_0') && is_array($request->day_exc_0)) {
            foreach ($request->day_exc_0 as $k => $v) {
                $excluded[intval($k)] = $v;
            }
        }

        $cannedDay = TourCannedDay::create([
            'images' => serialize($images),
            'expenses' => serialize($expenses),
            'included' => serialize($included),
            'excluded' => serialize($excluded),
        ]);

        // Save contents per language
        foreach ($langs as $L) {
            $title = trim(strval($request->input('title_' . $L, '')));
            if ($title !== '') {
                TourCannedDayContent::create([
                    'day_id' => $cannedDay->id,
                    'lang' => $L,
                    'title' => $title,
                    'description' => strval($request->input('description_' . $L, '')),
                ]);
            }
        }

        return redirect()->route('admin.canned-days.edit', $cannedDay->id)->with('success', 'Created successfully');
    }

    /**
     * AJAX store from Library modal
     */
    public function storeAjax(Request $request)
    {
        // Handle image upload
        $images = [];
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/services'), $filename);
                $images[] = 'uploads/services/' . $filename;
            }
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/services'), $filename);
            $images[] = 'uploads/services/' . $filename;
        }

        // Expenses
        $expenses = [];
        $expIds = $request->input('create_expenses_id', []);
        $expDescs = $request->input('create_expenses_desc', []);
        foreach ($expIds as $k => $eId) {
            $expenses[] = ['id' => intval($eId), 'desc' => $expDescs[$k] ?? ''];
        }

        // Inclusions
        $included = [];
        $modalIncluded = $request->input('create_included', []);
        foreach ($modalIncluded as $k => $v) {
            $included[intval($k)] = $v;
        }
        $excluded = [];
        $modalExcluded = $request->input('create_excluded', []);
        foreach ($modalExcluded as $k => $v) {
            $excluded[intval($k)] = $v;
        }

        $cannedDay = TourCannedDay::create([
            'images' => serialize($images),
            'expenses' => serialize($expenses),
            'included' => serialize($included),
            'excluded' => serialize($excluded),
        ]);

        // Save English content - use 'title' field first, fallback to 'description' for backward compat
        $title = $request->input('title', '') ?: $request->input('description', '');
        $description = $request->input('description', '') ?: $request->input('notes', '');
        TourCannedDayContent::create([
            'day_id' => $cannedDay->id,
            'lang' => 'en',
            'title' => $title,
            'description' => $description,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('admin.canned-days.edit', $cannedDay->id)->with('success', 'Created successfully');
    }

    public function edit($id)
    {
        $cannedDay = TourCannedDay::with('contents')->findOrFail($id);
        return view('admin.canned-days.edit', compact('cannedDay'));
    }

    public function update(Request $request, $id)
    {
        $cannedDay = TourCannedDay::findOrFail($id);
        $langs = ['en','fr','it','es','Ar','ge','pt'];

        // Save images
        $images = [];
        if ($request->has('dimages')) {
            foreach ($request->dimages as $k => $v) {
                $images[$k] = $v;
            }
        }

        // Save expenses
        $expenses = [];
        if ($request->has('expenses') && is_array($request->expenses)) {
            foreach ($request->expenses as $k => $v) {
                $expenses[] = ['id' => intval($v), 'desc' => $request->expenses_name[$k] ?? ''];
            }
        }

        // Save inclusions
        $included = [];
        if ($request->has('day_inc_0') && is_array($request->day_inc_0)) {
            foreach ($request->day_inc_0 as $k => $v) {
                $included[intval($k)] = $v;
            }
        }
        $excluded = [];
        if ($request->has('day_exc_0') && is_array($request->day_exc_0)) {
            foreach ($request->day_exc_0 as $k => $v) {
                $excluded[intval($k)] = $v;
            }
        }

        $cannedDay->update([
            'images' => serialize($images),
            'expenses' => serialize($expenses),
            'included' => serialize($included),
            'excluded' => serialize($excluded),
        ]);

        // Sync photos to all linked trip itinerary days
        $this->syncPhotosToLinkedDays($cannedDay);

        // Save contents per language
        foreach ($langs as $L) {
            $title = trim(strval($request->input('title_' . $L, '')));
            $description = strval($request->input('description_' . $L, ''));
            if ($title !== '' || TourCannedDayContent::where('day_id', $id)->where('lang', $L)->exists()) {
                TourCannedDayContent::updateOrCreate(
                    ['day_id' => $id, 'lang' => $L],
                    [
                        'title' => $title,
                        'description' => $description,
                    ]
                );
            }
        }

        return redirect()->route('admin.canned-days.edit', $id)->with('success', 'Saved successfully');
    }

    public function destroy($id)
    {
        $cannedDay = TourCannedDay::findOrFail($id);
        $cannedDay->contents()->delete();
        $cannedDay->delete();
        return redirect()->route('admin.canned-days.index')->with('success', 'Deleted');
    }

    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * AJAX: Return "Modify day" modal matching canned-days edit UI
     */
    public function editAjax($id)
    {
        $day = TourCannedDay::with('contents')->findOrFail($id);
        $contentsByLang = [];
        foreach ($day->contents as $c) {
            $contentsByLang[$c->lang] = $c;
        }
        $images = @unserialize($day->images);
        if (!is_array($images)) $images = [];

        $langs   = ['en','fr','it','es','Ar','ge','pt'];
        $flagMap = ['en'=>'🇬🇧','fr'=>'🇫🇷','it'=>'🇮🇹','es'=>'🇪🇸','Ar'=>'🇸🇦','ge'=>'🇩🇪','pt'=>'🇵🇹'];

        // Header
        $html  = '<script>document.getElementById("libModalHead").innerHTML=\'';
        $html .= '<h3>Modify Day #' . $id . '</h3>';
        $html .= '<div style="display:flex;gap:10px;align-items:center">';
        $html .= '<a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#1a6b54;text-decoration:none">Cancel</a>';
        $html .= '<button type="button" onclick="submitEditDay(' . $id . ')" style="padding:8px 18px;border-radius:8px;border:none;background:#1a6b54;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Save</button>';
        $html .= '</div>\';</script>';

        $html .= '<form id="editDayForm" enctype="multipart/form-data">' . csrf_field();

        // ── Photos ──
        $html .= '<div style="margin-bottom:20px;">';
        $html .= '<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">';
        $html .= '<span style="font-size:11px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:1px;">📷 Photos</span>';
        $html .= '</div>';
        $html .= '<input type="file" name="new_images[]" id="editDayImageInput" accept="image/*" multiple style="display:none" onchange="addDayImages(this)">';
        $html .= '<div id="dayPhotosRow" style="border:1px dashed #cbd5e0;border-radius:10px;min-height:100px;display:flex;overflow-x:auto;gap:8px;padding:10px;align-items:center;background:#f8fafc;">';
        foreach ($images as $img) {
            if (empty($img)) continue;
            $u = (str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/');
            $u = str_replace('https://pvt.jo', config('app.url'), $u);
            $u = str_replace(' ', '%20', $u); // encode spaces in path
            $html .= '<div style="position:relative;flex-shrink:0;height:88px;">';
            $html .= '<img src="' . $u . '" style="height:100%;border-radius:8px;object-fit:cover;min-width:88px;" onerror="this.style.display=\'none\'">';
            $html .= '<input type="hidden" name="existing_images[]" value="' . htmlspecialchars($img) . '">';
            $html .= '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:3px;right:3px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.65);color:#fff;font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            $html .= '</div>';
        }
        $html .= '<div onclick="document.getElementById(\'editDayImageInput\').click()" style="flex-shrink:0;width:88px;height:88px;border:2px dashed #cbd5e0;border-radius:10px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;color:#a0aec0;font-size:22px;gap:3px;">+<span style="font-size:10px;font-weight:600;">Add</span></div>';
        $html .= '</div></div>';

        // ── Language Tabs ──
        $html .= '<div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:16px;padding:5px;background:#f1f5f9;border-radius:12px;width:fit-content;">';
        foreach ($langs as $i => $L) {
            $flag   = $flagMap[$L] ?? '';
            $active = ($i === 0);
            $style  = $active
                ? 'background:#fff;color:#4f46e5;box-shadow:0 1px 5px rgba(0,0,0,0.12);'
                : 'background:transparent;color:#94a3b8;';
            $html .= '<button type="button" onclick="switchDayLang(\'' . $L . '\')" id="dayLangBtn_' . $L . '" ';
            $html .= 'style="' . $style . 'padding:6px 14px;border:none;border-radius:8px;font-size:11px;font-weight:800;letter-spacing:0.5px;cursor:pointer;transition:all 0.2s;text-transform:uppercase;">';
            $html .= $flag . ' ' . strtoupper($L) . '</button>';
        }
        $html .= '</div>';

        // ── Content per language ──
        foreach ($langs as $i => $L) {
            $c     = $contentsByLang[$L] ?? null;
            $title = $c ? htmlspecialchars($c->title) : '';
            $place = $c ? htmlspecialchars($c->place ?? '') : '';
            $desc  = $c ? ($c->description ?? '') : '';
            $display = ($i === 0) ? '' : 'display:none;';

            $html .= '<div id="dayLang_' . $L . '" style="' . $display . '">';

            // Title
            $html .= '<div style="margin-bottom:14px;">';
            $html .= '<label style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:6px;">Itinerary Title (' . strtoupper($L) . ')</label>';
            $html .= '<input type="text" name="title_' . $L . '" value="' . $title . '" ';
            $html .= 'style="width:100%;padding:11px 16px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:700;outline:none;background:#f8fafc;transition:border-color 0.2s;" ';
            $html .= 'placeholder="Title for ' . strtoupper($L) . '...">';
            $html .= '</div>';

            // Place
            $html .= '<div style="margin-bottom:14px;position:relative;">';
            $html .= '<label style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:6px;">Place (' . strtoupper($L) . ')</label>';
            $html .= '<input type="text" id="dayPlaceInput_' . $L . '" name="place_' . $L . '" value="' . $place . '" ';
            $html .= 'style="width:100%;padding:11px 16px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;outline:none;background:#f8fafc;transition:border-color 0.2s;" ';
            $html .= 'placeholder="e.g. Amman, Petra..." autocomplete="off" oninput="dayPlaceAutocomplete(this.value,\'' . $L . '\')">';
            $html .= '<div id="dayPlaceDrop_' . $L . '" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.12);z-index:9999;max-height:220px;overflow-y:auto;margin-top:4px;"></div>';
            $html .= '</div>';

            // Description — Quill editor
            $html .= '<div style="margin-bottom:14px;">';
            $html .= '<label style="font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:6px;">Description (' . strtoupper($L) . ')</label>';
            $html .= '<div id="quill_' . $L . '" style="min-height:200px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;line-height:1.6;"></div>';
            $html .= '<textarea name="description_' . $L . '" id="quillHidden_' . $L . '" style="display:none;">' . $desc . '</textarea>';
            $html .= '</div>';

            $html .= '</div>';
        }

        $html .= '</form>';

        // Language switcher + Quill init JS
        $langsJson = json_encode($langs);
        $langsPhpFirst = $langs[0];
        $html .= '<script>';
        $html .= 'window._dayQuills={};';
        // Init Quill for each language
        $html .= 'window._initDayQuills=function(){';
        $html .= 'var langs=' . $langsJson . ';';
        $html .= 'if(typeof Quill==="undefined"){setTimeout(window._initDayQuills,200);return;}';
        $html .= 'langs.forEach(function(L){';
        $html .= 'if(window._dayQuills[L])return;';
        $html .= 'var el=document.getElementById("quill_"+L);if(!el)return;';
        $html .= 'var q=new Quill(el,{theme:"snow",modules:{toolbar:[["bold","italic","underline"],[{list:"ordered"},{list:"bullet"}],["link"],["clean"]]}});';
        $html .= 'var hidden=document.getElementById("quillHidden_"+L);';
        $html .= 'if(hidden&&hidden.value){q.root.innerHTML=hidden.value;}';
        $html .= 'q.on("text-change",function(){if(hidden)hidden.value=q.root.innerHTML;});';
        $html .= 'window._dayQuills[L]=q;';
        $html .= '});';
        $html .= '};';
        $html .= 'window._initDayQuills();';
        // Language switcher
        $html .= 'window.switchDayLang=function(lang){';
        $html .= 'var langs=' . $langsJson . ';';
        $html .= 'langs.forEach(function(L){';
        $html .= 'var p=document.getElementById("dayLang_"+L);if(p)p.style.display="none";';
        $html .= 'var b=document.getElementById("dayLangBtn_"+L);if(b){b.style.background="transparent";b.style.color="#94a3b8";b.style.boxShadow="none";}';
        $html .= '});';
        $html .= 'var ap=document.getElementById("dayLang_"+lang);if(ap)ap.style.display="";';
        $html .= 'var ab=document.getElementById("dayLangBtn_"+lang);if(ab){ab.style.background="#fff";ab.style.color="#4f46e5";ab.style.boxShadow="0 1px 5px rgba(0,0,0,0.12)";}';
        $html .= 'if(window._dayQuills[lang]){var q=window._dayQuills[lang];var h=document.getElementById("quillHidden_"+lang);if(h)h.value=q.root.innerHTML;}';
        $html .= '};';
        // Sync all quills before submit
        $html .= 'document.getElementById("editDayForm").addEventListener("submit",function(){';
        $html .= 'Object.keys(window._dayQuills).forEach(function(L){';
        $html .= 'var q=window._dayQuills[L];var h=document.getElementById("quillHidden_"+L);if(h&&q)h.value=q.root.innerHTML;';
        $html .= '});';
        $html .= '});';
        // Nominatim autocomplete for place fields
        $html .= 'var _dayPlaceTimer={};';
        $html .= 'function dayPlaceAutocomplete(query,lang){';
        $html .= 'clearTimeout(_dayPlaceTimer[lang]);';
        $html .= 'var drop=document.getElementById("dayPlaceDrop_"+lang);';
        $html .= 'if(!drop)return;';
        $html .= 'if(!query||query.length<2){drop.style.display="none";drop.innerHTML="";return;}';
        $html .= '_dayPlaceTimer[lang]=setTimeout(function(){';
        $html .= 'fetch("https://nominatim.openstreetmap.org/search?format=json&q="+encodeURIComponent(query)+"&addressdetails=1&limit=6&accept-language=en")';
        $html .= '.then(function(r){return r.json();})';
        $html .= '.then(function(results){';
        $html .= 'drop.innerHTML="";';
        $html .= 'if(!results||!results.length){drop.style.display="none";return;}';
        $html .= 'results.forEach(function(place){';
        $html .= 'var addr=place.address||{};';
        $html .= 'var city=addr.city||addr.town||addr.village||addr.hamlet||addr.county||"";';
        $html .= 'var state=addr.state||"";';
        $html .= 'var country=addr.country||"";';
        $html .= 'var label=city||(state?state+", "+country:country)||place.display_name;';
        $html .= 'var btn=document.createElement("div");';
        $html .= 'btn.style.cssText="display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;color:#1e293b;cursor:pointer;border-bottom:1px solid #f1f5f9;";';
        $html .= 'btn.innerHTML="<i class=\\"fa fa-map-marker\\" style=\\"color:#9ca3af;font-size:13px;\\"></i> <span>"+label+"</span>";';
        $html .= 'btn.onmouseover=function(){this.style.background="#f0fdf8";};';
        $html .= 'btn.onmouseout=function(){this.style.background="";};';
        $html .= 'btn.onclick=function(){';
        $html .= 'var inp=document.getElementById("dayPlaceInput_"+lang);';
        $html .= 'if(inp)inp.value=label;';
        $html .= 'drop.style.display="none";drop.innerHTML="";';
        $html .= '};';
        $html .= 'drop.appendChild(btn);';
        $html .= '});';
        $html .= 'drop.style.display="block";';
        $html .= '}).catch(function(){drop.style.display="none";});';
        $html .= '},350);';
        $html .= '}';
        $html .= 'document.addEventListener("click",function(e){';
        $html .= 'document.querySelectorAll("[id^=dayPlaceDrop_]").forEach(function(d){if(!e.target.closest("[id^=dayPlaceDrop_]")&&!e.target.closest("[id^=dayPlaceInput_]"))d.style.display="none";});';
        $html .= '});';
        $html .= '</script>';
        // Load Quill CSS+JS if not already loaded
        $html .= '<script>if(!document.getElementById("quill-css")){var l=document.createElement("link");l.id="quill-css";l.rel="stylesheet";l.href="https://cdn.quilljs.com/1.3.7/quill.snow.css";document.head.appendChild(l);}if(!window.Quill&&!document.getElementById("quill-js")){var s=document.createElement("script");s.id="quill-js";s.src="https://cdn.quilljs.com/1.3.7/quill.min.js";s.onload=function(){window._initDayQuills();};document.head.appendChild(s);}</script>';

        return response()->json(['html' => $html]);
    }

    /**
     * AJAX update: saves images + all language content
     */
    public function updateAjax(Request $request, $id)
    {
        $day  = TourCannedDay::findOrFail($id);
        $langs = ['en','fr','it','es','Ar','ge','pt'];

        // Images
        $allImages = $request->input('existing_images', []);
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $file) {
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/services'), $filename);
                $allImages[] = 'uploads/services/' . $filename;
            }
        }
        $day->images = serialize(array_values($allImages));
        $day->save();

        // Sync photos to all linked trip itinerary days
        $this->syncPhotosToLinkedDays($day);

        // All language contents
        foreach ($langs as $L) {
            $title       = trim(strval($request->input('title_' . $L, '')));
            $place       = trim(strval($request->input('place_' . $L, '')));
            $description = strval($request->input('description_' . $L, ''));
            $existing = TourCannedDayContent::where('day_id', $id)->where('lang', $L)->first();
            if ($existing) {
                // Update existing record
                $existing->update(['title' => $title, 'place' => $place, 'description' => $description]);
            } elseif ($title !== '') {
                // Only create new record if title is provided
                TourCannedDayContent::create(['day_id' => $id, 'lang' => $L, 'title' => $title, 'place' => $place, 'description' => $description]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * AJAX: Get services by category for expense picker
     */
    public function servicesByCategory(Request $request)
    {
        $catId = $request->input('cat_id', 0);

        if (!$catId) {
            $categoryMap = [
                'Accommodation' => 403, 'Restaurants' => 456,
                'Tour guides' => 527, 'Transport' => 715, 'Activity' => 204,
            ];
            $cat = $request->input('category', '');
            $catId = $categoryMap[$cat] ?? 0;
        }
        if (!$catId) return response()->json([]);

        // Get all descendant category IDs recursively
        $allCatIds = [$catId];
        $check = [$catId];
        for ($i = 0; $i < 10; $i++) {
            $children = \DB::table('en33_services_categories')->whereIn('parent_id', $check)->pluck('id')->toArray();
            if (empty($children)) break;
            $allCatIds = array_merge($allCatIds, $children);
            $check = $children;
        }

        $services = \App\Models\Service::whereIn('category', $allCatIds)
            ->select('id', 'description', 'cost', 'category', 'vender')
            ->orderBy('description')
            ->get()
            ->map(function($s) {
                $catName = \DB::table('en33_services_categories')->where('id', $s->category)->value('name');
                $venderName = '';
                if ($s->vender) {
                    $u = \DB::table('en33_users')->where('id', $s->vender)->first(['first_name', 'last_name', 'company']);
                    if ($u) $venderName = trim(($u->company ?: '') ?: ($u->first_name . ' ' . $u->last_name));
                }
                return [
                    'id' => $s->id,
                    'description' => $s->description,
                    'cost' => number_format((float)$s->cost, 2),
                    'vender' => $venderName,
                    'subcategory' => $catName ?? '',
                ];
            });

        return response()->json($services);
    }

    /**
     * AJAX: Get category tree for expense picker
     */
    public function categoryTree()
    {
        $roots = [
            ['id' => 403, 'name' => 'Accommodation'],
            ['id' => 456, 'name' => 'Restaurants'],
            ['id' => 527, 'name' => 'Tour guides'],
            ['id' => 715, 'name' => 'Transport'],
            ['id' => 204, 'name' => 'Activity'],
        ];

        $tree = [];
        foreach ($roots as $root) {
            $children = \DB::table('en33_services_categories')
                ->where('parent_id', $root['id'])
                ->orderBy('name')
                ->get(['id', 'name', 'parent_id'])
                ->map(function($c) {
                    $subchildren = \DB::table('en33_services_categories')
                        ->where('parent_id', $c->id)
                        ->orderBy('name')
                        ->get(['id', 'name'])
                        ->toArray();
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'children' => $subchildren,
                    ];
                })->toArray();

            $tree[] = [
                'id' => $root['id'],
                'name' => $root['name'],
                'children' => $children,
            ];
        }

        return response()->json($tree);
    }

    /**
     * AJAX: Get inclusion items for inclusion picker
     */
    public function inclusionItems()
    {
        $items = \DB::table('en33_tours_inclusions')
            ->where('lang', 'en')
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($items);
    }

    /**
     * Sync canned day photos to all linked trip itinerary days
     */
    private function syncPhotosToLinkedDays(TourCannedDay $cannedDay)
    {
        $cdImages = @unserialize($cannedDay->images);
        if (!is_array($cdImages)) $cdImages = [];

        // Normalize paths
        $cdImages = array_values(array_filter(array_map(function($img) {
            if (!$img) return null;
            return (!str_starts_with($img, 'http')) ? '/' . ltrim($img, '/') : $img;
        }, $cdImages)));

        // Update all trip itinerary days linked to this canned day
        TripItineraryDay::where('canned_day_id', $cannedDay->id)
            ->update(['photos' => json_encode($cdImages)]);
    }
}
