<div class="row">
    @foreach($tours as $tour)
    <div class="md-6 bd-3 relative scroll-animate">
        <div class="pad">
            <div class="tours-random-box light-bordered">
                <div class="relative">
                    <a href="{{ url($lang . '/tours/' . strtolower($countries[$tour->start_country] ?? 'jordan') . '/' . $tour->url) }}/">
                        @php
                            $imgSrc = empty($tour->image) ? '' : url($tour->image);
                            $fallbackSvg = "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22400%22%20height%3D%22250%22%20viewBox%3D%220%200%20400%20250%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23f1f5f9%22%2F%3E%3Cpath%20d%3D%22M200%20100a15%2015%200%201%200%200-30%2015%2015%200%200%200%200%2030zm-50%2070l35-45%2025%2030%2035-15%2025%2030h-120z%22%20fill%3D%22%23cbd5e1%22%2F%3E%3C%2Fsvg%3E";
                        @endphp
                        <img src="{{ $imgSrc ?: $fallbackSvg }}" onerror="this.onerror=null; this.src='{{ $fallbackSvg }}';" style="width: 100%; height: 230px; object-fit: cover;" class="full-width block" alt="{{ html_entity_decode($tour->title, ENT_QUOTES, 'UTF-8') }}">
                    </a>
                    <div class="animated hover">
                        <div data-truncate="4">{{ html_entity_decode($tour->meta_desc, ENT_QUOTES, 'UTF-8') }}</div>
                    </div>
                </div>
                <div data-truncate="1" class="bold pad-t"><strong>{{ html_entity_decode($tour->title, ENT_QUOTES, 'UTF-8') }}</strong></div>
                <div class="row light-bordered-b" style="color: #333 !important;">
                    <div class="sd-6 align-left pad"><i class="fa-calendar-o"></i> Days: {{ $tour->days }}</div>
                    <div class="sd-6 align-right pad">{{ $tour->city }} - {{ $countries[$tour->start_country] ?? '' }}</div>
                </div>
                <div class="pad-t pad-b">
                    <div class="row">
                        <div class="sd-4 d-pad-t">
                            <a class="pvt-orange round-corners h-pad pad-r d-gap-t text-uppercase" href="{{ url($lang . '/tours/' . strtolower($countries[$tour->start_country] ?? 'jordan') . '/' . $tour->url) }}/"><i class="fa-info-circle"></i> Details</a>
                        </div>
                        <div class="sd-8 price">
                            @if($tour->min_price > 0)
                                <span class="price-from">From</span>
                                @php 
                                    $curr = $activeCurrency ?? session('currency', 'USD');
                                    $gSym = ['USD'=>'$','JOD'=>'JD','EUR'=>'€'][$curr] ?? '$'; 
                                    $gRate = ['USD'=>1,'JOD'=>0.709,'EUR'=>0.92][$curr] ?? 1; 
                                @endphp
                                <span class="price-value">{{ $gSym }}{{ number_format(round($tour->min_price * $gRate), 0) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
