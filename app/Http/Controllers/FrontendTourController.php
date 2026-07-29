<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourContent;
use App\Models\TourImage;
use App\Models\TourInclusion;
use App\Models\TourTec;
use App\Models\TourCategory;
use App\Models\TourType;
use App\Models\TourBooking;
use App\Models\Country;
use App\Models\City;
use App\Models\Invoice;
use Illuminate\Http\Request;

class FrontendTourController extends Controller
{
    public function show($lang, $country, $slug)
    {
        $commonData = (new FrontendController())->getCommonData($lang);

        $content = TourContent::where('url', $slug)->where('lang', $lang)->first();
        if (!$content) {
            abort(404);
        }

        $tour = Tour::findOrFail($content->tour_id);
        $isAdmin = auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin();
        if ($tour->status == 0 && !$isAdmin) {
            abort(404);
        }

        // Get related data
        $category = TourCategory::where('lang_id', $tour->category)->where('lang', $lang)->first();
        $type = TourType::where('lang_id', $tour->type)->where('lang', $lang)->first();
        $startCountry = Country::where('lang_id', $tour->start_country)->where('lang', $lang)->first();
        $finishCountry = Country::where('lang_id', $tour->finish_country)->where('lang', $lang)->first();
        $startCity = City::where('lang_id', $tour->start_city)->where('lang', $lang)->first();
        $finishCity = City::where('lang_id', $tour->finish_city)->where('lang', $lang)->first();

        // Images
        $images = TourImage::where('tour_id', $tour->id)->orderBy('id', 'desc')->get();

        // Pricing (regular, low season, high season)
        $pricingBases = $tour->pricing_bases ? @unserialize($tour->pricing_bases, ['allowed_classes' => false]) : [];
        $pricingGroups = $tour->pricing_groups ? @unserialize($tour->pricing_groups, ['allowed_classes' => false]) : [];
        $pricingBasesLow = $tour->pricing_bases_low ? @unserialize($tour->pricing_bases_low, ['allowed_classes' => false]) : [];
        $pricingGroupsLow = $tour->pricing_groups_low ? @unserialize($tour->pricing_groups_low, ['allowed_classes' => false]) : [];
        $pricingBasesHigh = $tour->pricing_bases_high ? @unserialize($tour->pricing_bases_high, ['allowed_classes' => false]) : [];
        $pricingGroupsHigh = $tour->pricing_groups_high ? @unserialize($tour->pricing_groups_high, ['allowed_classes' => false]) : [];
        if (!is_array($pricingBases)) $pricingBases = [];
        if (!is_array($pricingGroups)) $pricingGroups = [];
        if (!is_array($pricingBasesLow)) $pricingBasesLow = [];
        if (!is_array($pricingGroupsLow)) $pricingGroupsLow = [];
        if (!is_array($pricingBasesHigh)) $pricingBasesHigh = [];
        if (!is_array($pricingGroupsHigh)) $pricingGroupsHigh = [];
        ksort($pricingBases);
        ksort($pricingGroups);

        // Seasons data
        $seasons = \App\Models\TourSeason::select('from_date', 'to_date', 'type')->get()->toArray();

        // Pricing base labels
        $pricingBaseLabels = [
            0 => 'No Hotel Accommodations',
            1 => '1 Star', 2 => '2 Star', 3 => '3 Star',
            4 => '4 Star', 5 => '5 Star'
        ];

        // Technical Details
        $tecItems = TourTec::where('lang', $lang)->get();
        $tourTec = $tour->tec_details ? @unserialize($tour->tec_details, ['allowed_classes' => false]) : [];
        if (!is_array($tourTec)) $tourTec = [];

        // Inclusions
        $incItems = TourInclusion::where('lang', $lang)->get();
        $tourInc = $tour->inclusions ? @unserialize($tour->inclusions, ['allowed_classes' => false]) : [];
        if (!is_array($tourInc)) $tourInc = [];

        // Related Tours
        $relatedTours = Tour::with('contents')
            ->where('category', $tour->category)
            ->where('id', '!=', $tour->id)
            ->where('status', 1)
            ->inRandomOrder()
            ->limit($tour->relative_count ?: 5)
            ->get();

        // Fetch alternate URLs for language switcher
        $alternateContents = TourContent::where('tour_id', $tour->id)->get(['lang', 'url'])->pluck('url', 'lang');
        $alternateCountries = Country::where('lang_id', $tour->start_country)->get(['lang', 'name'])->pluck('name', 'lang');
        
        $alternateUrls = [];
        foreach($alternateContents as $l => $tourSlug) {
            $countryName = $alternateCountries[$l] ?? 'jordan';
            $alternateUrls[$l] = url($l . '/tours/' . strtolower($countryName) . '/' . $tourSlug);
        }

        return view('frontend.tour', array_merge($commonData, compact(
            'tour', 'content', 'category', 'type',
            'startCountry', 'finishCountry', 'startCity', 'finishCity',
            'images', 'pricingBases', 'pricingGroups',
            'pricingBasesLow', 'pricingGroupsLow',
            'pricingBasesHigh', 'pricingGroupsHigh',
            'pricingBaseLabels', 'seasons',
            'tecItems', 'tourTec', 'incItems', 'tourInc', 'relatedTours', 'lang', 'alternateUrls'
        )));

    }

