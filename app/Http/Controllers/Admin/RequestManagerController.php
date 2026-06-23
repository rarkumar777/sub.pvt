<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripRequest;
use App\Models\TripRequestMessage;
use App\Models\TripItinerary;
use App\Models\TripItineraryDay;
use App\Models\TourBooking;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class RequestManagerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $requests = TripRequest::orderBy('updated_at', 'desc')->get();
        $awaitingResponse = TripRequest::where('pipeline_stage', 'new_request')->where('is_read', 0)->count();
        $newRequests = TripRequest::where('pipeline_stage', 'new_request')->count();
        return view('admin.request-manager.index', compact('user', 'requests', 'awaitingResponse', 'newRequests'));
    }

    public function show($id)
    {
        $tripRequest = TripRequest::with(['messages', 'latestItinerary.days'])->findOrFail($id);
        $tripRequest->update(['is_read' => 1]);
        $user = auth()->user();
        return view('admin.request-manager.show', compact('tripRequest', 'user'));
    }

    public function sendMessage(Request $request, $id)
    {
        $tripRequest = TripRequest::findOrFail($id);
        $user = auth()->user();

        $message = $request->input('message', '');
        $attachment = null;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('request-attachments', 'public');
            $attachment = '/storage/' . $path;
            if (empty($message)) {
                $message = '📎 ' . $file->getClientOriginalName();
            }
        }

        if (empty($message) && empty($attachment)) {
            return response()->json(['success' => false, 'message' => 'Message or file required'], 422);
        }

        $msg = TripRequestMessage::create([
            'trip_request_id' => $tripRequest->id,
            'user_id' => $user->id,
            'sender_type' => 'agent',
            'sender_name' => $user->name ?? 'Admin',
            'message' => $message,
            'attachment' => $attachment,
        ]);

        $tripRequest->update(['updated_at' => now()]);

        return response()->json([
            'success' => true,
            'attachment' => $attachment,
            'filename' => $request->hasFile('attachment') ? $request->file('attachment')->getClientOriginalName() : null,
        ]);
    }

    public function pipeline()
    {
        $stages = ['new_request' => 'New Request', 'discovery' => 'Discovery', 'itinerary_creation' => 'First Itinerary Creation', 'fine_tuning' => 'Fine Tuning', 'validation' => 'Itinerary Validation'];
        $requests = TripRequest::orderBy('updated_at', 'desc')->get()->groupBy('pipeline_stage');
        return response()->json(['stages' => $stages, 'requests' => $requests]);
    }

    public function updateStage(Request $request, $id)
    {
        $trip = TripRequest::findOrFail($id);
        $trip->update(['pipeline_stage' => $request->input('stage')]);
        return response()->json(['success' => true]);
    }

    public function markRead($id)
    {
        TripRequest::findOrFail($id)->update(['is_read' => 1]);
        return response()->json(['success' => true]);
    }

    public function updateField(Request $request, $id)
    {
        $trip = TripRequest::findOrFail($id);
        $field = $request->input('field');
        $value = $request->input('value');

        $allowedFields = ['notes', 'phone', 'ideal_budget', 'max_budget', 'adults', 'children', 'assigned_to', 'next_action_date', 'participant_type', 'guide_type', 'email', 'departure_date', 'return_date', 'country', 'destination', 'first_name', 'last_name'];
        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Field not allowed'], 422);
        }

        $trip->update([$field => $value]);
        return response()->json(['success' => true, 'field' => $field, 'value' => $value]);
    }

    // ── Itinerary CRUD ──

    public function storeItinerary(Request $request, $id)
    {
        $trip = TripRequest::findOrFail($id);
        $itin = TripItinerary::create(array_merge(
            $request->only(['title', 'traveler_surname', 'language', 'arrival_date', 'cover_photo']),
            ['trip_request_id' => $trip->id]
        ));
        return response()->json(['success' => true, 'id' => $itin->id]);
    }

    public function updateItinerary(Request $request, $id, $itinId)
    {
        $itin = TripItinerary::where('trip_request_id', $id)->findOrFail($itinId);
        $data = $request->only(['title', 'traveler_surname', 'language', 'arrival_date', 'cover_photo', 'video_url',
            'price_per_person', 'num_travelers', 'price_includes', 'price_excludes', 'booking_conditions',
            'payment_conditions', 'reduced_mobility', 'passports_visas', 'travel_insurance',
            'nights_included', 'agency_commission', 'commission_type']);
        if (!isset($data['group_total']) && isset($data['price_per_person']) && isset($data['num_travelers'])) {
            $data['group_total'] = (float)$data['price_per_person'] * (int)$data['num_travelers'];
        } elseif (isset($request->group_total)) {
            $data['group_total'] = $request->group_total;
        }
        $itin->update($data);
        return response()->json(['success' => true, 'id' => $itin->id, 'group_total' => $itin->group_total]);
    }

    public function storeDay(Request $request, $id, $itinId)
    {
        $itin = TripItinerary::where('trip_request_id', $id)->findOrFail($itinId);
        $maxDay = $itin->days()->max('day_number') ?? 0;
        $data = $request->only(['title', 'destinations', 'description', 'breakfast', 'lunch', 'dinner',
            'accommodation_name', 'accommodation_description', 'accommodation_category', 'accommodation_stars', 'duration']);
        if ($request->has('services')) {
            $data['services'] = $request->input('services');
        }
        if ($request->has('photos')) {
            $data['photos'] = $request->input('photos');
        }
        $day = TripItineraryDay::create(array_merge(
            $data,
            ['trip_itinerary_id' => $itin->id, 'day_number' => $maxDay + 1]
        ));
        return response()->json(['success' => true, 'id' => $day->id]);
    }

    public function showDay($id, $itinId, $dayId)
    {
        $day = TripItineraryDay::where('trip_itinerary_id', $itinId)->findOrFail($dayId);
        return response()->json($day);
    }

    public function updateDay(Request $request, $id, $itinId, $dayId)
    {
        $day = TripItineraryDay::where('trip_itinerary_id', $itinId)->findOrFail($dayId);
        $data = $request->only(['title', 'destinations', 'description', 'breakfast', 'lunch', 'dinner',
            'accommodation_name', 'accommodation_description', 'accommodation_category', 'accommodation_stars', 'duration']);
        if ($request->has('services')) {
            $data['services'] = $request->input('services');
        }
        $day->update($data);
        return response()->json(['success' => true]);
    }

    public function deleteDay($id, $itinId, $dayId)
    {
        $day = TripItineraryDay::where('trip_itinerary_id', $itinId)->findOrFail($dayId);
        $day->delete();
        return response()->json(['success' => true]);
    }

    public function reorderDays(Request $request, $id, $itinId)
    {
        $itin = \App\Models\TripItinerary::where('trip_request_id', $id)->findOrFail($itinId);
        $dayIds = $request->input('ids', []);
        foreach ($dayIds as $index => $dayId) {
            \App\Models\TripItineraryDay::where('trip_itinerary_id', $itin->id)
                ->where('id', $dayId)
                ->update(['day_number' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }

    // ── Day Photo Upload ──

    public function uploadDayPhoto(Request $request, $id, $itinId, $dayId)
    {
        $day = TripItineraryDay::where('trip_itinerary_id', $itinId)->findOrFail($dayId);

        if (!$request->hasFile('photo')) {
            return response()->json(['success' => false, 'message' => 'No photo uploaded'], 422);
        }

        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension();
        $filename = 'day_' . time() . '_' . uniqid() . '.' . $ext;
        $file->move(public_path('uploads/day-photos'), $filename);
        $url = '/uploads/day-photos/' . $filename;

        $photos = $day->photos ?? [];
        $photos[] = $url;
        $day->update(['photos' => $photos]);

        return response()->json(['success' => true, 'url' => $url, 'photos' => $photos]);
    }

    public function deleteDayPhoto(Request $request, $id, $itinId, $dayId)
    {
        $day = TripItineraryDay::where('trip_itinerary_id', $itinId)->findOrFail($dayId);
        $url = $request->input('url');
        $photos = $day->photos ?? [];
        $photos = array_values(array_filter($photos, fn($p) => $p !== $url));
        $day->update(['photos' => $photos]);

        return response()->json(['success' => true, 'photos' => $photos]);
    }

    // ── Cover Photo Upload ──

    public function uploadCover(Request $request, $id)
    {
        if (!$request->hasFile('photo')) {
            return response()->json(['success' => false, 'message' => 'No photo uploaded'], 422);
        }

        $file = $request->file('photo');
        $ext = $file->getClientOriginalExtension();
        $filename = 'cover_' . time() . '_' . uniqid() . '.' . $ext;
        $file->move(public_path('uploads/cover-photos'), $filename);
        $url = '/uploads/cover-photos/' . $filename;

        return response()->json(['success' => true, 'url' => $url]);
    }

    // ── Trip Planner (full page) ──

    public function copyItinerary(Request $request, $id, $oldItinId)
    {
        $tripRequest = TripRequest::findOrFail($id);
        $oldItin = TripItinerary::with('days')->findOrFail($oldItinId);

        // Delete existing itineraries and their days for this request
        $existingItins = TripItinerary::where('trip_request_id', $id)->pluck('id');
        if ($existingItins->count() > 0) {
            \App\Models\TripItineraryDay::whereIn('trip_itinerary_id', $existingItins)->delete();
            TripItinerary::where('trip_request_id', $id)->delete();
        }

        // Copy the itinerary with all its data — keep original title per Evaneos reference
        $newItin = $oldItin->replicate();
        $newItin->trip_request_id = $id;
        // Keep original title exactly as-is (Evaneos does NOT rename copies)
        $newItin->save();

        // Copy all days with complete data
        foreach ($oldItin->days as $day) {
            $newDay = $day->replicate();
            $newDay->trip_itinerary_id = $newItin->id;
            $newDay->save();
        }

        return redirect('/admin/request-manager/' . $id . '/trip-planner?openEditor=1');
    }

    public function updateServiceQtys(Request $request, $id, $itinId)
    {
        $itin = TripItinerary::with('days')->where('trip_request_id', $id)->findOrFail($itinId);
        $dayQtys = $request->input('day_qtys', []);
        foreach ($itin->days as $day) {
            $dayNum = (string)$day->day_number;
            if (!isset($dayQtys[$dayNum])) continue;
            $services = $day->services ?? [];
            foreach ($dayQtys[$dayNum] as $idx => $qty) {
                if (isset($services[$idx])) {
                    $services[$idx]['qty'] = max(1, intval($qty));
                }
            }
            $day->services = $services;
            $day->save();
        }
        return response()->json(['success' => true]);
    }

    public function getServiceTotal($id, $itinId)
    {
        $itin = TripItinerary::with('days')->where('trip_request_id', $id)->findOrFail($itinId);
        $total = 0;
        $breakdown = [];
        foreach ($itin->days as $day) {
            $services = $day->services ?? [];
            foreach ($services as $svc) {
                $cost = floatval($svc['cost'] ?? 0);
                $total += $cost;
                if ($cost > 0) {
                    $breakdown[] = [
                        'day'  => $day->day_number,
                        'name' => $svc['name'] ?? 'Service',
                        'cost' => $cost,
                    ];
                }
            }
        }
        $numPax = ($itin->num_travelers ?? 1) ?: 1;
        return response()->json([
            'total'       => $total,
            'per_person'  => round($total / $numPax, 2),
            'num_pax'     => $numPax,
            'breakdown'   => $breakdown,
        ]);
    }

    public function tripPreview($id)
    {
        $tripRequest = TripRequest::with(['latestItinerary.days'])->findOrFail($id);
        $user = auth()->user();
        $itinerary = $tripRequest->latestItinerary;
        return view('admin.request-manager.trip-preview', compact('tripRequest', 'user', 'itinerary'));
    }

    public function tripQuote($id)
    {
        $tripRequest = TripRequest::with(['latestItinerary.days'])->findOrFail($id);
        $user = auth()->user();
        $itinerary = $tripRequest->latestItinerary;
        return view('admin.request-manager.trip-quote', compact('tripRequest', 'user', 'itinerary'));
    }

    public function generatePayment($id)
    {
        $tripRequest = TripRequest::with(['latestItinerary.days'])->findOrFail($id);
        $itin = $tripRequest->latestItinerary;
        $split = request()->get('split', 0);

        // Calculate total from services
        $items = [];
        $totalAmount = 0;
        if ($itin && $itin->days) {
            foreach ($itin->days as $day) {
                $svcs = $day->services ?? [];
                if (is_string($svcs)) $svcs = json_decode($svcs, true);
                if (is_array($svcs)) {
                    foreach ($svcs as $svc) {
                        $cost = floatval($svc['cost'] ?? 0);
                        $qty  = intval($svc['qty'] ?? 1);
                        $days = intval($svc['stay_duration'] ?? 1);
                        $lineTotal = $cost * max(1, $qty) * max(1, $days);
                        if ($lineTotal > 0) {
                            $items[] = [
                                'name'  => ($svc['name'] ?? 'Service') . ($svc['loc'] ? ' - ' . $svc['loc'] : ''),
                                'qty'   => max(1, $qty),
                                'price' => $cost * max(1, $days),
                            ];
                            $totalAmount += $lineTotal;
                        }
                    }
                }
            }
        }

        // Use group_total if set and > 0
        if ($itin && floatval($itin->group_total ?? 0) > 0) {
            $totalAmount = floatval($itin->group_total);
        }

        // Find user by email
        $userId = 0;
        if ($tripRequest->email) {
            $clientUser = User::where('email', $tripRequest->email)->first();
            if ($clientUser) $userId = $clientUser->id;
        }

        $clientName = trim(($tripRequest->first_name ?? '') . ' ' . ($tripRequest->last_name ?? ''));
        $totalPax = max(1, ($tripRequest->adults ?? 0) + ($tripRequest->children ?? 0));

        // Split payment: create separate invoice per person with participant names
        if ($split && $totalPax > 1) {
            $participants = request()->get('participants', []);
            $perPersonAmount = round($totalAmount / $totalPax, 2);
            $perPersonItems = [];
            foreach ($items as $item) {
                $perPersonItems[] = [
                    'name'  => $item['name'],
                    'qty'   => $item['qty'],
                    'price' => round($item['price'] / $totalPax, 2),
                ];
            }

            $invoiceIds = [];
            for ($p = 0; $p < $totalPax; $p++) {
                $pName = trim($participants[$p]['name'] ?? ('Person ' . ($p + 1)));
                $pEmail = trim($participants[$p]['email'] ?? '');

                // Try to find user by participant email
                $pUserId = $userId;
                if ($pEmail) {
                    $pUser = User::where('email', $pEmail)->first();
                    if ($pUser) $pUserId = $pUser->id;
                }

                $invoice = Invoice::create([
                    'user_id'              => $pUserId,
                    'desc'                 => 'Trip Payment - ' . $pName . ' (' . ($p+1) . '/' . $totalPax . ') - ' . ($itin->title ?? 'Trip'),
                    'date'                 => now()->toDateString(),
                    'due_to_date'          => $tripRequest->departure_date ?? now()->addDays(7)->toDateString(),
                    'items'                => serialize($perPersonItems),
                    'total'                => $perPersonAmount,
                    'cost'                 => $perPersonAmount,
                    'discount'             => '0',
                    'tax'                  => 0,
                    'status'               => 'u',
                    'type'                 => '',
                    'module'               => '',
                    'added_by'             => auth()->id(),
                    'paid_by'              => '',
                    'partly_payment'       => 0,
                    'total_paid'           => 0,
                    'discount_description' => '',
                    'sent_count'           => 0,
                    'invoices_set'         => '',
                ]);
                $invoiceIds[] = $invoice->id;
            }

            // Link invoices together via invoices_set
            $setString = implode(',', $invoiceIds);
            Invoice::whereIn('id', $invoiceIds)->update(['invoices_set' => $setString]);

            return redirect()->route('admin.invoices.edit', $invoiceIds[0])->with('success', $totalPax . ' split invoices created — $' . number_format($perPersonAmount, 2) . ' per person. Invoice IDs: ' . $setString);
        }

        // Single invoice (full amount)
        $invoice = Invoice::create([
            'user_id'              => $userId,
            'desc'                 => 'Trip Payment - ' . ($clientName ?: 'Client') . ' - ' . ($itin->title ?? 'Trip'),
            'date'                 => now()->toDateString(),
            'due_to_date'          => $tripRequest->departure_date ?? now()->addDays(7)->toDateString(),
            'items'                => serialize($items),
            'total'                => $totalAmount,
            'cost'                 => $totalAmount,
            'discount'             => '0',
            'tax'                  => 0,
            'status'               => 'u',
            'type'                 => '',
            'module'               => '',
            'added_by'             => auth()->id(),
            'paid_by'              => '',
            'partly_payment'       => 0,
            'total_paid'           => 0,
            'discount_description' => '',
            'sent_count'           => 0,
            'invoices_set'         => '',
        ]);

        return redirect()->route('admin.invoices.edit', $invoice->id)->with('success', 'Payment invoice generated for ' . ($clientName ?: 'client') . ' — Total: $' . number_format($totalAmount, 2));
    }

    public function syncToQuotation($id, $itinId)
    {
        $tripRequest = TripRequest::findOrFail($id);
        $itin = TripItinerary::with('days')->where('trip_request_id', $id)->findOrFail($itinId);
        
        $refNumber = 'REQ-' . $tripRequest->id . '-ITIN-' . $itinId;
        
        // Find existing quotation or create new
        $quotation = \App\Models\TourQuotation::where('ref_number', $refNumber)->first();
        
        if (!$quotation) {
            // Auto-create a linked Invoice for expense tracking
            $invoice = \App\Models\Invoice::create([
                'items' => '',
                'discount' => 0,
                'tax' => 0,
                'status' => 'un',
                'type' => 'q',
                'module' => 'tours',
                'user_id' => 0, // Direct client or attach user ID if exists
                'desc' => 'Quotation #' . $refNumber . ' - ' . ($tripRequest->first_name . ' ' . $tripRequest->last_name),
                'date' => $itin->arrival_date ?? now()->toDateString(),
                'total' => 0,
                'cost' => 0,
                'added_by' => auth()->id(),
                'paid_by' => 0,
                'partly_payment' => 0,
                'total_paid' => 0,
                'due_to_date' => $itin->arrival_date ?? now()->toDateString(),
                'discount_description' => '',
                'sent_count' => 0,
                'invoices_set' => '',
            ]);

            $quotation = \App\Models\TourQuotation::create([
                'customer_name' => $tripRequest->first_name . ' ' . $tripRequest->last_name,
                'email' => $tripRequest->email ?? '',
                'phone' => $tripRequest->phone ?? '',
                'description' => $itin->title ?? 'Trip Quote',
                'ref_number' => $refNumber,
                'travel_date' => $itin->arrival_date ?? now()->toDateString(),
                'days' => $itin->days->count() ?: 1,
                'nights' => max(0, $itin->days->count() - 1),
                'travelers_number' => $itin->num_travelers ?? $tripRequest->adults ?? 1,
                'pricing_base' => \App\Models\TourQuotationPricing::first()->id ?? 0,
                'lang' => $itin->language ?? 'en',
                'added_by' => auth()->id(),
                'last_edited' => auth()->id(),
                'views' => 0,
                'total_cost' => 0,
                'total' => 0,
                'invoice_id' => $invoice->id,
                'status' => 'draft',
                'profit_amount' => 0,
            ]);
        } else {
            // Update basic info
            $quotation->update([
                'days' => $itin->days->count() ?: 1,
                'nights' => max(0, $itin->days->count() - 1),
                'travelers_number' => $itin->num_travelers ?? $tripRequest->adults ?? 1,
                'last_edited' => auth()->id(),
            ]);
            // Clear existing days for fresh sync
            \App\Models\TourQuotationDay::where('quotation_id', $quotation->id)->delete();
        }

        $totalCost = 0;

        foreach ($itin->days as $index => $day) {
            $expensesArr = [];
            $dayCost = 0;
            
            // Extract services - Trip Planner stores them as:
            // {name, cost, loc, type, image, vendor, description}
            // 'vendor' = service ID from en33_services, 'cost' can be numeric or text
            $services = $day->services ?? [];
            if (is_array($services)) {
                foreach ($services as $serviceData) {
                    if (!is_array($serviceData)) continue;
                    
                    $qty = $itin->num_travelers ?? $tripRequest->adults ?? 1;
                    $serviceDate = $itin->arrival_date ? \Carbon\Carbon::parse($itin->arrival_date)->addDays($index)->toDateString() : now()->toDateString();
                    
                    // Try to find actual service by vendor field (service ID)
                    $serviceId = $serviceData['vendor'] ?? ($serviceData['id'] ?? null);
                    $serviceCost = 0;
                    $serviceDesc = $serviceData['name'] ?? 'Service';
                    
                    if ($serviceId && is_numeric($serviceId)) {
                        $service = \App\Models\Service::find($serviceId);
                        if ($service) {
                            $serviceCost = floatval($service->cost);
                            $serviceDesc = $service->description ?: $serviceDesc;
                        }
                    }
                    
                    // If no service found in DB, use cost from the trip planner data directly
                    if ($serviceCost == 0 && isset($serviceData['cost']) && is_numeric($serviceData['cost'])) {
                        $serviceCost = floatval($serviceData['cost']);
                    }
                    
                    // Add location info to description
                    $location = $serviceData['loc'] ?? '';
                    if ($location) {
                        $serviceDesc = $serviceDesc . ' (' . $location . ')';
                    }
                    
                    $expensesArr[] = [
                        'id' => $serviceId ?: 0,
                        'desc' => $serviceDesc,
                        'qty' => $qty,
                        'cost' => $serviceCost,
                        'date' => $serviceDate,
                    ];
                    $dayCost += ($serviceCost * $qty);
                }
            }

            \App\Models\TourQuotationDay::create([
                'quotation_id' => $quotation->id,
                'day_number' => $index + 1,
                'contents' => $day->description ?? '',
                'expenses' => !empty($expensesArr) ? serialize($expensesArr) : '',
                'total_cost' => $dayCost,
                'included' => '',
                'excluded' => '',
                'images' => !empty($day->photos) ? serialize($day->photos) : '',
            ]);
            
            $totalCost += $dayCost;
        }

        // Apply Pricing Base logic (basic margin calc)
        $pricing = $quotation->pricingBase;
        $totalSelling = $totalCost;
        if ($pricing) {
            if ($pricing->margin > 0) {
                // If margin is percentage
                $totalSelling = $totalCost + ($totalCost * ($pricing->margin / 100));
            }
        }

        $quotation->update([
            'total_cost' => $totalCost,
            'total' => $totalSelling,
            'profit_amount' => $totalSelling - $totalCost,
        ]);
        
        // Update the invoice as well
        if ($quotation->invoice) {
            $quotation->invoice->update([
                'cost' => $totalCost,
                'total' => $totalSelling,
            ]);
        }

        // Return a redirect to the Quotation Edit screen so they can review/tweak prices
        return redirect()->route('admin.quotations.edit', $quotation->id)->with('success', 'Itinerary synchronized to Quotation successfully!');
    }

    public function tripPlanner($id)
    {
        $tripRequest = TripRequest::with(['latestItinerary.days'])->findOrFail($id);
        $user = auth()->user();

        // Detect active tab from URL segment
        $segment = request()->segment(5); // 'My-quote', 'daybyday', or 'price'
        $activeTab = in_array($segment, ['My-quote', 'daybyday', 'price']) ? $segment : null;

        // Fetch previous quotes to allow copying
        $previousQuotes = \App\Models\TripItinerary::with(['tripRequest', 'days'])->orderByDesc('created_at')->get();

        // ── Pull real Library data ──
        // Canned Days
        $cannedDays = \App\Models\TourCannedDay::with(['contents' => function($q) {
            $q->where('lang', 'en');
        }])->orderByDesc('id')->get();

        // Services grouped by category (Accommodations, Activities, Transport)
        $jordanCountry = \App\Models\Country::where('name', 'Jordan')->where('lang', 'en')->first();
        $countryId = $jordanCountry ? $jordanCountry->id : 0;

        $libraryServices = [];
        if ($countryId) {
            $allowedPatterns = ['accommod', 'activit', 'transport', 'tranport'];
            $rootCategories = \App\Models\ServiceCategory::where('country_id', $countryId)
                ->where('parent_id', 0)
                ->where(function($q) use ($allowedPatterns) {
                    foreach ($allowedPatterns as $p) {
                        $q->orWhere('name', 'like', '%' . $p . '%');
                    }
                })
                ->orderByRaw("CASE WHEN LOWER(name) LIKE '%accommod%' THEN 1 WHEN LOWER(name) LIKE '%activit%' THEN 2 ELSE 3 END")
                ->get();

            foreach ($rootCategories as $rootCat) {
                $categoryIds = $this->getAllDescendantCategoryIds($rootCat->id, $countryId);
                $categoryIds[] = $rootCat->id;

                $services = \App\Models\Service::with('serviceCategory')
                    ->whereIn('category', $categoryIds)
                    ->orderByDesc('id')
                    ->get();

                if ($services->count() > 0) {
                    $libraryServices[] = [
                        'category' => $rootCat,
                        'services' => $services,
                    ];
                }
            }
        }

        // Service categories tree for service selector (Jordan only, country_id=123)
        $serviceCategories = \App\Models\ServiceCategory::with('children')
            ->where('country_id', $countryId)
            ->where(function($q){ $q->whereNull('parent_id')->orWhere('parent_id', 0); })
            ->orderBy('name')
            ->get();

        // Detect if we should auto-open editor (e.g. after copying a quote, or on tab URL)
        $autoOpenEditor = (request()->has('openEditor') && $tripRequest->latestItinerary) || $activeTab !== null;

        return view('admin.request-manager.trip-planner', compact(
            'tripRequest', 'user', 'previousQuotes', 'cannedDays', 'libraryServices', 'serviceCategories', 'autoOpenEditor', 'activeTab'
        ));
    }

    /**
     * AJAX: Search library items for Trip Planner
     */
    public function searchLibrary(Request $request)
    {
        $search = $request->input('q', '');
        $type = $request->input('type', 'days'); // days, accommodations, activities, transport

        if ($type === 'days') {
            $query = \App\Models\TourCannedDay::with(['contents' => fn($q) => $q->where('lang', 'en')]);
            if ($search) {
                $dayIds = \App\Models\TourCannedDayContent::where('lang', 'en')
                    ->where('title', 'like', '%' . $search . '%')
                    ->pluck('day_id');
                $query->whereIn('id', $dayIds);
            }
            $items = $query->orderByDesc('id')->limit(50)->get()->map(function($d) {
                $content = $d->contents->first();
                $images = @unserialize($d->images);
                $img = is_array($images) && !empty($images) ? $images[0] : null;
                if ($img && !str_starts_with($img, 'http')) $img = '/' . ltrim($img, '/');
                return [
                    'id' => $d->id,
                    'title' => $content ? $content->title : 'Untitled',
                    'description' => $content ? $content->description : '',
                    'image' => $img,
                ];
            });
        } else {
            $jordanCountry = \App\Models\Country::where('name', 'Jordan')->where('lang', 'en')->first();
            $countryId = $jordanCountry ? $jordanCountry->id : 0;

            // Use name-based pattern matching to find ALL relevant categories,
            // not just descendants of root categories (avoids issues with circular parent references)
            $namePatterns = $this->getCategoryNamePatterns($type);

            $categoryIds = [];

            // 1) Collect from root categories and their descendants (original approach)
            $patternMap = [
                'accommodations' => 'accommod',
                'hotels' => 'accommod',
                'activities' => 'activit',
                'transport' => 'transport',
                'restaurants' => 'restaur',
                'guides' => 'guide'
            ];
            $pattern = $patternMap[$type] ?? $type;

            $rootCats = \App\Models\ServiceCategory::where('country_id', $countryId)
                ->where('parent_id', 0)
                ->where(function($q) use ($pattern) {
                    $q->where('name', 'like', '%' . $pattern . '%');
                    if ($pattern === 'transport') {
                        $q->orWhere('name', 'like', '%tranport%');
                    }
                    if ($pattern === 'activit') {
                        $q->orWhere('name', 'like', '%pvt%');
                    }
                })
                ->get();

            foreach ($rootCats as $rootCat) {
                $categoryIds = array_merge($categoryIds, $this->getAllDescendantCategoryIds($rootCat->id, $countryId));
                $categoryIds[] = $rootCat->id;
            }

            // 2) Also find categories by name patterns (catches services in mislinked trees)
            if (!empty($namePatterns)) {
                $patternCatIds = \App\Models\ServiceCategory::where('country_id', $countryId)
                    ->where(function($q) use ($namePatterns) {
                        foreach ($namePatterns as $np) {
                            $q->orWhere('name', 'like', '%' . $np . '%');
                        }
                    })
                    ->pluck('id')
                    ->toArray();
                $categoryIds = array_merge($categoryIds, $patternCatIds);
            }

            $categoryIds = array_unique($categoryIds);

            $items = collect();
            if (count($categoryIds) > 0) {
                $subCat = $request->input('subCat');
                if ($subCat) {
                    // Use flexible name matching: strip trailing 's' for broader match
                    // e.g. "Camps" should also match "Luxury Camp", "Sun City Camp" etc.
                    $subCatBase = rtrim($subCat, 's');
                    
                    $subCatIds = [];
                    
                    // Try exact name match and get descendants
                    $subCatModel = \App\Models\ServiceCategory::where('name', $subCat)->where('country_id', $countryId)->first();
                    if ($subCatModel) {
                        $subCatIds = $this->getAllDescendantCategoryIds($subCatModel->id, $countryId);
                        $subCatIds[] = $subCatModel->id;
                    }
                    
                    // Also add categories matching by name pattern (broader match)
                    $subNameIds = \App\Models\ServiceCategory::where('country_id', $countryId)
                        ->where(function($q) use ($subCat, $subCatBase) {
                            $q->where('name', 'like', '%' . $subCat . '%')
                              ->orWhere('name', 'like', '%' . $subCatBase . '%');
                        })
                        ->pluck('id')->toArray();
                    $subCatIds = array_unique(array_merge($subCatIds, $subNameIds));
                    
                    if (!empty($subCatIds)) {
                        $categoryIds = array_intersect($categoryIds, $subCatIds);
                    }
                }

                $query = \App\Models\Service::with('serviceCategory')->whereIn('category', $categoryIds);
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('description', 'like', '%' . $search . '%')
                          ->orWhere('arrival', 'like', '%' . $search . '%')
                          ->orWhere('notes', 'like', '%' . $search . '%')
                          ->orWhereHas('serviceCategory', function($catQ) use ($search) {
                              $catQ->where('name', 'like', '%' . $search . '%');
                          })
                          ->orWhereIn('vender', function($vendorQ) use ($search) {
                              $vendorQ->select('id')->from('en33_users')->where('company', 'like', '%' . $search . '%');
                          });
                    });
                }
                $items = $query->orderByDesc('id')->limit(50)->get()->map(function($s) {
                    $img = $s->image;
                    if ($img && str_starts_with($img, '[')) {
                        $arr = json_decode($img, true);
                        $img = is_array($arr) && !empty($arr) ? $arr[0] : null;
                    }
                    if ($img && !str_starts_with($img, 'http') && !str_starts_with($img, '/')) $img = '/' . $img;

                    // Get category name for location display
                    $catName = '';
                    if ($s->serviceCategory) {
                        $catName = $s->serviceCategory->name;
                    }

                    $vendorName = '';
                    if ($s->vender) {
                        $vendorName = \DB::table('en33_users')->where('id', $s->vender)->value('company');
                    }
                    if (!$vendorName) {
                        $vendorName = $catName;
                    }

                    return [
                        'id'           => $s->id,
                        'title'        => $s->description ?: 'Untitled',
                        'description'  => $s->notes ?? '',
                        'image'        => $img,
                        'category'     => $s->arrival ?: ($catName ?: 'Jordan'),
                        'arrival'      => $s->arrival ?: 'Jordan', // Explicitly return the city/place
                        'cost'         => $s->cost ?? 0,
                        'acc_type'     => $s->acc_type ?? '',
                        'acc_category' => $s->acc_category ?? '',
                        'vendor'       => $vendorName ?: '',
                    ];
                });
            }
        }

        return response()->json(['items' => $items]);
    }

    /**
     * Get category name patterns for each service type to find categories by name
     */
    private function getCategoryNamePatterns($type)
    {
        $patterns = [
            'accommodations' => ['hotel', 'camp', 'homestay', 'resort', 'villa', 'accommod', 'RSCN', 'luxhotel', 'luxotel', 'lodge', 'guesthouse'],
            'hotels' => ['hotel', 'camp', 'homestay', 'resort', 'villa', 'accommod', 'RSCN', 'luxhotel', 'luxotel', 'lodge', 'guesthouse'],
            'activities' => ['activit', 'pvt', 'tour', 'jeep', 'horse', 'dive', 'snorkel', 'hike'],
            'transport' => ['car', 'van', 'bus', 'transport', 'tranport', 'rental', 'driver', 'taxi', 'transfer'],
            'restaurants' => ['restaur', 'lunch', 'dinner', 'cafe', 'dining'],
            'guides' => ['guide', 'escort'],
        ];
        return $patterns[$type] ?? [];
    }

    private function getAllDescendantCategoryIds($parentId, $countryId, $visited = [])
    {
        $ids = [];
        // Prevent circular references
        if (in_array($parentId, $visited)) {
            return $ids;
        }
        $visited[] = $parentId;

        $children = \App\Models\ServiceCategory::where('parent_id', $parentId)
            ->where('country_id', $countryId)
            ->pluck('id');
        foreach ($children as $childId) {
            if (!in_array($childId, $visited)) {
                $ids[] = $childId;
                $ids = array_merge($ids, $this->getAllDescendantCategoryIds($childId, $countryId, $visited));
            }
        }
        return $ids;
    }

    // ── Convert Trip Request → Booking ──

    public function convertToBooking(Request $request, $id)
    {
        $tripRequest = TripRequest::with(['latestItinerary.days'])->findOrFail($id);
        $itin = $tripRequest->latestItinerary;

        // ── Calculate selling price (service cost + agency commission) ──
        $serviceTotal = 0;
        if ($itin && $itin->days) {
            foreach ($itin->days as $day) {
                foreach (($day->services ?? []) as $svc) {
                    $cost  = floatval($svc['cost'] ?? 0);
                    $qty   = intval($svc['qty'] ?? 1);
                    $days  = intval($svc['stay_duration'] ?? 1);
                    $serviceTotal += $cost * max(1, $qty) * max(1, $days);
                }
            }
        }

        $commissionVal  = floatval($itin->agency_commission ?? 0);
        $commissionType = $itin->commission_type ?? 'percent';

        if ($commissionType === 'percent') {
            $sellingTotal = $serviceTotal * (1 + $commissionVal / 100);
        } else {
            $sellingTotal = $serviceTotal + $commissionVal;
        }

        // Use saved group_total if already set and > 0
        if ($itin && floatval($itin->group_total) > 0) {
            $sellingTotal = floatval($itin->group_total);
        }

        $numAdults   = intval($tripRequest->adults   ?? 1) ?: 1;
        $numChildren = intval($tripRequest->children ?? 0);
        $numPax      = $numAdults + $numChildren ?: 1;
        $pricePerPerson = $numPax > 0 ? round($sellingTotal / $numPax, 2) : $sellingTotal;

        // ── Find or create user from the traveller's email ──
        $user = null;
        if (!empty($tripRequest->email)) {
            $user = User::where('email', $tripRequest->email)->first();
            if (!$user) {
                $user = User::create([
                    'first_name'    => $tripRequest->first_name ?? '',
                    'last_name'     => $tripRequest->last_name  ?? '',
                    'email'         => $tripRequest->email,
                    'pass'          => bcrypt(\Illuminate\Support\Str::random(16)),
                    'phone'         => $tripRequest->phone ?? '',
                    'user_regdate'  => date('Y-m-d H:i:s'),
                    'status'        => 1,
                ]);
            }
        } else {
            $user = auth()->user();
        }

        // ── Build invoice items ──
        $items = [];
        $tripTitle = ($itin->title ?? null) ?: ($tripRequest->first_name . ' ' . $tripRequest->last_name . ' Trip');

        if ($numAdults > 0) {
            $items[] = ['name' => $tripTitle . ' (Adult)', 'qty' => $numAdults, 'price' => $pricePerPerson];
        }
        if ($numChildren > 0) {
            $items[] = ['name' => $tripTitle . ' (Child)', 'qty' => $numChildren, 'price' => round($pricePerPerson * 0.5, 2)];
        }
        $itemsTotal = collect($items)->sum(fn($i) => $i['qty'] * $i['price']);

        // ── Create Invoice ──
        $travelDate = $itin->arrival_date
            ? \Carbon\Carbon::parse($itin->arrival_date)->toDateString()
            : ($tripRequest->departure_date ?? date('Y-m-d'));

        $invoice = Invoice::create([
            'user_id'              => $user->id,
            'desc'                 => $tripTitle,
            'total'                => $itemsTotal,
            'date'                 => date('Y-m-d'),
            'due_to_date'          => $travelDate,
            'items'                => serialize($items),
            'discount'             => '0',
            'tax'                  => 0,
            'partly_payment'       => 0,
            'discount_description' => '',
            'status'               => 'pen',
            'total_paid'           => 0,
            'cost'                 => $serviceTotal,
            'type'                 => 'b',
            'module'               => 'tours',
            'added_by'             => auth()->id(),
            'paid_by'              => '',
            'sent_count'           => 0,
            'invoices_set'         => '',
        ]);

        // ── Count days from itinerary ──
        $dayCount    = $itin ? $itin->days->count() : 1;
        $nightCount  = max(0, $dayCount - 1);

        // ── Create Booking ──
        $booking = TourBooking::create([
            'user_id'               => $user->id,
            'tour_id'               => 0,
            'travel_date'           => $travelDate,
            'booked_in_date'        => date('Y-m-d'),
            'days'                  => $dayCount,
            'nights'                => $nightCount,
            'hotel_grade'           => 0,
            'room_single'           => $numAdults,
            'rooms_double'          => 0,
            'rooms_twin'            => 0,
            'rooms_triple'          => 0,
            'rooms_quad'            => 0,
            'start_country'         => 0,
            'note'                  => $tripRequest->notes ?? '',
            'adult'                 => $numAdults,
            'child'                 => $numChildren,
            'infant'                => 0,
            'invoice_id'            => $invoice->id,
            'added_by'              => auth()->id(),
            'trip_status'           => 'pen',
            'guest_name'            => trim(($tripRequest->first_name ?? '') . ' ' . ($tripRequest->last_name ?? '')),
        ]);

        // ── Create InvoiceExpense rows for each itinerary service ──
        // These show up in the "Reservations & Costs" / Expenses tab of the booking
        $expenseCount = 0;
        if ($itin && $itin->days) {
            foreach ($itin->days->sortBy('day_number') as $day) {
                $dayServices = $day->services ?? [];
                if (!is_array($dayServices)) continue;
                $dayOffset   = ($day->day_number ?? 1) - 1;
                $serviceDate = $itin->arrival_date
                    ? \Carbon\Carbon::parse($itin->arrival_date)->addDays($dayOffset)->toDateString()
                    : $travelDate;

                foreach ($dayServices as $svc) {
                    if (!is_array($svc)) continue;
                    $svcCost     = floatval($svc['cost'] ?? 0);
                    $svcQty      = max(1, intval($svc['qty'] ?? $numPax));
                    $svcDuration = max(1, intval($svc['stay_duration'] ?? 1));
                    $svcType     = $svc['type'] ?? '';
                    $svcLoc      = $svc['loc'] ?? '';
                    $serviceId   = (isset($svc['vendor']) && is_numeric($svc['vendor'])) ? intval($svc['vendor']) : 0;
                    $svcName     = $svc['name'] ?? 'Service';
                    $remarks     = $svcName . ' | Day ' . ($day->day_number ?? 1) . ($svcType ? ' – ' . $svcType : '') . ($svcLoc ? ' (' . $svcLoc . ')' : '');
                    $endDate     = $svcDuration > 1 ? \Carbon\Carbon::parse($serviceDate)->addDays($svcDuration - 1)->toDateString() : null;

                    \App\Models\InvoiceExpense::create([
                        'invoice_id'          => $invoice->id,
                        'service_id'          => $serviceId,
                        'cost'                => $svcCost,
                        'cost_per_unit'       => $svcCost,
                        'qty'                 => $svcQty,
                        'duration'            => $svcDuration,
                        'service_date'        => $serviceDate,
                        'service_end_date'    => $endDate,
                        'service_time'        => '',
                        'time'                => date('Y-m-d H:i:s'),
                        'status'              => 'pen',
                        'payment_status'      => 'u',
                        'vender'              => 0,
                        'vender_notify'       => 0,
                        'paid_by'             => '',
                        'confirmation_number' => '',
                        'remarks'             => $remarks,
                        'added_by'            => auth()->id(),
                    ]);
                    $expenseCount++;
                }
            }
        }

        // ── Generate booking itinerary HTML ──
        $itinHtml = '';
        if ($itin && $itin->days && $itin->days->count() > 0) {
            $guestName = trim(($tripRequest->first_name ?? '') . ' ' . ($tripRequest->last_name ?? ''));
            $cur = $tripRequest->currency ?? 'JOD';
            $itinHtml  = '<div style="font-family:Arial,sans-serif;max-width:860px;">';
            $itinHtml .= '<h2 style="color:#005e46;border-bottom:3px solid #005e46;padding-bottom:10px;">' . htmlspecialchars($tripTitle) . '</h2>';
            $itinHtml .= '<p style="color:#6b7280;margin-bottom:20px;"><strong>Date:</strong> ' . $travelDate . ' &nbsp;|&nbsp; <strong>Guest:</strong> ' . htmlspecialchars($guestName) . ' &nbsp;|&nbsp; <strong>Pax:</strong> ' . $numAdults . ' Adults' . ($numChildren > 0 ? ', ' . $numChildren . ' Children' : '') . '</p>';

            foreach ($itin->days->sortBy('day_number') as $day) {
                $dn    = $day->day_number ?? 1;
                $dDate = $itin->arrival_date ? \Carbon\Carbon::parse($itin->arrival_date)->addDays($dn - 1)->format('D, d M Y') : '';
                $itinHtml .= '<div style="margin-bottom:24px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">';
                $itinHtml .= '<div style="background:#005e46;color:#fff;padding:12px 20px;display:flex;align-items:center;gap:12px;">';
                $itinHtml .= '<span style="background:rgba(255,255,255,.2);border-radius:6px;padding:3px 12px;font-size:13px;font-weight:700;">Day ' . $dn . '</span>';
                $itinHtml .= '<span style="font-size:15px;font-weight:700;">' . htmlspecialchars($day->title ?? ('Day ' . $dn)) . '</span>';
                if ($dDate) $itinHtml .= '<span style="margin-left:auto;font-size:12px;opacity:.8;">' . $dDate . '</span>';
                $itinHtml .= '</div><div style="padding:16px 20px;">';
                if ($day->description) $itinHtml .= '<div style="font-size:14px;color:#374151;line-height:1.7;margin-bottom:12px;">' . $day->description . '</div>';

                $daySvcs = $day->services ?? [];
                if (is_array($daySvcs) && count($daySvcs)) {
                    $itinHtml .= '<table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:8px;">';
                    $itinHtml .= '<tr style="background:#f8fafc;"><th style="text-align:left;padding:7px 10px;border:1px solid #e5e7eb;">Service</th><th style="text-align:center;padding:7px 10px;border:1px solid #e5e7eb;width:55px;">Qty</th><th style="text-align:right;padding:7px 10px;border:1px solid #e5e7eb;width:110px;">Cost</th></tr>';
                    foreach ($daySvcs as $sv) {
                        if (!is_array($sv)) continue;
                        $ln = floatval($sv['cost'] ?? 0) * max(1, intval($sv['qty'] ?? $numPax)) * max(1, intval($sv['stay_duration'] ?? 1));
                        $itinHtml .= '<tr><td style="padding:7px 10px;border:1px solid #e5e7eb;"><strong>' . htmlspecialchars($sv['name'] ?? '') . '</strong>';
                        if ($sv['loc'] ?? '') $itinHtml .= ' <span style="color:#9ca3af;font-size:12px;">— ' . htmlspecialchars($sv['loc']) . '</span>';
                        $itinHtml .= '</td><td style="text-align:center;padding:7px 10px;border:1px solid #e5e7eb;">' . max(1, intval($sv['qty'] ?? $numPax)) . '</td>';
                        $itinHtml .= '<td style="text-align:right;padding:7px 10px;border:1px solid #e5e7eb;font-weight:700;color:#ea580c;">' . number_format($ln, 2) . ' ' . $cur . '</td></tr>';
                    }
                    $itinHtml .= '</table>';
                }
                $itinHtml .= '</div></div>';
            }

            $itinHtml .= '<div style="background:#f0fdf4;border:2px solid #005e46;border-radius:10px;padding:18px 24px;display:flex;justify-content:space-between;align-items:center;margin-top:8px;">';
            $itinHtml .= '<div><div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;">Net Cost</div><div style="font-size:22px;font-weight:800;color:#1f2937;">' . number_format($serviceTotal, 2) . ' ' . $cur . '</div></div>';
            $itinHtml .= '<div style="text-align:right"><div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;">Selling Price</div><div style="font-size:22px;font-weight:800;color:#005e46;">' . number_format($sellingTotal, 2) . ' ' . $cur . '</div></div>';
            $itinHtml .= '</div></div>';
        }

        if ($itinHtml) {
            $booking->update(['booking_itinerary' => $itinHtml]);
        }

        // ── Mark trip request as booking stage ──
        $tripRequest->update(['pipeline_stage' => 'validation']);

        return redirect()
            ->route('admin.bookings.edit', $booking->id)
            ->with('success', 'Booking #' . $booking->id . ' created! ' . $expenseCount . ' expenses added to Reservations & Costs. Net: ' . number_format($serviceTotal, 2) . ' — Selling: ' . number_format($sellingTotal, 2) . ' (' . ($tripRequest->currency ?? 'JOD') . ')');
    }
}
