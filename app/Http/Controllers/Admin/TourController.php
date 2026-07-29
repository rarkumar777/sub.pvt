<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourContent;
use App\Models\TourCategory;
use App\Models\TourType;
use App\Models\Country;
use App\Models\City;
use App\Models\TourImage;
use App\Models\TourInclusion;
use App\Models\TourSeason;
use App\Models\TourTec;
use App\Models\TourGuaranteedDeparture;
use App\Models\TourCustomInclusion;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $tours = Tour::with(['contents', 'startCountryRelation']);

        if ($request->filled('search')) {
            $search = $request->search;
            $tours->whereHas('contents', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $tours->where('category', $request->category);
        }

        if ($request->filled('type')) {
            $tours->where('type', $request->type);
        }

        $tours = $tours->orderByDesc('id')->paginate(20);
        $categories = TourCategory::where('lang', 'en')->get();
        $types = TourType::where('lang', 'en')->get();

        // Active filter labels
        $activeCategory = 'All Categories';
        $activeType = 'All Types';
        if ($request->filled('category')) {
            $cat = $categories->firstWhere('cat_id', $request->category) ?? $categories->firstWhere('id', $request->category);
            $activeCategory = $cat ? ($cat->name ?? $cat->title) : 'All Categories';
        }
        if ($request->filled('type')) {
            $tp = $types->firstWhere('type_id', $request->type) ?? $types->firstWhere('id', $request->type);
            $activeType = $tp ? ($tp->name ?? $tp->title) : 'All Types';
        }

        return view('admin.tours.index', compact('tours', 'categories', 'types', 'activeCategory', 'activeType'));
    }

    public function create(Request $request)
    {
        $categories = TourCategory::where('lang', 'en')->get();
        $types = TourType::where('lang', 'en')->get();
        $countries = Country::where('lang', 'en')->get();
        $tecItems = TourTec::where('lang', 'en')->get();
        $incItems = TourInclusion::where('lang', 'en')->where('lang_id', '!=', 72)->orderBy('name')->get();

        // Copy tour data if copy_tour param exists
        $copyTour = null;
        $copyContent = null;
        $copyTec = [];
        $copyInc = [];
        if ($request->filled('copy_tour')) {
            $copyTour = Tour::find($request->copy_tour);
            if ($copyTour) {
                $copyContent = $copyTour->contents->where('lang', 'en')->first();
                if ($copyTour->tec_details) {
                    $copyTec = @unserialize($copyTour->tec_details, ['allowed_classes' => false]) ?: [];
                }
                if ($copyTour->inclusions) {
                    $copyInc = @unserialize($copyTour->inclusions, ['allowed_classes' => false]) ?: [];
                }
            }
        }

        return view('admin.tours.create', compact('categories', 'types', 'countries', 'tecItems', 'incItems', 'copyTour', 'copyContent', 'copyTec', 'copyInc'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'en_title' => 'required|string|max:200',
            'en_desc' => 'nullable|string',
            'en_meta_desc' => 'required|string',
            'number_of_nights' => 'required|integer|min:0',
            'number_of_days' => 'required|integer|min:0',
            'category' => 'required|integer',
            'type' => 'required|integer',
        ], [
            'en_title.required' => 'The English title is required.',
            'en_meta_desc.required' => 'Short meta description is required.',
            'number_of_nights.required' => 'Nights are required.',
            'number_of_days.required' => 'Days are required.',
        ]);

        // Process technical details
        $tecIds = array_filter(explode('-', $request->input('tec', '')));
        $disable = [];
        $enable = [];
        $rates = [];
        foreach ($tecIds as $tk) {
            $tk = intval($tk);
            if ($request->input('tec_' . $tk) == 0) {
                $disable[] = $tk;
            }
            if ($request->input('tec_' . $tk) == 1) {
                $enable[] = $tk;
            }
            $rates[$tk] = $request->input('tec_rating' . $tk, 1);
        }
        $tecRate = serialize(['disable' => $disable, 'enable' => $enable, 'rates' => $rates]);

        // Process inclusions
        $incIds = array_filter(explode('-', $request->input('incs', '')));
        $exc = [];
        $inc = [];
        foreach ($incIds as $k) {
            $k = intval($k);
            if ($request->input('inc_' . $k) == 1) {
                $exc[] = $k;
            }
            if ($request->input('inc_' . $k) == 2) {
                $inc[] = $k;
            }
        }
        $inclusions = serialize(['exc' => $exc, 'inc' => $inc]);

        // Generate SEO url
        $url = $request->input('en_url', '') ?? '';
        $url = preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower(trim($url)));
        $url = preg_replace('/-+/', '-', $url);
        $url = trim($url, '-');
        if (empty($url)) {
            $url = preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower(trim($request->input('en_title', 'tour') ?? 'tour')));
            $url = preg_replace('/-+/', '-', $url);
        }

        // Check URL uniqueness
        $existing = TourContent::where('url', $url)->first();
        if ($existing) {
            $url = $url . '-' . time();
        }

        $tour = Tour::create([
            'nights' => intval($request->input('number_of_nights', 0)),
            'days' => intval($request->input('number_of_days', 0)),
            'status' => intval($request->input('status', 0)),
            'category' => intval($request->input('category', 0)),
            'type' => intval($request->input('type', 0)),
            'rating' => intval($request->input('rating', 1)),
            'f_start' => $request->input('featured_start', '') ?? '',
            'f_finish' => $request->input('featured_finish', '') ?? '',
            'sp_start' => $request->input('offer_start', '') ?? '',
            'sp_finish' => $request->input('offer_finish', '') ?? '',
            'start_country' => intval($request->input('start_country', 0)),
            'start_city' => intval($request->input('start_city', 0)),
            'finish_country' => intval($request->input('finish_country', 0)),
            'finish_city' => intval($request->input('finish_city', 0)),
            'tec_details' => $tecRate,
            'inclusions' => $inclusions,
            'map' => $request->input('map', '') ?? '',
            'relative_count' => intval($request->input('relative_tours_number', 5)),
            'contact_person' => $request->input('contact_email', '') ?? '',
            'partly_payment' => intval($request->input('partly_payment', 0)),
            'pricing_bases' => '',
            'pricing_groups' => '',
            'min_price' => 0,
            'max_price' => 0,
            'pricing_groups_low' => '',
            'pricing_bases_low' => '',
            'pricing_groups_high' => '',
            'pricing_bases_high' => '',
        ]);

        // Create tour contents for all languages
        $langs = ['en', 'fr', 'it', 'es', 'ar', 'ge', 'pt'];
        $enTitle = \Illuminate\Support\Str::limit($request->input('en_title', '') ?? '', 195, '');
        $enMetaDesc = \Illuminate\Support\Str::limit(strip_tags($request->input('en_meta_desc', '') ?? ''), 345, '');
        $enMetaKeyWords = \Illuminate\Support\Str::limit(strip_tags($request->input('en_meta_key_words', '') ?? ''), 245, '');
        $enDesc = $request->input('en_desc', '') ?? '';

        foreach ($langs as $lang) {
            TourContent::create([
                'tour_id' => $tour->id,
                'lang' => $lang,
                'title' => $enTitle,
                'meta_desc' => ($lang == 'en') ? $enMetaDesc : '',
                'meta_key_words' => ($lang == 'en') ? $enMetaKeyWords : '',
                'desc' => ($lang == 'en') ? $enDesc : '',
                'url' => $url,
            ]);
        }

        return redirect()->route('admin.tours.itinerary', $tour->id)->with('success', 'Tour created! Now configure the day-by-day itinerary with hotels, activities & meals.');
    }

    public function edit($id)
    {
        $tour = Tour::findOrFail($id);
        $content = $tour->contents->where('lang', 'en')->first();
        $categories = TourCategory::where('lang', 'en')->get();
        $types = TourType::where('lang', 'en')->get();
        $countries = Country::where('lang', 'en')->get();
        $startCities = City::where('lang', 'en')->where('country', $tour->start_country)->get();
        $finishCities = City::where('lang', 'en')->where('country', $tour->finish_country)->get();

        // Technical details
        $tecItems = TourTec::where('lang', 'en')->get();
        $tourTec = [];
        if ($tour->tec_details) {
            $tourTec = @unserialize($tour->tec_details, ['allowed_classes' => false]) ?: [];
        }

        // Inclusions
        $incItems = TourInclusion::where('lang', 'en')->where('lang_id', '!=', 72)->orderBy('name')->get();
        $tourInc = [];
        if ($tour->inclusions) {
            $tourInc = @unserialize($tour->inclusions, ['allowed_classes' => false]) ?: [];
        }

        return view('admin.tours.edit', compact('tour', 'content', 'categories', 'types', 'countries', 'startCities', 'finishCities', 'tecItems', 'tourTec', 'incItems', 'tourInc'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'en_title' => 'required|string|max:200',
            'en_desc' => 'required|string',
            'en_meta_desc' => 'required|string',
            'number_of_nights' => 'required|integer|min:0',
            'number_of_days' => 'required|integer|min:0',
            'category' => 'required|integer',
            'type' => 'required|integer',
        ], [
            'en_title.required' => 'The English title is required.',
            'en_desc.required' => 'Detailed itinerary and description is required.',
            'en_meta_desc.required' => 'Short meta description is required.',
            'number_of_nights.required' => 'Nights are required.',
            'number_of_days.required' => 'Days are required.',
        ]);

        $tour = Tour::findOrFail($id);

        // Process technical details
        $tecIds = array_filter(explode('-', $request->input('tec', '')));
        $disable = [];
        $enable = [];
        $rates = [];
        foreach ($tecIds as $tk) {
            $tk = intval($tk);
            if ($request->input('tec_' . $tk) == 0) {
                $disable[] = $tk;
            }
            if ($request->input('tec_' . $tk) == 1) {
                $enable[] = $tk;
            }
            $rates[$tk] = $request->input('tec_rating' . $tk, 1);
        }
        $tecRate = serialize(['disable' => $disable, 'enable' => $enable, 'rates' => $rates]);

        // Process inclusions
        $incIds = array_filter(explode('-', $request->input('incs', '')));
        $exc = [];
        $inc = [];
        foreach ($incIds as $k) {
            $k = intval($k);
            if ($request->input('inc_' . $k) == 1) {
                $exc[] = $k;
            }
            if ($request->input('inc_' . $k) == 2) {
                $inc[] = $k;
            }
        }
        $inclusions = serialize(['exc' => $exc, 'inc' => $inc]);

        $tour->update([
            'nights' => $request->input('number_of_nights', $tour->nights),
            'days' => $request->input('number_of_days', $tour->days),
            'status' => $request->input('status', $tour->status),
            'category' => $request->input('category', $tour->category),
            'type' => $request->input('type', $tour->type),
            'rating' => $request->input('rating', $tour->rating),
            'f_start' => $request->input('featured_start', '') ?? '',
            'f_finish' => $request->input('featured_finish', '') ?? '',
            'sp_start' => $request->input('offer_start', '') ?? '',
            'sp_finish' => $request->input('offer_finish', '') ?? '',
            'start_country' => $request->input('start_country', $tour->start_country),
            'start_city' => $request->input('start_city', $tour->start_city),
            'finish_country' => $request->input('finish_country', $tour->finish_country),
            'finish_city' => $request->input('finish_city', $tour->finish_city),
            'tec_details' => $tecRate,
            'inclusions' => $inclusions,
            'map' => $request->input('map', '') ?? '',
            'relative_count' => intval($request->input('relative_tours_number', 5)),
            'contact_person' => $request->input('contact_email', '') ?? '',
            'partly_payment' => intval($request->input('partly_payment', 0)),
        ]);

        // Update English Content
        $enUrl = $request->input('en_url', '');
        if (empty($enUrl)) {
            $enUrl = preg_replace('/[^a-zA-Z0-9\-]/', '-', strtolower(trim($request->input('en_title', 'tour'))));
            $enUrl = preg_replace('/-+/', '-', $enUrl);
        }
        
        $enContent = TourContent::where('tour_id', $tour->id)->where('lang', 'en')->first();
        if ($enContent) {
            $enContent->update([
                'title' => \Illuminate\Support\Str::limit($request->input('en_title', $enContent->title), 195, ''),
                'meta_desc' => \Illuminate\Support\Str::limit(strip_tags($request->input('en_meta_desc', $enContent->meta_desc)), 345, ''),
                'meta_key_words' => \Illuminate\Support\Str::limit(strip_tags($request->input('en_meta_key_words', $enContent->meta_key_words)), 245, ''),
                'desc' => $request->input('en_desc', $enContent->desc),
                'url' => rtrim($enUrl, '-'),
            ]);
        }

        return redirect()->route('admin.tours.edit', $tour->id)->with('success', 'Success');
    }

    public function destroy($id)
    {
        Tour::destroy($id);
        return redirect()->route('admin.tours.index')->with('success', 'Tour deleted successfully');
    }

    public function show($id)
    {
        return $this->edit($id);
    }

    // ===== Tour Images =====
    public function images($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        $images = TourImage::where('tour_id', $tourId)->orderBy('id', 'desc')->get();
        return view('admin.tours.images', compact('tour', 'images'));
    }

    public function storeImage(Request $request, $tourId)
    {
        $tour = Tour::findOrFail($tourId);
        TourImage::create(['tour_id' => $tourId, 'image' => $request->image]);
        return redirect()->route('admin.tours.images', $tourId)->with('success', 'Image added');
    }

    public function destroyImage($tourId, $imageId)
    {
        TourImage::where('id', $imageId)->where('tour_id', $tourId)->delete();
        return redirect()->route('admin.tours.images', $tourId)->with('success', 'Image deleted');
    }

    // ===== Tour Pricing =====
    public function pricing($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        return view('admin.tours.pricing', compact('tour'));
    }

    public function updatePricing(Request $request, $tourId)
    {
        $tour = Tour::findOrFail($tourId);

        // Save base pricing
        $updateData = $request->only(['min_price', 'max_price']);

        // Save hotel grade pricing bases
        if ($request->has('bases')) {
            $updateData['pricing_bases'] = serialize($request->input('bases'));
        }

        // Save additional cost items (transportation, guides, etc.)
        $extras = [];
        if ($request->has('extras')) {
            foreach ($request->input('extras') as $extra) {
                if (!empty($extra['name'])) {
                    $extras[] = [
                        'name' => $extra['name'],
                        'cost' => $extra['cost'] ?? '',
                        'pricing_type' => $extra['pricing_type'] ?? 'per_person',
                        'category' => $extra['category'] ?? 'other',
                    ];
                }
            }
        }
        $updateData['pricing_extras'] = json_encode($extras);

        $tour->update($updateData);
        return redirect()->route('admin.tours.pricing', $tourId)->with('success', 'Pricing updated');
    }

    // ===== Tour Inclusions (Per-Tour Free-Form) =====
    public function inclusions($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        $inclusions = TourCustomInclusion::where('tour_id', $tourId)->orderBy('sort_order')->get();
        return view('admin.tours.inclusions', compact('tour', 'inclusions'));
    }

    public function updateInclusions(Request $request, $tourId)
    {
        $tour = Tour::findOrFail($tourId);

        // Delete all existing custom inclusions for this tour
        TourCustomInclusion::where('tour_id', $tourId)->delete();

        // Insert new items
        if ($request->has('items')) {
            $sortOrder = 0;
            foreach ($request->input('items') as $item) {
                if (!empty($item['name'])) {
                    TourCustomInclusion::create([
                        'tour_id' => $tourId,
                        'name' => trim($item['name']),
                        'type' => $item['type'] ?? 'inclusion',
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }
        }

        return redirect()->route('admin.tours.inclusions', $tourId)->with('success', 'Inclusions & Exclusions saved successfully');
    }

    // ===== Itinerary =====
    public function itinerary($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        return view('admin.tours.itinerary', compact('tour'));
    }

    public function updateItinerary(Request $request, $tourId)
    {
        $tour = Tour::findOrFail($tourId);

        // Build itinerary data from form
        $itineraryData = [];
        if ($request->has('days')) {
            foreach ($request->input('days') as $dayNum => $dayInput) {
                $itineraryData[$dayNum] = [
                    'title' => $dayInput['title'] ?? '',
                    'description' => $dayInput['description'] ?? '',
                    'overnight' => $dayInput['overnight'] ?? '',
                    'meals' => $dayInput['meals'] ?? [],
                    'activities' => array_filter($dayInput['activities'] ?? [], function($a) { return !empty(trim($a)); }),
                    // Hotel / Accommodation
                    'hotel_name' => $dayInput['hotel_name'] ?? '',
                    'hotel_stars' => $dayInput['hotel_stars'] ?? '',
                    'hotel_category' => $dayInput['hotel_category'] ?? '',
                    'hotel_website' => $dayInput['hotel_website'] ?? '',
                    // Transport
                    'transport' => $dayInput['transport'] ?? '',
                    // Destination city
                    'destination' => $dayInput['destination'] ?? '',
                ];
            }
        }

        $tour->update([
            'itinerary_data' => json_encode($itineraryData, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('admin.tours.itinerary', $tourId)->with('success', 'Itinerary saved successfully');
    }

    // ===== Seasons =====
    public function seasons($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        $seasons = TourSeason::where('tour_id', $tourId)->get();
        return view('admin.tours.seasons', compact('tour', 'seasons'));
    }

    public function storeSeasons(Request $request, $tourId)
    {
        TourSeason::create(['tour_id' => $tourId, 'from_date' => $request->from_date, 'to_date' => $request->to_date, 'name' => $request->name ?? 'Season']);
        return redirect()->route('admin.tours.seasons', $tourId)->with('success', 'Season added');
    }

    // ===== TEC Details =====
    public function tecDetails($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        return view('admin.tours.tec', compact('tour'));
    }

    // ===== Per-Tour Departures =====
    public function departures($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        $departures = TourGuaranteedDeparture::where('tour_id', $tourId)->get();
        return view('admin.tours.departures', compact('tour', 'departures'));
    }

    // ===== Tour Edit Menu =====
    public function menu($tourId)
    {
        $tour = Tour::findOrFail($tourId);
        return view('admin.tours.menu', compact('tour'));
    }

    // ===== Tour Categories =====
    public function categories()
    {
        $categories = TourCategory::where('lang', 'en')->orderBy('name')->get();
        return view('admin.tours.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'category_en' => 'required|string|max:200',
            'category_fr' => 'required|string|max:200',
            'category_it' => 'required|string|max:200',
            'category_es' => 'required|string|max:200',
            'category_Ar' => 'required|string|max:200',
            'category_ge' => 'required|string|max:200',
            'category_pt' => 'required|string|max:200',
        ], [
            'category_en.required' => 'The English name field is required.',
            'category_fr.required' => 'The French name field is required.',
            'category_it.required' => 'The Italian name field is required.',
            'category_es.required' => 'The Spanish name field is required.',
            'category_Ar.required' => 'The Arabic name field is required.',
            'category_ge.required' => 'The German name field is required.',
            'category_pt.required' => 'The Portuguese name field is required.',
        ]);
        
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        foreach ($langs as $l) {
            $name = $request->input('category_' . $l, '');
            TourCategory::create(['name' => $name, 'lang' => $l, 'lang_id' => 0]);
        }
        $first = TourCategory::where('lang_id', 0)->where('lang', 'en')->first();
        if ($first) {
            TourCategory::where('lang_id', 0)->update(['lang_id' => $first->id]);
        }
        return redirect()->route('admin.tour-categories')->with('success', 'Category added successfully');
    }

    public function destroyCategory($id)
    {
        TourCategory::where('lang_id', $id)->delete();
        return redirect()->route('admin.tour-categories')->with('success', 'Category deleted');
    }

    public function editCategoryAjax($langId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $enCat = TourCategory::where('lang_id', $langId)->where('lang', 'en')->first();
        $catName = $enCat ? $enCat->name : 'Category';

        $fields = [];
        foreach ($langs as $l) {
            $row = TourCategory::where('lang_id', $langId)->where('lang', $l)->first();
            $fields[] = [
                'lang' => $l,
                'name' => $row ? $row->name : '',
                'action' => $row ? 'edit' : 'insert',
            ];
        }

        $html = '<div class="modal" id="edit_t"><div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[600px] !tw-max-w-[calc(100vw-40px)] tw-shadow-2xl">';
        $html .= '<div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">';
        $html .= '<h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">';
        $html .= '<i class="fa fa-edit tw-text-orange-400"></i> ' . htmlspecialchars($catName, ENT_QUOTES, 'UTF-8') . '</h3>';
        $html .= '<a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a></div>';
        
        $html .= '<form method="POST" action="' . route('admin.tour-categories.update', $langId) . '" class="tw-p-8">';
        $html .= csrf_field();
        $html .= '<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-5 tw-mb-8">';
        
        $langLabels = [
            'en' => 'English', 'fr' => 'French', 'it' => 'Italian', 
            'es' => 'Spanish', 'Ar' => 'Arabic', 'ge' => 'German', 'pt' => 'Portuguese'
        ];

        foreach ($fields as $f) {
            $label = $langLabels[$f['lang']] ?? strtoupper($f['lang']);
            $html .= '<div class="tw-flex tw-flex-col tw-gap-2">';
            $html .= '<label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">' . $label . ' Name</label>';
            $html .= '<input type="text" name="category_edit_' . $f['lang'] . '" value="' . htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8') . '" placeholder="Enter ' . strtolower($label) . ' name...">';
            $html .= '<input type="hidden" name="action_' . $f['lang'] . '" value="' . $f['action'] . '"></div>';
        }
        $html .= '</div>';
        $html .= '<input type="hidden" name="edit_category" value="' . $langId . '">';
        $html .= '<div class="tw-pt-6 tw-border-t tw-border-slate-50">';
        $html .= '<button type="submit" class="btn orange !tw-w-full !tw-py-4"><i class="fa fa-save"></i> Save Categories</button></div>';
        $html .= '</form></div></div>';

        return response($html);
    }

    public function updateCategory(Request $request, $langId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        foreach ($langs as $l) {
            $name = $request->input('category_edit_' . $l, '');
            $action = $request->input('action_' . $l, 'edit');
            if ($action === 'edit') {
                TourCategory::where('lang_id', $langId)->where('lang', $l)->update(['name' => $name]);
            } elseif ($action === 'insert' && !empty($name)) {
                TourCategory::create(['name' => $name, 'lang' => $l, 'lang_id' => $langId]);
            }
        }
        return redirect()->route('admin.tour-categories')->with('success', 'Category updated successfully');
    }

    // ===== Global Inclusions =====
    public function globalInclusions()
    {
        $inclusions = TourInclusion::where('lang', 'en')->orderBy('name')->get();
        return view('admin.tours.global-inclusions', compact('inclusions'));
    }

    public function storeGlobalInclusion(Request $request)
    {
        $request->validate([
            'inclusion_en' => 'required|string|max:200',
        ], [
            'inclusion_en.required' => 'The English name is required.',
        ]);

        $enName = $request->input('inclusion_en');
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        foreach ($langs as $l) {
            $name = $request->input('inclusion_' . $l, '');
            // Auto-copy English if other language field is empty
            if (empty(trim($name))) {
                $name = $enName;
            }
            TourInclusion::create(['name' => $name, 'lang' => $l, 'lang_id' => 0]);
        }
        $first = TourInclusion::where('lang_id', 0)->where('lang', 'en')->first();
        if ($first) {
            TourInclusion::where('lang_id', 0)->update(['lang_id' => $first->id]);
        }
        return redirect()->route('admin.tour-inclusions')->with('success', 'Inclusion added successfully');
    }

    public function destroyGlobalInclusion($id)
    {
        TourInclusion::where('lang_id', $id)->delete();
        return redirect()->route('admin.tour-inclusions')->with('success', 'Inclusion deleted');
    }

    public function editInclusionAjax($langId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $enInc = TourInclusion::where('lang_id', $langId)->where('lang', 'en')->first();
        $incName = $enInc ? $enInc->name : 'Inclusion';

        $fields = [];
        foreach ($langs as $l) {
            $row = TourInclusion::where('lang_id', $langId)->where('lang', $l)->first();
            $fields[] = [
                'lang' => $l,
                'name' => $row ? $row->name : '',
                'action' => $row ? 'edit' : 'insert',
            ];
        }

        $html = '<div class="modal" id="edit_t"><div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[600px] !tw-max-w-[calc(100vw-40px)] tw-shadow-2xl">';
        $html .= '<div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">';
        $html .= '<h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">';
        $html .= '<i class="fa fa-edit tw-text-orange-400"></i> ' . htmlspecialchars($incName, ENT_QUOTES, 'UTF-8') . '</h3>';
        $html .= '<a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a></div>';
        
        $html .= '<form method="POST" action="' . route('admin.tour-inclusions.update', $langId) . '" class="tw-p-8">';
        $html .= csrf_field();
        $html .= '<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-5 tw-mb-8">';
        
        $langLabels = [
            'en' => 'English', 'fr' => 'French', 'it' => 'Italian', 
            'es' => 'Spanish', 'Ar' => 'Arabic', 'ge' => 'German', 'pt' => 'Portuguese'
        ];

        foreach ($fields as $f) {
            $label = $langLabels[$f['lang']] ?? strtoupper($f['lang']);
            $html .= '<div class="tw-flex tw-flex-col tw-gap-2">';
            $html .= '<label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">' . $label . ' Name</label>';
            $html .= '<input type="text" name="inclusion_edit_' . $f['lang'] . '" value="' . htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8') . '" placeholder="Enter ' . strtolower($label) . ' name...">';
            $html .= '<input type="hidden" name="action_' . $f['lang'] . '" value="' . $f['action'] . '"></div>';
        }
        $html .= '</div>';
        $html .= '<input type="hidden" name="edit_inclusion" value="' . $langId . '">';
        $html .= '<div class="tw-pt-6 tw-border-t tw-border-slate-50">';
        $html .= '<button type="submit" class="btn orange !tw-w-full !tw-py-4"><i class="fa fa-save"></i> Save Inclusions</button></div>';
        $html .= '</form></div></div>';

        return response($html);
    }

    public function updateInclusion(Request $request, $langId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        foreach ($langs as $l) {
            $name = $request->input('inclusion_edit_' . $l, '');
            $action = $request->input('action_' . $l, 'edit');
            if ($action === 'edit') {
                TourInclusion::where('lang_id', $langId)->where('lang', $l)->update(['name' => $name]);
            } elseif ($action === 'insert' && !empty($name)) {
                TourInclusion::create(['name' => $name, 'lang' => $l, 'lang_id' => $langId]);
            }
        }
        return redirect()->route('admin.tour-inclusions')->with('success', 'Inclusion updated successfully');
    }

    // ===== Technical Details =====
    public function globalTecDetails()
    {
        $tecDetails = TourTec::where('lang', 'en')->orderBy('name')->get();
        return view('admin.tours.tec-details', compact('tecDetails'));
    }

    public function storeTecDetail(Request $request)
    {
        $request->validate([
            'tec_en' => 'required|string|max:200',
        ], [
            'tec_en.required' => 'The English name is required.',
        ]);
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $icon = $request->input('selected_icon', 3);
        $enName = $request->input('tec_en', 'Attribute');
        foreach ($langs as $l) {
            $name = $request->input('tec_' . $l);
            if (empty($name)) {
                $name = $enName;
            }
            TourTec::create(['name' => $name, 'lang' => $l, 'lang_id' => 0, 'icon' => $icon]);
        }
        $first = TourTec::where('lang_id', 0)->where('lang', 'en')->first();
        if ($first) {
            TourTec::where('lang_id', 0)->update(['lang_id' => $first->id]);
        }
        return redirect()->route('admin.tour-tec')->with('success', 'Technical detail added successfully');
    }

    public function destroyTecDetail($id)
    {
        TourTec::where('lang_id', $id)->delete();
        return redirect()->route('admin.tour-tec')->with('success', 'Technical detail deleted');
    }

    public function editTecAjax($langId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $enTec = TourTec::where('lang_id', $langId)->where('lang', 'en')->first();
        $tecName = $enTec ? $enTec->name : 'Technical Detail';
        $currentIcon = $enTec ? $enTec->icon : 3;

        $fields = [];
        foreach ($langs as $l) {
            $row = TourTec::where('lang_id', $langId)->where('lang', $l)->first();
            $fields[] = [
                'lang' => $l,
                'name' => $row ? $row->name : '',
                'action' => $row ? 'edit' : 'insert',
            ];
        }

        $html = '<div class="modal" id="edit_t"><div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[600px] !tw-max-w-[calc(100vw-40px)] tw-shadow-2xl">';
        $html .= '<div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">';
        $html .= '<h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">';
        $html .= '<i class="fa fa-edit tw-text-orange-400"></i> ' . htmlspecialchars($tecName, ENT_QUOTES, 'UTF-8') . '</h3>';
        $html .= '<a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a></div>';
        
        $html .= '<form method="POST" action="' . route('admin.tour-tec.update', $langId) . '" class="tw-p-8">';
        $html .= csrf_field();
        $html .= '<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-5 tw-mb-6">';
        
        $langLabels = [
            'en' => 'English', 'fr' => 'French', 'it' => 'Italian', 
            'es' => 'Spanish', 'Ar' => 'Arabic', 'ge' => 'German', 'pt' => 'Portuguese'
        ];

        foreach ($fields as $f) {
            $label = $langLabels[$f['lang']] ?? strtoupper($f['lang']);
            $html .= '<div class="tw-flex tw-flex-col tw-gap-2">';
            $html .= '<label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">' . $label . ' Name</label>';
            $html .= '<input type="text" name="tec_edit_' . $f['lang'] . '" value="' . htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8') . '" placeholder="Enter ' . strtolower($label) . ' name...">';
            $html .= '<input type="hidden" name="action_' . $f['lang'] . '" value="' . $f['action'] . '"></div>';
        }
        $html .= '</div>';

        $html .= '<div class="tw-flex tw-flex-col tw-gap-2 tw-mb-8">';
        $html .= '<label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Attribute Level (1-5)</label>';
        $html .= '<select name="selected_icon">';
        for ($i = 1; $i <= 5; $i++) {
            $sel = ($currentIcon == $i) ? ' selected' : '';
            $html .= '<option value="' . $i . '"' . $sel . '>Level ' . $i . '</option>';
        }
        $html .= '</select></div>';

        $html .= '<input type="hidden" name="edit_tec" value="' . $langId . '">';
        $html .= '<div class="tw-pt-6 tw-border-t tw-border-slate-50">';
        $html .= '<button type="submit" class="btn orange !tw-w-full !tw-py-4"><i class="fa fa-save"></i> Save Attributes</button></div>';
        $html .= '</form></div></div>';

        return response($html);
    }

    public function updateTecDetail(Request $request, $langId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $icon = $request->input('selected_icon', 3);
        $enName = $request->input('tec_edit_en');
        if (empty($enName)) {
            $enName = TourTec::where('lang_id', $langId)->where('lang', 'en')->value('name');
        }

        foreach ($langs as $l) {
            $name = $request->input('tec_edit_' . $l);
            if (empty($name)) {
                $name = $enName;
            }
            $action = $request->input('action_' . $l, 'edit');
            if ($action === 'edit') {
                TourTec::where('lang_id', $langId)->where('lang', $l)->update(['name' => $name, 'icon' => $icon]);
            } elseif ($action === 'insert' && !empty($name)) {
                TourTec::create(['name' => $name, 'lang' => $l, 'lang_id' => $langId, 'icon' => $icon]);
            }
        }
        return redirect()->route('admin.tour-tec')->with('success', 'Technical detail updated successfully');
    }

    // ===== Tour Settings =====
    public function tourSettings()
    {
        $settingsFile = storage_path('app/tours_settings.json');
        $settings = ['tax' => '0', 'latest_tours_number' => '8', 'rate_icon' => 'fa-star'];
        if (file_exists($settingsFile)) {
            $saved = json_decode(file_get_contents($settingsFile), true);
            if (is_array($saved)) {
                $settings = array_merge($settings, $saved);
            }
        }
        return view('admin.tours.tour-settings', compact('settings'));
    }

    public function saveTourSettings(Request $request)
    {
        $settings = [
            'tax' => $request->input('tax', '0'),
            'latest_tours_number' => $request->input('latest_tours_number', '8'),
            'rate_icon' => $request->input('selected_icon', 'fa-star'),
        ];
        $settingsFile = storage_path('app/tours_settings.json');
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        return redirect()->route('admin.tour-settings')->with('success', 'Settings saved successfully');
    }

    // ===== Tour Types =====
    public function types()
    {
        $types = TourType::where('lang', 'en')->orderBy('name')->get();
        return view('admin.tours.types', compact('types'));
    }

    public function storeType(Request $request)
    {
        $request->validate(['type_en' => 'required|string']);
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        foreach ($langs as $l) {
            $name = $request->input('type_' . $l, '');
            TourType::create(['name' => $name, 'lang' => $l, 'lang_id' => 0]);
        }
        // Get the ID of the first inserted row (en with lang_id=0)
        $first = TourType::where('lang_id', 0)->where('lang', 'en')->first();
        if ($first) {
            TourType::where('lang_id', 0)->update(['lang_id' => $first->id]);
        }
        return redirect()->route('admin.tour-types')->with('success', 'Type added successfully');
    }

    public function destroyType($id)
    {
        TourType::where('lang_id', $id)->delete();
        return redirect()->route('admin.tour-types')->with('success', 'Type deleted');
    }

    public function editTypeAjax($langId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        $enType = TourType::where('lang_id', $langId)->where('lang', 'en')->first();
        $typeName = $enType ? $enType->name : 'Type';

        $fields = [];
        foreach ($langs as $l) {
            $row = TourType::where('lang_id', $langId)->where('lang', $l)->first();
            $fields[] = [
                'lang' => $l,
                'name' => $row ? $row->name : '',
                'action' => $row ? 'edit' : 'insert',
            ];
        }

        $html = '<div class="modal" id="edit_t"><div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[600px] !tw-max-w-[calc(100vw-40px)] tw-shadow-2xl">';
        $html .= '<div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">';
        $html .= '<h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">';
        $html .= '<i class="fa fa-edit tw-text-orange-400"></i> ' . e($typeName) . '</h3>';
        $html .= '<a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a></div>';
        
        $html .= '<form method="POST" action="' . route('admin.tour-types.update', $langId) . '" class="tw-p-8">';
        $html .= csrf_field();
        $html .= '<div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-5 tw-mb-8">';
        
        $langLabels = [
            'en' => 'English', 'fr' => 'French', 'it' => 'Italian', 
            'es' => 'Spanish', 'Ar' => 'Arabic', 'ge' => 'German', 'pt' => 'Portuguese'
        ];

        foreach ($fields as $f) {
            $label = $langLabels[$f['lang']] ?? strtoupper($f['lang']);
            $html .= '<div class="tw-flex tw-flex-col tw-gap-2">';
            $html .= '<label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">' . $label . ' Name</label>';
            $html .= '<input type="text" name="type_edit_' . $f['lang'] . '" value="' . htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8') . '" placeholder="Enter ' . strtolower($label) . ' name...">';
            $html .= '<input type="hidden" name="action_' . $f['lang'] . '" value="' . $f['action'] . '"></div>';
        }
        $html .= '</div>';
        $html .= '<input type="hidden" name="edit_type" value="' . $langId . '">';
        $html .= '<div class="tw-pt-6 tw-border-t tw-border-slate-50">';
        $html .= '<button type="submit" class="btn orange !tw-w-full !tw-py-4"><i class="fa fa-save"></i> Save Tour Types</button></div>';
        $html .= '</form></div></div>';

        return response($html);
    }

    public function updateType(Request $request, $langId)
    {
        $langs = ['en', 'fr', 'it', 'es', 'Ar', 'ge', 'pt'];
        foreach ($langs as $l) {
            $name = $request->input('type_edit_' . $l, '');
            $action = $request->input('action_' . $l, 'edit');
            if ($action === 'edit') {
                TourType::where('lang_id', $langId)->where('lang', $l)->update(['name' => $name]);
            } elseif ($action === 'insert' && !empty($name)) {
                TourType::create(['name' => $name, 'lang' => $l, 'lang_id' => $langId]);
            }
        }
        return redirect()->route('admin.tour-types')->with('success', 'Type updated successfully');
    }

    // ===== Global Pricing =====
    public function globalPricing()
    {
        $tours = Tour::with('contents')->get();
        return view('admin.tours.global-pricing', compact('tours'));
    }
    // ===== Global Seasons =====
    public function globalSeasons(Request $request)
    {
        // Handle POST - add new season
        if ($request->isMethod('post')) {
            TourSeason::create([
                'tour_id' => 0,
                'from_date' => $request->input('date_from'),
                'to_date' => $request->input('date_to'),
                'type' => $request->input('type', 'H'),
            ]);
            return redirect()->route('admin.tours-seasons')->with('success', 'Season added successfully');
        }

        $seasons = TourSeason::where('tour_id', 0)->orderBy('from_date', 'ASC')->get();
        return view('admin.tours.global-seasons', compact('seasons'));
    }

    public function deleteGlobalSeason($id)
    {
        TourSeason::where('id', $id)->where('tour_id', 0)->delete();
        return redirect()->route('admin.tours-seasons')->with('success', 'Season deleted');
    }
}
