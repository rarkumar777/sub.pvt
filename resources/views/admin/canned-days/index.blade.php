@extends('admin.layouts.app')

@section('title', 'Admin | Canned Days')

@section('content')
<style>
    .ev-library-shell {
        --ev-green: #ea580c;
        --ev-link: #ea580c;
        --ev-cyan: #00a3b5;
        --ev-border: #e3e7e9;
        --ev-text: #20272c;
        --ev-muted: #6d7a82;
        max-width: 780px;
        margin: -12px auto 56px;
        color: var(--ev-text);
        font-family: "Inter", Arial, sans-serif;
    }
    .ev-library-tools {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 142px;
        gap: 16px;
        align-items: center;
        margin-bottom: 36px;
    }
    .ev-search-wrap {
        position: relative;
    }
    .ev-search-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #7d8991;
        font-size: 18px;
        z-index: 1;
    }
    .ev-search-wrap input {
        width: 100% !important;
        height: 42px !important;
        border: 1px solid var(--ev-border) !important;
        border-radius: 3px !important;
        box-shadow: 0 1px 4px rgba(0,0,0,.08) !important;
        padding: 0 14px 0 46px !important;
        font-size: 16px !important;
        color: var(--ev-text) !important;
        background: #fff !important;
    }
    .ev-lang-select {
        height: 42px !important;
        border: 1px solid #cfd6da !important;
        border-radius: 4px !important;
        padding: 0 12px !important;
        font-size: 15px !important;
        color: var(--ev-text) !important;
        background: #fff !important;
        box-shadow: none !important;
    }
    .ev-section-head {
        display: grid;
        grid-template-columns: 46px minmax(0, 1fr) auto;
        gap: 18px;
        align-items: center;
        margin: 0 0 28px;
    }
    .ev-section-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #20272c;
        font-size: 42px;
    }
    .ev-section-title {
        margin: 0;
        font-size: 18px;
        font-weight: 500;
        line-height: 1.3;
    }
    .ev-see-more {
        border: 0;
        background: transparent;
        color: var(--ev-link);
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        padding: 0 8px;
    }
    .ev-card-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .ev-day-card {
        position: relative;
        min-height: 106px;
        border-radius: 2px;
        overflow: hidden;
        background: #555;
        box-shadow: 0 1px 4px rgba(0,0,0,.18);
        isolation: isolate;
    }
    .ev-day-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(0,0,0,.55), rgba(0,0,0,.18)), var(--ev-bg);
        background-size: cover;
        background-position: center;
        z-index: -2;
    }
    .ev-day-card::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,.12);
        z-index: -1;
    }
    .ev-day-body {
        min-height: 106px;
        padding: 28px 58px 22px 18px;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .ev-location {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0,0,0,.35);
    }
    .ev-card-title {
        margin: 0;
        color: #fff !important;
        font-size: 24px !important;
        line-height: 1.22 !important;
        font-weight: 500 !important;
        letter-spacing: 0 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,.35);
    }
    .ev-menu-button {
        position: absolute;
        top: 14px;
        right: 12px;
        width: 28px;
        height: 34px;
        border: 0;
        background: transparent;
        color: #fff;
        cursor: pointer;
        font-size: 22px;
        line-height: 1;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-shadow: 0 1px 2px rgba(0,0,0,.45);
    }
    .ev-card-menu {
        position: absolute;
        top: 44px;
        right: 12px;
        width: 142px;
        display: none;
        background: #fff;
        border: 1px solid var(--ev-border);
        box-shadow: 0 8px 22px rgba(0,0,0,.18);
        z-index: 4;
    }
    .ev-card-menu.open {
        display: block;
    }
    .ev-card-menu button,
    .ev-card-menu a {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border: 0;
        background: #fff;
        color: #263238;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        text-align: left;
        cursor: pointer;
    }
    .ev-card-menu button:hover,
    .ev-card-menu a:hover {
        background: #f3f6f6;
        color: var(--ev-link);
    }
    .ev-card-menu a.ev-danger {
        color: #db2447;
    }
    .ev-card-langs {
        position: absolute;
        right: 12px;
        bottom: 10px;
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        justify-content: flex-end;
        max-width: 180px;
    }
    .ev-lang-chip {
        min-width: 26px;
        height: 21px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 2px;
        background: rgba(255,255,255,.92);
        color: #132027;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .ev-empty {
        padding: 54px 20px;
        border: 1px dashed #d8dee2;
        background: #fff;
        text-align: center;
        color: var(--ev-muted);
    }
    .ev-add-fab {
        position: fixed;
        right: 30px;
        bottom: 68px;
        z-index: 899;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        height: 48px;
        padding: 0 18px;
        border: 0;
        border-radius: 999px;
        background: #ea580c;
        color: #fff;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: .02em;
        box-shadow: 0 8px 22px rgba(0,0,0,.25);
        cursor: pointer;
    }
    .ev-add-fab i {
        font-size: 18px;
    }
    .ev-add-lang {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,.75);
        font-size: 17px;
        line-height: 1;
    }
    @media (max-width: 720px) {
        .ev-library-shell {
            max-width: 100%;
            margin-top: 0;
        }
        .ev-library-tools {
            grid-template-columns: 1fr;
        }
        .ev-section-head {
            grid-template-columns: 40px minmax(0, 1fr);
        }
        .ev-see-more {
            grid-column: 2;
            justify-self: start;
        }
        .ev-card-title {
            font-size: 21px !important;
        }
        .ev-add-fab {
            right: 18px;
            bottom: 24px;
        }
    }
