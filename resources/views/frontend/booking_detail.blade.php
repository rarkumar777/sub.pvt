@extends('frontend.layout')
@section('title', ($guestName ?: 'Booking #' . $booking->id) . ' - Booking Details')

@section('content')
<div id="main-contents">
    {{-- Header --}}
    <div style="background-color:#f4f4f4; border-bottom: 1px solid #e0e0e0; padding: 15px 30px; display:flex; align-items:center; justify-content:space-between;">
        <h2 style="font-size:20px; font-weight:bold; color:#555; margin:0;">
            Booking > {{ $guestName ?: 'Booking #' . $booking->id }}
        </h2>
        <a href="javascript:void(0);" onclick="document.getElementById('itinerary').style.display='block';" class="btn blue small" style="padding:6px 14px; font-size:12px; background:#337ab7; color:#fff; border-radius:3px;">
            <i class="fa-tag"></i> Details
        </a>
    </div>

    {{-- Expenses Table --}}
    <div style="padding: 20px 30px; min-height: 400px; background:#fff;">
        <div class="responsive-table">
            <table style="width:100%; border-collapse: collapse; border:1px solid #e0e0e0;">
                <thead>
                    <tr style="background:#f5f5f5; border-bottom:1px solid #e0e0e0;">
                        <th style="padding:12px 15px; text-align:left; font-size:13px; font-weight:bold; color:#333; border-right:1px solid #e0e0e0;">Description</th>
                        <th style="padding:12px 15px; text-align:left; font-size:13px; font-weight:bold; color:#333; border-right:1px solid #e0e0e0;">Vender<br>Status</th>
                        <th style="padding:12px 15px; text-align:left; font-size:13px; font-weight:bold; color:#333; border-right:1px solid #e0e0e0;">Date</th>
                        <th style="padding:12px 15px; text-align:left; font-size:13px; font-weight:bold; color:#333;">Country<br>Category</th>
                    </tr>
                </thead>
                <tbody>
                    @if($expenses->isEmpty())
                        <tr>
                            <td colspan="4" style="padding:40px 15px; text-align:center; font-size:14px; color:#888;">
                                No services or expenses have been attached to this booking yet.
                            </td>
                        </tr>
                    @else
                        @foreach($expenses as $expense)
                        @php
                            $service = $expense->service;
                            $vendor = $expense->venderUser;
                            $category = $service ? $service->serviceCategory : null;
                            $parentCategory = $category ? $category->parent : null;

                            // Description
                            $description = $service ? $service->description : ($expense->desc ?? 'N/A');

                            // Vendor info
                            $venderName = 'N/A';
                            $venderPhone = '';
                            if ($vendor) {
                                $venderName = !empty($vendor->company) ? $vendor->company : trim($vendor->first_name . ' ' . $vendor->last_name);
                                $venderPhone = $vendor->phone ?: $vendor->mobile ?: '';
                            }

                            // Status
                            $statusLabel = $statusList[$expense->status] ?? ucfirst($expense->status);
                            $statusColor = $statusColors[$expense->status] ?? 'grey';

                            // Category name
                            $categoryName = '';
                            if ($category) {
                                $categoryName = $category->name;
                            }

                            // Country from service
                            $countryName = '';
                            if ($service && $service->country) {
                                $countryName = $countries[$service->country] ?? '';
                            }

                            // Row background
                            $rowBg = $loop->iteration % 2 == 0 ? '#f9f9f9' : '#ffffff';

                            $vendorCategoryLabel = '';
                            if ($vendorCategoryLabel == '') {
                                $vendorCategoryLabel = '*' . $venderName . ' - ' . $countryName;
                            }
                        @endphp
                        <tr style="border-bottom:1px solid #e0e0e0; background:{{ $rowBg }};">
                            <td style="padding:12px 15px; vertical-align:top; border-right:1px solid #e0e0e0;">
                                <div style="font-size:13px; color:#333;">
                                    <span style="background:#555; color:#fff; padding:2px 6px; border-radius:3px; font-size:11px; margin-right:5px;">Qty ({{ $expense->qty }})</span>
                                    - {{ $description }}
                                </div>
                            </td>
                            <td style="padding:12px 15px; vertical-align:top; border-right:1px solid #e0e0e0;">
                                <div style="font-size:13px; color:#333;">{{ $venderName }}</div>
                                @if($venderPhone)
                                <div style="font-size:12px; color:#333; margin-top:2px; border:1px solid #ccc; display:inline-block; padding:1px 5px; background:#f9f9f9; border-radius:2px;">
                                    &#128222; {{ $venderPhone }}
                                </div>
                                @endif
                                <div style="margin-top:4px;">
                                    @php
                                        $bgColor = '#999';
                                        if ($statusColor == 'green') $bgColor = '#5cb85c';
                                        elseif ($statusColor == 'orange') $bgColor = '#f0ad4e';
                                        elseif ($statusColor == 'red') $bgColor = '#d9534f';
                                        elseif ($statusColor == 'blue') $bgColor = '#337ab7';
                                    @endphp
                                    <span style="display:inline-block; padding:2px 8px; border-radius:3px; font-size:11px; color:#fff; background:{{ $bgColor }};">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </td>
                            <td style="padding:12px 15px; vertical-align:top; font-size:13px; white-space:nowrap; color:#333; border-right:1px solid #e0e0e0;">
                                <div>In: {{ $expense->service_date ?: '-' }}</div>
                                <div>Out: {{ $expense->service_end_date ?: '-' }}</div>
                            </td>
                            <td style="padding:12px 15px; vertical-align:top;">
                                @if($countryName)
                                <div style="margin-bottom:4px;">
                                    <span style="display:inline-block; padding:2px 8px; border-radius:3px; font-size:11px; color:#fff; background:#337ab7;">
                                        {{ $countryName }}
                                    </span>
                                </div>
                                @endif
                                @if($vendorCategoryLabel)
                                <div>
                                    <span style="display:inline-block; padding:2px 8px; border-radius:3px; font-size:11px; color:#3c763d; background:#dff0d8;">
                                        {{ $vendorCategoryLabel }}
                                    </span>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Details Modal --}}