    /**
     * Show public booking detail page (expenses/services for a booking)
     * URL: /{lang}/tours/booking/{id}-{code}/
     */
    public function showBooking($lang, $bookingSlug)
    {
        // Parse slug: "1069-fea0e" => id=1069, code=fea0e
        $parts = explode('-', $bookingSlug, 2);
        if (count($parts) !== 2) {
            abort(404);
        }

        $bookingId = intval($parts[0]);
        $code = $parts[1];

        // Validate code (security hash)
        $expectedCode = substr(md5($bookingId), 0, 5);
        if ($code !== $expectedCode) {
            abort(404);
        }

        $booking = TourBooking::with(['user', 'invoice', 'tour.contents'])->findOrFail($bookingId);

        // Load expenses for this booking's invoice
        $expenses = collect();
        if ($booking->invoice_id) {
            $invoiceIds = [$booking->invoice_id];

            // Check for linked invoices
            if ($booking->invoice && !empty($booking->invoice->invoices_set)) {
                $linkedInvoices = @unserialize($booking->invoice->invoices_set, ['allowed_classes' => false]);
                if (is_array($linkedInvoices) && count($linkedInvoices) > 0) {
                    $invoiceIds = $linkedInvoices;
                }
            }

            $expenses = \App\Models\InvoiceExpense::with(['service.serviceCategory.parent', 'venderUser'])
                ->whereIn('invoice_id', $invoiceIds)
                ->orderBy('service_date', 'asc')
                ->get();
        }

        // Guest name or user name for page title
        $guestName = '';
        if (!empty($booking->guest_name)) {
            $guestName = $booking->guest_name;
        } elseif ($booking->user) {
            $guestName = trim($booking->user->first_name . ' ' . $booking->user->last_name);
            if (empty($guestName) && !empty($booking->user->company)) {
                $guestName = $booking->user->company;
            }
        }

        // Country names for expenses
        $countries = \App\Models\Country::where('lang', 'en')->pluck('name', 'lang_id')->toArray();

        // Status maps
        $statusList = [
            'pen' => 'Pending', 'can' => 'Cancelled', 'com' => 'Completed',
            'inp' => 'In Process', 'con' => 'Confirmed',
        ];
        $statusColors = [
            'pen' => 'orange', 'can' => 'red', 'com' => 'green',
            'inp' => 'blue', 'con' => 'green',
        ];

        // Common frontend data
        $frontendCtrl = new \App\Http\Controllers\FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        return view('frontend.booking_detail', array_merge($commonData, compact(
            'booking', 'expenses', 'guestName', 'countries',
            'statusList', 'statusColors', 'lang'
        )));
    }

    /**
     * Parse date to Y-m-d format.
     * The datepicker uses yyyy-mm-dd format, but also handle DD-MM-YYYY.
     */
    private function parseDate($dateStr)
    {
        if (empty($dateStr)) return null;

        $parts = preg_split('/[-\/\.]/', $dateStr);
        if (count($parts) === 3) {
            // YYYY-MM-DD format (from datepicker)
            if (intval($parts[0]) >= 2000) {
                return sprintf('%04d-%02d-%02d', intval($parts[0]), intval($parts[1]), intval($parts[2]));
            }
            // DD-MM-YYYY format
            $day = intval($parts[0]);
            $month = intval($parts[1]);
            $year = intval($parts[2]);
            if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12 && $year >= 2000) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // Fallback to strtotime
        $ts = strtotime($dateStr);
        if ($ts) return date('Y-m-d', $ts);

        return null;
    }

    /**
     * Determine season type for a given date
     */
    private function getSeasonType($dateStr)
    {
        $seasons = \App\Models\TourSeason::select('from_date', 'to_date', 'type')->get();
        $selectedDate = strtotime($dateStr);
        if (!$selectedDate) return null;

        foreach ($seasons as $season) {
            $from = strtotime($season->from_date);
            $to = strtotime($season->to_date);
            if ($selectedDate >= $from && $selectedDate <= $to) {
                return $season->type;
            }
        }
        return null; // Regular season
    }

    /**
     * Get the correct pricing arrays based on season
     */
    private function getSeasonalPricing(Tour $tour, $seasonType)
    {
        if ($seasonType === 'H') {
            $bases = $tour->pricing_bases_high ? @unserialize($tour->pricing_bases_high, ['allowed_classes' => false]) : [];
            $groups = $tour->pricing_groups_high ? @unserialize($tour->pricing_groups_high, ['allowed_classes' => false]) : [];
        } elseif ($seasonType === 'L') {
            $bases = $tour->pricing_bases_low ? @unserialize($tour->pricing_bases_low, ['allowed_classes' => false]) : [];
            $groups = $tour->pricing_groups_low ? @unserialize($tour->pricing_groups_low, ['allowed_classes' => false]) : [];
        } else {
            $bases = $tour->pricing_bases ? @unserialize($tour->pricing_bases, ['allowed_classes' => false]) : [];
            $groups = $tour->pricing_groups ? @unserialize($tour->pricing_groups, ['allowed_classes' => false]) : [];
        }
        if (!is_array($bases)) $bases = [];
        if (!is_array($groups)) $groups = [];
        return [$bases, $groups];
    }

