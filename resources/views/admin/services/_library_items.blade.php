{{-- === DAYS Section (Banner Cards) === --}}
@if(isset($cannedDays) && $cannedDays->count() > 0 && (empty($catFilterActive) || $catFilterActive === 'days'))
<div class="lib-section">
    <div class="lib-section-header">
        <div class="lib-section-title">
            <i class="fa fa-calendar-o"></i>
            <span>Days</span>
        </div>
        @if(isset($totalDays) && $totalDays > 5 && (empty($catFilterActive) || $catFilterActive !== 'days'))
        <a href="javascript:void(0)" onclick="seeMoreFilter('days','Days')" class="lib-see-more">See more</a>
        @endif
    </div>
    @foreach($cannedDays as $day)
    @php
        $content = $day->contents->first();
        $title = $content ? $content->title : 'Untitled Day';
        $images = @unserialize($day->images);
        $bgImg = (is_array($images) && count($images) > 0) ? $images[0] : '';
        if($bgImg) $bgImg = str_replace('https://pvt.jo', config('app.url'), $bgImg);
        
        // Mock location if not found in content
        $loc = 'Jordan';
        if(stripos($title, 'Amman') !== false) $loc = 'Amman';
        if(stripos($title, 'Petra') !== false) $loc = 'Petra';
        if(stripos($title, 'Wadi Rum') !== false) $loc = 'Wadi Rum';
    @endphp
    <div class="day-banner-card" onclick="editDay({{ $day->id }})">
        @if($bgImg)
        @php $bgUrl = (str_starts_with($bgImg, 'http')) ? $bgImg : '/' . ltrim($bgImg, '/'); $bgUrl = str_replace(' ', '%20', $bgUrl); @endphp
        <div class="day-banner-bg" style="background-image:url('{{ $bgUrl }}')"></div>
        @else
        <div class="day-banner-bg" style="background:linear-gradient(135deg,#2d3748,#4a5568)"></div>
        @endif
        <div class="day-banner-overlay">
            <div class="day-banner-loc"><i class="fa fa-map-marker"></i> {{ strtoupper($loc) }}</div>
            <div class="day-banner-title">{{ $title }}</div>
        </div>
        <button class="day-banner-dots" onclick="event.stopPropagation();toggleMenu(this)">
            <i class="fa fa-ellipsis-v"></i>
        </button>
        <div class="lib-dropdown">
            <a href="#" onclick="event.preventDefault();editDay({{ $day->id }})"><i class="fa fa-pencil" style="width:16px"></i> Modify</a>
            <div class="divider"></div>
            <a href="{{ route('admin.canned-days.destroy.get', $day->id) }}" class="del" onclick="return confirm('Delete this day?')"><i class="fa fa-trash" style="width:16px"></i> Delete</a>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- === Service Category Sections === --}}
@foreach($groupedServices as $group)
@php
    $cat = $group['category'];
    $services = $group['services'];
    $total = $group['total'];
    $cn = strtolower($cat->name);
    $catId = $cat->id;

    // Use controller-provided type to determine service type flags reliably
    $groupType   = $group['type'] ?? 'service';
    $isAccom     = ($groupType === 'accommodation');
    $isActivity  = ($groupType === 'activity_section');
    $isTransport = ($groupType === 'transport');
    $isTransportSection = ($groupType === 'transport_section');
    $isRestaurant = ($groupType === 'restaurant');
    $isRestaurantSection = ($groupType === 'restaurant_section');
    $isGuide     = ($groupType === 'guide');
    // Keep these secondary checks for icon/color display purposes
    $isHotel = ($catId == 404 || str_contains($cn,'hotel'));

    $fa = 'fa-folder';
    $color = '';
    if($isAccom) { $fa = 'fa-moon-o'; $color = 'color:#0891b2;'; }
    elseif($isHotel) { $fa = 'fa-building'; $color = 'color:#0e7490;'; }
    elseif($isActivity) { $fa = 'fa-binoculars'; $color = 'color:#ea580c;'; }
    elseif($isTransport || $isTransportSection) { $fa = 'fa-car'; $color = 'color:#7c3aed;'; }
    elseif($isRestaurant || $isRestaurantSection) { $fa = 'fa-cutlery'; $color = 'color:#dc2626;'; }
    elseif($isGuide) { $fa = 'fa-user-o'; $color = 'color:#d97706;'; }
    
    // Use display_name from controller if set, otherwise clean up the raw name
    $displayName = $cat->display_name ?? $cat->name;
    $displayName = ltrim($displayName, '* ');
    if(str_contains($displayName, ' - ')) $displayName = explode(' - ', $displayName)[0];
    
    if ($isActivity) {
        $displayName = 'All Activities';
    }
