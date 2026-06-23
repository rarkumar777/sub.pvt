<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourBooking;
use App\Models\Tour;
use App\Models\User;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\TourTraveler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = TourBooking::with(['tour.contents', 'user', 'invoice', 'guaranteedDeparture'])
            ->orderBy('id', 'desc');

        // Hide completed by default (unless show)
        if (!$request->filled('completed') || $request->completed != 's') {
            $bookings->where('trip_status', '!=', 'com');
        }

        // Hide cancelled by default (unless show)
        if (!$request->filled('cancelled') || $request->cancelled != 's') {
            $bookings->where('trip_status', '!=', 'can');
        }

        // Filter by country
        if ($request->filled('start_country') && $request->start_country > 0) {
            $bookings->where('start_country', $request->start_country);
        }

        // Filter by booking number
        if ($request->filled('booking_number') && is_numeric($request->booking_number)) {
            $bookings->where('id', trim($request->booking_number));
        }

        // Filter by invoice number
        if ($request->filled('invoice') && is_numeric($request->invoice)) {
            $bookings->where('invoice_id', trim($request->invoice));
        }

        // Filter by title (invoice desc)
        if ($request->filled('title')) {
            $bookings->whereHas('invoice', function ($q) use ($request) {
                $q->where('desc', 'like', '%' . $request->title . '%');
            });
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $bookings->where('travel_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $bookings->where('travel_date', '<=', $request->to_date);
        }

        // Filter by user email
        if ($request->filled('user_email')) {
            $bookings->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->user_email . '%');
            });
        }

        $bookings = $bookings->paginate(20);

        // Get distinct countries used in bookings
        $countryIds = DB::table('en33_tours_booking')
            ->select('start_country')
            ->distinct()
            ->pluck('start_country')
            ->filter()
            ->toArray();

        $bookingCountries = [];
        if (!empty($countryIds)) {
            $bookingCountries = Country::whereIn('lang_id', $countryIds)
                ->where('lang', 'en')
                ->pluck('name', 'lang_id')
                ->toArray();
        }

        return view('admin.bookings.index', compact('bookings', 'bookingCountries'));
    }

    public function create()
    {
        $tours = Tour::with('contents')->get();
        $users = User::orderBy('first_name')->get();
        $countries = Country::where('lang', 'en')->pluck('name', 'lang_id')->toArray();

        // Accommodation options
        $accommodations = [
            0 => 'No Hotel Accommodations',
            1 => '1 Star', 2 => '2 Star', 3 => '3 Star',
            4 => '4 Star', 5 => '5 Star'
        ];

        // Load inclusions
        $inclusions = \App\Models\TourInclusion::where('lang', 'en')->orderBy('name')->get();

        // Check for copy_booking
        $copyBooking = null;
        if (request('copy_booking')) {
            $copyBooking = TourBooking::with(['user', 'invoice'])->find(request('copy_booking'));
        }

        return view('admin.bookings.create', compact(
            'tours', 'users', 'countries', 'accommodations', 'inclusions', 'copyBooking'
        ));
    }

    public function store(Request $request)
    {
        // Find user by email
        $user = User::where('email', $request->user_email)->first();
        if (!$user) {
            return redirect()->back()->withErrors(['User email not found'])->withInput();
        }

        // Calculate pricing from form
        $adultCount = intval($request->adult ?? 1);
        $childCount = intval($request->child ?? 0);
        $infantCount = intval($request->infant ?? 0);
        $singleRooms = intval($request->single ?? 0);

        $priceAdult = floatval($request->price_adult ?? 0);
        $priceChild = floatval($request->price_child ?? 0);
        $priceInfant = floatval($request->price_infant ?? 0);
        $priceSingleSupplement = floatval($request->price_single_supplement ?? 0);

        // Build invoice items
        $items = [];
        $itemsTotal = 0;

        if ($priceAdult > 0 && $adultCount > 0) {
            $lineTotal = $priceAdult * $adultCount;
            $items[] = ['name' => $request->title ?? 'Booking', 'qty' => $adultCount, 'price' => $priceAdult];
            $itemsTotal += $lineTotal;
        }
        if ($priceChild > 0 && $childCount > 0) {
            $lineTotal = $priceChild * $childCount;
            $items[] = ['name' => 'Child', 'qty' => $childCount, 'price' => $priceChild];
            $itemsTotal += $lineTotal;
        }
        if ($priceInfant > 0 && $infantCount > 0) {
            $lineTotal = $priceInfant * $infantCount;
            $items[] = ['name' => 'Infant', 'qty' => $infantCount, 'price' => $priceInfant];
            $itemsTotal += $lineTotal;
        }
        if ($priceSingleSupplement > 0 && $singleRooms > 0) {
            $lineTotal = $priceSingleSupplement * $singleRooms;
            $items[] = ['name' => 'Single supplement', 'qty' => $singleRooms, 'price' => $priceSingleSupplement];
            $itemsTotal += $lineTotal;
        }

        // Create invoice first
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'desc' => $request->title ?? '',
            'total' => $itemsTotal,
            'date' => date('Y-m-d'),
            'due_to_date' => $request->date ?? date('Y-m-d'),
            'items' => serialize($items),
            'discount' => '0',
            'tax' => 0,
            'partly_payment' => 0,
            'discount_description' => '',
            'status' => 'pen',
            'total_paid' => 0,
            'cost' => 0,
            'type' => 'b',
            'module' => 'tours',
            'added_by' => auth()->id(),
            'paid_by' => '',
            'sent_count' => 0,
            'invoices_set' => '',
        ]);

        // Create booking
        $booking = TourBooking::create([
            'user_id' => $user->id,
            'tour_id' => $request->tour_id ?? 0,
            'travel_date' => $request->date ?? date('Y-m-d'),
            'booked_in_date' => date('Y-m-d'),
            'days' => intval($request->days),
            'nights' => intval($request->nights),
            'hotel_grade' => intval($request->hotel_grade),
            'room_single' => intval($request->single ?? 0),
            'rooms_double' => intval($request->double ?? 0),
            'rooms_twin' => intval($request->twin ?? 0),
            'rooms_triple' => intval($request->triple ?? 0),
            'rooms_quad' => intval($request->quad ?? 0),
            'start_country' => intval($request->start_country),
            'note' => $request->notes ?? '',
            'adult' => intval($request->adult ?? 1),
            'child' => intval($request->child ?? 0),
            'infant' => intval($request->infant ?? 0),
            'invoice_id' => $invoice->id,
            'added_by' => auth()->id(),
            'trip_status' => 'pen',
            'guest_name' => $request->guest_name ?? '',
        ]);

        // Auto-populate itinerary from tour description
        $itinerary = $request->desc ?? '';
        if (empty($itinerary) && $booking->tour_id > 0) {
            $tourContent = \App\Models\TourContent::where('tour_id', $booking->tour_id)
                ->where('lang', 'en')->first();
            if ($tourContent && !empty($tourContent->desc)) {
                $itinerary = $tourContent->desc;
            }
        }
        if (!empty($itinerary)) {
            $booking->update(['booking_itinerary' => $itinerary]);
        }

        return redirect()->route('admin.bookings.edit', $booking->id)->with('success', 'Booking created successfully');
    }

    public function edit($id)
    {
        $booking = TourBooking::with(['tour.contents', 'user', 'travelers', 'invoice'])->findOrFail($id);

        // Redirect guaranteed departure bookings
        if ($booking->guaranteed_departure_id > 0) {
            return redirect()->route('admin.bookings.guaranteed.edit', $booking->id);
        }

        $tours = Tour::with('contents')->get();
        $users = User::orderBy('first_name')->get();
        $countries = Country::where('lang', 'en')->pluck('name', 'lang_id')->toArray();

        // Load itinerary from database (with file fallback for legacy data)
        $itinerary = $booking->booking_itinerary ?? '';
        if (empty($itinerary) && $booking->booked_in_date) {
            $dateParts = explode('-', $booking->booked_in_date);
            $itineraryPath = base_path('../pvt.jo/config/modules/tours/booking_data/' . $dateParts[0] . '/' . $dateParts[1] . '/' . $booking->id . '.html');
            if (File::exists($itineraryPath)) {
                $itinerary = html_entity_decode(File::get($itineraryPath));
            }
        }

        // Unserialize invoice items
        $invoiceItems = [];
        if ($booking->invoice && $booking->invoice->items) {
            $invoiceItems = @unserialize($booking->invoice->items, ['allowed_classes' => false]);
            if (!is_array($invoiceItems)) $invoiceItems = [];
        }

        // Accommodation options
        $accommodations = [
            0 => 'No Hotel Accommodations',
            1 => '1 Star', 2 => '2 Star', 3 => '3 Star',
            4 => '4 Star', 5 => '5 Star'
        ];

        // Load all inclusions
        $allInclusions = \App\Models\TourInclusion::where('lang', 'en')->orderBy('name')->get();

        // Get tour's inclusion settings (inc/exc arrays)
        $tourIncluded = [];
        $tourExcluded = [];
        if ($booking->tour && $booking->tour->inclusions) {
            $incData = @unserialize($booking->tour->inclusions, ['allowed_classes' => false]);
            if (is_array($incData)) {
                $tourIncluded = $incData['inc'] ?? [];
                $tourExcluded = $incData['exc'] ?? [];
            }
        }

        return view('admin.bookings.edit', compact(
            'booking', 'tours', 'users', 'countries',
            'itinerary', 'invoiceItems', 'accommodations',
            'allInclusions', 'tourIncluded', 'tourExcluded'
        ));
    }

    public function update(Request $request, $id)
    {
        $booking = TourBooking::with(['invoice', 'travelers'])->findOrFail($id);

        // Find user by email
        $user = User::where('email', $request->user_email)->first();
        if (!$user) {
            return redirect()->back()->withErrors(['User email not found'])->withInput();
        }

        // Update booking
        $booking->update([
            'user_id' => $user->id,
            'travel_date' => $request->date,
            'days' => intval($request->days),
            'nights' => intval($request->nights),
            'hotel_grade' => intval($request->hotel_grade),
            'room_single' => intval($request->single),
            'rooms_double' => intval($request->double),
            'rooms_twin' => intval($request->twin),
            'rooms_triple' => intval($request->triple),
            'rooms_quad' => intval($request->quad),
            'start_country' => intval($request->start_country),
            'note' => $request->notes,
            'adult' => intval($request->adult),
            'child' => intval($request->child),
            'infant' => intval($request->infant),
            'trip_status' => '',
            'guest_name' => $request->guest_name,
        ]);

        // Update invoice
        if ($booking->invoice) {
            // Calculate items total
            $items = [];
            $itemsTotal = 0;
            $currentCount = intval($request->current_count) + 1;
            for ($i = 1; $i < $currentCount; $i++) {
                if ($request->has('item_' . $i)) {
                    $name = $request->input('item_' . $i);
                    $qty = intval($request->input('item_qty_' . $i));
                    $price = floatval($request->input('item_price_' . $i));
                    $items[] = ['name' => $name, 'qty' => $qty, 'price' => $price];
                    $itemsTotal += $qty * $price;
                }
            }

            // Discount
            $discountStr = $request->discount_amount . $request->discount_type;
            $discount = 0;
            if ($request->discount_type == '%') {
                $discount = (floatval($request->discount_amount) / 100) * $itemsTotal;
            } else {
                $discount = floatval($request->discount_amount);
            }

            // Tax
            $taxPercent = floatval($request->tax);
            $taxAmount = ($taxPercent / 100) * ($itemsTotal - $discount);
            $grandTotal = ($itemsTotal - $discount) + $taxAmount;

            $booking->invoice->update([
                'items' => serialize($items),
                'total' => $grandTotal,
                'discount' => $discountStr,
                'tax' => $taxPercent,
                'user_id' => $user->id,
                'desc' => $request->title ?? '',
                'date' => $request->invoice_date,
                'due_to_date' => $request->due_to_date,
                'partly_payment' => $request->partly_payment ?? 0,
                'discount_description' => $request->discount_description ?? '',
            ]);
        }

        // Handle travelers - delete all and re-insert (matching reference site approach)
        TourTraveler::where('booking_id', $booking->id)->delete();

        $singleCount = intval($request->single);
        $doubleCount = intval($request->double);
        $twinCount = intval($request->twin);
        $tripleCount = intval($request->triple);
        $quadCount = intval($request->quad);
        $travelersCount = intval($request->adult) + intval($request->child);

        // No-hotel mode: treat all travelers as single rooms
        if (intval($request->nights) == 0 || intval($request->hotel_grade) == 0) {
            $singleCount = $travelersCount;
            $twinCount = 0;
            $doubleCount = 0;
            $tripleCount = 0;
            $quadCount = 0;
        }

        $roomTypes = [
            ['count' => $singleCount, 'prefix' => 's', 'slots' => 1],
            ['count' => $doubleCount, 'prefix' => 'd', 'slots' => 2],
            ['count' => $twinCount, 'prefix' => 't', 'slots' => 2],
            ['count' => $tripleCount, 'prefix' => 'tr', 'slots' => 3],
            ['count' => $quadCount, 'prefix' => 'q', 'slots' => 4],
        ];

        foreach ($roomTypes as $type) {
            for ($i = 1; $i <= $type['count']; $i++) {
                for ($slot = 1; $slot <= $type['slots']; $slot++) {
                    $fieldSuffix = $type['prefix'] . '_' . $i . '_' . $slot;

                    $passportIssue = $request->input('traveler_passport_issue_' . $fieldSuffix);
                    $passportExpire = $request->input('traveler_passport_expire_' . $fieldSuffix);
                    $birthDate = $request->input('traveler_birth_date_' . $fieldSuffix);

                    TourTraveler::create([
                        'booking_id' => $booking->id,
                        'guaranteed_booking_id' => 0,
                        'name' => $request->input('traveler_name_' . $fieldSuffix) ?? '',
                        'passport_number' => $request->input('traveler_passport_number_' . $fieldSuffix) ?? '',
                        'passport_issue' => !empty($passportIssue) ? $passportIssue : null,
                        'passport_expire' => !empty($passportExpire) ? $passportExpire : null,
                        'birth_date' => !empty($birthDate) ? $birthDate : null,
                        'nationality' => $request->input('traveler_nationality_' . $fieldSuffix) ?? '',
                        'flight_number' => $request->input('traveler_flight_number_' . $fieldSuffix) ?? '',
                        'border' => $request->input('traveler_border_' . $fieldSuffix) ?? '',
                        'traveler_id' => $slot,
                        'room_id' => $type['prefix'] . '_' . $i,
                    ]);
                }
            }
        }

        // Save itinerary to database
        $booking->update(['booking_itinerary' => $request->desc]);

        return redirect()->route('admin.bookings.edit', $booking->id)->with('success', 'Booking updated successfully');
    }

    public function markCancelled($id)
    {
        $booking = TourBooking::findOrFail($id);
        $booking->update(['trip_status' => 'can']);
        return redirect()->route('admin.bookings.edit', $booking->id)->with('success', 'Booking marked as cancelled');
    }

    public function destroy($id)
    {
        TourBooking::destroy($id);
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted');
    }

    public function show($id)
    {
        return $this->edit($id);
    }

    public function travelers($id)
    {
        $booking = TourBooking::with('travelers')->findOrFail($id);
        return view('admin.bookings.travelers', compact('booking'));
    }

    public function storeTraveler(Request $request, $id)
    {
        $data = $request->all();
        $data['booking_id'] = $id;
        TourTraveler::create($data);
        return redirect()->route('admin.bookings.travelers', $id)->with('success', 'Traveler added');
    }

    public function manifest($id)
    {
        $booking = TourBooking::with(['travelers', 'tour.contents', 'user'])->findOrFail($id);
        return view('admin.bookings.manifest', compact('booking'));
    }

    public function editGuaranteed(Request $request, $id)
    {
        $booking = TourBooking::with(['guaranteedDeparture.tour.contents', 'guaranteedDeparture.departureBookings.user', 'guaranteedDeparture.departureBookings.invoice'])->findOrFail($id);

        // Handle mark as cancelled
        if ($request->has('mark_as') && $request->mark_as == 'can') {
            $booking->update(['trip_status' => 'can']);
            return redirect()->route('admin.bookings.guaranteed.edit', $id)->with('success', 'Marked as cancelled');
        }

        // Handle POST - save itinerary and notes
        if ($request->isMethod('post')) {
            // Save itinerary to database
            $booking->update([
                'note' => $request->input('notes', ''),
                'booking_itinerary' => $request->input('desc', ''),
            ]);

            return redirect()->route('admin.bookings.guaranteed.edit', $id)->with('success', 'Saved successfully');
        }

        // Load itinerary from database (with file fallback for legacy)
        $itinerary = $booking->booking_itinerary ?? '';
        if (empty($itinerary)) {
            $bookedDate = explode('-', $booking->booked_in_date ?? date('Y-m-d'));
            $filePath = base_path('../pvt.jo/config/modules/tours/booking_data/' . ($bookedDate[0] ?? date('Y')) . '/' . ($bookedDate[1] ?? date('m')) . '/' . $booking->id . '.html');
            if (file_exists($filePath)) {
                $itinerary = html_entity_decode(file_get_contents($filePath));
            }
        }

        $countries = \App\Models\Country::pluck('name', 'id')->toArray();
        $gd = $booking->guaranteedDeparture;
        $tourTitle = '';
        if ($gd && $gd->tour && $gd->tour->contents->first()) {
            $tourTitle = $gd->tour->contents->first()->title;
        } elseif ($gd) {
            $tourTitle = $gd->title;
        }

        return view('admin.bookings.edit-guaranteed', compact('booking', 'itinerary', 'countries', 'tourTitle', 'gd'));
    }
}