</style>

<div class="ev-library-shell">
    @if(session('success'))
    <div style="background:#e7f5ef;border-left:4px solid #ea580c;color:#ea580c;padding:12px 16px;margin-bottom:20px;font-size:13px;font-weight:700;">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="ev-library-tools">
        <div class="ev-search-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="search" placeholder="Search in my library" autocomplete="off">
        </div>
        <select id="libraryLang" class="ev-lang-select" aria-label="Language">
            <option value="en">🇬🇧 English</option>
            <option value="fr">🇫🇷 French</option>
            <option value="it">🇮🇹 Italian</option>
            <option value="es">🇪🇸 Spanish</option>
            <option value="ar">🇯🇴 Arabic</option>
        </select>
    </div>

    <section>
        <div class="ev-section-head">
            <div class="ev-section-icon" aria-hidden="true"><i class="fa fa-calendar-o"></i></div>
            <h2 class="ev-section-title">Days</h2>
            <button type="button" class="ev-see-more" onclick="showAllCannedDays()">See more</button>
        </div>

        <div id="canned_days_list" class="ev-card-list">
            @forelse($cannedDays as $index => $day)
            @php
                $content = $day->contents->where('lang', 'en')->first() ?: $day->contents->first();
                $title = $content && trim($content->title) !== '' ? trim($content->title) : '(No title Available)';
                $images = @unserialize($day->images);
                if (!is_array($images)) {
                    $images = [];
                }
                $firstImage = collect($images)->filter()->first();
                $imageUrl = '';
                if ($firstImage) {
                    $imageUrl = str_starts_with($firstImage, 'http') ? $firstImage : asset(ltrim($firstImage, '/'));
                }
                $fallbacks = [
                    'linear-gradient(90deg, #73523e, #2a2230)',
                    'linear-gradient(90deg, #777, #555)',
                    'linear-gradient(90deg, #6b3d20, #201712)',
                    'linear-gradient(90deg, #5b6d73, #1c3336)',
                    'linear-gradient(90deg, #725f43, #2a281e)',
                ];
                $bgValue = $imageUrl ? 'url("' . str_replace('"', '%22', $imageUrl) . '")' : $fallbacks[$index % count($fallbacks)];
                $langs = $day->contents->pluck('lang')->filter()->map(function ($lang) {
                    return $lang === 'Ar' ? 'AR' : strtoupper($lang);
                })->unique()->values();
                $place = str_contains(strtolower($title), 'petra') ? 'Petra' : (str_contains(strtolower($title), 'amman') ? 'Amman' : 'Jordan');
            @endphp
            <article class="ev-day-card ev-library-item" data-title="{{ strtolower($title) }}" data-langs="{{ strtolower($langs->implode(' ')) }}" style="--ev-bg: {{ $bgValue }};">
                <div class="ev-day-body">
                    <div class="ev-location"><i class="fa fa-map-marker"></i> {{ $place }}</div>
                    <h3 class="ev-card-title">{{ $title }}</h3>
                </div>
                <button type="button" class="ev-menu-button" onclick="toggleCannedMenu(event, {{ $day->id }})" aria-label="Open actions">⋮</button>
                <div id="canned_menu_{{ $day->id }}" class="ev-card-menu">
                    <button type="button" onclick="openEditModal({{ $day->id }}); closeCannedMenus();"><i class="fa fa-pencil"></i> Edit</button>
                    <a href="{{ route('admin.canned-days.destroy.get', $day->id) }}" class="ev-danger" onclick="return confirm('Note: This will permanently delete the canned day itinerary. Continue?');"><i class="fa fa-trash"></i> Delete</a>
                </div>
                @if($langs->isNotEmpty())
                <div class="ev-card-langs">
                    @foreach($langs as $lang)
                    <span class="ev-lang-chip">{{ $lang }}</span>
                    @endforeach
                </div>
                @endif
            </article>
            @empty
            <div class="ev-empty ev-library-item">
                <strong>No Records Found</strong>
                <div style="margin-top:6px;">Create your first reusable day template.</div>
            </div>
            @endforelse
        </div>
    </section>
</div>

<button type="button" class="ev-add-fab" onclick="openCreateModal()">
    <i class="fa fa-plus"></i>
    <span>ADD</span>
    <span class="ev-add-lang">🇬🇧</span>
</button>