<div id="itinerary" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none;">
    <div style="background: #fff; width: 90%; max-width: 800px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); border-radius: 4px; max-height: 90vh; overflow-y: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
        <a href="javascript:void(0);" onclick="document.getElementById('itinerary').style.display='none';" title="Close" style="position:absolute; right:15px; top:15px; font-size:24px; color:#999; text-decoration:none;">&times;</a>
        
        <div style="padding: 20px 30px; border-bottom: 1px solid #f0f0f0;">
            <h2 style="margin:0; font-size:20px; color:#333; font-weight:bold;">
                <i class="fa fa-calendar" style="margin-right:8px;"></i> Booking > {{ $guestName ?: 'Booking #' . $booking->id }}
            </h2>
        </div>
        
        <div style="padding: 20px 30px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:14px; color:#333;">
                <span>Travel Date: {{ $booking->travel_date }}</span>
                <span>Days: {{ $booking->days }}</span>
            </div>
            <div style="margin-bottom:20px; font-size:14px; color:#333;">
                <span>Nights: {{ $booking->nights }}</span>
            </div>
            
            <div style="background:#f5f5f5; padding:8px 15px; font-weight:bold; color:#333; font-size:14px; margin-bottom:10px;">Travelers</div>
            <div style="display:flex; gap:60px; font-size:14px; color:#333; margin-bottom:20px; padding:0 15px;">
                <span>Adult: {{ $booking->adult ?: 0 }}</span>
                <span>Child: {{ $booking->child ?: 0 }}</span>
                <span>infant: {{ $booking->infant ?: 0 }}</span>
            </div>
            
            <div style="background:#f5f5f5; padding:8px 15px; font-weight:bold; color:#333; font-size:14px; margin-bottom:10px;">Hotel Rooms</div>
            <div style="display:flex; gap:60px; font-size:14px; color:#333; margin-bottom:20px; padding:0 15px;">
                <span>Single: {{ $booking->room_single ?: 0 }}</span>
                <span>Double: {{ $booking->rooms_double ?: 0 }}</span>
                <span>Twin: {{ $booking->rooms_twin ?: 0 }}</span>
                <span>Triple: {{ $booking->rooms_triple ?: 0 }}</span>
            </div>
            
            @if(!empty($booking->note))
            <div style="background:#e4f5ff; padding:8px 15px; color:#005b9f; font-size:14px; font-weight:bold; margin-bottom:15px;">
                Notes
            </div>
            <div style="padding:0 15px 20px 15px; font-size:14px; color:#333;">
                {!! nl2br(e($booking->note)) !!}
            </div>
            @endif
            
            <h3 style="font-size:18px; font-weight:bold; color:#333; margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Itinerary</h3>
            <div style="padding: 0 15px; font-size:14px; color:#333; line-height:1.6;">
                {!! $booking->desc ?? $booking->itinerary ?? 'No itinerary available.' !!}
            </div>
        </div>
    </div>
</div>
@endsection
