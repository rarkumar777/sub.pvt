<?php

namespace App\Http\Controllers;

use App\Models\TopNav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    /**
     * Get common data shared across all frontend views
     */
    public function getCommonData($lang = 'en')
    {
        \Illuminate\Support\Facades\App::setLocale($lang);
        
        // Navigation items
        $navItems = TopNav::where('lang', $lang)
            ->where('parent_id', 0)
            ->orderBy('link_order')
            ->get();

        $children = TopNav::where('lang', $lang)
            ->where('parent_id', '!=', 0)
            ->orderBy('link_order')
            ->get();

        $navChildren = [];
        foreach ($children as $child) {
            $navChildren[$child->parent_id][] = $child;
        }

        // Active languages
        $langPath = storage_path('app/languages.json');
        $langConfig = file_exists($langPath) ? json_decode(file_get_contents($langPath), true) : [];
        $activeLangs = [];
        if (isset($langConfig['languages'])) {
            foreach ($langConfig['languages'] as $l) {
                if ($l['active']) {
                    $activeLangs[] = $l['code'];
                }
            }
        }

        // Currencies
        $profilePath = storage_path('app/company_profile.json');
        $profile = file_exists($profilePath) ? json_decode(file_get_contents($profilePath), true) : [];
        $currencies = [];
        if (isset($profile['currencies'])) {
            foreach ($profile['currencies'] as $c) {
                $currencies[$c['id'] ?? $c['name']] = $c;
            }
        }

        // Footer content
        $footerPath = public_path('config/footer/' . $lang . '.php');
        $footerContent = file_exists($footerPath) ? file_get_contents($footerPath) : '';

        // Read frontend default currency from admin global config
        $defaultCurrency = 'JOD';
        $globalConfigPath = base_path('../pvt.jo/config/global.php');
        if (file_exists($globalConfigPath)) {
            $gcContent = file_get_contents($globalConfigPath);
            if (preg_match("/\\\$GOGIES\['front_currency'\]='([^']*)'/", $gcContent, $gcm)) {
                $fcVal = $gcm[1];
                if ($fcVal) {
                    // Admin saves currency ID (e.g. 59, 78, 108) - look up the name
                    if (is_numeric($fcVal)) {
                        $currName = \DB::table('en33_currency')->where('lang_id', $fcVal)->where('lang', 'en')->value('symbol');
                        $defaultCurrency = strtoupper($currName ?: 'JOD');
                    } else {
                        $defaultCurrency = $fcVal;
                    }
                }
            }
        }
        $activeCurrency = $_COOKIE['user_currency'] ?? $defaultCurrency;

        return compact('navItems', 'navChildren', 'activeLangs', 'currencies', 'activeCurrency', 'footerContent', 'lang', 'profile');
    }

    /**
     * Home page
     */
    public function home($lang = 'en')
    {
        $data = $this->getCommonData($lang);
        $data['isHome'] = true;

        // Load Global Config for Layout
        $configPath = base_path('../pvt.jo/config/global.php');
        $defaultLayout = 'Homepage layout';
        if (file_exists($configPath)) {
            $content = file_get_contents($configPath);
            if (preg_match("/\$GOGIES\['defaultlayout'\]='([^']+)'/", $content, $m)) {
                $defaultLayout = $m[1];
            }
        }

        // Load specific layout blocks
        $layoutFile = storage_path('app/layouts/' . $defaultLayout . '.php');
        $center_top = []; $center_bottom = []; $left_side = []; $right_side = [];
        $sliderId = 0;
        
        if (file_exists($layoutFile)) {
            if (!defined('gogies')) define('gogies', true);
            include $layoutFile;
            $data['center_top'] = $center_top ?? [];
            $data['center_bottom'] = $center_bottom ?? [];
            $data['left_side'] = $left_side ?? [];
            $data['right_side'] = $right_side ?? [];
            $data['sliderId'] = $GOGIES['slider'] ?? 0;
        } else {
            // Default fallback if layout file doesn't exist
            $data['center_top'] = [['tours', 'latest_tours', 'Latest Tours', '']];
            $data['center_bottom'] = [];
            $data['left_side'] = [];
            $data['right_side'] = [];
            $data['sliderId'] = 0;
        }

        // Slider images (using sliderId from layout)
        // If sliderId is 0, use the 'default' slider from DB
        if (empty($data['sliderId'])) {
            $defaultSlider = DB::table('en33_slider')->where('name', 'default')->first();
            if ($defaultSlider) {
                $data['sliderId'] = $defaultSlider->id;
            }
        }

        $data['sliderImages'] = collect();
        if (!empty($data['sliderId'])) {
            $data['sliderImages'] = DB::table('en33_slider_images as si')
                ->leftJoin('en33_slider_contents as sc', function($join) use ($lang) {
                    $join->on('si.id', '=', 'sc.image_id')
                         ->where('sc.lang', '=', $lang);
                })
                ->where('si.slider_id', $data['sliderId'])
                ->select('si.id', 'si.image', 'si.price', 'sc.text', 'sc.text2', 'sc.text3', 'sc.link')
                ->orderBy('si.id', 'desc')
                ->get();
        }
        
        // If still no images, get newest from any slider
        if ($data['sliderImages']->isEmpty()) {
            $data['sliderImages'] = DB::table('en33_slider_images as si')
                ->leftJoin('en33_slider_contents as sc', function($join) use ($lang) {
                    $join->on('si.id', '=', 'sc.image_id')
                         ->where('sc.lang', '=', $lang);
                })
                ->select('si.id', 'si.image', 'si.price', 'sc.text', 'sc.text2', 'sc.text3', 'sc.link')
                ->orderBy('si.id', 'desc')
                ->limit(5)
                ->get();
        }

        // Pre-cache necessary data for potential blocks
        $data['countries'] = DB::table('en33_countries')->where('lang', $lang)->whereIn('lang_id', [71, 123, 1565])->orderByRaw('FIELD(lang_id, 123, 1565, 71)')->pluck('name', 'lang_id');
        $data['tourCategories'] = DB::table('en33_tours_categories')->where('lang', $lang)->get();
        $data['tourTypes'] = DB::table('en33_tours_types')->where('lang', $lang)->get();
        
        $data['latestTours'] = DB::table('en33_tours as t')
            ->leftJoin('en33_tours_contents as c', function($join) use ($lang) {
                $join->on('t.id', '=', 'c.tour_id')->where('c.lang', '=', $lang);
            })
            ->leftJoin('en33_cities as city', function($join) use ($lang) {
                $join->on('city.lang_id', '=', 't.start_city')->where('city.lang', '=', $lang);
            })
            ->where('t.status', '1')
            ->select('t.*', 'c.title', 'c.desc', 'c.meta_desc', 'c.url', 'city.name as city')
            ->orderBy('t.id', 'desc')
            ->limit(8)
            ->get();

        $data['jordanTours'] = DB::table('en33_tours as t')
            ->leftJoin('en33_tours_contents as c', function($join) use ($lang) {
                $join->on('t.id', '=', 'c.tour_id')->where('c.lang', '=', $lang);
            })
            ->leftJoin('en33_cities as city', function($join) use ($lang) {
                $join->on('city.lang_id', '=', 't.start_city')->where('city.lang', '=', $lang);
            })
            ->where('t.status', '1')
            ->where('t.start_country', '123')
            ->select('t.*', 'c.title', 'c.desc', 'c.meta_desc', 'c.url', 'city.name as city')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('frontend.home', $data);
    }

    /**
     * Tours listing page
     */
    public function toursList(Request $request, $lang = 'en')
    {
        $data = $this->getCommonData($lang);

        $query = DB::table('en33_tours as t')
            ->leftJoin('en33_tours_contents as c', function($join) use ($lang) {
                $join->on('t.id', '=', 'c.tour_id')->where('c.lang', '=', $lang);
            })
            ->where('t.status', '1')
            ->select('t.*', 'c.title', 'c.desc', 'c.meta_desc', 'c.url');

        // Filter by country
        if ($request->filled('country')) {
            $query->where('t.start_country', $request->country);
        }
        // Filter by category
        if ($request->filled('category')) {
            $query->where('t.category', $request->category);
        }
        // Filter by type
        if ($request->filled('type')) {
            $query->where('t.type', $request->type);
        }
        // Filter by min price
        if ($request->filled('min_price')) {
            $query->where('t.min_price', '>=', $request->min_price);
        }
        // Filter by max price
        if ($request->filled('max_price')) {
            $query->where('t.min_price', '<=', $request->max_price);
        }
        // Filter by days
        if ($request->filled('days')) {
            $query->where('t.days', '<=', $request->days);
        }

        $data['tours'] = $query->orderBy('t.id', 'desc')->get();
        $data['countries'] = DB::table('en33_countries')->where('lang', $lang)->whereIn('lang_id', [71, 123, 1565])->orderByRaw('FIELD(lang_id, 123, 1565, 71)')->pluck('name', 'lang_id');
        $data['tourCategories'] = DB::table('en33_tours_categories')->where('lang', $lang)->get();
        $data['tourTypes'] = DB::table('en33_tours_types')->where('lang', $lang)->get();

        return view('frontend.tours_list', $data);
    }

    /**
     * General inquiry page
     */
    public function inquiry($lang = 'en')
    {
        $data = $this->getCommonData($lang);
        return view('frontend.inquiry', $data);
    }

    /**
     * Handle general inquiry form submission
     */
    public function submitInquiry(Request $request, $lang = 'en')
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
            'captcha' => 'required',
        ]);

        // Validate captcha
        $sessionCaptcha = session('inquiry_captcha_code');
        if (strtolower($request->captcha) !== strtolower($sessionCaptcha)) {
            return back()->withErrors(['captcha' => 'Invalid captcha code.'])->withInput();
        }

        // Create quotation record
        $refNumber = 'INQ-' . date('Ymd') . '-' . rand(1000, 9999);
        $quotation = new \App\Models\TourQuotation();
        $quotation->customer_name = substr($request->input('name'), 0, 150);
        $quotation->email = substr($request->input('email'), 0, 150);
        $quotation->phone = substr($request->input('phone', ''), 0, 17);
        $quotation->ref_number = $refNumber;
        $quotation->travel_date = $request->input('travel_date', '');
        $quotation->days = 0;
        $quotation->nights = 0;
        $quotation->pricing_base = 0;
        $quotation->description = substr($request->input('message', ''), 0, 500);
        $quotation->travelers_number = intval($request->input('travelers', 1));
        $quotation->lang = substr($lang, 0, 4);
        $quotation->added_by = auth()->check() ? auth()->id() : 0;
        $quotation->last_edited = time();
        $quotation->views = 0;
        $quotation->total_cost = 0;
        $quotation->total = 0;
        $quotation->status = 'draft';
        $quotation->save();

        $customerName  = $request->input('name');
        $customerEmail = $request->input('email');
        $adminEmail    = 'info@pvt.jo';

        // 1) Notify admin
        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($quotation, $customerName, $customerEmail, $adminEmail, $refNumber) {
                $body  = "<h2>New Inquiry Received — {$refNumber}</h2>";
                $body .= "<p><strong>Name:</strong> {$customerName}</p>";
                $body .= "<p><strong>Email:</strong> {$customerEmail}</p>";
                $body .= "<p><strong>Phone:</strong> " . ($quotation->phone ?: '—') . "</p>";
                $body .= "<p><strong>Travel Date:</strong> " . ($quotation->travel_date ?: '—') . "</p>";
                $body .= "<p><strong>Travelers:</strong> {$quotation->travelers_number}</p>";
                $body .= "<p><strong>Message:</strong><br>" . nl2br(e($quotation->description)) . "</p>";

                $message->to($adminEmail, 'PVT Reservations')
                        ->subject("New Inquiry: {$refNumber} — {$customerName}")
                        ->html($body);
            });
        } catch (\Exception $e) {
            \Log::error('Inquiry admin email failed: ' . $e->getMessage());
        }

        // 2) Confirmation to customer
        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($customerName, $customerEmail, $refNumber) {
                $body  = "<p>Dear {$customerName},</p>";
                $body .= "<p>Thank you for reaching out to PV Travels. We have received your inquiry (<strong>{$refNumber}</strong>) and our team will get back to you shortly.</p>";
                $body .= "<p>Best regards,<br>PV Travels Team<br>info@pvt.jo</p>";

                $message->to($customerEmail, $customerName)
                        ->subject("We received your inquiry — {$refNumber}")
                        ->html($body);
            });
        } catch (\Exception $e) {
            \Log::error('Inquiry customer email failed: ' . $e->getMessage());
        }

        return redirect('/' . $lang . '/inquiry/')->with('success', 'Thank you! We received your inquiry and will contact you soon.');
    }

    /**
     * CMS page view
     */
    public function page($lang, $slug)
    {
        $data = $this->getCommonData($lang);

        // Find page by slug (with fallback to English)
        $page = DB::table('en33_pages')
            ->where('url', $slug)
            ->where('lang', $lang)
            ->first();

        // Fallback to English if not found
        if (!$page && strtolower($lang) !== 'en') {
            $page = DB::table('en33_pages')
                ->where('url', $slug)
                ->where('lang', 'en')
                ->first();
        }

        if (!$page) {
            abort(404);
        }

        // Fetch alternate URLs for language switcher
        $alternateUrls = DB::table('en33_pages')
            ->where('lang_id', $page->lang_id)
            ->get(['lang', 'url'])
            ->pluck('url', 'lang')
            ->map(function($slug, $l) {
                return url($l . '/' . $slug);
            })->toArray();
        $data['alternateUrls'] = $alternateUrls;

        // Parse shortcodes in the decoded content
        $decodedContents = htmlspecialchars_decode($page->contents ?? '');
        $data['pageContent'] = $this->parseShortcodes($decodedContents, $lang);
        $data['page'] = $page;
        
        return view('frontend.page', $data);
    }

    /**
     * Parse shortcodes within CMS content
     */
    private function parseShortcodes($content, $lang)
    {
        // Support [sc_tours_country country=123,124] or [sc_tours_country ids=123]
        $content = preg_replace_callback('/\[sc_tours_country\s+(country|ids)=["\']?([^"\'\]\s]+)["\']?\]/', function($matches) use ($lang) {
            $ids = array_map('trim', explode(',', $matches[2]));
            
            $tours = DB::table('en33_tours as t')
                ->leftJoin('en33_tours_contents as c', function($join) use ($lang) {
                    $join->on('t.id', '=', 'c.tour_id')->where('c.lang', '=', $lang);
                })
                ->leftJoin('en33_cities as city', function($join) use ($lang) {
                    $join->on('city.lang_id', '=', 't.start_city')->where('city.lang', '=', $lang);
                })
                ->where('t.status', '1')
                ->whereIn('t.start_country', $ids)
                ->select('t.*', 'c.title', 'c.desc', 'c.meta_desc', 'c.url', 'city.name as city')
                ->get();

            $countries = DB::table('en33_countries')->where('lang', $lang)->whereIn('lang_id', [71, 123, 1565])->orderByRaw('FIELD(lang_id, 123, 1565, 71)')->pluck('name', 'lang_id');

            if ($tours->isEmpty()) return '';

            return view('frontend.partials.tours_grid', compact('tours', 'lang', 'countries'))->render();
        }, $content);

        // Support [sc_cats_ids ids=23,24]
        $content = preg_replace_callback('/\[sc_cats_ids\s+(ids|id)=["\']?([^"\'\]\s]+)["\']?\]/', function($matches) use ($lang) {
            $ids = array_map('trim', explode(',', $matches[2]));
            
            $tours = DB::table('en33_tours as t')
                ->leftJoin('en33_tours_contents as c', function($join) use ($lang) {
                    $join->on('t.id', '=', 'c.tour_id')->where('c.lang', '=', $lang);
                })
                ->leftJoin('en33_cities as city', function($join) use ($lang) {
                    $join->on('city.lang_id', '=', 't.start_city')->where('city.lang', '=', $lang);
                })
                ->where('t.status', '1')
                ->whereIn('t.category', $ids)
                ->select('t.*', 'c.title', 'c.desc', 'c.meta_desc', 'c.url', 'city.name as city')
                ->get();

            $countries = DB::table('en33_countries')->where('lang', $lang)->whereIn('lang_id', [71, 123, 1565])->orderByRaw('FIELD(lang_id, 123, 1565, 71)')->pluck('name', 'lang_id');

            if ($tours->isEmpty()) return '';

            return view('frontend.partials.tours_grid', compact('tours', 'lang', 'countries'))->render();
        }, $content);

        return $content;
    }
}