@endphp
<div class="lib-section">
    <div class="lib-section-header">
        <div class="lib-section-title">
            <i class="fa {{ $fa }}" style="{{ $color }}"></i>
            <span style="{{ $color }}">{{ $displayName }}</span>
        </div>
        @if(($total > 4 || (!empty($group['subCategories']) && $group['subCategories']->count() > 10)) && (empty($catFilterActive) || $catFilterActive != $cat->id))
        <a href="javascript:void(0)" onclick="seeMoreFilter('{{ $cat->id }}','{{ addslashes($displayName) }}')" class="lib-see-more">See more</a>
        @endif
    </div>

    {{-- Subcategory filter cards --}}
    @if(!empty($group['subCategories']) && $group['subCategories']->count() > 0 && !$isTransport && !$isTransportSection && !$isRestaurantSection)
    <div style="margin: 0 0 14px 0;">
        <div class="accom-card-ev" onclick="seeMoreFilter('{{ $cat->id }}','{{ addslashes($displayName) }}')" style="padding:16px; margin-bottom:8px; align-items:center;">
            <div class="accom-info-ev" style="margin-left:0;">
                <div class="accom-title-row">
                    <i class="fa {{ $fa }}" style="{{ $color }}"></i>
                    <a href="javascript:void(0)" class="accom-title-ev" style="{{ $color }}; margin-left:8px;">All</a>
                </div>
            </div>
            <div class="accom-dots"><i class="fa fa-ellipsis-v"></i></div>
        </div>
        @php
            $subCatsToDisplay = $group['subCategories'];
            $isSeeMoreView = (!empty($catFilterActive) && $catFilterActive == $cat->id);
            $globalItemCount = 0;
            $totalCombinedItems = (!empty($group['subCategories']) ? $group['subCategories']->count() : 0) + (!empty($services) ? $services->count() : 0);
        @endphp
        <div id="subcats-list-{{ $cat->id }}">
        @foreach($subCatsToDisplay as $index => $sub)
        @php
            if (!$isSeeMoreView && $globalItemCount >= 10) break;
            $globalItemCount++;
            $subName = $sub->name;
            if(str_contains($subName, ' - ')) $subName = explode(' - ', $subName)[0];
            $subName = ltrim($subName, '* ');

            $catImgUrl = '';
            if(!empty($sub->image)){
                $decoded = @json_decode($sub->image, true);
                $catImg = is_array($decoded) ? $decoded[0] : $sub->image;
                if($catImg) {
                    $catImgUrl = (str_starts_with($catImg, 'http')) ? $catImg : '/' . ltrim($catImg, '/');
                }
            }
            
            if(empty($catImgUrl) && isset($sub->services) && $sub->services->count() > 0) {
                foreach($sub->services as $svc) {
                    if(!empty($svc->image)) {
                        $decoded = @json_decode($svc->image, true);
                        $svcImg = is_array($decoded) ? $decoded[0] : $svc->image;
                        if($svcImg) {
                            $catImgUrl = (str_starts_with($svcImg, 'http')) ? $svcImg : '/' . ltrim($svcImg, '/');
                            break;
                        }
                    }
                }
            }
            
            $alignItems = 'flex-start';
            $displayStyle = ($isSeeMoreView && $globalItemCount > 10) ? 'none' : 'flex';
        @endphp
        <div class="accom-card-ev sub-item-{{ $cat->id }}" onclick="editCat({{ $sub->id }}, event)" style="display:{{ $displayStyle }}; padding:16px; margin-bottom:8px; align-items:{{ $alignItems }};">
            @if($catImgUrl)
            <img src="{{ $catImgUrl }}" class="accom-thumb" style="width:40px;height:40px;border-radius:4px;object-fit:cover;margin-right:12px;">
            @elseif($isAccom)
            <div class="accom-thumb" style="width:40px;height:40px;border-radius:4px;background:#f8fafc;border:1px solid #f1f5f9;margin-right:12px;"></div>
            @endif
            <div class="accom-info-ev" style="margin-left:0;">
                <div class="accom-title-row">
                    @if(!$catImgUrl && !$isAccom)
                    <i class="fa {{ $fa }}" style="{{ $color }}"></i>
                    @endif
                    <a href="javascript:void(0)" class="accom-title-ev" style="{{ $color }}; margin-left:{{ ($catImgUrl || $isAccom) ? '0' : '8px' }};">{{ $subName }}</a>
                </div>
                @php
                    $subCity = ($sub->parent && $sub->parent->name) ? $sub->parent->name : 'Jordan';
                @endphp
                <div class="accom-loc-row" style="margin-top:4px;">
                    <i class="fa fa-map-marker"></i>
                    <span>{{ $sub->arrival ?: $subCity }}</span>
                </div>
                <div class="accom-desc-snippet" style="margin-top:4px;">
                    {{ strip_tags($sub->description ?: 'This service located in ' . ($sub->arrival ?: $subCity)) }}
                </div>
            </div>
            <div class="accom-dots"><i class="fa fa-ellipsis-v"></i></div>
        </div>
        @endforeach
        </div>
    </div>
    @endif


    @foreach($services as $service)
    @php
        $locText = $service->arrival;
        if (!$locText) {
            if ($isAccom) {
                // For accommodation: show hotel name + city as location
                $hotelName = ($service->serviceCategory) ? $service->serviceCategory->name : '';
                $cityName  = ($service->serviceCategory && $service->serviceCategory->parent) ? $service->serviceCategory->parent->name : 'Jordan';
                $locText = $hotelName ? $hotelName . ' - ' . $cityName : $cityName;
            } elseif ($isActivity) {
                // For activity: use serviceCategory name as city, then keyword fallback
                $locText = ($service->serviceCategory) ? $service->serviceCategory->name : '';
                if (!$locText) {
                    $descLoc = strtolower($service->description);
                    if(str_contains($descLoc, 'amman')) $locText = 'Amman';
                    elseif(str_contains($descLoc, 'petra')) $locText = 'Petra';
                    elseif(str_contains($descLoc, 'wadi rum') || str_contains($descLoc, ' rum') || str_contains($descLoc, 'bedouin')) $locText = 'Wadi Rum';
                    elseif(str_contains($descLoc, 'madaba')) $locText = 'Madaba';
                    elseif(str_contains($descLoc, 'aqaba')) $locText = 'Aqaba';
                    elseif(str_contains($descLoc, 'jerash')) $locText = 'Jerash';
                    elseif(str_contains($descLoc, 'dead sea')) $locText = 'Dead Sea';
                    elseif(str_contains($descLoc, 'ajloun')) $locText = 'Ajloun';
                    elseif(str_contains($descLoc, 'kerak') || str_contains($descLoc, 'karak')) $locText = 'Kerak';
                    elseif(str_contains($descLoc, 'dana')) $locText = 'Dana';
                    elseif(str_contains($descLoc, 'salt')) $locText = 'Salt';
                    elseif(str_contains($descLoc, 'nebo')) $locText = 'Mount Nebo';
                    elseif(str_contains($descLoc, 'amman airport') || str_contains($descLoc, 'qaia')) $locText = 'Amman Airport (QAIA)';
                }
            } else {
                $descLoc = strtolower($service->description);
                if(str_contains($descLoc, 'amman')) $locText = 'Amman';
                elseif(str_contains($descLoc, 'petra')) $locText = 'Petra';
                elseif(str_contains($descLoc, 'wadi rum') || str_contains($descLoc, ' rum') || str_contains($descLoc, 'bedouin')) $locText = 'Wadi Rum';
                elseif(str_contains($descLoc, 'madaba')) $locText = 'Madaba';
                elseif(str_contains($descLoc, 'aqaba')) $locText = 'Aqaba';
                elseif(str_contains($descLoc, 'jerash')) $locText = 'Jerash';
                elseif(str_contains($descLoc, 'dead sea')) $locText = 'Dead Sea';
                elseif(str_contains($descLoc, 'ajloun')) $locText = 'Ajloun';
                elseif(str_contains($descLoc, 'kerak') || str_contains($descLoc, 'karak')) $locText = 'Kerak';
                elseif(str_contains($descLoc, 'dana')) $locText = 'Dana';
                elseif(str_contains($descLoc, 'salt')) $locText = 'Salt';
                elseif(str_contains($descLoc, 'nebo')) $locText = 'Mount Nebo';
                elseif(str_contains($descLoc, 'amman airport') || str_contains($descLoc, 'qaia')) $locText = 'Amman Airport (QAIA)';
            }
        }

        // Skip only non-activity, non-accommodation rows with no location
        if (empty($locText) && !$isAccom && !$isActivity) {
            continue;
        }

        if (isset($isSeeMoreView) && !$isSeeMoreView && isset($globalItemCount) && $globalItemCount >= 10) break;
        if(isset($globalItemCount)) {
            $globalItemCount++;
            $displayStyle = ($isSeeMoreView && $globalItemCount > 10) ? 'none' : 'flex';
        } else {
            $displayStyle = 'flex';
        }
        $img = $service->image;
        if($img){
            $decoded = @json_decode($img, true);
            $img = is_array($decoded) ? $decoded[0] : $img;
        }
        // If it's an accommodation and no image, use a white transparent box or none to match screenshot
        if ($isAccom && !$img) {
            $imgUrl = ''; // We will handle empty image in html
        } else {
            $imgUrl = $img ? ((str_starts_with($img, 'http')) ? $img : '/' . ltrim($img, '/')) : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=100&h=100&fit=crop';
        }
        
        // Handle flags for Accommodations
        $flagsHtml = '';
        if($isAccom && !empty($service->website)) {
            $flagEmojis = ['fr' => '🇫🇷', 'en' => '🇬🇧', 'it' => '🇮🇹', 'es' => '🇪🇸', 'de' => '🇩🇪', 'se' => '🇸🇪', 'nl' => '🇳🇱'];
            $codes = explode(',', $service->website);
            foreach($codes as $code) {
                $code = trim($code);
                if(isset($flagEmojis[$code])) {
                    $flagsHtml .= $flagEmojis[$code];
                }
            }
        }
    @endphp
    <div class="accom-card-ev sub-item-{{ $cat->id }}" onclick="editSvc({{ $service->id }}{{ $isAccom ? ",'accommodation'" : ($isActivity ? ",'activity_section'" : ($isTransport ? ",'transport'" : ($isTransportSection ? ",'transport_section'" : ($isRestaurantSection ? ",'restaurant_section'" : ($isRestaurant ? ",'restaurant'" : ''))))) }})" style="display:{{ $displayStyle }}; position:relative; {{ ($isAccom || $isTransportSection || $isRestaurantSection) ? 'padding-bottom:28px;' : '' }}">
        @if($imgUrl)
        <img src="{{ $imgUrl }}" class="accom-thumb">
        @else
        <div class="accom-thumb" style="background:#f8fafc; border:1px solid #f1f5f9;"></div>
        @endif
        <div class="accom-info-ev" style="{{ ($isAccom || $isTransportSection || $isRestaurantSection) ? 'width:100%;' : '' }}">
            <div class="accom-title-row">
                @if($isAccom)
                <div style="color:#0891b2;font-size:18px;position:relative;display:inline-block;padding-right:6px;line-height:1;"><i class="fa fa-moon-o"></i><span style="font-size:10px;font-weight:bold;position:absolute;top:-4px;right:-4px;letter-spacing:-1px;">z<sup style="font-size:7px;">Z</sup></span></div>
                @else
                <i class="fa {{ $fa }}" style="{{ $color }}"></i>
                @endif
                <a href="javascript:void(0)" class="accom-title-ev" style="{{ $color }}">{{ $isAccom ? $service->description : $service->description }}</a>
            </div>
            @if($locText)
            <div class="accom-loc-row">
                <i class="fa fa-map-marker"></i>
                <span>{{ $locText }}</span>
            </div>
            @endif
            @if($service->notes && !$isAccom)
            <div class="accom-desc-snippet">
                {{ strip_tags($service->notes) }}
            </div>
            @elseif($isAccom && $service->notes)
            <div class="accom-desc-snippet" style="color:#333;font-size:12px;margin-top:4px;">
                {{ strip_tags($service->notes) }}
            </div>
            @endif
        </div>
        <button class="accom-dots-ev" onclick="event.stopPropagation();toggleMenu(this)"><i class="fa fa-ellipsis-v"></i></button>
        <div class="lib-dropdown">
            <a href="#" onclick="event.preventDefault();editSvc({{ $service->id }}{{ $isAccom ? ",'accommodation'" : ($isActivity ? ",'activity_section'" : ($isTransport ? ",'transport'" : ($isTransportSection ? ",'transport_section'" : ($isRestaurantSection ? ",'restaurant_section'" : ($isRestaurant ? ",'restaurant'" : ''))))) }})"><i class="fa fa-pencil" style="width:16px"></i> Modify</a>
            @if(!$isActivity && !$isTransport && !$isTransportSection && !$isRestaurant && !$isRestaurantSection)
            <a href="#" onclick="event.preventDefault();event.stopPropagation();openSeasons({{ $service->id }}{{ $isAccom ? ",'accommodation'" : '' }})"><i class="fa fa-calendar" style="width:16px"></i> Seasons</a>
            @endif
            <div class="divider"></div>
            <a href="#" class="del" onclick="event.preventDefault();delSvc({{ $service->id }},'{{ addslashes($service->description) }}'{{ $isAccom ? ",'accommodation'" : ($isActivity ? ",'accommodation'" : ($isTransportSection ? ",'accommodation'" : ($isRestaurantSection ? ",'accommodation'" : ($isTransport ? ",'transport'" : ($isRestaurant ? ",'restaurant'" : ''))))) }})"><i class="fa fa-trash" style="width:16px"></i> Delete</a>
        </div>
        @if($isAccom && $flagsHtml)
        <div style="position:absolute;bottom:8px;right:16px;font-size:10px;color:#888;font-weight:600;">
            also exists in: <span style="font-size:14px;letter-spacing:1px;margin-left:2px;vertical-align:middle;">{!! $flagsHtml !!}</span>
        </div>
        @endif
    </div>
    @endforeach

    
    @if(isset($isSeeMoreView) && $isSeeMoreView && isset($totalCombinedItems) && $totalCombinedItems > 10)
    <div class="tw-text-center tw-mt-4" id="load-more-container-{{ $cat->id }}" style="margin-bottom:24px;">
        <button type="button" class="btn btn-primary" onclick="var b=this,o=b.innerHTML;b.innerHTML='<i class=&quot;fa fa-spinner fa-spin&quot;></i> Loading...';b.disabled=true;setTimeout(function(){var t=document.querySelectorAll('.sub-item-{{ $cat->id }}'),h=0,r=0;for(var i=0;i<t.length;i++){if(t[i].style.display==='none'){if(r<10){t[i].style.display='flex';r++;}else{h++;}}}b.innerHTML=o;b.disabled=false;if(h===0)b.parentElement.style.display='none';}, 400);" style="background:#0891b2;border:none;padding:8px 24px;border-radius:4px;color:#fff;font-weight:600;">Load More</button>
    </div>
    @endif
</div>
@endforeach
