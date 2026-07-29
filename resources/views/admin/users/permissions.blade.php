@extends('admin.layouts.app')
@section('title', 'Admin | User Permission')

@section('content')
<style>
    /* ═══════════════════════════════════════════════════ */
    /*  Vertical Org-Chart Tree - Material 3              */
    /* ═══════════════════════════════════════════════════ */
    .v-tree { display: flex; flex-direction: column; align-items: center; }
    .v-tree ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding-top: 28px;
        position: relative;
        list-style: none;
        margin: 0; padding-left: 0; padding-right: 0;
    }
    /* Vertical line from parent down */
    .v-tree ul::before {
        content: '';
        position: absolute;
        top: 0; left: 50%;
        width: 2px; height: 28px;
        background: #fed7aa;
    }
    /* Horizontal connector across siblings */
    .v-tree ul > li { position: relative; padding: 28px 6px 0; display: flex; flex-direction: column; align-items: center; }
    .v-tree ul > li::before,
    .v-tree ul > li::after {
        content: '';
        position: absolute;
        top: 0; height: 2px;
        background: #fed7aa;
    }
    .v-tree ul > li::before { left: 0; right: 50%; }
    .v-tree ul > li::after  { left: 50%; right: 0; }
    .v-tree ul > li:first-child::before { left: 50%; }
    .v-tree ul > li:last-child::after  { right: 50%; }
    .v-tree ul > li:only-child::before,
    .v-tree ul > li:only-child::after { display: none; }
    /* Vertical line from horizontal connector down to node */
    .v-tree ul > li > .v-node::before {
        content: '';
        position: absolute;
        top: -28px; left: 50%;
        width: 2px; height: 28px;
        background: #fed7aa;
    }

    /* Node card */
    .v-node {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        z-index: 1;
    }
    .v-node:hover { border-color: #fb923c; background: #fff7ed; color: #c2410c; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(249,115,22,0.15); }
    .v-node.is-checked { border-color: #f97316; background: #fff7ed; color: #c2410c; }

    /* Material 3 Checkbox inside node */
    .v-node input[type="checkbox"] {
        -webkit-appearance: none; appearance: none;
        width: 18px; height: 18px;
        border: 2px solid #94a3b8; border-radius: 5px;
        cursor: pointer; position: relative; flex-shrink: 0;
        transition: all 0.2s;
    }
    .v-node input[type="checkbox"]:checked { background: #f97316; border-color: #f97316; }
    .v-node input[type="checkbox"]:checked::after {
        content: ''; position: absolute;
        left: 4px; top: 0; width: 6px; height: 10px;
        border: solid white; border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    /* Root node special styling */
    .v-root {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 12px 28px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        border: none; border-radius: 16px;
        font-size: 14px; font-weight: 700; color: #fff;
        box-shadow: 0 4px 14px rgba(249,115,22,0.35);
        cursor: pointer; user-select: none; position: relative; z-index: 1;
    }
    .v-root input[type="checkbox"] {
        -webkit-appearance: none; appearance: none;
        width: 20px; height: 20px;
        border: 2px solid rgba(255,255,255,0.5); border-radius: 5px;
        cursor: pointer; position: relative; flex-shrink: 0;
        transition: all 0.2s;
    }
    .v-root input[type="checkbox"]:checked { background: rgba(255,255,255,0.3); border-color: #fff; }
    .v-root input[type="checkbox"]:checked::after {
        content: ''; position: absolute;
        left: 4px; top: 1px; width: 7px; height: 11px;
        border: solid white; border-width: 0 2.5px 2.5px 0;
        transform: rotate(45deg);
    }

    /* Section scroll */
    .tree-scroll { padding: 24px 16px 32px; overflow: hidden; }
</style>

<div class="tw-font-sans tw-max-w-screen-2xl tw-mx-auto tw-pb-16">

    {{-- Header --}}
    <header class="tw-mb-8">
        <nav class="tw-flex tw-text-xs tw-font-medium tw-text-slate-500 tw-mb-3 tw-uppercase tw-tracking-wider">
            <ol class="tw-inline-flex tw-items-center">
                <li><a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-500 tw-transition-colors tw-no-underline">Dashboard</a></li>
                <li><i class="fa fa-chevron-right tw-mx-2 tw-text-[10px] tw-text-slate-300"></i></li>
                <li><a href="{{ route('admin.users.index') }}" class="hover:tw-text-orange-500 tw-transition-colors tw-no-underline">Users</a></li>
                <li><i class="fa fa-chevron-right tw-mx-2 tw-text-[10px] tw-text-slate-300"></i></li>
                <li class="tw-text-orange-500 tw-font-bold">Permissions</li>
            </ol>
        </nav>
        <div class="tw-flex tw-flex-col sm:tw-flex-row tw-justify-between tw-items-start sm:tw-items-end tw-gap-4">
            <div>
                <h1 class="tw-text-3xl tw-font-normal tw-tracking-tight tw-text-slate-900 tw-m-0">Access Control</h1>
                <div class="tw-flex tw-items-center tw-gap-3 tw-mt-3">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-orange-500 tw-text-white tw-flex tw-items-center tw-justify-center tw-text-sm tw-font-bold">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="tw-text-sm tw-font-semibold tw-text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</span>
                        <span class="tw-text-xs tw-text-slate-400 tw-block">{{ $user->email }}</span>
                    </div>
                </div>
            </div>
            <button type="submit" form="permissions-form" class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-full tw-bg-orange-500 tw-px-8 tw-py-2.5 tw-text-sm tw-font-medium tw-text-white tw-shadow-sm hover:tw-bg-orange-600 tw-transition-colors tw-border-none tw-cursor-pointer">
                <i class="fa fa-check"></i> Save Permissions
            </button>
        </div>
    </header>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-rounded-2xl tw-p-4 tw-flex tw-items-center tw-gap-3 tw-mb-8">
        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-emerald-500 tw-text-white tw-flex tw-items-center tw-justify-center tw-text-xs"><i class="fa fa-check"></i></div>
        <p class="tw-text-emerald-800 tw-font-medium tw-text-sm tw-m-0">{{ session('success') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.users.permissions.update', $user->id) }}" id="permissions-form">
        @csrf
        @php $p = $userPerms; @endphp

        <div class="tw-space-y-6">

            {{-- ═══ CMS Section ═══ --}}
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3 tw-bg-purple-50">
                    <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-purple-500 tw-text-white tw-flex tw-items-center tw-justify-center"><i class="fa fa-desktop"></i></div>
                    <span class="tw-text-base tw-font-bold tw-text-purple-900">Content Management</span>
                </div>
                <div class="tree-scroll">
                    <div class="v-tree">
                        <label class="v-root"><input type="checkbox" name="admin" value="1" {{ isset($p['admin']) ? 'checked' : '' }}> <i class="fa fa-shield"></i> Administrations</label>
                        <ul>
                            <li>
                                <label class="v-node"><input type="checkbox" name="cms_" value="1" {{ isset($p['cms_']) ? 'checked' : '' }}> CMS</label>
                                <ul>
                                    <li>
                                        <label class="v-node"><input type="checkbox" name="sliders" value="1" {{ isset($p['sliders']) ? 'checked' : '' }}> Sliders</label>
                                        <ul><li><label class="v-node"><input type="checkbox" name="slider_images" value="1" {{ isset($p['slider_images']) ? 'checked' : '' }}> Images</label></li></ul>
                                    </li>
                                    <li>
                                        <label class="v-node"><input type="checkbox" name="layouts" value="1" {{ isset($p['layouts']) ? 'checked' : '' }}> Layouts</label>
                                        <ul><li><label class="v-node"><input type="checkbox" name="blocks" value="1" {{ isset($p['blocks']) ? 'checked' : '' }}> Blocks</label></li></ul>
                                    </li>
                                    <li><label class="v-node"><input type="checkbox" name="layouts_settings" value="1" {{ isset($p['layouts_settings']) ? 'checked' : '' }}> Layout Settings</label></li>
                                    <li>
                                        <label class="v-node"><input type="checkbox" name="customblocks" value="1" {{ isset($p['customblocks']) ? 'checked' : '' }}> Custom Blocks</label>
                                        <ul><li><label class="v-node"><input type="checkbox" name="edit_block" value="1" {{ isset($p['edit_block']) ? 'checked' : '' }}> Edit</label></li></ul>
                                    </li>
                                    <li>
                                        <label class="v-node"><input type="checkbox" name="nav" value="1" {{ isset($p['nav']) ? 'checked' : '' }}> Top Nav</label>
                                        <ul><li><label class="v-node"><input type="checkbox" name="edit_nav_link" value="1" {{ isset($p['edit_nav_link']) ? 'checked' : '' }}> Edit</label></li></ul>
                                    </li>
                                    <li><label class="v-node"><input type="checkbox" name="footer_contents" value="1" {{ isset($p['footer_contents']) ? 'checked' : '' }}> Footer</label></li>
                                    <li>
                                        <label class="v-node"><input type="checkbox" name="manage_pages" value="1" {{ isset($p['manage_pages']) ? 'checked' : '' }}> Pages</label>
                                        <ul>
                                            <li><label class="v-node"><input type="checkbox" name="add_page" value="1" {{ isset($p['add_page']) ? 'checked' : '' }}> Add</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="edit_page" value="1" {{ isset($p['edit_page']) ? 'checked' : '' }}> Edit</label></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ═══ Settings Section ═══ --}}
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3 tw-bg-sky-50">
                    <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-sky-500 tw-text-white tw-flex tw-items-center tw-justify-center"><i class="fa fa-cog"></i></div>
                    <span class="tw-text-base tw-font-bold tw-text-sky-900">Settings & Configuration</span>
                </div>
                <div class="tree-scroll">
                    <div class="v-tree">
                        <label class="v-root"><input type="checkbox" name="settings_" value="1" {{ isset($p['settings_']) ? 'checked' : '' }}> <i class="fa fa-cog"></i> Settings</label>
                        <ul>
                            <li>
                                <label class="v-node"><input type="checkbox" name="settings_global" value="1" {{ isset($p['settings_global']) ? 'checked' : '' }}> General</label>
                                <ul>
                                    <li><label class="v-node"><input type="checkbox" name="settings" value="1" {{ isset($p['settings']) ? 'checked' : '' }}> Global</label></li>
                                    <li>
                                        <label class="v-node"><input type="checkbox" name="seo_global" value="1" {{ isset($p['seo_global']) ? 'checked' : '' }}> SEO</label>
                                        <ul>
                                            <li><label class="v-node"><input type="checkbox" name="settings_seo" value="1" {{ isset($p['settings_seo']) ? 'checked' : '' }}> General</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="settings_sitemap" value="1" {{ isset($p['settings_sitemap']) ? 'checked' : '' }}> Sitemap</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="settings_on_page_seo" value="1" {{ isset($p['settings_on_page_seo']) ? 'checked' : '' }}> On-Page</label></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <label class="v-node"><input type="checkbox" name="countries" value="1" {{ isset($p['countries']) ? 'checked' : '' }}> Countries</label>
                                        <ul><li><label class="v-node"><input type="checkbox" name="cities" value="1" {{ isset($p['cities']) ? 'checked' : '' }}> Cities</label></li></ul>
                                    </li>
                                    <li><label class="v-node"><input type="checkbox" name="currency" value="1" {{ isset($p['currency']) ? 'checked' : '' }}> Currency</label></li>
                                    <li><label class="v-node"><input type="checkbox" name="company_profile" value="1" {{ isset($p['company_profile']) ? 'checked' : '' }}> Company</label></li>
                                </ul>
                            </li>
                            <li><label class="v-node"><input type="checkbox" name="payment_gate_ways" value="1" {{ isset($p['payment_gate_ways']) ? 'checked' : '' }}> Payments</label></li>
                            <li>
                                <label class="v-node"><input type="checkbox" name="languages" value="1" {{ isset($p['languages']) ? 'checked' : '' }}> Languages</label>
                                <ul><li><label class="v-node"><input type="checkbox" name="translatefile" value="1" {{ isset($p['translatefile']) ? 'checked' : '' }}> Translate</label></li></ul>
                            </li>
                            <li><label class="v-node"><input type="checkbox" name="managemods" value="1" {{ isset($p['managemods']) ? 'checked' : '' }}> Modules</label></li>
                            <li><label class="v-node"><input type="checkbox" name="emails_templates" value="1" {{ isset($p['emails_templates']) ? 'checked' : '' }}> Emails</label></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ═══ Tours Section ═══ --}}
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3 tw-bg-emerald-50">
                    <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-emerald-500 tw-text-white tw-flex tw-items-center tw-justify-center"><i class="fa fa-plane"></i></div>
                    <span class="tw-text-base tw-font-bold tw-text-emerald-900">Tours & Bookings</span>
                </div>
                <div class="tree-scroll">
                    <div class="v-tree">
                        <label class="v-root"><input type="checkbox" name="tours" value="1" {{ isset($p['tours']) ? 'checked' : '' }}> <i class="fa fa-plane"></i> Tours</label>
                        <ul>
                            <li>
                                <label class="v-node"><input type="checkbox" name="tours_index" value="1" {{ isset($p['tours_index']) ? 'checked' : '' }}> Booking</label>
                                <ul>
                                    <li><label class="v-node"><input type="checkbox" name="tours_add_booking" value="1" {{ isset($p['tours_add_booking']) ? 'checked' : '' }}> Add</label></li>
                                    <li><label class="v-node"><input type="checkbox" name="tours_edit_booking" value="1" {{ isset($p['tours_edit_booking']) ? 'checked' : '' }}> Edit</label></li>
                                </ul>
                            </li>
                            <li>
                                <label class="v-node"><input type="checkbox" name="tours_quotations" value="1" {{ isset($p['tours_quotations']) ? 'checked' : '' }}> Quotation</label>
                                <ul>
                                    <li>
                                        <label class="v-node"><input type="checkbox" name="tours_quotation" value="1" {{ isset($p['tours_quotation']) ? 'checked' : '' }}> Manage</label>
                                        <ul>
                                            <li><label class="v-node"><input type="checkbox" name="tours_add_quotation" value="1" {{ isset($p['tours_add_quotation']) ? 'checked' : '' }}> Add</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="tours_edit_quotation" value="1" {{ isset($p['tours_edit_quotation']) ? 'checked' : '' }}> Edit</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="tours_send_quotation" value="1" {{ isset($p['tours_send_quotation']) ? 'checked' : '' }}> Send</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="tours_delete_quotation" value="1" {{ isset($p['tours_delete_quotation']) ? 'checked' : '' }}> Delete</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="tours_copy_quotation" value="1" {{ isset($p['tours_copy_quotation']) ? 'checked' : '' }}> Copy</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="tours_quotation_fast_access" value="1" {{ isset($p['tours_quotation_fast_access']) ? 'checked' : '' }}> Fast Access</label></li>
                                        </ul>
                                    </li>
                                    <li><label class="v-node"><input type="checkbox" name="tours_quotation_pricing" value="1" {{ isset($p['tours_quotation_pricing']) ? 'checked' : '' }}> Pricing</label></li>
                                    <li>
                                        <label class="v-node"><input type="checkbox" name="tours_canned_days" value="1" {{ isset($p['tours_canned_days']) ? 'checked' : '' }}> Canned Days</label>
                                        <ul>
                                            <li><label class="v-node"><input type="checkbox" name="tours_add_canned_day" value="1" {{ isset($p['tours_add_canned_day']) ? 'checked' : '' }}> Add</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="tours_edit_canned_day" value="1" {{ isset($p['tours_edit_canned_day']) ? 'checked' : '' }}> Edit</label></li>
                                            <li><label class="v-node"><input type="checkbox" name="tours_delete_canned_day" value="1" {{ isset($p['tours_delete_canned_day']) ? 'checked' : '' }}> Delete</label></li>
                                        </ul>
                                    </li>
                                    <li><label class="v-node"><input type="checkbox" name="tours_quotation_email_template" value="1" {{ isset($p['tours_quotation_email_template']) ? 'checked' : '' }}> Emails</label></li>
                                </ul>
                            </li>
                            <li>
                                <label class="v-node"><input type="checkbox" name="tours_manage_tours" value="1" {{ isset($p['tours_manage_tours']) ? 'checked' : '' }}> Tours</label>
                                <ul>
                                    <li><label class="v-node"><input type="checkbox" name="tours_add_tour" value="1" {{ isset($p['tours_add_tour']) ? 'checked' : '' }}> Add</label></li>
                                    <li><label class="v-node"><input type="checkbox" name="tours_edit_tour" value="1" {{ isset($p['tours_edit_tour']) ? 'checked' : '' }}> Edit</label></li>
                                    <li><label class="v-node"><input type="checkbox" name="tours_delete_tour" value="1" {{ isset($p['tours_delete_tour']) ? 'checked' : '' }}> Delete</label></li>
                                    <li><label class="v-node"><input type="checkbox" name="tours_tour_guaranteed_departure" value="1" {{ isset($p['tours_tour_guaranteed_departure']) ? 'checked' : '' }}> Guaranteed</label></li>
                                </ul>
                            </li>
                            <li><label class="v-node"><input type="checkbox" name="tours_tour_types" value="1" {{ isset($p['tours_tour_types']) ? 'checked' : '' }}> Types</label></li>
                            <li><label class="v-node"><input type="checkbox" name="tours_tour_categories" value="1" {{ isset($p['tours_tour_categories']) ? 'checked' : '' }}> Categories</label></li>
                            <li><label class="v-node"><input type="checkbox" name="tours_tour_inclusions" value="1" {{ isset($p['tours_tour_inclusions']) ? 'checked' : '' }}> Inclusions</label></li>
                            <li><label class="v-node"><input type="checkbox" name="tours_tec_details" value="1" {{ isset($p['tours_tec_details']) ? 'checked' : '' }}> Technical</label></li>
                            <li><label class="v-node"><input type="checkbox" name="tours_seasons" value="1" {{ isset($p['tours_seasons']) ? 'checked' : '' }}> Seasons</label></li>
                            <li><label class="v-node"><input type="checkbox" name="tours_settings" value="1" {{ isset($p['tours_settings']) ? 'checked' : '' }}> Settings</label></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ═══ Bottom Row: Expenses, Invoices, Services, Users ═══ --}}
            <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-2 tw-gap-6">
                {{-- Expenses --}}
                <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                    <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3 tw-bg-amber-50">
                        <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-amber-500 tw-text-white tw-flex tw-items-center tw-justify-center"><i class="fa fa-money"></i></div>
                        <span class="tw-text-base tw-font-bold tw-text-amber-900">Expenses</span>
                    </div>
                    <div class="tree-scroll">
                        <div class="v-tree">
                            <label class="v-root" style="background:linear-gradient(135deg,#f59e0b,#d97706)"><input type="checkbox" name="expenses" value="1" {{ isset($p['expenses']) ? 'checked' : '' }}> <i class="fa fa-money"></i> Expenses</label>
                            <ul>
                                <li><label class="v-node"><input type="checkbox" name="restricted_services" value="1" {{ isset($p['restricted_services']) ? 'checked' : '' }}> Restricted</label></li>
                                <li><label class="v-node"><input type="checkbox" name="expenses_cost" value="1" {{ isset($p['expenses_cost']) ? 'checked' : '' }}> Cost</label></li>
                                <li><label class="v-node"><input type="checkbox" name="expenses_history" value="1" {{ isset($p['expenses_history']) ? 'checked' : '' }}> History</label></li>
                                <li><label class="v-node"><input type="checkbox" name="expenses_venders" value="1" {{ isset($p['expenses_venders']) ? 'checked' : '' }}> Vendors</label></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Invoices --}}
                <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                    <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3 tw-bg-rose-50">
                        <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-rose-500 tw-text-white tw-flex tw-items-center tw-justify-center"><i class="fa fa-file-text-o"></i></div>
                        <span class="tw-text-base tw-font-bold tw-text-rose-900">Invoices</span>
                    </div>
                    <div class="tree-scroll">
                        <div class="v-tree">
                            <label class="v-root" style="background:linear-gradient(135deg,#f43f5e,#be123c)"><input type="checkbox" name="invoices" value="1" {{ isset($p['invoices']) ? 'checked' : '' }}> <i class="fa fa-file-text-o"></i> Invoices</label>
                            <ul>
                                <li><label class="v-node"><input type="checkbox" name="add_invoice" value="1" {{ isset($p['add_invoice']) ? 'checked' : '' }}> Add</label></li>
                                <li><label class="v-node"><input type="checkbox" name="edit_invoice" value="1" {{ isset($p['edit_invoice']) ? 'checked' : '' }}> Edit</label></li>
                                <li><label class="v-node"><input type="checkbox" name="delete_invoice" value="1" {{ isset($p['delete_invoice']) ? 'checked' : '' }}> Delete</label></li>
                                <li>
                                    <label class="v-node"><input type="checkbox" name="invoice_transactions" value="1" {{ isset($p['invoice_transactions']) ? 'checked' : '' }}> Transactions</label>
                                    <ul>
                                        <li><label class="v-node"><input type="checkbox" name="add_invoice_transaction" value="1" {{ isset($p['add_invoice_transaction']) ? 'checked' : '' }}> Add</label></li>
                                        <li><label class="v-node"><input type="checkbox" name="edit_invoice_transaction" value="1" {{ isset($p['edit_invoice_transaction']) ? 'checked' : '' }}> Edit</label></li>
                                        <li><label class="v-node"><input type="checkbox" name="delete_invoice_transaction" value="1" {{ isset($p['delete_invoice_transaction']) ? 'checked' : '' }}> Delete</label></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Services --}}
                <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                    <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3 tw-bg-teal-50">
                        <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-teal-500 tw-text-white tw-flex tw-items-center tw-justify-center"><i class="fa fa-wrench"></i></div>
                        <span class="tw-text-base tw-font-bold tw-text-teal-900">Services</span>
                    </div>
                    <div class="tree-scroll">
                        <div class="v-tree">
                            <label class="v-root" style="background:linear-gradient(135deg,#14b8a6,#0d9488)"><input type="checkbox" name="services_" value="1" {{ isset($p['services_']) ? 'checked' : '' }}> <i class="fa fa-wrench"></i> Services</label>
                            <ul>
                                <li><label class="v-node"><input type="checkbox" name="services/manage_services" value="1" {{ isset($p['services/manage_services']) ? 'checked' : '' }}> Manage</label></li>
                                <li><label class="v-node"><input type="checkbox" name="services/services_venders" value="1" {{ isset($p['services/services_venders']) ? 'checked' : '' }}> Vendors</label></li>
                                <li><label class="v-node"><input type="checkbox" name="services/services_settings" value="1" {{ isset($p['services/services_settings']) ? 'checked' : '' }}> Settings</label></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Users --}}
                <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                    <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3 tw-bg-orange-50">
                        <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-orange-500 tw-text-white tw-flex tw-items-center tw-justify-center"><i class="fa fa-users"></i></div>
                        <span class="tw-text-base tw-font-bold tw-text-orange-900">Users</span>
                    </div>
                    <div class="tree-scroll">
                        <div class="v-tree">
                            <label class="v-root" style="background:linear-gradient(135deg,#f97316,#ea580c)"><input type="checkbox" name="users_" value="1" {{ isset($p['users_']) ? 'checked' : '' }}> <i class="fa fa-users"></i> Users</label>
                            <ul>
                                <li><label class="v-node"><input type="checkbox" name="manage_users" value="1" {{ isset($p['manage_users']) ? 'checked' : '' }}> Manage</label></li>
                                <li><label class="v-node"><input type="checkbox" name="add_user" value="1" {{ isset($p['add_user']) ? 'checked' : '' }}> Add</label></li>
                                <li><label class="v-node"><input type="checkbox" name="edit_user" value="1" {{ isset($p['edit_user']) ? 'checked' : '' }}> Edit</label></li>
                                <li><label class="v-node"><input type="checkbox" name="user_groups" value="1" {{ isset($p['user_groups']) ? 'checked' : '' }}> Groups</label></li>
                                <li><label class="v-node"><input type="checkbox" name="user_permission" value="1" {{ isset($p['user_permission']) ? 'checked' : '' }}> Permission</label></li>
                                <li><label class="v-node"><input type="checkbox" name="edit_group_fields" value="1" {{ isset($p['edit_group_fields']) ? 'checked' : '' }}> Fields</label></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- System Tools --}}
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3 tw-bg-slate-50">
                    <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-slate-700 tw-text-white tw-flex tw-items-center tw-justify-center"><i class="fa fa-hdd-o"></i></div>
                    <span class="tw-text-base tw-font-bold tw-text-slate-900">System Tools</span>
                </div>
                <div class="tree-scroll">
                    <div class="v-tree">
                        <label class="v-root" style="background:linear-gradient(135deg,#475569,#334155)"><input type="checkbox" name="file_manager" value="1" {{ isset($p['file_manager']) ? 'checked' : '' }}> <i class="fa fa-hdd-o"></i> System</label>
                        <ul>
                            <li><label class="v-node"><input type="checkbox" name="file_manager" value="1" {{ isset($p['file_manager']) ? 'checked' : '' }}> File Manager</label></li>
                            <li><label class="v-node"><input type="checkbox" name="tools/database" value="1" {{ isset($p['tools/database']) ? 'checked' : '' }}> Database</label></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <div class="tw-mt-8 tw-flex tw-justify-end">
            <button type="submit" class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-full tw-bg-orange-500 tw-px-10 tw-py-3.5 tw-text-sm tw-font-medium tw-text-white tw-shadow-lg tw-shadow-orange-200 hover:tw-bg-orange-600 tw-transition-colors tw-border-none tw-cursor-pointer">
                <i class="fa fa-save"></i> Save Permissions
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    function updateNodes() {
        document.querySelectorAll('.v-node input[type="checkbox"]').forEach(function(cb) {
            cb.closest('.v-node').classList.toggle('is-checked', cb.checked);
        });
    }
    updateNodes();

    $('input[type="checkbox"]').change(function() {
        var $this = $(this), checked = $this.prop("checked"),
            $li = $this.closest('li').length ? $this.closest('li') : $this.closest('.v-tree');
        $li.find('input[type="checkbox"]').prop('checked', checked);
        checkParents($this.closest('li'));
        updateNodes();
    });

    function checkParents($li) {
        var $parentUl = $li.parent('ul');
        var $parentLi = $parentUl.parent('li');
        if (!$parentLi.length) return;
        var allChecked = true;
        $parentUl.children('li').each(function() {
            if (!$(this).find('> label input[type="checkbox"]').prop('checked')) allChecked = false;
        });
        $parentLi.find('> label input[type="checkbox"]').prop('checked', allChecked);
        checkParents($parentLi);
    }
});
</script>
@endpush
