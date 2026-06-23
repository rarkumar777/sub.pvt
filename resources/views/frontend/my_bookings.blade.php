@extends('frontend.layout')
@section('title', 'My Bookings')

@section('content')
<div id="main-contents">
    <div class="full-wdith grey pvt-pad-tb">
        <div class="wrap" style="max-width: 1200px;">
            <div class="row cell">
                
                {{-- Left Sidebar Account Menu --}}
                <div class="md-3" style="padding-right: 20px;">
                    <div class="white shadow-box" style="border-radius: 8px; overflow: hidden; margin-bottom: 30px;">
                        <div style="background: #a44b11; color: #fff; padding: 15px 20px; font-size: 18px; font-weight: bold; text-transform: uppercase;">
                            <i class="fa-user-circle-o"></i> My Account
                        </div>
                        <ul style="list-style:none; padding:10px 0; margin:0;">
                            <li>
                                <a href="/{{ $lang }}/users/account/my-bookings/" style="display:block; padding:12px 20px; color:#eb9950; font-weight:600; text-decoration:none; background:#fff5eb; border-left:4px solid #eb9950;">
                                    <i class="fa-list-ul" style="width:25px; color:#eb9950;"></i> My Bookings
                                </a>
                            </li>
                            <li>
                                <a href="/{{ $lang }}/users/account/my-messages/" style="display:block; padding:12px 20px; color:#444; font-weight:600; text-decoration:none; border-left:4px solid transparent; transition:all 0.3s;" onmouseover="this.style.background='#f9f9f9'; this.style.color='#eb9950';" onmouseout="this.style.background='transparent'; this.style.color='#444';">
                                    <i class="fa-comments" style="width:25px; color:#bbb;"></i> My Messages
                                </a>
                            </li>
                            <li>
                                <a href="/{{ $lang }}/users/account/edit-account/" style="display:block; padding:12px 20px; color:#444; font-weight:600; text-decoration:none; border-left:4px solid transparent; transition:all 0.3s;" onmouseover="this.style.background='#f9f9f9'; this.style.color='#eb9950';" onmouseout="this.style.background='transparent'; this.style.color='#444';">
                                    <i class="fa-edit" style="width:25px; color:#bbb;"></i> Edit Account
                                </a>
                            </li>
                            <li style="border-top:1px solid #eee; margin-top:10px; padding-top:10px;">
                                <a href="/{{ $lang }}/users/logout/" style="display:block; padding:12px 20px; color:#d9534f; font-weight:600; text-decoration:none; border-left:4px solid transparent; transition:all 0.3s;" onmouseover="this.style.background='#fdf3f2';" onmouseout="this.style.background='transparent';">
                                    <i class="fa-sign-out" style="width:25px;"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="md-9">
                    <div class="white shadow-box" style="margin-bottom:30px; min-height: 500px; border-radius: 8px; overflow: hidden;">

                        <div class="pvt-orange" style="padding: 15px 20px;">
                            <h2 style="font-size:22px; color:#fff; margin:0; text-transform: uppercase; font-weight: bold;">
                                <i class="fa-list-ul"></i> My Bookings
                            </h2>
                        </div>

                    <div class="sd-12 pad">
                        @if($bookings->isEmpty())
                            <div style="text-align:center; padding:50px 0;">
                                <i class="fa-calendar-o" style="font-size:60px; color:#ddd; margin-bottom:20px; display:block;"></i>
                                <h3 style="color:#999;">You have no bookings yet.</h3>
                                <a href="/{{ $lang }}/" class="btn blue" style="margin-top:20px;">Explore Tours</a>
                            </div>
                        @else
                            <div class="responsive-table">
                                <table class="table" style="width:100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background:#f5f5f5; border-bottom:2px solid #eee;">
                                            <th style="padding:12px; text-align:left;">Booking ID</th>
                                            <th style="padding:12px; text-align:left;">Tour Name</th>
                                            <th style="padding:12px; text-align:left;">Travel Date</th>
                                            <th style="padding:12px; text-align:left;">Status</th>
                                            <th style="padding:12px; text-align:left;">Invoice</th>
                                            <th style="padding:12px; text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookings as $booking)
                                            <tr style="border-bottom:1px solid #eee;">
                                                <td style="padding:12px;">#{{ $booking->id }}</td>
                                                <td style="padding:12px;">
                                                    @if($booking->tour && $booking->tour->contents->first())
                                                        <strong>{{ $booking->tour->contents->first()->title }}</strong>
                                                    @else
                                                        Booking #{{ $booking->id }}
                                                    @endif
                                                </td>
                                                <td style="padding:12px;">{{ $booking->travel_date }}</td>
                                                <td style="padding:12px;">
                                                    @php
                                                        $statusClass = 'grey';
                                                        $statusLabel = $booking->trip_status;
                                                        if($booking->trip_status == 'confirmed' || $booking->trip_status == 'con') { $statusClass = 'success'; $statusLabel = 'Confirmed'; }
                                                        elseif($booking->trip_status == 'pending') { $statusClass = 'warning'; $statusLabel = 'Pending'; }
                                                        elseif($booking->trip_status == 'cancelled' || $booking->trip_status == 'can') { $statusClass = 'error'; $statusLabel = 'Cancelled'; }
                                                    @endphp
                                                    <span class="label {{ $statusClass }}" style="font-size:11px; padding:2px 8px;">{{ strtoupper($statusLabel) }}</span>
                                                </td>
                                                <td style="padding:12px;">
                                                    @if($booking->invoice)
                                                        @php
                                                            $invStatusClass = 'grey';
                                                            if($booking->invoice->status == 'p') $invStatusClass = 'success';
                                                            elseif($booking->invoice->status == 'u') $invStatusClass = 'error';
                                                            elseif($booking->invoice->status == 'pp') $invStatusClass = 'warning';
                                                        @endphp
                                                        <span class="{{ $invStatusClass }}-text" style="font-weight:bold;">
                                                            {{ number_format($booking->invoice->total, 2) }} {{ $activeCurrency }}
                                                        </span>
                                                        <span style="font-size:10px; color:#999; display:block;">({{ $booking->invoice->status == 'p' ? 'Paid' : 'Unpaid' }})</span>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td style="padding:12px; text-align:center;">
                                                    @if($booking->invoice)
                                                        <a href="/{{ $lang }}/invoice/{{ $booking->invoice->id }}/" class="btn {{ $booking->invoice->status == 'u' ? 'blue' : 'green' }}" style="padding:4px 12px; font-size:12px;">
                                                            <i class="fa-file-text-o"></i> {{ $booking->invoice->status == 'u' ? 'Pay Now' : 'View Invoice' }}
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
