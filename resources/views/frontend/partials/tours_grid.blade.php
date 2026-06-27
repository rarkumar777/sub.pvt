<style>
.tour-card-pro {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.18);
    transition: all 0.3s ease;
    border: 1px solid #f3f4f6;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
}
.tour-card-pro:hover {
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.18);
    transform: translateY(-4px);
}
.tour-card-pro .img-container {
    position: relative;
    width: 100%;
    height: 220px;
    overflow: hidden;
    background: #f9fafb;
}
.tour-card-pro .img-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}
.tour-card-pro:hover .img-container img {
    transform: scale(1.05);
}
.tour-card-pro .price-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    background: rgba(255, 255, 255, 0.95);
    color: #111827;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 800;
    font-size: 14px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 2;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.5);
}
.tour-card-pro .price-badge span {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
    margin-right: 2px;
}
.tour-card-pro .content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.tour-card-pro .title {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 8px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-top: 0;
}
.tour-card-pro .desc {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 16px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-grow: 1;
}
.tour-card-pro .meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 16px;
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: 16px;
}
.tour-card-pro .meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #4b5563;
    font-weight: 600;
}
.tour-card-pro .meta-item svg {
    color: #3b82f6;
}
.tour-card-pro .btn-details {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #fff7ed;
    color: #ea580c;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.2s ease;
    text-decoration: none;
}
.tour-card-pro .btn-details:hover {
    background: #ea580c;
    color: white;
}
.tour-card-pro .btn-details svg {
    width: 16px;
    height: 16px;
    transition: transform 0.2s;
}
.tour-card-pro .btn-details:hover svg {
    transform: translateX(4px);
}
</style>

<div class="row" style="display: flex; flex-wrap: wrap; align-items: stretch;">
    @foreach($tours as $tour)
    <div class="md-6 bd-3 relative scroll-animate" style="display: flex; flex-direction: column;">
        <div class="pad" style="flex-grow: 1; display: flex; flex-direction: column;">
            <div class="tour-card-pro" style="flex-grow: 1;">
                <div class="img-container">
                    <a href="{{ url($lang . '/tours/' . strtolower($countries[$tour->start_country] ?? 'jordan') . '/' . $tour->url) }}/">
                        @php
                            $imgSrc = empty($tour->image) ? '' : url($tour->image);
                            $fallbackSvg = "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22400%22%20height%3D%22250%22%20viewBox%3D%220%200%20400%20250%22%3E%3Crect%20width%3D%22100%25%22%20height%3D%22100%25%22%20fill%3D%22%23f3f4f6%22%2F%3E%3Cpath%20d%3D%22M200%20100a15%2015%200%201%200%200-30%2015%2015%200%200%200%200%2030zm-50%2070l35-45%2025%2030%2035-15%2025%2030h-120z%22%20fill%3D%22%23d1d5db%22%2F%3E%3C%2Fsvg%3E";
                        @endphp
                        <img src="{{ $imgSrc ?: $fallbackSvg }}" onerror="this.onerror=null; this.src='{{ $fallbackSvg }}';" alt="{{ html_entity_decode($tour->title, ENT_QUOTES, 'UTF-8') }}">
                    </a>
                    @if($tour->min_price > 0)
                        @php 
                            $curr = $activeCurrency ?? session('currency', 'USD');
                            $gSym = ['USD'=>'$','JOD'=>'JD','EUR'=>'€'][$curr] ?? '$'; 
                            $gRate = ['USD'=>1,'JOD'=>0.709,'EUR'=>0.92][$curr] ?? 1; 
                        @endphp
                        <div class="price-badge">
                            <span>From</span> {{ $gSym }}{{ number_format(round($tour->min_price * $gRate), 0) }}
                        </div>
                    @endif
                </div>
                <div class="content">
                    <h3 class="title"><a href="{{ url($lang . '/tours/' . strtolower($countries[$tour->start_country] ?? 'jordan') . '/' . $tour->url) }}/" style="color: inherit; text-decoration: none;">{{ html_entity_decode($tour->title, ENT_QUOTES, 'UTF-8') }}</a></h3>
                    <div class="desc">{{ html_entity_decode($tour->meta_desc, ENT_QUOTES, 'UTF-8') }}</div>
                    
                    <div class="meta">
                        <div class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            {{ $tour->days }} Days
                        </div>
                        <div class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            {{ $tour->city }} {{ $tour->city ? '-' : '' }} {{ $countries[$tour->start_country] ?? 'Jordan' }}
                        </div>
                    </div>

                    <a class="btn-details" href="{{ url($lang . '/tours/' . strtolower($countries[$tour->start_country] ?? 'jordan') . '/' . $tour->url) }}/">
                        View Details
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
