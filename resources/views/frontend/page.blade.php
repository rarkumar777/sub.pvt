@extends('frontend.layout')

@section('title', $page->title ?? 'Page')
@section('description', $page->meta_desc ?? '')
@section('keywords', $page->meta_key ?? '')

@section('content')
<style>
    /* Fix hotel/destination card gaps: equal height cards in each row */
    .body-wrap .pad .row {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 24px !important;
    }
    /* Prevent clearfix pseudo-elements from becoming empty flex items that take up grid space */
    .body-wrap .pad .row::before,
    .body-wrap .pad .row::after {
        display: none !important;
    }
    .body-wrap .pad .md-4,
    .body-wrap .pad .bd-3 {
        display: flex !important;
        flex-direction: column !important;
        float: none !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    @media (min-width: 768px) {
        .body-wrap .pad .md-4,
        .body-wrap .pad .bd-3 {
            width: calc(50% - 12px) !important;
        }
    }
    @media (min-width: 1024px) {
        .body-wrap .pad .md-4,
        .body-wrap .pad .bd-3 {
            width: calc(33.333% - 16px) !important;
        }
    }
    .body-wrap .pad .white.shadow-box {
        display: flex !important;
        flex-direction: column !important;
        height: 100% !important;
        margin-bottom: 20px !important;
    }
    .body-wrap .pad .white.shadow-box img {
        width: 100% !important;
        height: 186px !important;
        object-fit: cover !important;
    }
    .body-wrap .pad .white.shadow-box [data-truncate] {
        flex-grow: 1 !important;
    }
    /* Fix body paragraph text color - prevent orange inheritance */
    .body-wrap .d-pad > .pad > p,
    .body-wrap .d-pad > .pad > p span,
    .body-wrap .d-pad > .pad > p strong {
        color: #333333 !important;
    }
</style>
<div class="body-wrap" style="padding-top: 80px;">
    <div class="wrap">
        <div class="row">
            <div class="sd-12 nopad">
                <div class="row">
                    <div class="d-pad">
                        @php
                            $starCount = 0;
                            if (isset($pageContent) && preg_match('/(\d+)\s*Star/i', $pageContent, $matches)) {
                                $starCount = (int)$matches[1];
                            }
                        @endphp
                        <h1 style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <span>{{ $page->title ?? '' }}</span>
                            @if($starCount > 0)
                                <span style="display: flex; align-items: center; gap: 4px;">
                                    @for($i = 0; $i < $starCount; $i++)
                                        <i data-lucide="star" class="w-6 h-6 text-amber-400 fill-current" style="color: #fbbf24;"></i>
                                    @endfor
                                </span>
                            @endif
                        </h1>
                        <div class="pad" style="margin-top: 15px;">
                            {!! $pageContent ?? '' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
