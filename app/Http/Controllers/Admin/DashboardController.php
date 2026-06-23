<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourBooking;
use App\Models\TourQuotation;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'bookings' => TourBooking::count(),
            'quotations' => TourQuotation::count(),
            'invoices' => Invoice::count(),
            'users' => User::count(),
        ];

        // Recent bookings (last 5)
        $recentBookings = TourBooking::orderBy('id', 'desc')->limit(5)->get();

        // Recent expenses (last 5)
        $recentExpenses = \App\Models\InvoiceExpense::with('venderUser')
            ->orderBy('id', 'desc')->limit(5)->get();

        // Revenue & expense totals
        $totalRevenue = Invoice::sum('total') ?: 0;
        $totalExpenses = \App\Models\InvoiceExpense::sum('cost') ?: 0;

        // This month's bookings count
        $monthlyBookings = TourBooking::whereMonth('booked_in_date', now()->month)
            ->whereYear('booked_in_date', now()->year)->count();

        // Booking status breakdown
        $bookingStatuses = [
            'pending' => TourBooking::where('trip_status', 'pen')->count(),
            'confirmed' => TourBooking::where('trip_status', 'con')->count(),
            'completed' => TourBooking::where('trip_status', 'com')->count(),
            'cancelled' => TourBooking::where('trip_status', 'can')->count(),
        ];

        // Monthly booking trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyTrends[] = [
                'label' => $date->format('M'),
                'count' => TourBooking::whereMonth('booked_in_date', $date->month)
                    ->whereYear('booked_in_date', $date->year)->count(),
            ];
        }
        $maxTrend = max(array_column($monthlyTrends, 'count')) ?: 1;

        // Today's stats
        $todayBookings = TourBooking::whereDate('booked_in_date', today())->count();
        $todayExpenses = \App\Models\InvoiceExpense::whereDate('service_date', today())->sum('cost') ?: 0;

        return view('admin.dashboard', compact(
            'stats', 'recentBookings', 'recentExpenses',
            'totalRevenue', 'totalExpenses', 'monthlyBookings',
            'bookingStatuses', 'monthlyTrends', 'maxTrend',
            'todayBookings', 'todayExpenses'
        ));
    }

    public function autoAdvisor()
    {
        return view('admin.auto-advisor');
    }

    public function myAccount()
    {
        $user = auth()->user();
        $countries = \App\Models\Country::where('lang', 'en')->orderBy('name')->get();
        $cities = \App\Models\City::where('lang', 'en')->orderBy('name')->get();
        return view('admin.my-account', compact('user', 'countries', 'cities'));
    }

    public function updateMyAccount(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:en33_users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'url' => 'nullable|url|max:255',
            'country' => 'nullable|integer',
            'city' => 'nullable|integer',
            'company' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|integer|in:1,2',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

        // Extra fields mapping
        $extraFields = ['url', 'country', 'city', 'company', 'mobile', 'fax', 'address', 'gender'];
        foreach ($extraFields as $field) {
            if ($request->has($field)) {
                $user->$field = $request->input($field) ?? '';
            }
        }
        
        if ($request->has('telephone')) {
            $user->phone = $request->input('telephone') ?? '';
        }
        if ($request->has('birth_date')) {
            $user->birth_day = $request->input('birth_date') ?? '';
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $user->avatar = '/uploads/avatars/' . $filename;
        }

        $user->save();

        return redirect()->route('admin.my-account')->with('success', 'Account updated successfully');
    }
}