{{-- ═══ EDIT CANNED DAY MODAL ═══ --}}
<div id="editCannedDayOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999;">
    <div style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.45); backdrop-filter:blur(4px);" onclick="closeEditModal()"></div>
    <div style="position:fixed; top:0; left:0; right:0; bottom:0; display:flex; align-items:center; justify-content:center; padding:1.5rem; pointer-events:none;">
        <div id="editCannedDayPanel" style="background:#fff; border-radius:16px; box-shadow:0 25px 60px -12px rgba(0,0,0,.25); width:100%; max-width:1100px; max-height:88vh; display:flex; flex-direction:column; pointer-events:auto; font-family:'Inter',sans-serif; overflow:hidden;">
            {{-- Modal Header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:20px 28px; border-bottom:1px solid #e5e7eb; flex-shrink:0;">
                <h3 style="font-size:18px; font-weight:800; color:#1a1a1a; margin:0;">Modify day</h3>
                <div style="display:flex; align-items:center; gap:12px;">
                    <button type="button" onclick="closeEditModal()" style="font-size:14px; font-weight:700; color:#6b7280; background:none; border:none; cursor:pointer; padding:6px 12px;">Cancel</button>
                    <button type="button" id="editModalSaveBtn" onclick="submitEditModal()" style="padding:10px 24px; border-radius:10px; border:none; background:#ea580c; color:#fff; font-size:13px; font-weight:700; cursor:pointer; transition:all .2s;">Save</button>
                </div>
            </div>
            {{-- Modal Body --}}
            <div style="overflow-y:auto; flex:1; padding:24px 28px;">
                <div id="editModalLoading" style="text-align:center; padding:60px 0; color:#94a3b8;">
                    <i class="fa fa-spinner fa-spin" style="font-size:28px;"></i>
                    <p style="margin-top:12px; font-size:13px; font-weight:600;">Loading...</p>
                </div>
                <div id="editModalContent" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ CREATE CANNED DAY MODAL ═══ --}}
<div id="createCannedDayOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999;">
    <div style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.45); backdrop-filter:blur(4px);" onclick="closeCreateModal()"></div>
    <div style="position:fixed; top:0; left:0; right:0; bottom:0; display:flex; align-items:center; justify-content:center; padding:1.5rem; pointer-events:none;">
        <div style="background:#fff; border-radius:16px; box-shadow:0 25px 60px -12px rgba(0,0,0,.25); width:100%; max-width:1100px; max-height:88vh; display:flex; flex-direction:column; pointer-events:auto; font-family:'Inter',sans-serif; overflow:hidden;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:20px 28px; border-bottom:1px solid #e5e7eb; flex-shrink:0;">
                <h3 style="font-size:18px; font-weight:800; color:#1a1a1a; margin:0;">New day</h3>
                <div style="display:flex; align-items:center; gap:12px;">
                    <button type="button" onclick="closeCreateModal()" style="font-size:14px; font-weight:700; color:#6b7280; background:none; border:none; cursor:pointer; padding:6px 12px;">Cancel</button>
                    <button type="button" id="createModalSaveBtn" onclick="submitCreateModal()" style="padding:10px 24px; border-radius:10px; border:none; background:#ea580c; color:#fff; font-size:13px; font-weight:700; cursor:pointer; transition:all .2s;">Save</button>
                </div>
            </div>
            <div style="overflow-y:auto; flex:1; padding:24px 28px;">
                <form id="createDayForm" enctype="multipart/form-data">
                    @csrf
                    {{-- Flags --}}
                    <div style="display:flex;gap:8px;margin-bottom:22px;align-items:center;">
                        <div style="width:40px;height:32px;border-radius:6px;border:2px solid transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">🇫🇷</div>
                        <div style="width:40px;height:32px;border-radius:6px;border:2px solid #ea580c;background:#ea580c;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">🇬🇧</div>
                        <div style="width:40px;height:32px;border-radius:6px;border:2px solid transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">🇮🇹</div>
                        <div style="width:40px;height:32px;border-radius:6px;border:2px solid transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">🇪🇸</div>
                        <div style="width:40px;height:32px;border-radius:6px;border:2px solid transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">🇩🇪</div>
                        <div style="width:40px;height:32px;border-radius:6px;border:2px solid transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">🇸🇪</div>
                        <div style="width:40px;height:32px;border-radius:6px;border:2px solid transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;">🇳🇱</div>
                    </div>
                    {{-- Photos --}}
                    <div style="margin-bottom:16px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;"><span style="font-size:11px;font-weight:700;color:#555;">Photos:</span><a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a></div>
                        <input type="file" name="new_images[]" id="createDayImageInput" accept="image/*" multiple style="display:none" onchange="addCreateDayImages(this)">
                        <div id="createDayPhotosRow" style="border:1px dashed #ccc;border-radius:4px;min-height:120px;display:flex;overflow-x:auto;gap:8px;padding:8px;align-items:center;">
                            <div onclick="document.getElementById('createDayImageInput').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#aaa;font-size:28px;">📷</div>
                        </div>
                    </div>
                    {{-- Title + Site(s) - Horizontal 3-column --}}
                    <div style="display:flex;gap:16px;margin-bottom:16px;">
                        <div style="flex:0 0 240px;">
                            <fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Day Title</legend><input type="text" name="title" required maxlength="255" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder="Give this day a clear title..."></fieldset>
                            <fieldset style="border:1px solid #ddd;border-radius:4px;padding:8px 12px;margin:0;min-height:120px;"><legend style="font-size:10px;color:#999;margin-left:2px;padding:0 4px;">Site(s)</legend>
                                <div id="createDaySiteTags" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px;"></div>
                                <div style="display:flex;align-items:center;gap:6px;cursor:pointer;" onclick="addCreateSiteTag()"><i class="fa fa-map-marker" style="color:#ea580c;"></i><span style="font-size:13px;color:#ea580c;font-weight:600;">Add a destination</span></div>
                            </fieldset>
                        </div>
                        <div style="flex:1;">
                            <fieldset style="border:1px solid #ddd;border-radius:4px;padding:4px;margin:0;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend><textarea id="createDescTinymce" name="description" class="modal-tinymce"></textarea></fieldset>
                        </div>
                    </div>
                    {{-- Expenses + Inclusions --}}
                    <div style="display:flex;gap:16px;margin-bottom:16px;">
                        <div style="flex:1;">
                            <fieldset style="border:1px solid #ddd;border-radius:4px;padding:10px 12px;margin:0;">
                                <legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">💰 Expenses</legend>
                                <button type="button" onclick="addCreateExpense()" style="padding:4px 12px;border-radius:4px;border:none;background:#ea580c;color:#fff;font-size:11px;font-weight:700;cursor:pointer;margin-bottom:8px;">+ Add New</button>
                                <div id="createExpenseList"></div>
                            </fieldset>
                        </div>
                        <div style="flex:2;">
                            <fieldset style="border:1px solid #ddd;border-radius:4px;padding:10px 12px;margin:0;">
                                <legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Inclusions</legend>
                                <button type="button" onclick="addCreateInclusion()" style="padding:4px 12px;border-radius:4px;border:none;background:#f97316;color:#fff;font-size:11px;font-weight:700;cursor:pointer;margin-bottom:8px;">+ Add Inclusions</button>
                                <div style="display:flex;gap:12px;">
                                    <fieldset style="flex:1;border:1px solid #d1fae5;border-radius:4px;padding:8px;margin:0;min-height:60px;background:#f0fdf4;"><legend style="font-size:10px;color:#059669;padding:0 4px;">Included</legend><div id="createIncludedList"></div></fieldset>
                                    <fieldset style="flex:1;border:1px solid #fecaca;border-radius:4px;padding:8px;margin:0;min-height:60px;background:#fef2f2;"><legend style="font-size:10px;color:#dc2626;padding:0 4px;">Excluded</legend><div id="createExcludedList"></div></fieldset>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══ EXPENSE PICKER MODAL ═══ --}}
