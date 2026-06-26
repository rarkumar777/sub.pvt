@extends('admin.layouts.app')

@section('title', 'Admin | Dashboard')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8 tw-pb-10">

    {{-- Header --}}
    <div class="tw-flex tw-items-center tw-gap-4">
        <div class="tw-w-14 tw-h-14 tw-bg-gradient-to-br tw-from-indigo-500 tw-to-violet-600 tw-text-white tw-flex tw-items-center tw-justify-center tw-rounded-2xl tw-text-2xl tw-shadow-lg tw-shadow-indigo-200">
            <i class="fa fa-th-large"></i>
        </div>
        <div>
            <h1 class="tw-text-3xl tw-font-black tw-text-slate-900 tw-tracking-tight tw-m-0" style="color:red !important">Dashboard</h1>
            <p class="tw-text-slate-400 tw-text-sm tw-font-medium tw-mt-1">Welcome back! Here's your system overview.</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-shadow-sm tw-p-6">
        <h3 class="tw-text-sm tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-4 tw-m-0">Quick Actions</h3>
        <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 tw-gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-rounded-xl tw-bg-slate-50 hover:tw-bg-indigo-50 tw-text-slate-600 hover:tw-text-indigo-600 tw-no-underline tw-text-sm tw-font-semibold tw-transition-all">
                <i class="fa fa-plus-circle tw-text-slate-300"></i> New Booking
            </a>
            <a href="{{ route('admin.quotations.index') }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-rounded-xl tw-bg-slate-50 hover:tw-bg-emerald-50 tw-text-slate-600 hover:tw-text-emerald-600 tw-no-underline tw-text-sm tw-font-semibold tw-transition-all">
                <i class="fa fa-file-text-o tw-text-slate-300"></i> New Quotation
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-rounded-xl tw-bg-slate-50 hover:tw-bg-rose-50 tw-text-slate-600 hover:tw-text-rose-600 tw-no-underline tw-text-sm tw-font-semibold tw-transition-all">
                <i class="fa fa-money tw-text-slate-300"></i> View Expenses
            </a>
            <a href="{{ route('admin.users.index') }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-3 tw-rounded-xl tw-bg-slate-50 hover:tw-bg-amber-50 tw-text-slate-600 hover:tw-text-amber-600 tw-no-underline tw-text-sm tw-font-semibold tw-transition-all">
                <i class="fa fa-user-plus tw-text-slate-300"></i> Manage Users
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-5">
        {{-- Bookings --}}
        <a href="{{ route('admin.bookings.index') }}" class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-6 tw-shadow-sm hover:tw-shadow-md hover:tw-border-indigo-200 tw-transition-all tw-group tw-no-underline tw-cursor-pointer">
            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                <div class="tw-w-12 tw-h-12 tw-rounded-xl tw-bg-indigo-50 tw-flex tw-items-center tw-justify-center tw-text-indigo-500 tw-text-xl group-hover:tw-bg-indigo-500 group-hover:tw-text-white tw-transition-all">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
                <span class="tw-text-[10px] tw-font-bold tw-text-emerald-500 tw-bg-emerald-50 tw-px-2 tw-py-1 tw-rounded-lg tw-uppercase">{{ $monthlyBookings }} this month</span>
            </div>
            <div class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-1">Bookings</div>
            <div class="tw-text-3xl tw-font-black tw-text-slate-900">{{ number_format($stats['bookings']) }}</div>
        </a>

        {{-- Quotations --}}
        <a href="{{ route('admin.quotations.index') }}" class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-6 tw-shadow-sm hover:tw-shadow-md hover:tw-border-emerald-200 tw-transition-all tw-group tw-no-underline tw-cursor-pointer">
            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                <div class="tw-w-12 tw-h-12 tw-rounded-xl tw-bg-emerald-50 tw-flex tw-items-center tw-justify-center tw-text-emerald-500 tw-text-xl group-hover:tw-bg-emerald-500 group-hover:tw-text-white tw-transition-all">
                    <i class="fa fa-pie-chart"></i>
                </div>
            </div>
            <div class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-1">Quotations</div>
            <div class="tw-text-3xl tw-font-black tw-text-slate-900">{{ number_format($stats['quotations']) }}</div>
        </a>

        {{-- Invoices --}}
        <div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-6 tw-shadow-sm hover:tw-shadow-md hover:tw-border-amber-200 tw-transition-all tw-group">
            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                <div class="tw-w-12 tw-h-12 tw-rounded-xl tw-bg-amber-50 tw-flex tw-items-center tw-justify-center tw-text-amber-500 tw-text-xl group-hover:tw-bg-amber-500 group-hover:tw-text-white tw-transition-all">
                    <i class="fa fa-bank"></i>
                </div>
            </div>
            <div class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-1">Invoices</div>
            <div class="tw-text-3xl tw-font-black tw-text-slate-900">{{ number_format($stats['invoices']) }}</div>
        </div>

        {{-- Users --}}
        <div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-6 tw-shadow-sm hover:tw-shadow-md hover:tw-border-rose-200 tw-transition-all tw-group">
            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                <div class="tw-w-12 tw-h-12 tw-rounded-xl tw-bg-rose-50 tw-flex tw-items-center tw-justify-center tw-text-rose-500 tw-text-xl group-hover:tw-bg-rose-500 group-hover:tw-text-white tw-transition-all">
                    <i class="fa fa-users"></i>
                </div>
            </div>
            <div class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-1">Users</div>
            <div class="tw-text-3xl tw-font-black tw-text-slate-900">{{ number_format($stats['users']) }}</div>
        </div>
    </div>

    {{-- Financial Overview --}}
    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-5">
        <div class="tw-bg-gradient-to-br tw-from-indigo-600 tw-to-violet-700 tw-rounded-2xl tw-p-6 tw-text-white tw-shadow-lg tw-shadow-indigo-200 tw-relative tw-overflow-hidden">
            <div class="tw-absolute tw-right-[-20px] tw-top-[-20px] tw-w-32 tw-h-32 tw-bg-white/10 tw-rounded-full"></div>
            <div class="tw-relative tw-z-10">
                <div class="tw-flex tw-items-center tw-gap-2 tw-mb-3">
                    <i class="fa fa-line-chart tw-text-indigo-200"></i>
                    <span class="tw-text-xs tw-font-bold tw-text-indigo-200 tw-uppercase tw-tracking-widest">Total Revenue</span>
                </div>
                <div class="tw-text-3xl tw-font-black">{{ number_format($totalRevenue, 2) }} <span class="tw-text-lg tw-font-bold tw-text-indigo-200">JOD</span></div>
            </div>
        </div>

        <div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-6 tw-shadow-sm">
            <div class="tw-flex tw-items-center tw-gap-2 tw-mb-3">
                <i class="fa fa-minus-circle tw-text-rose-400"></i>
                <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Total Expenses</span>
            </div>
            <div class="tw-text-3xl tw-font-black tw-text-rose-600">{{ number_format($totalExpenses, 2) }} <span class="tw-text-lg tw-font-bold tw-text-slate-300">JOD</span></div>
        </div>

        <div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-6 tw-shadow-sm">
            <div class="tw-flex tw-items-center tw-gap-2 tw-mb-3">
                <i class="fa fa-trophy tw-text-emerald-400"></i>
                <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Net Profit</span>
            </div>
            <div class="tw-text-3xl tw-font-black {{ ($totalRevenue - $totalExpenses) >= 0 ? 'tw-text-emerald-600' : 'tw-text-rose-600' }}">{{ number_format($totalRevenue - $totalExpenses, 2) }} <span class="tw-text-lg tw-font-bold tw-text-slate-300">JOD</span></div>
        </div>
    </div>

    {{-- Booking Trends + Status Breakdown --}}
    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-5 tw-gap-5">

        {{-- Monthly Trends Line Chart --}}
        <div class="tw-col-span-3 tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-6 tw-shadow-sm">
            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                <div>
                    <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-m-0">Booking Trends</h3>
                    <p class="tw-text-[11px] tw-text-slate-400 tw-mt-1 tw-m-0">Last 6 months overview</p>
                </div>
                <div class="tw-flex tw-items-center tw-gap-1.5 tw-px-3 tw-py-1.5 tw-bg-slate-50 tw-rounded-lg tw-text-[11px] tw-text-slate-500 tw-font-semibold">
                    <i class="fa fa-calendar"></i> {{ now()->subMonths(5)->format('M Y') }} – {{ now()->format('M Y') }}
                </div>
            </div>
            <div style="height: 220px;">
                <canvas id="bookingTrendsChart"></canvas>
            </div>
        </div>

        {{-- Status Breakdown + Today --}}
        <div class="tw-col-span-2 tw-flex tw-flex-col tw-gap-5">
            {{-- Status Cards --}}
            <div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-6 tw-shadow-sm">
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-m-0 tw-mb-4">Booking Status</h3>
                <div class="tw-grid tw-grid-cols-2 tw-gap-3">
                    <div class="tw-bg-amber-50 tw-rounded-xl tw-p-3 tw-text-center">
                        <div class="tw-text-2xl tw-font-black tw-text-amber-600">{{ $bookingStatuses['pending'] }}</div>
                        <div class="tw-text-[10px] tw-font-bold tw-text-amber-500 tw-uppercase tw-tracking-wider tw-mt-1">Pending</div>
                    </div>
                    <div class="tw-bg-emerald-50 tw-rounded-xl tw-p-3 tw-text-center">
                        <div class="tw-text-2xl tw-font-black tw-text-emerald-600">{{ $bookingStatuses['confirmed'] }}</div>
                        <div class="tw-text-[10px] tw-font-bold tw-text-emerald-500 tw-uppercase tw-tracking-wider tw-mt-1">Confirmed</div>
                    </div>
                    <div class="tw-bg-blue-50 tw-rounded-xl tw-p-3 tw-text-center">
                        <div class="tw-text-2xl tw-font-black tw-text-blue-600">{{ $bookingStatuses['completed'] }}</div>
                        <div class="tw-text-[10px] tw-font-bold tw-text-blue-500 tw-uppercase tw-tracking-wider tw-mt-1">Completed</div>
                    </div>
                    <div class="tw-bg-rose-50 tw-rounded-xl tw-p-3 tw-text-center">
                        <div class="tw-text-2xl tw-font-black tw-text-rose-600">{{ $bookingStatuses['cancelled'] }}</div>
                        <div class="tw-text-[10px] tw-font-bold tw-text-rose-500 tw-uppercase tw-tracking-wider tw-mt-1">Cancelled</div>
                    </div>
                </div>
            </div>

            {{-- Today's Activity --}}
            <div class="tw-bg-gradient-to-br tw-from-slate-800 tw-to-slate-900 tw-rounded-2xl tw-p-6 tw-shadow-sm tw-text-white tw-relative tw-overflow-hidden">
                <div class="tw-absolute tw-right-[-15px] tw-top-[-15px] tw-w-24 tw-h-24 tw-bg-white/5 tw-rounded-full"></div>
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-300 tw-m-0 tw-mb-4 tw-uppercase tw-tracking-widest tw-text-[11px]"><i class="fa fa-clock-o tw-mr-1"></i> Today's Activity</h3>
                <div class="tw-flex tw-items-center tw-justify-between">
                    <div>
                        <div class="tw-text-2xl tw-font-black">{{ $todayBookings }}</div>
                        <div class="tw-text-[10px] tw-text-slate-400 tw-font-bold tw-uppercase tw-mt-1">Bookings</div>
                    </div>
                    <div class="tw-w-px tw-h-10 tw-bg-slate-700"></div>
                    <div>
                        <div class="tw-text-2xl tw-font-black tw-text-rose-400">{{ number_format($todayExpenses, 0) }}</div>
                        <div class="tw-text-[10px] tw-text-slate-400 tw-font-bold tw-uppercase tw-mt-1">Expenses (JOD)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Data --}}
    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-2 tw-gap-5">

        {{-- Recent Bookings --}}
        <div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-shadow-sm tw-overflow-hidden">
            <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-50 tw-flex tw-items-center tw-justify-between">
                <div class="tw-flex tw-items-center tw-gap-2">
                    <div class="tw-w-1 tw-h-5 tw-bg-indigo-500 tw-rounded-full"></div>
                    <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-m-0">Recent Bookings</h3>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="tw-text-xs tw-font-bold tw-text-indigo-500 tw-no-underline hover:tw-text-indigo-700">View All →</a>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                @forelse($recentBookings as $bk)
                <div class="tw-px-6 tw-py-3.5 tw-flex tw-items-center tw-justify-between hover:tw-bg-slate-50/50 tw-transition-colors">
                    <div class="tw-flex tw-items-center tw-gap-3">
                        <div class="tw-w-9 tw-h-9 tw-rounded-lg tw-bg-indigo-50 tw-flex tw-items-center tw-justify-center tw-text-indigo-500 tw-text-xs tw-font-black">#{{ $bk->id }}</div>
                        <div>
                            <div class="tw-text-sm tw-font-semibold tw-text-slate-700">{{ $bk->guest_name ?: 'Guest' }}</div>
                            <div class="tw-text-[11px] tw-text-slate-400">{{ $bk->booked_in_date ? date('d M Y', strtotime($bk->booked_in_date)) : 'N/A' }}</div>
                        </div>
                    </div>
                    @php
                        $bkStatus = $bk->status ?? 'pen';
                        $bkColors = ['pen'=>'tw-bg-amber-50 tw-text-amber-600','con'=>'tw-bg-emerald-50 tw-text-emerald-600','com'=>'tw-bg-blue-50 tw-text-blue-600','can'=>'tw-bg-rose-50 tw-text-rose-600'];
                        $bkLabels = ['pen'=>'Pending','con'=>'Confirmed','com'=>'Completed','can'=>'Cancelled'];
                    @endphp
                    <span class="tw-text-[10px] tw-font-bold tw-px-2 tw-py-1 tw-rounded-md tw-uppercase {{ $bkColors[$bkStatus] ?? 'tw-bg-slate-50 tw-text-slate-500' }}">{{ $bkLabels[$bkStatus] ?? $bkStatus }}</span>
                </div>
                @empty
                <div class="tw-px-6 tw-py-8 tw-text-center tw-text-slate-400 tw-text-sm">No bookings yet</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Expenses --}}
        <div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-shadow-sm tw-overflow-hidden">
            <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-50 tw-flex tw-items-center tw-justify-between">
                <div class="tw-flex tw-items-center tw-gap-2">
                    <div class="tw-w-1 tw-h-5 tw-bg-rose-500 tw-rounded-full"></div>
                    <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-m-0">Recent Expenses</h3>
                </div>
                <a href="{{ route('admin.expenses.index') }}" class="tw-text-xs tw-font-bold tw-text-indigo-500 tw-no-underline hover:tw-text-indigo-700">View All →</a>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                @forelse($recentExpenses as $exp)
                <div class="tw-px-6 tw-py-3.5 tw-flex tw-items-center tw-justify-between hover:tw-bg-slate-50/50 tw-transition-colors">
                    <div class="tw-flex tw-items-center tw-gap-3">
                        <div class="tw-w-9 tw-h-9 tw-rounded-lg tw-bg-rose-50 tw-flex tw-items-center tw-justify-center tw-text-rose-500 tw-text-xs">
                            <i class="fa fa-money"></i>
                        </div>
                        <div>
                            <div class="tw-text-sm tw-font-semibold tw-text-slate-700">{{ $exp->remarks ?: 'Expense #'.$exp->id }}</div>
                            <div class="tw-text-[11px] tw-text-slate-400">{{ optional($exp->venderUser)->company ?: optional($exp->venderUser)->first_name ?: 'Vendor' }}</div>
                        </div>
                    </div>
                    <span class="tw-text-sm tw-font-bold tw-text-rose-600">-{{ number_format($exp->cost, 2) }}</span>
                </div>
                @empty
                <div class="tw-px-6 tw-py-8 tw-text-center tw-text-slate-400 tw-text-sm">No expenses yet</div>
                @endforelse
            </div>
        </div>
    </div>


</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('bookingTrendsChart');
    if (!ctx) return;

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyTrends, 'label')) !!},
            datasets: [{
                label: 'Bookings',
                data: {!! json_encode(array_column($monthlyTrends, 'count')) !!},
                borderColor: '#6366f1',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 2.5,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#6366f1',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 12, weight: '700' },
                    bodyFont: { size: 13, weight: '600' },
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        title: function(items) { return items[0].label + ' {{ now()->format("Y") }}'; },
                        label: function(item) { return item.raw + ' bookings'; }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: '600' }, color: '#94a3b8' },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { font: { size: 11, weight: '600' }, color: '#94a3b8', stepSize: Math.ceil({!! $maxTrend !!} / 4) },
                    border: { display: false }
                }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
});
</script>
@endpush