    /**
     * Calculate pricing for a given base and traveler counts
     */
    private function calculatePricing($bases, $groups, $priceBase, $adultCount, $childCount, $infantCount)
    {
        $basePricing = $bases[$priceBase] ?? null;
        if (!$basePricing) {
            return [
                'adult_price' => 0, 'child_price' => 0, 'infant_price' => 0,
                'adult_total' => 0, 'child_total' => 0, 'infant_total' => 0,
                'single_supplement' => 0, 'total' => 0,
            ];
        }

        $travelersCount = $adultCount + $childCount;

        // Get group pricing (override base price if group exists)
        $adultPrice = floatval($basePricing['price'] ?? 0);
        $childPrice = floatval($basePricing['price'] ?? 0);
        $infantPrice = floatval($basePricing['price'] ?? 0);

        if (isset($groups[$priceBase]) && is_array($groups[$priceBase])) {
            foreach ($groups[$priceBase] as $minTravelers => $val) {
                if ($travelersCount >= intval($minTravelers)) {
                    $adultPrice = floatval($val['adult'] ?? $adultPrice);
                    $childPrice = floatval($val['child'] ?? $childPrice);
                    $infantPrice = floatval($val['infant'] ?? $infantPrice);
                }
            }
        }

        $adultTotal = $adultPrice * $adultCount;
        $childTotal = ($childCount > 0) ? $childPrice * $childCount : 0;
        $infantTotal = ($infantCount > 0) ? $infantPrice * $infantCount : 0;
        $singleSupplement = floatval($basePricing['single_supplement'] ?? 0);

        return [
            'adult_price' => $adultPrice,
            'child_price' => $childPrice,
            'infant_price' => $infantPrice,
            'adult_total' => $adultTotal,
            'child_total' => $childTotal,
            'infant_total' => $infantTotal,
            'single_supplement' => $singleSupplement,
            'total' => $adultTotal + $childTotal + $infantTotal,
        ];
    }

    /**
     * Handle "Book it Now" button click - show confirmation page (step 2)
     * OR if confirmed, create booking.
     */
    public function showBookTour(Request $request, $lang, $id)
    {
        $tour = Tour::findOrFail($id);
        $content = TourContent::where('tour_id', $id)->where('lang', $lang)->first();
        if (!$content) {
            abort(404);
        }

        // Common frontend data
        $frontendCtrl = new \App\Http\Controllers\FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        // Default values for the form
        $travelDate = date('Y-m-d', strtotime('+30 days'));
        $adultCount = 2;
        $childCount = 0;
        $infantCount = 0;
        $roomsSingle = 0;
        $roomsDouble = 1;
        $roomsTwin = 0;
        $roomsTriple = 0;
        $roomsQuad = 0;
        $bookingNote = '';

        // Hotel grade labels
        $pricingBaseLabels = [
            0 => 'No Hotel Accommodations',
            1 => '1 Star', 2 => '2 Star', 3 => '3 Star',
            4 => '4 Star', 5 => '5 Star'
        ];

        // Determine available hotel grades from all seasonal pricing
        $allBases = $tour->pricing_bases ? @unserialize($tour->pricing_bases, ['allowed_classes' => false]) : [];
        $allBasesLow = $tour->pricing_bases_low ? @unserialize($tour->pricing_bases_low, ['allowed_classes' => false]) : [];
        $allBasesHigh = $tour->pricing_bases_high ? @unserialize($tour->pricing_bases_high, ['allowed_classes' => false]) : [];
        if (!is_array($allBases)) $allBases = [];
        if (!is_array($allBasesLow)) $allBasesLow = [];
        if (!is_array($allBasesHigh)) $allBasesHigh = [];
        $availableGradeKeys = array_unique(array_merge(array_keys($allBases), array_keys($allBasesLow), array_keys($allBasesHigh)));
        sort($availableGradeKeys);
        $availableGrades = [];
        foreach ($availableGradeKeys as $gk) {
            $availableGrades[$gk] = $pricingBaseLabels[$gk] ?? ($gk . ' Star');
        }
        // If no grades found, fallback to showing 3,4,5
        if (empty($availableGrades)) {
            $availableGrades = [3 => '3 Star', 4 => '4 Star', 5 => '5 Star'];
        }

        // Default to first available grade
        $priceBase = array_key_first($availableGrades);
        $hotelRate = $pricingBaseLabels[$priceBase] ?? 'N/A';

        // Pricing logic for the initial view
        $seasonType = $this->getSeasonType($travelDate);
        list($currentBases, $currentGroups) = $this->getSeasonalPricing($tour, $seasonType);
        $pricing = $this->calculatePricing($currentBases, $currentGroups, $priceBase, $adultCount, $childCount, $infantCount);
        $grandTotal = $pricing['total'];

        // Extra data for JS
        $pricingBases = $tour->pricing_bases ? @unserialize($tour->pricing_bases, ['allowed_classes' => false]) : [];
        $pricingGroups = $tour->pricing_groups ? @unserialize($tour->pricing_groups, ['allowed_classes' => false]) : [];
        $pricingBasesLow = $tour->pricing_bases_low ? @unserialize($tour->pricing_bases_low, ['allowed_classes' => false]) : [];
        $pricingGroupsLow = $tour->pricing_groups_low ? @unserialize($tour->pricing_groups_low, ['allowed_classes' => false]) : [];
        $pricingBasesHigh = $tour->pricing_bases_high ? @unserialize($tour->pricing_bases_high, ['allowed_classes' => false]) : [];
        $pricingGroupsHigh = $tour->pricing_groups_high ? @unserialize($tour->pricing_groups_high, ['allowed_classes' => false]) : [];
        $seasons = \App\Models\TourSeason::select('from_date', 'to_date', 'type')->get()->toArray();
        $startCountry = Country::where('lang_id', $tour->start_country)->where('lang', $lang)->first();

        $user = auth()->user();
        $isUser = auth()->check();

        return view('frontend.book_tour', array_merge($commonData, compact(
            'tour', 'content', 'lang', 'startCountry',
            'travelDate', 'priceBase', 'hotelRate',
            'adultCount', 'childCount', 'infantCount',
            'roomsSingle', 'roomsDouble', 'roomsTwin', 'roomsTriple', 'roomsQuad',
            'bookingNote', 'pricingBaseLabels', 'availableGrades', 'pricing', 'grandTotal',
            'pricingBases', 'pricingGroups', 'pricingBasesLow', 'pricingGroupsLow',
            'pricingBasesHigh', 'pricingGroupsHigh', 'seasons', 'user', 'isUser'
        )));
    }