<div id="expensePickerOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:10001;">
    <div style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5);" onclick="closeExpensePicker()"></div>
    <div style="position:fixed; top:0; left:0; right:0; bottom:0; display:flex; align-items:center; justify-content:center; padding:1.5rem; pointer-events:none;">
        <div style="background:#fff; border-radius:6px; box-shadow:0 5px 30px rgba(0,0,0,.3); width:100%; max-width:720px; max-height:80vh; display:flex; flex-direction:column; pointer-events:auto; overflow:hidden; border:1px solid #ccc;">
            {{-- Header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 18px; border-bottom:1px solid #ddd; background:#f5f5f5;">
                <h4 style="margin:0; font-size:15px; font-weight:700;"><i class="fa fa-cog" style="margin-right:6px;"></i> Expenses-&gt; Add New</h4>
                <button type="button" onclick="closeExpensePicker()" style="background:none; border:none; font-size:20px; cursor:pointer; color:#666; line-height:1;">&times;</button>
            </div>
            {{-- Body: two panels --}}
            <div style="display:flex; flex:1; overflow:hidden; min-height:350px;">
                {{-- Left Panel: Category Tree --}}
                <div style="width:280px; border-right:1px solid #ddd; overflow-y:auto; padding:10px; background:#fafafa;">
                    <input type="text" id="expCatSearch" placeholder="Search..." oninput="filterExpCategories()" style="width:100%; padding:6px 10px; border:1px solid #ccc; border-radius:3px; font-size:12px; margin-bottom:8px; box-sizing:border-box;">
                    <div id="expCategoryTree" style="font-size:12px;"></div>
                </div>
                {{-- Right Panel: Services --}}
                <div style="flex:1; overflow-y:auto; padding:10px; position:relative;">
                    <div id="expRightPlaceholder" style="display:flex; align-items:center; justify-content:center; height:100%; color:#ccc;">
                        <i class="fa fa-chevron-left" style="font-size:80px;"></i>
                    </div>
                    <div id="expServiceLoading" style="display:none; text-align:center; padding:40px; color:#94a3b8;">
                        <i class="fa fa-spinner fa-spin" style="font-size:22px;"></i><p style="margin-top:8px; font-size:12px;">Loading...</p>
                    </div>
                    <div id="expServiceList" style="display:none;"></div>
                    <div id="expServiceEmpty" style="display:none; text-align:center; padding:40px; color:#999; font-size:13px;">No services found.</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ INCLUSION PICKER MODAL ═══ --}}
