<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourGuaranteedDeparture;
use App\Models\Tour;
use Illuminate\Http\Request;

class GuaranteedDepartureController extends Controller
{
    public function index(Request $request)
    {
        $query = TourGuaranteedDeparture::with('tour.contents');

        // Filter: Hide completed (default)
        if ($request->input('completed', 'h') !== 's') {
            $query->where(function($q) {
                $q->where('status', '!=', 'com')->orWhere('status', '=', '');
            });
        }

        // Filter: Hide cancelled (default)
        if ($request->input('cancelled', 'h') !== 's') {
            $query->where(function($q) {
                $q->where('status', '!=', 'can')->orWhere('status', '=', '');
            });
        }

        // Filter: Booking number
        if ($request->filled('booking_number') && is_numeric($request->booking_number)) {
            $query->where('booking_id', $request->booking_number);
        }

        // Filter: Title search
        if ($request->filled('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }

        // Filter: Date from
        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }

        // Filter: Date to
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        $departures = $query->orderBy('date', 'ASC')->paginate(20);
        return view('admin.guaranteed-departures.index', compact('departures'));
    }

    public function create()
    {
        $tours = Tour::with('contents')->get();
        return view('admin.guaranteed-departures.create', compact('tours'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_id' => 'required|integer|exists:en33_tours,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'min_to_operate' => 'nullable|integer|min:0',
            'max_to_operate' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:10',
            'adult_price' => 'nullable|numeric|min:0',
            'early_bird_price' => 'nullable|numeric|min:0',
            'last_minute_price' => 'nullable|numeric|min:0',
            'child_price' => 'nullable|numeric|min:0',
            'child_early_bird_price' => 'nullable|numeric|min:0',
            'child_last_minute_price' => 'nullable|numeric|min:0',
            'hotel_grade' => 'nullable|integer|min:0|max:5',
        ]);

        // Ensure status is never null
        $validated['status'] = $validated['status'] ?? '';

        TourGuaranteedDeparture::create($request->except('_token', '_method') + ['status' => $validated['status']]);
        return redirect()->route('admin.guaranteed-departures.index')->with('success', 'Created');
    }

    public function edit($id)
    {
        $departure = TourGuaranteedDeparture::with(['tour.contents', 'departureBookings'])->findOrFail($id);
        $tours = Tour::with('contents')->get();
        return view('admin.guaranteed-departures.edit', compact('departure', 'tours'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tour_id' => 'required|integer|exists:en33_tours,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'min_to_operate' => 'nullable|integer|min:0',
            'max_to_operate' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:10',
            'adult_price' => 'nullable|numeric|min:0',
            'early_bird_price' => 'nullable|numeric|min:0',
            'last_minute_price' => 'nullable|numeric|min:0',
            'child_price' => 'nullable|numeric|min:0',
            'child_early_bird_price' => 'nullable|numeric|min:0',
            'child_last_minute_price' => 'nullable|numeric|min:0',
            'hotel_grade' => 'nullable|integer|min:0|max:5',
        ]);

        // Ensure status is never null
        $data = $request->except('_token', '_method');
        $data['status'] = $data['status'] ?? '';

        TourGuaranteedDeparture::findOrFail($id)->update($data);
        return redirect()->route('admin.guaranteed-departures.edit', $id)->with('success', 'Updated');
    }

    public function destroy($id)
    {
        TourGuaranteedDeparture::destroy($id);
        return redirect()->route('admin.guaranteed-departures.index')->with('success', 'Deleted');
    }

    public function show($id) { return $this->edit($id); }
}