    public function bookTour(Request $request, $lang, $id)
    {
        $tour = Tour::findOrFail($id);
        $content = TourContent::where('tour_id', $id)->where('lang', $lang)->first();
        if (!$content) {
            abort(404);
        }

        // Set locale for translations
        \Illuminate\Support\Facades\App::setLocale($lang);

        // Validate request with translated messages
        $request->validate([
            'guest_name' => 'required|string|max:50',
            'email' => 'required|email|max:60',
            'travel_date' => 'required|date|after:today',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'hotel_grade' => 'required|integer|min:0|max:5',
            'phone' => 'required|string|max:20',
            'rooms_double' => 'nullable|integer|min:0',
            'rooms_single' => 'nullable|integer|min:0',
            'rooms_triple' => 'nullable|integer|min:0',
        ], [
            'guest_name.required' => __('Full Name is required.'),
            'email.required' => __('Email Address is required.'),
            'email.email' => __('Please enter a valid email address.'),
            'travel_date.required' => __('Travel date is required.'),
            'travel_date.after' => __('Travel date must be a future date.'),
            'adults.required' => __('Number of adults is required.'),
            'adults.min' => __('At least 1 adult is required.'),
            'phone.required' => __('Phone number is required.'),
            'hotel_grade.required' => __('Hotel standard is required.'),
        ]);

        // At least one room type must be selected
        $totalRooms = intval($request->input('rooms_double', 0)) + intval($request->input('rooms_single', 0)) + intval($request->input('rooms_triple', 0));
        if ($totalRooms < 1) {
            return back()->withErrors(['rooms' => __('Please select at least one room.')])->withInput();
        }

        // Check if logged in, if not redirect to login
        if (!auth()->check()) {
            return redirect('/' . $lang . '/users/login/?ret=' . urlencode($request->fullUrl()));
        }

        $dbDate = $request->input('travel_date');
        $priceBase = intval($request->input('hotel_grade', 4));
        $adultCount = intval($request->input('adults', 1));
        $childCount = intval($request->input('children', 0));
        $infantCount = intval($request->input('infants', 0));
        $roomsSingle = intval($request->input('rooms_single', 0));
        $roomsDouble = intval($request->input('rooms_double', 0));
        $roomsTriple = intval($request->input('rooms_triple', 0));

        // Calculate pricing for invoice
        $seasonType = $this->getSeasonType($dbDate);
        list($currentBases, $currentGroups) = $this->getSeasonalPricing($tour, $seasonType);
        $pricing = $this->calculatePricing($currentBases, $currentGroups, $priceBase, $adultCount, $childCount, $infantCount);

        // Single supplement
        $singleSupplementFee = floatval($currentBases[$priceBase]['single_supplement'] ?? 0);
        $singleSupplementTotal = 0;
        if ($priceBase > 0 && $roomsSingle > 0 && ($adultCount + $childCount) > 1) {
            $singleSupplementTotal = $singleSupplementFee * $roomsSingle;
        }

        $grandTotal = $pricing['total'] + $singleSupplementTotal;

        // Fallback: if pricing calculation returned 0, use min_price * pax count
        if ($grandTotal <= 0) {
            $fallbackPrice = floatval($tour->min_price ?? 100);
            $childFallback = $fallbackPrice * 0.5;
            $grandTotal = ($fallbackPrice * $adultCount) + ($childFallback * $childCount);
            $pricing['adult_price'] = $fallbackPrice;
            $pricing['child_price'] = $childFallback;
            $pricing['infant_price'] = 0;
            $pricing['total'] = $grandTotal;
        }

        // Build invoice items array
        $invoiceItems = [];
        if ($adultCount > 0) {
            $invoiceItems[] = ['name' => 'Adult', 'qty' => $adultCount, 'price' => $pricing['adult_price']];
        }
        if ($childCount > 0) {
            $invoiceItems[] = ['name' => 'Child', 'qty' => $childCount, 'price' => $pricing['child_price']];
        }
        if ($infantCount > 0) {
            $invoiceItems[] = ['name' => 'Infant', 'qty' => $infantCount, 'price' => $pricing['infant_price']];
        }
        if ($singleSupplementTotal > 0) {
            $invoiceItems[] = ['name' => 'Single Supplement Fee', 'qty' => $roomsSingle, 'price' => $singleSupplementFee];
        }

        // Get tour title for invoice description
        $tourTitle = $content->title ?? 'Tour Booking';

        // Create Invoice
        $invoice = new \App\Models\Invoice();
        $invoice->items = serialize($invoiceItems);
        $invoice->discount = 0;
        $invoice->tax = 0;
        $invoice->status = 'u'; // unpaid
        $invoice->type = 'o';   // online
        $invoice->module = 'tours';
        $invoice->user_id = auth()->id();
        $invoice->desc = 'Booking > ' . $tourTitle;
        $invoice->date = date('Y-m-d');
        $invoice->total = $grandTotal;
        $invoice->cost = 0;
        $invoice->added_by = auth()->id();
        $invoice->paid_by = '';
        $invoice->partly_payment = 0;
        $invoice->total_paid = 0;
        $invoice->due_to_date = date('Y-m-d', strtotime('+2 days'));
        $invoice->discount_description = '';
        $invoice->sent_count = 0;
        $invoice->invoices_set = '';
        $invoice->save();

        // Create Booking
        $booking = new TourBooking();
        $booking->tour_id = $tour->id;
        $booking->user_id = auth()->id();
        $booking->invoice_id = $invoice->id;
        $booking->travel_date = $dbDate;
        $booking->booked_in_date = date('Y-m-d');
        $booking->days = intval($tour->days);
        $booking->nights = intval($tour->nights);
        $booking->hotel_grade = $priceBase;
        $booking->adult = $adultCount;
        $booking->child = $childCount;
        $booking->infant = $infantCount;
        $booking->room_single = $roomsSingle;
        $booking->rooms_double = $roomsDouble;
        $booking->rooms_twin = 0;
        $booking->rooms_triple = $roomsTriple;
        $booking->rooms_quad = 0;
        $booking->note = substr($request->input('note', ''), 0, 500);
        $booking->start_country = $tour->start_country;
        $booking->added_by = auth()->id();
        $booking->trip_status = 'pen';
        $booking->guest_name = $request->input('guest_name', auth()->user()->name ?? '');
        $booking->save();

        // --- Send email notifications ---
        $bGuestName  = $request->input('guest_name');
        $bGuestEmail = $request->input('email');
        $bTourTitle  = $content->title ?? 'Tour Booking';

        // 1) Notify admin (info@pvt.jo + rarkumar777@gmail.com)
        try {
            $adminBody  = "<h2 style='color:#f59e0b;'>New Booking Request — PV Travels</h2>";
            $adminBody .= "<table style='border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:14px;'>";
            $adminBody .= "<tr><td style='padding:8px;font-weight:bold;color:#555;width:140px;'>Tour:</td><td style='padding:8px;'>" . e($bTourTitle) . "</td></tr>";
            $adminBody .= "<tr style='background:#f9f9f9;'><td style='padding:8px;font-weight:bold;color:#555;'>Booking ID:</td><td style='padding:8px;'>#BK-{$booking->id}</td></tr>";
            $adminBody .= "<tr><td style='padding:8px;font-weight:bold;color:#555;'>Guest:</td><td style='padding:8px;'>" . e($bGuestName) . "</td></tr>";
            $adminBody .= "<tr style='background:#f9f9f9;'><td style='padding:8px;font-weight:bold;color:#555;'>Email:</td><td style='padding:8px;'><a href='mailto:" . e($bGuestEmail) . "'>" . e($bGuestEmail) . "</a></td></tr>";
            $adminBody .= "<tr><td style='padding:8px;font-weight:bold;color:#555;'>Travel Date:</td><td style='padding:8px;'>" . e($dbDate) . "</td></tr>";
            $adminBody .= "<tr style='background:#f9f9f9;'><td style='padding:8px;font-weight:bold;color:#555;'>Adults:</td><td style='padding:8px;'>{$adultCount} Adults | {$childCount} Children | {$infantCount} Infants</td></tr>";
            $adminBody .= "<tr><td style='padding:8px;font-weight:bold;color:#555;'>Hotel Grade:</td><td style='padding:8px;'>{$priceBase} Star</td></tr>";
            $adminBody .= "<tr style='background:#f9f9f9;'><td style='padding:8px;font-weight:bold;color:#555;'>Total:</td><td style='padding:8px;font-weight:bold;color:#16a34a;'>JD " . number_format($grandTotal, 2) . "</td></tr>";
            $adminBody .= "</table>";
            $adminBody .= "<hr style='margin:20px 0;border:none;border-top:1px solid #eee;'>";
            $adminBody .= "<p style='color:#999;font-size:12px;'>This booking was submitted via PV Travels website.</p>";

            \Illuminate\Support\Facades\Mail::html($adminBody, function ($msg) use ($bGuestName, $bTourTitle, $booking) {
                $msg->to('info@pvt.jo', 'PV Travels')
                    ->to('rarkumar777@gmail.com', 'Admin')
                    ->subject("New Booking #BK-{$booking->id}: {$bTourTitle} — {$bGuestName}");
            });
        } catch (\Exception $e) {
            \Log::error('Booking admin email failed: ' . $e->getMessage());
        }

        // 2) Confirmation to customer
        try {
            $customerBody  = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>";
            $customerBody .= "<div style='background:#f59e0b;padding:30px;text-align:center;border-radius:8px 8px 0 0;'>";
            $customerBody .= "<h1 style='color:#fff;margin:0;font-size:24px;'>Booking Request Received!</h1></div>";
            $customerBody .= "<div style='background:#fff;padding:30px;border:1px solid #eee;border-top:none;border-radius:0 0 8px 8px;'>";
            $customerBody .= "<p style='font-size:16px;'>Dear " . e($bGuestName) . ",</p>";
            $customerBody .= "<p>Thank you for booking <strong>" . e($bTourTitle) . "</strong> with PV Travels. We have received your request and our team will contact you shortly to confirm your reservation.</p>";
            $customerBody .= "<table style='border-collapse:collapse;width:100%;font-size:14px;margin:20px 0;'>";
            $customerBody .= "<tr style='background:#f9f9f9;'><td style='padding:10px;font-weight:bold;color:#555;'>Booking ID:</td><td style='padding:10px;font-weight:bold;'>#BK-{$booking->id}</td></tr>";
            $customerBody .= "<tr><td style='padding:10px;font-weight:bold;color:#555;'>Tour:</td><td style='padding:10px;'>" . e($bTourTitle) . "</td></tr>";
            $customerBody .= "<tr style='background:#f9f9f9;'><td style='padding:10px;font-weight:bold;color:#555;'>Travel Date:</td><td style='padding:10px;'>" . e($dbDate) . "</td></tr>";
            $customerBody .= "<tr><td style='padding:10px;font-weight:bold;color:#555;'>Total Amount:</td><td style='padding:10px;font-weight:bold;color:#16a34a;'>JD " . number_format($grandTotal, 2) . "</td></tr>";
            $customerBody .= "</table>";
            $customerBody .= "<p>If you have any questions, please contact us at <a href='mailto:info@pvt.jo'>info@pvt.jo</a> or call <a href='tel:+96277996601'>+962 77996601</a>.</p>";
            $customerBody .= "<p style='margin-top:30px;'>Best regards,<br><strong>PV Travels Team</strong></p>";
            $customerBody .= "</div></div>";

            \Illuminate\Support\Facades\Mail::html($customerBody, function ($msg) use ($bGuestName, $bGuestEmail, $bTourTitle, $booking) {
                $msg->to($bGuestEmail, $bGuestName)
                    ->subject("Booking Confirmed #BK-{$booking->id}: {$bTourTitle} — PV Travels");
            });
        } catch (\Exception $e) {
            \Log::error('Booking customer email failed: ' . $e->getMessage());
        }
        // --- End email notifications ---


        // --- Initiate PayTabs Payment ---
        $paytabsConfig = config('services.paytabs');
        $tourTitle = $content->title ?? 'Tour Booking';

        // Skip payment if PAYTABS_SKIP_PAYMENT=true in .env (admin bypass)
        if (env('PAYTABS_SKIP_PAYMENT', false)) {
            $invoice = \App\Models\Invoice::find($booking->invoice_id);
            if ($invoice) {
                $invoice->status   = 'p';
                $invoice->paid_by  = 'manual';
                $invoice->save();
            }
            $booking->trip_status = 'con';
            $booking->save();
            return redirect('/' . $lang . '/tours/booking_success/' . $booking->id)
                ->with('success', 'Booking created and confirmed! (Payment bypassed for testing)');
        }

        // LOCAL SIMULATE: PayTabs rejects localhost URLs (error code 210: Invalid Callback URL)
        // Set PAYTABS_LOCAL_SIMULATE=true in .env for local testing
        if (env('PAYTABS_LOCAL_SIMULATE', false)) {
            \Log::info('PayTabs Local Simulate: Marking booking #' . $booking->id . ' as paid (simulate mode)');
            $invoice = \App\Models\Invoice::find($booking->invoice_id);
            if ($invoice) {
                $invoice->status     = 'p';
                $invoice->total_paid = $invoice->total;
                $invoice->paid_by    = 'paytabs_simulated';
                $invoice->save();
            }
            $booking->trip_status = 'con';
            $booking->save();
            return redirect('/' . $lang . '/tours/booking_success/' . $booking->id)
                ->with('success', 'Booking confirmed! Payment simulated successfully for local testing.');
        }

        // Build PayTabs return/callback URLs using APP_URL
        $appUrl      = rtrim(config('app.url'), '/');
        $returnUrl   = $appUrl . '/payment/return?booking_id=' . $booking->id;
        $callbackUrl = $appUrl . '/payment/callback';

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(20)->withHeaders([
                'Authorization' => $paytabsConfig['server_key'] ?? '',
                'Content-Type'  => 'application/json',
            ])->post($paytabsConfig['base_url'] . 'payment/request', [
                'profile_id'       => (int)($paytabsConfig['profile_id'] ?? 0),
                'tran_type'        => 'sale',
                'tran_class'       => 'ecom',
                'cart_id'          => (string)$booking->id,
                'cart_currency'    => $paytabsConfig['currency'] ?? 'USD',
                'cart_amount'      => (float)$grandTotal,
                'cart_description' => 'Tour Booking: ' . $tourTitle,
                'paypage_lang'     => $lang,
                'customer_details' => [
                    'name'    => $request->input('guest_name'),
                    'email'   => $request->input('email'),
                    'phone'   => $request->input('phone', '00000000'),
                    'street1' => 'n/a',
                    'city'    => 'Amman',
                    'state'   => 'Amman',
                    'country' => 'JO',
                    'zip'     => '11181',
                ],
                'callback' => $callbackUrl,
                'return'   => $returnUrl,
            ]);

            \Log::info('PayTabs Booking Request', [
                'http_code'  => $response->status(),
                'booking_id' => $booking->id,
                'response'   => $response->json(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['redirect_url'])) {
                    session([
                        'paytabs_tran_ref'   => $data['tran_ref'] ?? '',
                        'paytabs_booking_id' => $booking->id,
                    ]);
                    return redirect($data['redirect_url']);
                }
            }

            // PayTabs API returned error — extract exact message
            $respData = $response->json();
            $ptCode   = $respData['code']    ?? '';
            $ptMsg    = $respData['message'] ?? 'Unknown error (HTTP ' . $response->status() . ')';
            \Log::error('PayTabs Booking Payment Failed', [
                'http_code'  => $response->status(),
                'pt_code'    => $ptCode,
                'pt_msg'     => $ptMsg,
                'booking_id' => $booking->id,
            ]);

            // In test mode: force success as fallback when API fails
            if (env('PAYTABS_TEST_MODE')) {
                \Log::info('PayTabs TEST_MODE: Force success fallback for booking #' . $booking->id);
                $invoice = \App\Models\Invoice::find($booking->invoice_id);
                if ($invoice) {
                    $invoice->status     = 'p';
                    $invoice->total_paid = $invoice->total;
                    $invoice->paid_by    = 'paytabs_test_fallback';
                    $invoice->save();
                }
                $booking->trip_status = 'con';
                $booking->save();
                return redirect('/' . $lang . '/tours/booking_success/' . $booking->id)
                    ->with('success', 'Booking confirmed! (Test mode — payment accepted)');
            }

            return redirect('/' . $lang . '/tours/book_tour/' . $tour->id)
                ->with('error', 'Payment gateway error: ' . $ptMsg . '. Please try again or contact support.');

        } catch (\Exception $e) {
            \Log::error('PayTabs Booking Exception', ['error' => $e->getMessage(), 'booking_id' => $booking->id]);

            // Exception fallback in test mode
            if (env('PAYTABS_TEST_MODE')) {
                $invoice = \App\Models\Invoice::find($booking->invoice_id);
                if ($invoice) {
                    $invoice->status     = 'p';
                    $invoice->total_paid = $invoice->total;
                    $invoice->paid_by    = 'paytabs_test_fallback';
                    $invoice->save();
                }
                $booking->trip_status = 'con';
                $booking->save();
                return redirect('/' . $lang . '/tours/booking_success/' . $booking->id)
                    ->with('success', 'Booking confirmed! (Test mode — gateway exception bypassed)');
            }

            return redirect('/' . $lang . '/tours/booking_success/' . $booking->id)
                ->with('success', 'Booking created. Payment gateway unavailable — our team will contact you.');
        }
    }


    public function quotation($lang, $id)
    {
        $quotation = \App\Models\TourQuotation::with('quotationDays')->findOrFail($id);

        // Get the agent (user who added the quotation)
        $agent = null;
        if ($quotation->added_by) {
            $agent = \App\Models\User::find($quotation->added_by);
        }

        // Common frontend data
        $frontendCtrl = new \App\Http\Controllers\FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        return view('frontend.quotation', array_merge($commonData, compact('quotation', 'agent', 'lang')));
    }

    public function showInquery($lang, $id)
    {
        $tour = Tour::findOrFail($id);
        $content = TourContent::where('tour_id', $id)->where('lang', $lang)->first();
        if (!$content) {
            abort(404);
        }

        // Inclusions
        $incItems = TourInclusion::where('lang', $lang)->get();
        $tourInc = $tour->inclusions ? @unserialize($tour->inclusions, ['allowed_classes' => false]) : [];
        if (!is_array($tourInc)) $tourInc = [];

        // Common frontend data
        $frontendCtrl = new \App\Http\Controllers\FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        return view('frontend.inquery', array_merge($commonData, compact('tour', 'content', 'lang', 'incItems', 'tourInc')));
    }

    public function inquery(Request $request, $lang, $id)
    {
        $tour = Tour::findOrFail($id);
        $content = TourContent::where('tour_id', $id)->where('lang', $lang)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'adult' => 'required|numeric|min:1',
            'date' => 'required',
            'captcha' => 'required',
        ]);

        // Validate captcha
        $sessionCaptcha = session('tour_captcha_code');
        if (strtolower($request->captcha) !== strtolower($sessionCaptcha)) {
            return back()->withErrors(['captcha' => 'Invalid captcha code. Please try again.'])->withInput();
        }

        // Build travelers data
        $adultCount = intval($request->input('adult', 1));
        $childCount = intval($request->input('child', 0));
        $infantCount = intval($request->input('infant', 0));
        $totalTravelers = $adultCount + $childCount + $infantCount;

        $hotelGrade = $request->input('hotel_grade', 0);
        $hotelSingle = $request->input('hotel_room_single', 0);
        $hotelDouble = $request->input('hotel_room_double', 0);
        $hotelTriple = $request->input('hotel_room_triple', 0);

        // Generate ref number
        $refNumber = 'INQ-' . date('Ymd') . '-' . rand(1000, 9999);

        // Date processing (from DD-MM-YYYY to YYYY-MM-DD)
        $dbDate = $this->parseDate($request->input('date')) ?? '';

        // Build description with user notes + travelers breakdown
        $userNotes = $request->input('desc') ?? '';
        $detailsText = "Adult: $adultCount, Child: $childCount, Infant: $infantCount";
        $detailsText .= " | Hotel Rooms - Single: $hotelSingle, Double: $hotelDouble, Triple: $hotelTriple";
        if ($hotelGrade > 0) {
            $detailsText .= " | Hotel Grade: $hotelGrade Star";
        }
        $fullDescription = $userNotes ? $userNotes . "\n---\n" . $detailsText : $detailsText;

        // Create quotation record
        $quotation = new \App\Models\TourQuotation();
        $quotation->customer_name = substr($request->input('name') ?? '', 0, 150);
        $quotation->email = substr($request->input('email') ?? '', 0, 150);
        $quotation->phone = substr($request->input('telephone') ?? '', 0, 17);
        $quotation->ref_number = $refNumber;
        $quotation->travel_date = $dbDate;
        $quotation->days = intval($request->input('days') ?? $tour->days ?? 0);
        $quotation->nights = intval($request->input('nights') ?? $tour->nights ?? 0);
        $quotation->pricing_base = intval($hotelGrade);
        $quotation->description = substr($fullDescription, 0, 500);
        $quotation->travelers_number = $totalTravelers;
        $quotation->lang = substr($lang ?? 'en', 0, 4);
        $quotation->added_by = auth()->check() ? auth()->id() : 0;
        $quotation->last_edited = time();
        $quotation->views = 0;
        $quotation->total_cost = 0;
        $quotation->total = 0;
        $quotation->status = 'draft';
        $quotation->save();

        // --- Send email notifications ---
        $iqName    = $request->input('name');
        $iqEmail   = $request->input('email');
        $iqTour    = $content->title ?? 'Tour';
        $iqAdmin   = 'info@pvt.jo';

        // 1) Notify admin
        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($msg) use ($quotation, $iqName, $iqEmail, $iqTour, $iqAdmin, $refNumber, $dbDate, $adultCount, $childCount, $infantCount) {
                $body  = "<h2>New Tour Inquiry — {$refNumber}</h2>";
                $body .= "<p><strong>Tour:</strong> {$iqTour}</p>";
                $body .= "<p><strong>Name:</strong> {$iqName}</p>";
                $body .= "<p><strong>Email:</strong> {$iqEmail}</p>";
                $body .= "<p><strong>Phone:</strong> " . ($quotation->phone ?: '—') . "</p>";
                $body .= "<p><strong>Travel Date:</strong> " . ($dbDate ?: '—') . "</p>";
                $body .= "<p><strong>Adults:</strong> {$adultCount} | <strong>Children:</strong> {$childCount} | <strong>Infants:</strong> {$infantCount}</p>";
                $body .= "<p><strong>Notes:</strong><br>" . nl2br(e($quotation->description)) . "</p>";
                $msg->to($iqAdmin, 'PVT Reservations')
                    ->subject("New Tour Inquiry: {$refNumber} — {$iqName}")
                    ->html($body);
            });
        } catch (\Exception $e) {
            \Log::error('Inquery admin email failed: ' . $e->getMessage());
        }

        // 2) Confirmation to customer
        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($msg) use ($iqName, $iqEmail, $iqTour, $refNumber) {
                $body  = "<p>Dear {$iqName},</p>";
                $body .= "<p>Thank you for your inquiry about <strong>{$iqTour}</strong>. We have received your request (<strong>{$refNumber}</strong>) and will get back to you shortly.</p>";
                $body .= "<p>Best regards,<br>PV Travels Team<br>info@pvt.jo</p>";
                $msg->to($iqEmail, $iqName)
                    ->subject("Inquiry Received: {$iqTour} — {$refNumber}")
                    ->html($body);
            });
        } catch (\Exception $e) {
            \Log::error('Inquery customer email failed: ' . $e->getMessage());
        }
        // --- End email notifications ---

        // Redirect back to tour page with success
        $countryName = Country::where('lang_id', $tour->start_country)->where('lang', $lang)->value('name') ?? '';
        $slug = $content->url ?? '';

        return redirect('/' . $lang . '/tours/' . strtolower($countryName) . '/' . $slug . '/')
            ->with('success', 'We got your request! We will contact you soon.');
    }

    /**
     * Show invoice page (frontend)
     */
    public function showInvoice($lang, $id)
    {
        $invoice = \App\Models\Invoice::findOrFail($id);

        // Only allow the invoice owner to see it
        if (auth()->check() && auth()->id() != $invoice->user_id) {
            // Admin users can also see it
            $user = auth()->user();
            if (!($user->group == 1 || $user->group == 2)) {
                abort(403);
            }
        }

        // Parse invoice items
        $items = @unserialize($invoice->items, ['allowed_classes' => false]);
        if (!is_array($items)) $items = [];

        // Calculate totals
        $subtotal = 0;
        foreach ($items as &$item) {
            $item['total'] = floatval($item['price'] ?? 0) * intval($item['qty'] ?? 1);
            $subtotal += $item['total'];
        }
        unset($item);

        $discount = floatval($invoice->discount ?? 0);
        $tax = floatval($invoice->tax ?? 0);
        $total = floatval($invoice->total ?? $subtotal);
        $grandTotal = $total;

        // Get customer info
        $customer = \App\Models\User::find($invoice->user_id);

        // Company profile
        $profilePath = storage_path('app/company_profile.json');
        $profile = file_exists($profilePath) ? json_decode(file_get_contents($profilePath), true) : [];
        $companyName = $profile['langs'][$lang]['name'] ?? $profile['langs']['en']['name'] ?? 'Company';
        $companyAddress = strip_tags($profile['langs'][$lang]['address'] ?? $profile['langs']['en']['address'] ?? '');
        $companyEmail = $profile['email'] ?? '';
        $companyPhone = $profile['telephone'] ?? '';
        $companyFax = $profile['fax'] ?? '';
        $companyLogo = $profile['logo'] ?? '';

        // Status labels
        $statusLabels = [
            'u' => ['label' => 'Unpaid', 'class' => 'error'],
            'p' => ['label' => 'Paid', 'class' => 'success'],
            'pp' => ['label' => 'Partially Paid', 'class' => 'warning'],
            'c' => ['label' => 'Cancelled', 'class' => 'grey'],
        ];
        $status = $statusLabels[$invoice->status] ?? ['label' => 'Unknown', 'class' => 'grey'];

        // Common frontend data
        $frontendCtrl = new \App\Http\Controllers\FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        return view('frontend.invoice', array_merge($commonData, compact(
            'invoice', 'items', 'subtotal', 'discount', 'tax', 'total', 'grandTotal',
            'customer', 'companyName', 'companyAddress', 'companyEmail', 'companyPhone', 'companyFax', 'companyLogo',
            'status', 'lang'
        )));
    }

    /**
     * Show booking success page
     */
    public function bookingSuccess($lang, $id)
    {
        $booking = TourBooking::findOrFail($id);

        // Verify ownership
        if (auth()->check() && auth()->id() != $booking->user_id) {
            $user = auth()->user();
            if (!($user->group == 1 || $user->group == 2)) {
                abort(403);
            }
        }

        $invoice = Invoice::find($booking->invoice_id);
        $tour = Tour::find($booking->tour_id);
        $content = $tour ? TourContent::where('tour_id', $tour->id)->where('lang', $lang)->first() : null;
        $tourTitle = $content->title ?? 'Tour Booking';

        $frontendCtrl = new \App\Http\Controllers\FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        return view('frontend.booking_success', array_merge($commonData, [
            'booking' => $booking,
            'invoice' => $invoice,
            'tour' => $tour,
            'tourTitle' => $tourTitle,
        ]));
    }
}