<div id="inclusionPickerOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:10001;">
    <div style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5);" onclick="closeInclusionPicker()"></div>
    <div style="position:fixed; top:0; left:0; right:0; bottom:0; display:flex; align-items:center; justify-content:center; padding:1.5rem; pointer-events:none;">
        <div style="background:#fff; border-radius:6px; box-shadow:0 5px 30px rgba(0,0,0,.3); width:100%; max-width:680px; max-height:80vh; display:flex; flex-direction:column; pointer-events:auto; overflow:hidden; border:1px solid #ccc;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 18px; border-bottom:1px solid #ddd; background:#f5f5f5;">
                <h4 style="margin:0; font-size:15px; font-weight:700;">Add New</h4>
                <button type="button" onclick="closeInclusionPicker()" style="background:none; border:none; font-size:20px; cursor:pointer; color:#666; line-height:1;">&times;</button>
            </div>
            <div style="padding:10px 18px; border-bottom:1px solid #eee;">
                <input type="text" id="incSearchBox" placeholder="Search..." oninput="filterInclusionItems()" style="width:100%; padding:8px 12px; border:1px solid #ccc; border-radius:3px; font-size:13px; box-sizing:border-box;">
            </div>
            <div style="overflow-y:auto; flex:1; padding:0;">
                <div id="incItemsLoading" style="text-align:center; padding:30px; color:#94a3b8;">
                    <i class="fa fa-spinner fa-spin" style="font-size:22px;"></i>
                </div>
                <div id="incItemsList"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="/assets/admin/tinymce/tinymce.min.js"></script>
<script>
document.getElementById('search').addEventListener('keyup', function() {
    var filter = this.value.toLowerCase();
    var cards = document.querySelectorAll('#canned_days_list .ev-library-item');
    cards.forEach(function(card) {
        var text = ((card.getAttribute('data-title') || '') + ' ' + (card.getAttribute('data-langs') || '') + ' ' + (card.textContent || '')).toLowerCase();
        card.style.display = text.indexOf(filter) > -1 ? '' : 'none';
    });
});

var currentEditId = null;

function toggleCannedMenu(event, id) {
    event.preventDefault();
    event.stopPropagation();
    var menu = document.getElementById('canned_menu_' + id);
    var shouldOpen = menu && !menu.classList.contains('open');
    closeCannedMenus();
    if (menu && shouldOpen) {
        menu.classList.add('open');
    }
}

function closeCannedMenus() {
    document.querySelectorAll('.ev-card-menu.open').forEach(function(menu) {
        menu.classList.remove('open');
    });
}

function showAllCannedDays() {
    document.getElementById('search').value = '';
    document.querySelectorAll('#canned_days_list .ev-library-item').forEach(function(card) {
        card.style.display = '';
    });
}

document.addEventListener('click', function() {
    closeCannedMenus();
});

function openEditModal(id) {
    currentEditId = id;
    document.getElementById('editCannedDayOverlay').style.display = 'block';
    document.getElementById('editModalLoading').style.display = 'block';
    document.getElementById('editModalContent').style.display = 'none';
    document.body.style.overflow = 'hidden';

    fetch('/admin/canned-days/' + id + '/edit-ajax', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('editModalContent').innerHTML = data.html;
        document.getElementById('editModalLoading').style.display = 'none';
        document.getElementById('editModalContent').style.display = 'block';
        // Init TinyMCE on edit modal textarea
        initModalTinymce('.edit-modal-tinymce');
    })
    .catch(err => {
        document.getElementById('editModalLoading').innerHTML = '<p style="color:#ef4444;font-weight:700;">Error loading data</p>';
    });
}

function closeEditModal() {
    document.getElementById('editCannedDayOverlay').style.display = 'none';
    document.body.style.overflow = '';
    currentEditId = null;
}

function submitEditModal() {
    if (!currentEditId) return;
    var btn = document.getElementById('editModalSaveBtn');
    btn.textContent = 'Saving...';
    btn.disabled = true;

    var form = document.getElementById('editDayForm');
    if (!form) { btn.textContent = 'Save'; btn.disabled = false; return; }

    var formData = new FormData(form);
    fetch('/admin/canned-days/' + currentEditId + '/update-ajax', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.textContent = '✓ Saved!';
            btn.style.background = '#059669';
            setTimeout(() => { closeEditModal(); location.reload(); }, 600);
        } else {
            btn.textContent = 'Save'; btn.disabled = false;
        }
    })
    .catch(() => { btn.textContent = 'Save'; btn.disabled = false; });
}

function addDayImages(input) {
    var row = document.getElementById('dayPhotosRow');
    if (!row || !input.files) return;
    Array.from(input.files).forEach(function(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var div = document.createElement('div');
            div.style.cssText = 'position:relative;flex-shrink:0;height:104px;';
            div.innerHTML = '<img src="'+e.target.result+'" style="height:100%;border-radius:4px;object-fit:cover;">' +
                '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            row.insertBefore(div, row.lastElementChild);
        };
        reader.readAsDataURL(file);
    });
}

function addDaySiteTag() {
    var name = prompt('Enter destination name:');
    if (!name) return;
    var container = document.getElementById('daySiteTags');
    if (!container) return;
    var tag = document.createElement('span');
    tag.style.cssText = 'display:inline-flex;align-items:center;gap:4px;background:#fff7ed;color:#ea580c;padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600;';
    tag.innerHTML = name + ' <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;color:#ea580c;cursor:pointer;font-size:14px;padding:0 2px;">×</button>';
    container.appendChild(tag);
}

// ═══ EXPENSE PICKER ═══
var _expenseTarget = 'edit';
var _expTreeData = null;
function addModalExpense() { _expenseTarget = 'edit'; openExpensePicker(); }
function addCreateExpense() { _expenseTarget = 'create'; openExpensePicker(); }
function openExpensePicker() {
    document.getElementById('expensePickerOverlay').style.display = 'block';
    document.getElementById('expCatSearch').value = '';
    document.getElementById('expServiceList').style.display = 'none';
    document.getElementById('expServiceList').innerHTML = '';
    document.getElementById('expServiceEmpty').style.display = 'none';
    document.getElementById('expServiceLoading').style.display = 'none';
    document.getElementById('expRightPlaceholder').style.display = 'flex';
    if (!_expTreeData) {
        fetch('/admin/canned-days/category-tree', { headers:{'X-Requested-With':'XMLHttpRequest'} })
        .then(function(r){return r.json();})
        .then(function(data){ _expTreeData = data; renderExpTree(data); });
    } else { renderExpTree(_expTreeData); }
}
function closeExpensePicker() { document.getElementById('expensePickerOverlay').style.display = 'none'; }
function renderExpTree(data) {
    var html = '';
    data.forEach(function(root) {
        var hasKids = root.children && root.children.length;
        html += '<div class="exp-tree-root" data-name="'+root.name.toLowerCase()+'">';
        html += '<div style="display:flex;align-items:center;gap:4px;padding:3px 0;">';
        if (hasKids) html += '<span onclick="toggleExpNode(this)" style="cursor:pointer;font-size:14px;width:16px;text-align:center;user-select:none;">+</span>';
        else html += '<span style="width:16px;"></span>';
        html += '<label style="cursor:pointer;display:flex;align-items:center;gap:4px;"><input type="radio" name="expCatRadio" value="'+root.id+'" onchange="loadExpServicesByCatId('+root.id+')"> '+root.name+'</label>';
        html += '</div>';
        if (hasKids) {
            html += '<div class="exp-tree-children" style="display:none;margin-left:20px;">';
            root.children.forEach(function(child) {
                var hasSubKids = child.children && child.children.length;
                html += '<div class="exp-tree-child" data-name="'+child.name.toLowerCase()+'">';
                html += '<div style="display:flex;align-items:center;gap:4px;padding:2px 0;">';
                if (hasSubKids) html += '<span onclick="toggleExpNode(this)" style="cursor:pointer;font-size:14px;width:16px;text-align:center;user-select:none;">+</span>';
                else html += '<span style="width:16px;"></span>';
                html += '<label style="cursor:pointer;display:flex;align-items:center;gap:4px;"><input type="radio" name="expCatRadio" value="'+child.id+'" onchange="loadExpServicesByCatId('+child.id+')"> '+child.name+'</label>';
                html += '</div>';
                if (hasSubKids) {
                    html += '<div class="exp-tree-children" style="display:none;margin-left:20px;">';
                    child.children.forEach(function(sub) {
                        html += '<div class="exp-tree-sub" data-name="'+(sub.name||'').toLowerCase()+'">';
                        html += '<div style="display:flex;align-items:center;gap:4px;padding:2px 0;">';
                        html += '<span style="width:16px;"></span>';
                        html += '<label style="cursor:pointer;display:flex;align-items:center;gap:4px;"><input type="radio" name="expCatRadio" value="'+sub.id+'" onchange="loadExpServicesByCatId('+sub.id+')"> '+sub.name+'</label>';
                        html += '</div></div>';
                    });
                    html += '</div>';
                }
                html += '</div>';
            });
            html += '</div>';
        }
        html += '</div>';
    });
    document.getElementById('expCategoryTree').innerHTML = html;
}
function toggleExpNode(el) {
    var sibling = el.parentElement.nextElementSibling;
    if (sibling && sibling.classList.contains('exp-tree-children')) {
        var showing = sibling.style.display !== 'none';
        sibling.style.display = showing ? 'none' : 'block';
        el.textContent = showing ? '+' : '-';
    }
}
function filterExpCategories() {
    var q = document.getElementById('expCatSearch').value.toLowerCase();
    var items = document.querySelectorAll('#expCategoryTree [data-name]');
    items.forEach(function(el) { el.style.display = el.getAttribute('data-name').indexOf(q) > -1 ? '' : 'none'; });
}
function loadExpServicesByCatId(catId) {
    document.getElementById('expRightPlaceholder').style.display = 'none';
    document.getElementById('expServiceList').style.display = 'none';
    document.getElementById('expServiceList').innerHTML = '';
    document.getElementById('expServiceEmpty').style.display = 'none';
    document.getElementById('expServiceLoading').style.display = 'block';
    fetch('/admin/canned-days/services-by-category?cat_id=' + catId, { headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(function(r){return r.json();})
    .then(function(data) {
        document.getElementById('expServiceLoading').style.display = 'none';
        var list = document.getElementById('expServiceList');
        if (!data.length) { document.getElementById('expServiceEmpty').style.display = 'block'; return; }
        list.style.display = 'block';
        // Table header
        var thead = document.createElement('div');
        thead.style.cssText = 'display:flex;padding:8px 10px;background:#f0f0f0;border-bottom:2px solid #ddd;font-size:11px;font-weight:700;color:#555;';
        thead.innerHTML = '<div style="flex:2;">Description</div><div style="flex:1;text-align:center;">Cost</div><div style="flex:1;">Vender</div><div style="width:60px;text-align:center;"></div>';
        list.appendChild(thead);
        data.forEach(function(s) {
            var row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;padding:8px 10px;border-bottom:1px solid #eee;font-size:12px;';
            row.onmouseenter = function(){this.style.background='#fef9ef';};
            row.onmouseleave = function(){this.style.background='';};
            var safeDesc = s.description.replace(/'/g,"\\'");
            var safeVender = (s.vender||'').replace(/'/g,"\\'");
            var safeSub = (s.subcategory||'').replace(/'/g,"\\'");
            row.innerHTML = '<div style="flex:2;font-weight:600;color:#333;">'+s.description+'</div><div style="flex:1;text-align:center;color:#555;">'+s.cost+' JOD</div><div style="flex:1;color:#888;font-size:11px;">'+s.vender+'</div><div style="width:60px;text-align:center;"><button type="button" onclick="selectExpenseService('+s.id+',\''+safeDesc+'\',\''+s.cost+'\',\''+safeVender+'\',\''+safeSub+'\')" style="padding:4px 10px;border-radius:4px;border:none;background:#e67e22;color:#fff;font-size:11px;font-weight:600;cursor:pointer;">select</button></div>';
            list.appendChild(row);
        });
    })
    .catch(function(){ document.getElementById('expServiceLoading').style.display='none'; });
}
function selectExpenseService(id, desc, cost, vender, subcat) {
    var listId, idName, descName;
    if (_expenseTarget === 'edit') {
        listId = 'modalExpenseList'; idName = 'modal_expenses_id[]'; descName = 'modal_expenses_desc[]';
    } else {
        listId = 'createExpenseList'; idName = 'create_expenses_id[]'; descName = 'create_expenses_desc[]';
    }
    var list = document.getElementById(listId);
    if (!list) return;
    var div = document.createElement('div');
    div.style.cssText = 'display:flex;align-items:center;padding:8px 10px;background:#f8f9fa;border-radius:6px;margin-bottom:4px;font-size:12px;border:1px solid #e5e7eb;';
    div.innerHTML = '<div style="flex:1;"><div style="font-weight:700;color:#1a1a1a;">'+desc+'</div><div style="font-size:11px;color:#888;margin-top:2px;">'+subcat+' · '+vender+'</div></div><div style="font-weight:700;color:#ea580c;margin-right:10px;white-space:nowrap;">'+cost+' JOD</div><input type="hidden" name="'+idName+'" value="'+id+'"><input type="hidden" name="'+descName+'" value="'+desc+'"><button type="button" onclick="this.parentElement.remove()" style="width:22px;height:22px;border-radius:4px;border:none;background:#ef4444;color:#fff;font-size:10px;cursor:pointer;flex-shrink:0;">✕</button>';
    list.appendChild(div);
    closeExpensePicker();
}

// ═══ INCLUSION PICKER ═══
var _incTarget = 'edit';
var _incItemsData = null;
function addModalInclusion() { _incTarget = 'edit'; openInclusionPicker(); }
function addCreateInclusion() { _incTarget = 'create'; openInclusionPicker(); }
function openInclusionPicker() {
    document.getElementById('inclusionPickerOverlay').style.display = 'block';
    document.getElementById('incSearchBox').value = '';
    if (!_incItemsData) {
        document.getElementById('incItemsLoading').style.display = 'block';
        document.getElementById('incItemsList').innerHTML = '';
        fetch('/admin/canned-days/inclusion-items', { headers:{'X-Requested-With':'XMLHttpRequest'} })
        .then(function(r){return r.json();})
        .then(function(data){ _incItemsData = data; document.getElementById('incItemsLoading').style.display = 'none'; renderInclusionItems(data); });
    } else { renderInclusionItems(_incItemsData); }
}
function closeInclusionPicker() { document.getElementById('inclusionPickerOverlay').style.display = 'none'; }
function renderInclusionItems(data) {
    var html = '';
    data.forEach(function(item) {
        html += '<div class="inc-item-row" data-name="'+item.name.toLowerCase()+'" style="display:flex;align-items:center;justify-content:space-between;padding:10px 18px;border-bottom:1px solid #eee;">';
        html += '<span style="font-size:13px;font-weight:500;color:#333;flex:1;">'+item.name+'</span>';
        html += '<div style="display:flex;gap:6px;">';
        html += '<button type="button" onclick="selectInclusion(\''+item.name.replace(/'/g,"\\'")+'\',true)" style="padding:4px 12px;border-radius:3px;border:none;background:#27ae60;color:#fff;font-size:11px;font-weight:600;cursor:pointer;">✓ Included</button>';
        html += '<button type="button" onclick="selectInclusion(\''+item.name.replace(/'/g,"\\'")+'\',false)" style="padding:4px 12px;border-radius:3px;border:none;background:#e74c3c;color:#fff;font-size:11px;font-weight:600;cursor:pointer;">✗ Excluded</button>';
        html += '</div></div>';
    });
    document.getElementById('incItemsList').innerHTML = html;
}
function filterInclusionItems() {
    var q = document.getElementById('incSearchBox').value.toLowerCase();
    var rows = document.querySelectorAll('.inc-item-row');
    rows.forEach(function(r){ r.style.display = r.getAttribute('data-name').indexOf(q) > -1 ? '' : 'none'; });
}
function selectInclusion(name, isIncluded) {
    var listId, inputName;
    if (_incTarget === 'edit') {
        listId = isIncluded ? 'modalIncludedList' : 'modalExcludedList';
        inputName = isIncluded ? 'modal_included[]' : 'modal_excluded[]';
    } else {
        listId = isIncluded ? 'createIncludedList' : 'createExcludedList';
        inputName = isIncluded ? 'create_included[]' : 'create_excluded[]';
    }
    var list = document.getElementById(listId);
    if (!list) return;
    var div = document.createElement('div');
    div.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:4px 6px;background:#fff;border-radius:4px;margin-bottom:3px;font-size:12px;';
    div.innerHTML = '<span>' + (isIncluded ? '✔ ' : '✗ ') + name + '</span><input type="hidden" name="'+inputName+'" value="'+name+'"><button type="button" onclick="this.parentElement.remove()" style="width:20px;height:20px;border-radius:3px;border:none;background:#ef4444;color:#fff;font-size:9px;cursor:pointer;">✕</button>';
    list.appendChild(div);
    closeInclusionPicker();
}

// ═══ CREATE MODAL FUNCTIONS ═══
function openCreateModal() {
    document.getElementById('createCannedDayOverlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
}
function closeCreateModal() {
    document.getElementById('createCannedDayOverlay').style.display = 'none';
    document.body.style.overflow = '';
}
function submitCreateModal() {
    var btn = document.getElementById('createModalSaveBtn');
    var form = document.getElementById('createDayForm');
    if (!form) return;
    var titleInput = form.querySelector('input[name="title"]');
    if (!titleInput || !titleInput.value.trim()) { alert('Please enter a day title'); return; }
    btn.textContent = 'Saving...'; btn.disabled = true;
    var formData = new FormData(form);
    fetch('{{ route("admin.canned-days.store-ajax") }}', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.textContent = '✓ Saved!';
            btn.style.background = '#059669';
            setTimeout(() => { closeCreateModal(); location.reload(); }, 600);
        } else { btn.textContent = 'Save'; btn.disabled = false; }
    })
    .catch(() => { btn.textContent = 'Save'; btn.disabled = false; });
}
function addCreateDayImages(input) {
    var row = document.getElementById('createDayPhotosRow');
    if (!row || !input.files) return;
    Array.from(input.files).forEach(function(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var div = document.createElement('div');
            div.style.cssText = 'position:relative;flex-shrink:0;height:104px;';
            div.innerHTML = '<img src="'+e.target.result+'" style="height:100%;border-radius:4px;object-fit:cover;">' +
                '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            row.insertBefore(div, row.lastElementChild);
        };
        reader.readAsDataURL(file);
    });
}
function addCreateSiteTag() {
    var name = prompt('Enter destination name:');
    if (!name) return;
    var c = document.getElementById('createDaySiteTags');
    if (!c) return;
    var tag = document.createElement('span');
    tag.style.cssText = 'display:inline-flex;align-items:center;gap:4px;background:#fff7ed;color:#ea580c;padding:4px 10px;border-radius:12px;font-size:12px;font-weight:600;';
    tag.innerHTML = name + ' <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;color:#ea580c;cursor:pointer;font-size:14px;padding:0 2px;">×</button>';
    c.appendChild(tag);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeEditModal(); closeCreateModal(); }
});

// ═══ TinyMCE Init for Modals ═══
function initModalTinymce(selector) {
    // Remove any existing instance first
    if (typeof tinymce !== 'undefined') {
        tinymce.remove(selector);
        tinymce.init({
            selector: selector,
            plugins: 'advlist autolink link image lists charmap preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table directionality emoticons paste textcolor colorpicker textpattern',
            toolbar1: 'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | link image media | forecolor backcolor | table | code fullscreen',
            menubar: false,
            statusbar: false,
            toolbar_items_size: 'small',
            height: 180,
            verify_html: false,
            content_css: false,
            content_style: "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'); body { font-family: 'Inter', sans-serif; font-size: 14px; color: #334155; line-height: 1.6; padding: 12px; }",
            setup: function(editor) {
                editor.on('change', function() { tinymce.triggerSave(); });
            }
        });
    }
}

// Init TinyMCE for Create modal on page load
document.addEventListener('DOMContentLoaded', function() {
    initModalTinymce('#createDescTinymce');
});
</script>
@endsection
