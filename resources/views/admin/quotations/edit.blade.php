@extends('admin.layouts.app')

@section('title', 'Admin | Edit Quotation #' . $quotation->id)



@section('content')
<style>
    .ev-quote-stack {
        width: 100%;
    }
    .ev-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .14);
        padding: 24px;
    }
    .ev-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 28px;
    }
    .ev-panel-dots {
        display: flex;
        gap: 9px;
        margin-bottom: 6px;
    }
    .ev-panel-dots span {
        width: 8px;
        height: 8px;
        background: #ea580c;
        border-radius: 999px;
        display: block;
    }
    .ev-panel-title {
        color: #1f2933;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.15;
        margin: 0;
    }
    .ev-panel-toggle {
        border: 0;
        background: transparent;
        color: #1f2933;
        font-size: 16px;
        padding: 8px;
        line-height: 1;
        cursor: pointer;
    }
    .ev-request-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 58px;
        width: 100%;
    }
    .ev-request-item b {
        color: #1f2933;
        display: block;
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 4px;
    }
    .ev-request-item span {
        color: #111827;
        display: block;
        font-size: 16px;
        line-height: 1.45;
    }
    .ev-request-full {
        grid-column: 1 / -1;
    }
    .ev-request-info-row {
        align-items: center;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        margin-top: 10px;
    }
    .ev-info-icon {
        align-items: center;
        background: #f97316;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        font-size: 12px;
        height: 18px;
        justify-content: center;
        width: 18px;
    }
    .ev-important-box {
        border: 1px solid #d8dde3;
        border-right: 3px solid #ea580c;
        border-radius: 4px;
        box-shadow: 0 1px 5px rgba(15, 23, 42, .15);
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 18px;
        padding: 14px;
        max-width: 560px;
    }
    .ev-important-box b {
        display: block;
        font-size: 15px;
        margin-bottom: 4px;
    }
    .ev-important-box p {
        color: #243040;
        font-size: 14px;
        line-height: 1.28;
        margin: 0;
    }
    .ev-important-box a {
        align-self: center;
        color: #ea580c;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.1;
        text-decoration: none;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .ev-form-grid {
        display: grid;
        gap: 16px;
    }
    .ev-field {
        position: relative;
    }
    .ev-field label {
        background: #fff;
        color: #8b98a9;
        font-size: 12px;
        left: 12px;
        line-height: 1;
        padding: 0 4px;
        position: absolute;
        top: -6px;
        z-index: 1;
    }
    .ev-input,
    .ev-select {
        background: #fff;
        border: 1px solid #cfd6df;
        border-radius: 3px;
        color: #111827;
        font-size: 16px;
        height: 56px;
        padding: 0 13px;
        width: 100%;
    }
    .ev-select {
        appearance: auto;
    }
    .ev-field-icon {
        color: #1f2933;
        font-size: 18px;
        pointer-events: none;
        position: absolute;
        right: 13px;
        top: 19px;
    }
    .ev-cover-title {
        color: #1f2933;
        font-size: 14px;
        font-weight: 800;
        margin: 2px 0 16px;
    }
    .ev-cover-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(220px, 273px);
        gap: 24px;
    }
    .ev-cover-photo {
        background: #f1f5f9;
        height: 152px;
        overflow: hidden;
        position: relative;
    }
    .ev-cover-photo img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }
    .ev-cover-remove {
        align-items: center;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 999px;
        color: #1f2933;
        display: inline-flex;
        font-size: 18px;
        font-weight: 700;
        height: 26px;
        justify-content: center;
        position: absolute;
        right: -10px;
        top: 8px;
        width: 26px;
    }
    .ev-cover-upload {
        align-items: center;
        background: #fff;
        border: 2px dashed #aeb8c4;
        color: #9aa4af;
        display: flex;
        font-size: 32px;
        height: 152px;
        justify-content: center;
        width: 100%;
    }
    .ev-hidden {
        display: none;
    }
    .qtp-editor-bar {
        align-items: center;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        display: grid;
        grid-template-columns: minmax(120px, 1fr) auto minmax(420px, 1fr);
        min-height: 56px;
        padding: 0 14px;
        width: 100%;
    }
    .qtp-editor-tabs {
        align-items: center;
        display: flex;
        height: 56px;
    }
    .qtp-editor-tabs button {
        align-items: center;
        background: transparent;
        border: 0;
        border-bottom: 3px solid transparent;
        color: #4b5563;
        cursor: pointer;
        display: flex;
        font-size: 15px;
        font-weight: 800;
        height: 56px;
        justify-content: center;
        padding: 0 48px;
    }
    .qtp-editor-tabs button.active {
        border-bottom-color: #ea580c;
        color: #ea580c;
    }
    .qtp-editor-actions {
        align-items: center;
        display: flex;
        gap: 8px;
        grid-column: 3;
        justify-content: flex-end;
    }
    .qtp-action-btn {
        align-items: center;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        color: #111827;
        display: inline-flex;
        font-size: 13px;
        font-weight: 800;
        gap: 8px;
        min-height: 34px;
        padding: 7px 12px;
        text-decoration: none;
    }
    .qtp-action-btn.disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }
    .qtp-alert-icon {
        align-items: center;
        border: 2px solid #f04b2f;
        border-radius: 999px;
        color: #f04b2f;
        display: inline-flex;
        font-size: 13px;
        font-weight: 900;
        height: 20px;
        justify-content: center;
        width: 20px;
    }
    .qtp-tab-panel {
        width: 100%;
    }
    .qtp-day-shell {
        background: #f7f7f5;
        border: 1px solid #e5e7eb;
        display: grid;
        grid-template-columns: 322px minmax(0, 1fr);
        min-height: 650px;
        width: 100%;
    }
    .qtp-day-sidebar {
        background: #fff;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        max-height: 760px;
        overflow-y: auto;
        padding: 10px 0 76px;
        position: relative;
    }
    .qtp-day-item {
        align-items: flex-start;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        gap: 10px;
        margin: 0 18px 8px;
        padding: 9px;
        text-align: left;
    }
    .qtp-day-item.active {
        background: #eee9e5;
    }
    .qtp-day-thumb {
        background: #e5e7eb;
        border-radius: 3px;
        flex: 0 0 40px;
        height: 40px;
        overflow: hidden;
    }
    .qtp-day-thumb img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }
    .qtp-day-thumb-empty {
        align-items: center;
        color: #9ca3af;
        display: flex;
        justify-content: center;
    }
    .qtp-day-meta {
        min-width: 0;
    }
    .qtp-day-number {
        color: #111827;
        font-size: 16px;
        font-weight: 500;
        line-height: 1.1;
        margin-bottom: 3px;
    }
    .qtp-day-title {
        color: #111827;
        font-size: 13px;
        line-height: 1.3;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .qtp-day-loc {
        align-items: center;
        color: #8a95a3;
        display: flex;
        font-size: 11px;
        gap: 5px;
        margin-top: 8px;
    }
    .qtp-add-day {
        align-items: center;
        background: #ea580c;
        border: 0;
        border-radius: 3px;
        bottom: 10px;
        color: #fff;
        cursor: pointer;
        display: flex;
        font-size: 16px;
        font-weight: 800;
        gap: 10px;
        left: 0;
        min-height: 40px;
        padding: 10px 20px;
        position: sticky;
        text-align: left;
        width: fit-content;
    }
    .qtp-day-content {
        background: #f7f7f5;
        max-height: 760px;
        overflow-y: auto;
        padding: 26px 38px;
    }
    .qtp-day-edit {
        background: transparent !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        margin: 0 auto;
        max-width: 860px;
    }
    .qtp-day-edit > div:first-child {
        background: transparent !important;
        border-bottom: 1px solid #d8dde3;
        box-shadow: none !important;
        color: #1f2933 !important;
        gap: 12px;
        justify-content: flex-start !important;
        margin-bottom: 24px;
        padding: 0 0 18px !important;
    }
    .qtp-day-edit > div:first-child > div:first-child {
        gap: 10px !important;
    }
    .qtp-day-edit > div:first-child .tw-w-10 {
        background: #b4bec8 !important;
        border-radius: 8px !important;
        color: #fff !important;
        font-size: 0 !important;
        height: 32px !important;
        width: 42px !important;
    }
    .qtp-day-edit > div:first-child .tw-w-10::before {
        content: "D" attr(data-day-label);
        font-size: 15px;
        font-weight: 800;
    }
    .qtp-day-edit > div:first-child span {
        display: none !important;
    }
    .qtp-day-edit > div:first-child h4 {
        background: #e5e7eb;
        border-radius: 4px;
        color: #1f2933 !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        padding: 7px 12px;
    }
    .qtp-day-edit > div:first-child > .tw-flex:last-child {
        display: none !important;
    }
    .qtp-day-edit > div:nth-child(2) {
        background: #fff;
        border: 1px solid #e5e7eb;
        display: flex !important;
        flex-direction: column;
        gap: 28px;
        padding: 26px 18px !important;
    }
    .qtp-day-edit > div:nth-child(2) > div {
        width: 100%;
    }
    .qtp-day-edit .tox-tinymce,
    .qtp-day-edit .mce-tinymce {
        border-radius: 0 !important;
        box-shadow: none !important;
    }
    .qtp-day-edit .tox-toolbar,
    .qtp-day-edit .tox-statusbar {
        display: none !important;
    }
    .qtp-day-edit textarea.tinymce {
        min-height: 210px !important;
    }
    .qtp-day-edit #images_1,
    .qtp-day-edit [id^="images_"] {
        flex-wrap: nowrap !important;
        gap: 8px !important;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    .qtp-day-edit [id^="images_"] > div {
        border-radius: 0 !important;
        flex: 0 0 200px;
        height: 150px !important;
        width: 200px !important;
    }
    .qtp-price-panel {
        background: #f7f7f5;
        border: 1px solid #e5e7eb;
        padding: 32px 20px;
    }
    .qtp-price-card {
        margin: 0 auto 20px;
        max-width: 760px;
    }
    .qtp-price-total-row {
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        margin: 20px 0;
        padding: 18px 0;
    }
    .qtp-price-total-label {
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }
    .qtp-price-total-value {
        color: #111827;
        font-size: 30px;
        font-weight: 900;
    }
    .qtp-price-row {
        align-items: end;
        display: grid;
        gap: 12px;
        grid-template-columns: 28px 1fr 1.5fr 1fr;
        margin-bottom: 16px;
    }
    .qtp-check {
        color: #ea580c;
        font-size: 19px;
        padding-bottom: 17px;
    }
    .qtp-price-actions {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .qtp-green-link {
        background: transparent;
        border: 0;
        color: #ea580c;
        cursor: pointer;
        font-size: 13px;
        font-weight: 800;
    }
    .qtp-toast {
        background: #ea580c;
        border-radius: 4px;
        bottom: 22px;
        color: #fff;
        font-size: 13px;
        font-weight: 800;
        padding: 10px 16px;
        position: fixed;
        right: 22px;
        z-index: 99999;
    }
    .qtp-canned-overlay {
        align-items: center;
        background: rgba(0, 0, 0, .55);
        bottom: 0;
        display: none;
        justify-content: center;
        left: 0;
        padding: 24px;
        position: fixed;
        right: 0;
        top: 0;
        z-index: 10020;
    }
    .qtp-canned-modal {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, .28);
        max-height: 88vh;
        max-width: 900px;
        overflow-y: auto;
        padding: 24px;
        width: min(900px, 94vw);
    }
    .qtp-canned-head {
        align-items: flex-start;
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 12px;
    }
    .qtp-canned-head h3 {
        color: #1f2933;
        font-size: 22px;
        font-weight: 900;
        line-height: 1.1;
        margin: 0 0 3px;
    }
    .qtp-canned-day-meta {
        color: #64748b;
        display: flex;
        font-size: 13px;
        gap: 10px;
    }
    .qtp-canned-create {
        align-items: center;
        background: transparent;
        border: 0;
        color: #ea580c;
        cursor: pointer;
        display: flex;
        font-size: 13px;
        font-weight: 900;
        gap: 8px;
        text-transform: uppercase;
    }
    .qtp-canned-create i {
        align-items: center;
        background: #ea580c;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        height: 20px;
        justify-content: center;
        width: 20px;
    }
    .qtp-canned-toolbar {
        display: grid;
        gap: 14px;
        grid-template-columns: minmax(0, 1fr) 150px;
        margin-bottom: 16px;
    }
    .qtp-canned-search {
        align-items: center;
        border: 1px solid #f97316;
        border-radius: 4px;
        display: flex;
        gap: 10px;
        height: 42px;
        padding: 0 12px;
    }
    .qtp-canned-search i {
        color: #7b8794;
        font-size: 17px;
    }
    .qtp-canned-search input {
        border: 0;
        color: #1f2933;
        flex: 1;
        font-size: 15px;
        outline: 0;
    }
    .qtp-canned-lang {
        border: 1px solid #d1d5db;
        border-radius: 4px;
        color: #1f2933;
        font-size: 14px;
        height: 42px;
        padding: 0 10px;
        width: 100%;
    }
    .qtp-canned-list {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .qtp-canned-card {
        background: #9ca3af center/cover no-repeat;
        border: 0;
        color: #fff;
        cursor: pointer;
        min-height: 162px;
        overflow: hidden;
        padding: 0;
        position: relative;
        text-align: left;
        width: 100%;
    }
    .qtp-canned-card::before {
        background: linear-gradient(90deg, rgba(15,23,42,.64), rgba(15,23,42,.26) 72%, rgba(0,0,0,.36));
        content: "";
        inset: 0;
        position: absolute;
    }
    .qtp-canned-card-body {
        bottom: 26px;
        left: 32px;
        position: absolute;
        right: 72px;
        z-index: 1;
    }
    .qtp-canned-locations {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 10px;
    }
    .qtp-canned-locations span {
        align-items: center;
        display: flex;
        font-size: 12px;
        font-weight: 800;
        gap: 8px;
        text-shadow: 0 1px 2px rgba(0,0,0,.4);
    }
    .qtp-canned-title {
        font-size: 24px;
        font-weight: 500;
        line-height: 1.2;
        text-shadow: 0 1px 2px rgba(0,0,0,.55);
    }
    .qtp-canned-more {
        color: #fff;
        font-size: 22px;
        position: absolute;
        right: 18px;
        top: 20px;
        z-index: 1;
    }
    .qtp-day-title-field,
    .qtp-sites-section,
    .qtp-meal-section,
    .qtp-accommodation-section,
    .qtp-services-section {
        width: 100%;
    }
    .qtp-day-description-panel {
        order: 3 !important;
    }
    .qtp-day-side-panel {
        order: 8 !important;
    }
    .qtp-day-photos-panel {
        order: 1 !important;
    }
    .qtp-day-title-field {
        order: 2 !important;
        position: relative;
    }
    .qtp-sites-section {
        order: 4 !important;
    }
    .qtp-meal-section {
        order: 5 !important;
    }
    .qtp-accommodation-section {
        order: 6 !important;
    }
    .qtp-services-section {
        order: 7 !important;
    }
    .qtp-floating-field {
        position: relative;
    }
    .qtp-floating-field label {
        background: #fff;
        color: #8b98a9;
        font-size: 12px;
        left: 12px;
        padding: 0 5px;
        position: absolute;
        top: -7px;
        z-index: 1;
    }
    .qtp-floating-field input,
    .qtp-floating-field textarea {
        border: 1px solid #cfd6df;
        border-radius: 3px;
        font-size: 16px;
        min-height: 56px;
        padding: 14px;
        width: 100%;
    }
    .qtp-char-count {
        color: #a3acb8;
        font-size: 12px;
        margin-top: 3px;
        text-align: right;
    }
    .qtp-section-title {
        color: #1f2933;
        font-size: 14px;
        font-weight: 900;
        margin: 20px 0 12px;
    }
    .qtp-service-row {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .qtp-tag {
        align-items: center;
        background: #e5e7eb;
        border-radius: 3px;
        color: #111827;
        display: inline-flex;
        font-size: 14px;
        font-weight: 800;
        gap: 7px;
        min-height: 26px;
        padding: 4px 10px;
    }
    .qtp-tag button {
        align-items: center;
        background: #bfc5cc;
        border: 0;
        border-radius: 999px;
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        height: 18px;
        justify-content: center;
        width: 18px;
    }
    .qtp-add-inline {
        align-items: center;
        background: transparent;
        border: 0;
        color: #ea580c;
        cursor: pointer;
        display: inline-flex;
        font-size: 17px;
        font-weight: 900;
        gap: 10px;
    }
    .qtp-add-inline i {
        font-size: 22px;
    }
    .qtp-site-input {
        border: 0;
        display: inline-block;
        font-size: 14px;
        min-height: 30px;
        min-width: 140px;
        outline: none;
        padding: 4px 6px;
    }
    .qtp-site-row {
        align-items: center;
        border: 1px solid #d1d5db;
        border-radius: 3px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        min-height: 38px;
        padding: 4px 8px;
        position: relative;
    }
    .qtp-site-dropdown {
        background: #fff;
        border: 1px solid #d1d5db;
        border-top: 0;
        border-radius: 0 0 4px 4px;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        display: none;
        left: 0;
        max-height: 240px;
        overflow-y: auto;
        position: absolute;
        right: 0;
        top: 100%;
        z-index: 100;
    }
    .qtp-site-dropdown-item {
        align-items: center;
        background: #fff;
        border: 0;
        color: #111827;
        cursor: pointer;
        display: flex;
        font-size: 14px;
        gap: 8px;
        padding: 10px 14px;
        text-align: left;
        width: 100%;
    }
    .qtp-site-dropdown-item:hover,
    .qtp-site-dropdown-item.active {
        background: #f3f4f6;
    }
    .qtp-site-dropdown-item .qtp-site-city {
        color: #111827;
        font-weight: 500;
    }
    .qtp-site-dropdown-item .qtp-site-region {
        color: #111827;
    }
    .qtp-site-dropdown-item .qtp-site-country {
        color: #ea580c;
        font-weight: 600;
    }
    .qtp-meal-option {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 14px;
    }
    .qtp-meal-option input[type="radio"] {
        accent-color: #ea580c;
        height: 21px;
        width: 21px;
    }
    .qtp-meal-checks {
        align-items: center;
        display: inline-flex;
        gap: 22px;
    }
    .qtp-meal-checks label {
        align-items: center;
        color: #a9b2bd;
        display: inline-flex;
        gap: 8px;
    }
    .qtp-meal-checks input {
        accent-color: #ea580c;
        height: 18px;
        width: 18px;
    }
    /* ── New accommodation card ── */
    .qtp-accom-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        gap: 0;
        overflow: hidden;
        position: relative;
    }
    .qtp-accom-photo-wrap {
        background: #f1f5f9;
        height: 180px;
        overflow: hidden;
        width: 100%;
    }
    .qtp-accom-main-photo {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }
    .qtp-accom-photo-placeholder {
        align-items: center;
        color: #94a3b8;
        display: flex;
        font-size: 48px;
        height: 100%;
        justify-content: center;
        width: 100%;
    }
    .qtp-accom-body {
        padding: 20px 20px 20px 20px;
    }
    .qtp-accom-fields {
        display: grid;
        gap: 16px;
        grid-template-columns: 1fr 1fr;
    }
    .qtp-accom-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .qtp-accom-field--full {
        grid-column: 1 / -1;
    }
    .qtp-accom-field label {
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .qtp-accom-field input,
    .qtp-accom-field textarea,
    .qtp-accom-field select {
        border: 1px solid #d1d5db;
        border-radius: 4px;
        color: #111827;
        font-size: 14px;
        min-height: 36px;
        padding: 7px 10px;
        width: 100%;
    }
    .qtp-accom-field textarea {
        min-height: 72px;
        resize: vertical;
    }
    .qtp-accom-field select {
        background: #fff;
    }
    /* keep old card-close + alt-list styles */
    .qtp-card-close {
        background: transparent;
        border: 0;
        color: #111827;
        cursor: pointer;
        font-size: 28px;
        position: absolute;
        right: 10px;
        top: 10px;
    }
    .qtp-alt-list {
        display: grid;
        gap: 8px;
        margin-top: 10px;
    }
    .qtp-alt-input {
        border: 1px solid #d1d5db;
        border-radius: 3px;
        font-size: 14px;
        min-height: 34px;
        padding: 6px 10px;
        width: 100%;
    }
    .qtp-service-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 10px;
    }
    .qtp-service-buttons button {
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 3px;
        color: #111827;
        cursor: pointer;
        font-size: 14px;
        font-weight: 800;
        padding: 9px 14px;
    }
    .qtp-service-buttons button:hover {
        border-color: #ea580c;
        color: #ea580c;
    }
    .qtp-service-chip {
        background: #e8f5f1;
        color: #ea580c;
    }
    /* Evaneos-style service cards */
    .qtp-svc-card {
        align-items: flex-start;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        display: flex;
        gap: 14px;
        margin-bottom: 10px;
        padding: 14px;
        position: relative;
    }
    .qtp-svc-card-icon {
        align-items: center;
        border: 1.5px solid #8b1553;
        border-radius: 4px;
        display: flex;
        flex-shrink: 0;
        height: 64px;
        justify-content: center;
        width: 64px;
    }
    .qtp-svc-card-icon i {
        color: #8b1553;
        font-size: 22px;
    }
    .qtp-svc-card-img {
        border-radius: 4px;
        flex-shrink: 0;
        height: 64px;
        object-fit: cover;
        width: 80px;
    }
    .qtp-svc-card-body {
        flex: 1;
        min-width: 0;
        padding-right: 20px;
    }
    .qtp-svc-card-title {
        align-items: center;
        display: flex;
        font-size: 14px;
        font-weight: 600;
        gap: 6px;
        margin-bottom: 6px;
    }
    .qtp-svc-card-loc {
        align-items: center;
        background: #f3f4f6;
        border-radius: 4px;
        color: #374151;
        display: inline-flex;
        font-size: 11px;
        gap: 4px;
        padding: 2px 8px;
    }
    .qtp-svc-card-loc i {
        color: #9ca3af;
        font-size: 10px;
    }
    .qtp-svc-card-close {
        background: transparent;
        border: 0;
        color: #9ca3af;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
        position: absolute;
        right: 10px;
        top: 10px;
    }
    .qtp-svc-card-close:hover {
        color: #ef4444;
    }
    .qtp-svc-card-alt {
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 3px;
        color: #374151;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 600;
        gap: 4px;
        margin-top: 8px;
        padding: 4px 12px;
    }
    .qtp-svc-card-alt:hover {
        border-color: #ea580c;
        color: #ea580c;
    }
    .qtp-service-overlay {
        align-items: center;
        background: rgba(0, 0, 0, .55);
        bottom: 0;
        display: none;
        justify-content: center;
        left: 0;
        padding: 24px;
        position: fixed;
        right: 0;
        top: 0;
        z-index: 10030;
    }
    .qtp-service-modal {
        background: #fff;
        border-radius: 4px;
        box-shadow: 0 24px 60px rgba(0, 0, 0, .30);
        max-height: 88vh;
        max-width: 900px;
        overflow-y: auto;
        padding: 24px;
        width: min(900px, 94vw);
    }
    .qtp-service-modal-head {
        align-items: flex-start;
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 12px;
    }
    .qtp-service-modal-head h3 {
        color: #1f2933;
        font-size: 22px;
        font-weight: 900;
        line-height: 1.1;
        margin: 0 0 4px;
    }
    .qtp-service-type-label {
        color: #9b0067;
        font-size: 15px;
        font-weight: 900;
    }
    .qtp-service-create {
        align-items: center;
        background: transparent;
        border: 0;
        color: #9b0067;
        cursor: pointer;
        display: flex;
        font-size: 13px;
        font-weight: 900;
        gap: 8px;
        text-transform: uppercase;
    }
    .qtp-service-create i {
        align-items: center;
        background: #9b0067;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        height: 20px;
        justify-content: center;
        width: 20px;
    }
    .qtp-service-toolbar {
        display: grid;
        gap: 14px;
        grid-template-columns: minmax(0, 1fr) 150px;
        margin-bottom: 22px;
    }
    .qtp-service-search {
        align-items: center;
        border: 1px solid #f97316;
        border-radius: 4px;
        display: flex;
        gap: 10px;
        height: 42px;
        padding: 0 12px;
    }
    .qtp-service-search i {
        color: #7b8794;
        font-size: 17px;
    }
    .qtp-service-search input {
        border: 0;
        color: #1f2933;
        flex: 1;
        font-size: 15px;
        outline: 0;
    }
    .qtp-service-lang {
        border: 1px solid #d1d5db;
        border-radius: 4px;
        color: #1f2933;
        font-size: 14px;
        height: 42px;
        padding: 0 10px;
        width: 100%;
    }
    .qtp-service-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .qtp-service-result {
        align-items: flex-start;
        background: #fff;
        border: 0;
        color: #111827;
        cursor: pointer;
        display: grid;
        gap: 16px;
        grid-template-columns: 58px minmax(0, 1fr);
        padding: 10px 16px;
        text-align: left;
        width: 100%;
    }
    .qtp-service-result:hover {
        background: #faf7fb;
    }
    .qtp-service-result-icon {
        align-items: center;
        border: 1px solid #9b0067;
        color: #9b0067;
        display: flex;
        font-size: 25px;
        height: 55px;
        justify-content: center;
        width: 58px;
    }
    .qtp-service-result-title {
        color: #9b0067;
        display: block;
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 9px;
    }
    .qtp-service-result-meta {
        color: #1f2933;
        display: block;
        font-size: 13px;
        line-height: 1.45;
        margin-bottom: 6px;
    }
    .qtp-service-result-desc {
        color: #111827;
        font-size: 14px;
        line-height: 1.4;
        margin: 0;
    }
    .qtp-service-empty {
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
        padding: 35px 10px;
        text-align: center;
    }
    @media (max-width: 900px) {
        .ev-request-grid,
        .ev-request-info-row,
        .ev-important-box,
        .ev-cover-grid {
            grid-template-columns: 1fr;
        }
        .qtp-editor-bar {
            align-items: stretch;
            flex-direction: column;
        }
        .qtp-editor-tabs {
            justify-content: space-around;
            width: 100%;
        }
        .qtp-editor-tabs button {
            flex: 1;
            padding: 0 12px;
        }
        .qtp-editor-actions {
            justify-content: center;
            padding: 10px;
            position: static;
            transform: none;
        }
        .qtp-day-shell {
            grid-template-columns: 1fr;
        }
        .qtp-day-sidebar,
        .qtp-day-content {
            max-height: none;
        }
        .qtp-price-row {
            grid-template-columns: 1fr;
        }
        .qtp-canned-toolbar {
            grid-template-columns: 1fr;
        }
        .qtp-check {
            display: none;
        }
        .ev-important-box a {
            white-space: normal;
        }
    }
</style>
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Navigation Tabs (Sub-pages) --}}
    @include('admin.quotations._nav')
    {{-- Header Section --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Edit Quotation <span class="tw-text-orange-600">#{{ $quotation->ref_number ?: $quotation->id }}</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Modify existing quotation details and itinerary</p>
        </div>
        <div class="tw-flex tw-gap-3">
            <a href="/{{ $quotation->lang ?: 'en' }}/tours/quotation/{{ $quotation->id }}/" target="_blank" class="btn blue">
                <i class="fa fa-eye"></i> View Live
            </a>
            <a href="{{ route('admin.quotations.index') }}" class="btn red">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-xl"></i>
        <p class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.quotations.update', $quotation->id) }}" id="quotation_form" class="tw-flex tw-flex-col tw-gap-8">
        @csrf
        @method('PUT')
        @php
            $fullName = trim($quotation->customer_name ?? '');
            $nameParts = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY);
            $travellerFirstName = $nameParts[0] ?? $fullName;
            $travellerSurname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : $fullName;
            $arrivalDate = $quotation->travel_date ? \Carbon\Carbon::parse($quotation->travel_date) : null;
            $departureDate = $arrivalDate ? $arrivalDate->copy()->addDays(max(0, (int) $quotation->days - 1)) : null;
            $requestId = $quotation->id;
            if (!empty($quotation->ref_number) && preg_match('/REQ-([0-9]+)/i', $quotation->ref_number, $matches)) {
                $requestId = $matches[1];
            }
            $destination = !empty($quotation->description) && strtolower(trim($quotation->description)) !== 'trip quote'
                ? $quotation->description
                : 'Jordan';
            $coverImage = '';
            foreach ($quotation->quotationDays->sortBy('day_number') as $coverDay) {
                $dayImages = !empty($coverDay->images) ? (@unserialize($coverDay->images) ?: []) : [];
                if (!empty($dayImages[0])) {
                    $coverImage = $dayImages[0];
                    if (!\Illuminate\Support\Str::startsWith($coverImage, ['/', 'http://', 'https://'])) {
                        $coverImage = '';
                    }
                    break;
                }
            }
            $langValue = strtolower($quotation->lang ?: 'en');
            $langValue = $langValue === 'ge' ? 'de' : $langValue;
            $languages = [
                'en' => 'English',
                'fr' => 'French',
                'it' => 'Italian',
                'es' => 'Spanish',
                'ar' => 'Arabic',
                'de' => 'German',
                'zh' => 'Chinese',
                'pt' => 'Portuguese',
            ];
            $qtpPax = max(1, (int) ($quotation->travelers_number ?: 1));
            $qtpBaseCost = (float) ($totalExpenses ?? 0);
            $qtpProfit = (float) ($profitAmount ?? ($quotation->profit_amount ?? 0));
            $qtpClientTotal = (float) (($clientTotal ?? 0) ?: ($quotation->total ?: ($qtpBaseCost + $qtpProfit)));
            $qtpPerPerson = $qtpClientTotal > 0 ? round($qtpClientTotal / $qtpPax, 2) : 0;
            $qtpCannedDays = $cannedDays->map(function ($cd) {
                $content = $cd->contents->first();
                $images = @unserialize($cd->images);
                $images = is_array($images) ? array_values(array_filter($images)) : [];
                $images = array_values(array_filter(array_map(function ($img) {
                    if (!$img) return '';
                    return \Illuminate\Support\Str::startsWith($img, ['http://', 'https://', '/']) ? $img : '/' . ltrim($img, '/');
                }, $images)));
                $expenses = @unserialize($cd->expenses);
                $included = @unserialize($cd->included);
                $excluded = @unserialize($cd->excluded);
                return [
                    'id' => $cd->id,
                    'title' => $content ? $content->title : 'Untitled day',
                    'description' => $content ? $content->description : '',
                    'image' => $images[0] ?? '/uploads/filemanager/Photos/Petra/Kahzneh.jpg',
                    'images' => $images,
                    'expenses' => is_array($expenses) ? array_values($expenses) : [],
                    'included' => is_array($included) ? array_values($included) : [],
                    'excluded' => is_array($excluded) ? array_values($excluded) : [],
                ];
            })->values();
        @endphp

        <input type="hidden" name="email" value="{{ $quotation->email }}">
        <input type="hidden" name="phone" value="{{ $quotation->phone }}">
        <input type="hidden" name="ref_number" value="{{ $quotation->ref_number }}">
        <input type="hidden" name="days" id="qtp_days_input" value="{{ $quotation->days }}">
        <input type="hidden" name="nights" id="qtp_nights_input" value="{{ $quotation->nights }}">
        <input type="hidden" name="travelers_number" value="{{ $quotation->travelers_number }}">
        <input type="hidden" name="pricing_base" value="{{ $quotation->pricing_base }}">

        <div class="qtp-editor-bar">
            <div class="qtp-editor-tabs">
                <button type="button" class="active" onclick="showQtpTab('quote', this);">My quote</button>
                <button type="button" onclick="showQtpTab('day', this);">Day by day</button>
                <button type="button" onclick="showQtpTab('price', this);">Price</button>
            </div>
            <div class="qtp-editor-actions">
                <a href="{{ route('admin.library') }}" target="_blank" class="qtp-action-btn"><i class="fa fa-th-large"></i> Library</a>
                <a href="/{{ $quotation->lang ?: 'en' }}/tours/quotation/{{ $quotation->id }}/" target="_blank" class="qtp-action-btn"><i class="fa fa-eye"></i> View preview</a>
                <span class="qtp-action-btn disabled"><i class="fa fa-link"></i> Share to the traveller</span>
                <span class="qtp-alert-icon">!</span>
            </div>
        </div>

        <div class="qtp-tab-panel" id="qtpPanelQuote">
        <div class="ev-quote-stack tw-flex tw-flex-col tw-gap-6">
            {{-- Traveller Request --}}
            <section class="ev-panel">
                <div class="ev-panel-head">
                    <div>
                        <div class="ev-panel-dots"><span></span><span></span><span></span></div>
                        <h3 class="ev-panel-title">Traveller request</h3>
                    </div>
                    <button type="button" class="ev-panel-toggle" onclick="toggleEvPanel('traveller_request_body', this);">
                        <i class="fa fa-chevron-up"></i>
                    </button>
                </div>
                <div id="traveller_request_body">
                    <div class="ev-request-grid">
                        <div class="ev-request-item ev-request-full">
                            <b>Request ID</b>
                            <span>{{ $requestId }}</span>
                        </div>
                        <div class="ev-request-item">
                            <b>Traveller first name</b>
                            <span>{{ $travellerFirstName ?: '-' }}</span>
                        </div>
                        <div class="ev-request-item">
                            <b>Traveller surname</b>
                            <span>{{ $travellerSurname ?: '-' }}</span>
                        </div>
                        <div class="ev-request-item ev-request-full">
                            <b>Destination</b>
                            <span>{{ $destination }}</span>
                        </div>
                        <div class="ev-request-item">
                            <b>Arrival date</b>
                            <span>{{ $arrivalDate ? $arrivalDate->format('l, F j, Y') : '-' }}</span>
                        </div>
                        <div class="ev-request-item">
                            <b>Departure date</b>
                            <span>{{ $departureDate ? $departureDate->format('l, F j, Y') : '-' }}</span>
                        </div>
                        <div class="ev-request-item ev-request-full">
                            <b>Number of travellers</b>
                            <div class="ev-request-info-row">
                                <span>{{ $quotation->travelers_number ?: '-' }}</span>
                                <span class="ev-info-icon">i</span>
                            </div>
                        </div>
                        <div class="ev-request-item ev-request-full">
                            <b>Desired accompaniment</b>
                            <span>{{ (int) $quotation->travelers_number > 1 ? 'group' : 'private' }}</span>
                        </div>
                    </div>
                    <div class="ev-important-box tw-mt-5">
                        <div>
                            <b>Important</b>
                            <p>If you need to adjust the number of pax please go to Request Manager. Please refresh this page when you've done the modifications in Request Manager.</p>
                        </div>
                        <a href="{{ route('admin.request-manager') }}">Go to Request<br>Manager</a>
                    </div>
                </div>
            </section>

            {{-- Personalize --}}
            <section class="ev-panel">
                <div class="ev-panel-head">
                    <div>
                        <div class="ev-panel-dots"><span></span><span></span><span></span></div>
                        <h3 class="ev-panel-title">Personalize</h3>
                    </div>
                    <button type="button" class="ev-panel-toggle" onclick="toggleEvPanel('personalize_body', this);">
                        <i class="fa fa-chevron-up"></i>
                    </button>
                </div>
                <div id="personalize_body" class="ev-form-grid">
                    <div class="ev-field">
                        <label>Quote title</label>
                        <input type="text" name="description" value="{{ $quotation->description }}" class="ev-input" required>
                    </div>
                    <div class="ev-field">
                        <label>Traveller surname</label>
                        <input type="text" name="customer_name" value="{{ $quotation->customer_name }}" class="ev-input" maxlength="255" required>
                    </div>
                    <div class="ev-field">
                        <label>Language of quote</label>
                        <select name="lang" class="ev-select">
                            @foreach($languages as $code => $label)
                            <option value="{{ $code }}" {{ $langValue === $code ? 'selected' : '' }}>{{ strtoupper($code) }} {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ev-field">
                        <label>Arrival date</label>
                        <input type="text" name="travel_date" value="{{ $quotation->travel_date }}" class="ev-input datepicker" required>
                        <i class="fa fa-calendar ev-field-icon"></i>
                    </div>
                    <div>
                        <div class="ev-cover-title">Cover photo</div>
                        <div class="ev-cover-grid">
                            <div class="ev-cover-photo">
                                @if($coverImage)
                                <img src="{{ $coverImage }}" alt="Cover photo">
                                <button type="button" class="ev-cover-remove" onclick="this.closest('.ev-cover-photo').classList.add('ev-hidden');">&times;</button>
                                @endif
                            </div>
                            <button type="button" class="ev-cover-upload" onclick="triggerFirstDayImageSelector();">
                                <i class="fa fa-camera"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        </div>

        {{-- Dynamic Day Sections --}}
        <div class="qtp-tab-panel" id="qtpPanelDay" style="display:none;">
            <div class="qtp-day-shell">
                <aside class="qtp-day-sidebar" id="qtp_day_sidebar">
                    @foreach($quotation->quotationDays->sortBy('day_number') as $dayNav)
                    @php
                        $navDay = $dayNav->day_number;
                        $navImages = !empty($dayNav->images) ? (@unserialize($dayNav->images) ?: []) : [];
                        $navThumb = !empty($navImages[0]) ? $navImages[0] : '';
                        if ($navThumb && !\Illuminate\Support\Str::startsWith($navThumb, ['/', 'http://', 'https://'])) {
                            $navThumb = '';
                        }
                        $navRawContents = $dayNav->contents ?? '';
                        $navMeta = [];
                        if (preg_match('/^\s*<!--QTP_META:([A-Za-z0-9+\/=]+)-->/' , $navRawContents, $navMatch)) {
                            $decodedMeta = json_decode(base64_decode($navMatch[1]), true);
                            $navMeta = is_array($decodedMeta) ? $decodedMeta : [];
                            $navRawContents = preg_replace('/^\s*<!--QTP_META:[A-Za-z0-9+\/=]+-->\s*/', '', $navRawContents);
                        }
                        $navText = trim(strip_tags($navRawContents));
                        $navTitle = !empty($navMeta['title']) ? $navMeta['title'] : ($navText ? \Illuminate\Support\Str::limit($navText, 34) : ('Day ' . $navDay));
                        $navDate = $quotation->travel_date ? date('Y-m-d', strtotime($quotation->travel_date . ' + ' . ($navDay - 1) . ' days')) : '';
                    @endphp
                    <button type="button" class="qtp-day-item {{ $loop->first ? 'active' : '' }}" data-qtp-day-nav="{{ $navDay }}" onclick="selectQtpDay({{ $navDay }}, this);">
                        <span class="qtp-day-thumb {{ $navThumb ? '' : 'qtp-day-thumb-empty' }}">
                            @if($navThumb)
                            <img src="{{ $navThumb }}" alt="Day {{ $navDay }}">
                            @else
                            <i class="fa fa-image"></i>
                            @endif
                        </span>
                        <span class="qtp-day-meta">
                            <span class="qtp-day-number">Day {{ $navDay }}</span>
                            <span class="qtp-day-title">{{ $navTitle }}</span>
                            <span class="qtp-day-loc"><i class="fa fa-map-marker"></i> {{ $destination }}</span>
                        </span>
                    </button>
                    @endforeach
                    <button type="button" class="qtp-add-day" onclick="addQtpDay();"><i class="fa fa-plus"></i> Add another day</button>
                </aside>
                <div class="qtp-day-content">
        <div id="day_sections_container" class="tw-flex tw-flex-col tw-gap-8">
            @foreach($quotation->quotationDays->sortBy('day_number') as $day)
            @php
                $c = $day->day_number;
                $currentDate = $quotation->travel_date ? date('Y-m-d', strtotime($quotation->travel_date . ' + ' . ($c - 1) . ' days')) : '';
                $currentDateLabel = $quotation->travel_date ? date('l, F j, Y', strtotime($quotation->travel_date . ' + ' . ($c - 1) . ' days')) : '';
                $expenses = !empty($day->expenses) ? (@unserialize($day->expenses) ?: []) : [];
                $included = !empty($day->included) ? (@unserialize($day->included) ?: []) : [];
                $excluded = !empty($day->excluded) ? (@unserialize($day->excluded) ?: []) : [];
                $images = !empty($day->images) ? (@unserialize($day->images) ?: []) : [];
                $dayRawContents = $day->contents ?? '';
                $dayMeta = [];
                if (preg_match('/^\s*<!--QTP_META:([A-Za-z0-9+\/=]+)-->/' , $dayRawContents, $dayMatch)) {
                    $decodedMeta = json_decode(base64_decode($dayMatch[1]), true);
                    $dayMeta = is_array($decodedMeta) ? $decodedMeta : [];
                    $dayRawContents = preg_replace('/^\s*<!--QTP_META:[A-Za-z0-9+\/=]+-->\s*/', '', $dayRawContents);
                }
                $dayTitleValue = $dayMeta['title'] ?? (trim(strip_tags($dayRawContents)) ? \Illuminate\Support\Str::limit(trim(strip_tags($dayRawContents)), 70, '') : 'Day ' . $c);
                $daySites = !empty($dayMeta['sites']) && is_array($dayMeta['sites']) ? $dayMeta['sites'] : [$destination];
                $dayMealType = $dayMeta['meal_type'] ?? 'none';
                $dayMealOptions = !empty($dayMeta['meal_options']) && is_array($dayMeta['meal_options']) ? $dayMeta['meal_options'] : [];
                $dayAccommodation = !empty($dayMeta['accommodation']) && is_array($dayMeta['accommodation']) ? $dayMeta['accommodation'] : [];
                $dayAccommodationName     = $dayAccommodation['name']        ?? '';
                $dayAccommodationLocation = $dayAccommodation['location']    ?? $destination;
                $dayAccommodationImage    = $dayAccommodation['image']       ?? '';
                $dayAccommodationDesc     = $dayAccommodation['description'] ?? '';
                $dayAccommodationType     = $dayAccommodation['type']        ?? '';
                $dayAccommodationCategory = $dayAccommodation['category']    ?? '';
                $dayAccommodationAlternatives = !empty($dayMeta['accommodation_alternatives']) && is_array($dayMeta['accommodation_alternatives']) ? $dayMeta['accommodation_alternatives'] : [];
                $dayServices = !empty($dayMeta['services']) && is_array($dayMeta['services']) ? $dayMeta['services'] : [];
                // Fallback image from day photos
                $dayFallbackImage = '';
                foreach ($images as $maybeImg) {
                    if (\Illuminate\Support\Str::startsWith($maybeImg, ['/', 'http://', 'https://'])) {
                        $dayFallbackImage = $maybeImg;
                        break;
                    }
                }
                $dayAccommodationImage = $dayAccommodationImage ?: $dayFallbackImage;
            @endphp
            <div class="box !tw-p-0 !tw-overflow-hidden qtp-day-edit" data-qtp-day="{{ $c }}" style="{{ $loop->first ? '' : 'display:none;' }}">
                {{-- Day Header --}}
                <div class="tw-px-8 tw-py-5 tw-bg-slate-900 tw-text-white tw-flex tw-justify-between tw-items-center shadow-lg">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-lg tw-font-black" data-day-label="{{ $c }}">{{ sprintf('%02d', $c) }}</div>
                        <div>
                            <span class="tw-text-orange-400 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest">Itinerary Details</span>
                            <h4 class="tw-text-sm tw-font-extrabold !tw-m-0">{{ $currentDateLabel ?: ('Day ' . $c) }}</h4>
                        </div>
                    </div>
                    <div class="tw-flex tw-gap-3">
                        <a href="javascript:void(0);" class="tw-px-4 tw-py-2 tw-bg-white/10 tw-rounded-xl tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wider tw-text-white hover:tw-bg-white/20 tw-transition-all tw-no-underline">Fill from Canned Day</a>
                    </div>
                </div>

                <div class="tw-p-8 tw-grid tw-grid-cols-1 lg:tw-grid-cols-12 tw-gap-8">
                    <div class="qtp-day-title-field tw-col-span-full">
                        <div class="qtp-floating-field">
                            <input type="text" name="day_title_{{ $c }}" value="{{ $dayTitleValue }}" maxlength="255" oninput="syncQtpDayTitle({{ $c }}, this.value); updateQtpCharCount(this, 'day_title_count_{{ $c }}');">
                            <label>Day title</label>
                        </div>
                        <div class="qtp-char-count" id="day_title_count_{{ $c }}">({{ strlen($dayTitleValue) }}/255)</div>
                    </div>

                    {{-- Left: TinyMCE --}}
                    <div class="qtp-day-description-panel tw-col-span-full tw-flex tw-flex-col tw-gap-3">
                        <label class="tw-flex tw-items-center tw-gap-2 tw-text-sm tw-font-bold tw-text-slate-900">Description</label>
                        <textarea class="tinymce" name="desc_day_{{ $c }}" style="min-height:300px;">{!! $dayRawContents !!}</textarea>
                    </div>

                    <div class="qtp-sites-section tw-col-span-full">
                        <div class="qtp-section-title">Site(s)</div>
                        <div class="qtp-site-row" id="qtp_sites_{{ $c }}">
                            @foreach($daySites as $site)
                            <span class="qtp-tag">
                                {{ $site }}
                                <input type="hidden" name="day_sites_{{ $c }}[]" value="{{ $site }}">
                                <button type="button" onclick="this.parentElement.remove();">&times;</button>
                            </span>
                            @endforeach
                            <input type="text" class="qtp-site-input" id="qtp_site_input_{{ $c }}" placeholder="Type destination..." autocomplete="off" oninput="qtpSiteAutocomplete({{ $c }}, this.value)" onkeydown="qtpSiteInputKey(event, {{ $c }})">
                            <div class="qtp-site-dropdown" id="qtp_site_dropdown_{{ $c }}"></div>
                        </div>
                    </div>

                    <div class="qtp-meal-section tw-col-span-full">
                        <div class="qtp-section-title">Meal</div>
                        <label class="qtp-meal-option">
                            <input type="radio" name="meal_type_{{ $c }}" value="included" {{ $dayMealType === 'included' ? 'checked' : '' }} onchange="toggleQtpMeal({{ $c }});">
                            <span>Meals included</span>
                            <span class="qtp-meal-checks" id="qtp_meal_checks_{{ $c }}">
                                <label><input type="checkbox" name="meal_options_{{ $c }}[]" value="breakfast" {{ in_array('breakfast', $dayMealOptions) ? 'checked' : '' }}> breakfast</label>
                                <label><input type="checkbox" name="meal_options_{{ $c }}[]" value="lunch" {{ in_array('lunch', $dayMealOptions) ? 'checked' : '' }}> lunch</label>
                                <label><input type="checkbox" name="meal_options_{{ $c }}[]" value="dinner" {{ in_array('dinner', $dayMealOptions) ? 'checked' : '' }}> dinner</label>
                            </span>
                        </label>
                        <label class="qtp-meal-option">
                            <input type="radio" name="meal_type_{{ $c }}" value="none" {{ $dayMealType !== 'included' ? 'checked' : '' }} onchange="toggleQtpMeal({{ $c }});">
                            <span>No meals</span>
                        </label>
                    </div>


                    <div class="qtp-services-section tw-col-span-full">
                        <div class="qtp-section-title">Add a service:</div>
                        <div id="qtp_services_{{ $c }}">
                            @foreach($dayServices as $service)
                            @php
                                $svcObj = json_decode($service, true);
                                $isJson = is_array($svcObj) && isset($svcObj['name']);
                                $svcName = $isJson ? $svcObj['name'] : $service;
                                $svcCost = $isJson ? ($svcObj['cost'] ?? '') : '';
                                $svcImage = $isJson ? ($svcObj['image'] ?? '') : '';
                                $svcCategory = $isJson ? ($svcObj['category'] ?? 'Jordan') : 'Jordan';
                                $svcDesc = $isJson ? ($svcObj['description'] ?? '') : '';
                                $svcServiceType = $isJson ? ($svcObj['type'] ?? '') : '';
                                // Auto-detect type
                                $svcType = 'Other';
                                if($svcServiceType) {
                                    $stLower = strtolower($svcServiceType);
                                    if(str_contains($stLower,'transport')) $svcType = 'Transport';
                                    elseif(str_contains($stLower,'activ')) $svcType = 'Activity';
                                    elseif(str_contains($stLower,'accommod') || str_contains($stLower,'hotel')) $svcType = 'Accommodation';
                                    elseif(str_contains($stLower,'guide')) $svcType = 'Guide';
                                    elseif(str_contains($stLower,'restaurant')) $svcType = 'Restaurant';
                                } else {
                                    $svcLower = strtolower($svcName);
                                    if(str_contains($svcLower,'transfer') || str_contains($svcLower,'transport') || str_contains($svcLower,'car')) $svcType = 'Transport';
                                    elseif(str_contains($svcLower,'hotel') || str_contains($svcLower,'accommodation') || str_contains($svcLower,'resort')) $svcType = 'Accommodation';
                                    elseif(str_contains($svcLower,'visit') || str_contains($svcLower,'tour') || str_contains($svcLower,'activity')) $svcType = 'Activity';
                                    elseif(str_contains($svcLower,'guide')) $svcType = 'Guide';
                                    elseif(str_contains($svcLower,'restaurant') || str_contains($svcLower,'dinner') || str_contains($svcLower,'lunch')) $svcType = 'Restaurant';
                                }
                                $colorMap = ['Transport'=>'#8b1553','Activity'=>'#e65100','Guide'=>'#d97706','Restaurant'=>'#c05621','Accommodation'=>'#ea580c','Other'=>'#ea580c'];
                                $iconMap = ['Transport'=>'fa-car','Activity'=>'fa-camera','Guide'=>'fa-user','Restaurant'=>'fa-cutlery','Accommodation'=>'fa-bed','Other'=>'fa-plus'];
                                $sColor = $colorMap[$svcType] ?? '#ea580c';
                                $sIcon = $iconMap[$svcType] ?? 'fa-plus';
                            @endphp
                            <div class="qtp-svc-card">
                                <input type="hidden" name="day_services_{{ $c }}[]" value="{{ $service }}">
                                @if($svcType === 'Transport')
                                <div class="qtp-svc-card-icon" style="border-color:{{ $sColor }};"><i class="fa {{ $sIcon }}" style="color:{{ $sColor }};"></i></div>
                                @elseif($svcImage)
                                <img class="qtp-svc-card-img" src="{{ $svcImage }}" alt="">
                                @else
                                <div class="qtp-svc-card-icon" style="border-color:{{ $sColor }};"><i class="fa {{ $sIcon }}" style="color:{{ $sColor }};"></i></div>
                                @endif
                                <div class="qtp-svc-card-body">
                                    <div class="qtp-svc-card-title" style="color:{{ $sColor }};">{{ $svcName }}</div>
                                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                        <span class="qtp-svc-card-loc"><i class="fa fa-map-marker"></i> {{ $svcCategory }}</span>
                                        @if($svcCost)
                                        <span style="font-size:12px;font-weight:700;color:#1e293b;">JOD {{ $svcCost }}</span>
                                        @endif
                                    </div>
                                    @if($svcDesc)
                                    <div style="font-size:11px;color:#6b7280;margin-top:4px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ Str::limit(strip_tags($svcDesc), 120) }}</div>
                                    @endif
                                    <button type="button" class="qtp-svc-card-alt"><i class="fa fa-plus" style="font-size:9px;"></i> Add an alternative</button>
                                </div>
                                <button type="button" class="qtp-svc-card-close" onclick="this.closest('.qtp-svc-card').remove();">&times;</button>
                            </div>
                            @endforeach
                        </div>
                        <div class="qtp-service-buttons">
                            <button type="button" onclick="addQtpService({{ $c }}, 'Activity');">Activity</button>
                            <button type="button" onclick="addQtpService({{ $c }}, 'Transport');">Transport</button>
                            <button type="button" onclick="addQtpService({{ $c }}, 'Accommodation');">Accommodation</button>
                            <button type="button" onclick="addQtpService({{ $c }}, 'Restaurant');">Restaurant</button>
                            <button type="button" onclick="addQtpService({{ $c }}, 'Guide');">Guide</button>
                            <button type="button" onclick="addQtpService({{ $c }}, 'Other');">Other</button>
                        </div>
                    </div>

                    {{-- Hidden expense inputs are kept only to avoid losing existing saved values. --}}
                    <div class="qtp-day-side-panel tw-col-span-full tw-flex tw-flex-col tw-gap-8">
                        <div id="expense_list_{{ $c }}" class="tw-hidden">
                            @foreach($expenses as $expKey => $expData)
                            @if(is_array($expData))
                            <div>
                                <input type="hidden" name="expenses_qty_{{ $c }}[{{ $expKey }}]" value="{{ $expData['qty'] or 1 }}">
                                <input type="hidden" name="expenses_day_{{ $c }}[{{ $expKey }}]" value="{{ $expData['id'] or '' }}">
                                <input type="hidden" name="expenses_name_{{ $c }}[{{ $expKey }}]" value="{{ $expData['desc'] or '' }}">
                            </div>
                            @endif
                            @endforeach
                        </div>

                        {{-- Inclusions Section --}}
                        <div class="tw-flex tw-flex-col tw-gap-4">
                            <div class="tw-flex tw-justify-between tw-items-center">
                                <label class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider"><i class="fa fa-paperclip tw-text-orange-500"></i> Inclusions / Exclusions</label>
                                <button type="button" class="tw-px-3 tw-py-1.5 tw-bg-slate-900 tw-text-white tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wider tw-rounded-lg hover:tw-bg-black tw-transition-colors" onclick="addInclusion({{ $c }});"><i class="fa fa-cog tw-mr-1"></i> Manage</button>
                            </div>
                            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                                <div class="tw-p-4 tw-bg-emerald-50/50 tw-rounded-2xl tw-border tw-border-emerald-100/50 tw-min-h-[100px]"><span class="tw-text-[11px] tw-font-black tw-text-emerald-600 tw-uppercase tw-tracking-widest tw-block tw-mb-2">Included</span>
                                    <div id="day_inc_{{ $c }}" class="tw-flex tw-flex-col tw-gap-1">
                                        @foreach($included as $incKey => $incVal)
                                        <div class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-bg-white tw-rounded-lg tw-border tw-border-slate-100 tw-group">
                                            <span class="tw-text-[11px] tw-font-medium tw-text-slate-600 tw-flex tw-items-center tw-gap-2"><i class="fa fa-check tw-text-emerald-500"></i> {{ $incVal }}</span>
                                            <input type="hidden" name="day_inc_{{ $c }}[{{ $incKey }}]" value="{{ $incVal }}">
                                            <button type="button" onclick="this.parentElement.remove();" class="tw-opacity-0 group-hover:tw-opacity-100 tw-text-rose-400 hover:tw-text-rose-600 tw-transition-all"><i class="fa fa-times-circle"></i></button>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="tw-p-4 tw-bg-rose-50/50 tw-rounded-2xl tw-border tw-border-rose-100/50 tw-min-h-[100px]"><span class="tw-text-[11px] tw-font-black tw-text-rose-600 tw-uppercase tw-tracking-widest tw-block tw-mb-2">Excluded</span>
                                    <div id="day_exc_{{ $c }}" class="tw-flex tw-flex-col tw-gap-1">
                                        @foreach($excluded as $excKey => $excVal)
                                        <div class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-bg-white tw-rounded-lg tw-border tw-border-slate-100 tw-group">
                                            <span class="tw-text-[11px] tw-font-medium tw-text-slate-600 tw-flex tw-items-center tw-gap-2"><i class="fa fa-times tw-text-rose-500"></i> {{ $excVal }}</span>
                                            <input type="hidden" name="day_exc_{{ $c }}[{{ $excKey }}]" value="{{ $excVal }}">
                                            <button type="button" onclick="this.parentElement.remove();" class="tw-opacity-0 group-hover:tw-opacity-100 tw-text-rose-400 hover:tw-text-rose-600 tw-transition-all"><i class="fa fa-times-circle"></i></button>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom: Images --}}
                    <div class="qtp-day-photos-panel tw-col-span-full tw-pt-8 tw-border-t tw-border-slate-100">
                        <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
                            <label class="tw-flex tw-items-center tw-gap-3 tw-text-sm tw-font-bold tw-text-slate-900"><span>Photos</span><a href="javascript:void(0);" class="tw-text-emerald-700 tw-font-black tw-no-underline">How to choose the right photos?</a></label>
                            <button type="button" class="image_selector tw-px-3 tw-py-1.5 tw-bg-white tw-text-slate-500 tw-border tw-border-slate-200 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wider tw-rounded-lg" data-input-name="day_images_{{ $c }}"><i class="fa fa-camera tw-mr-1"></i> Add photo</button>
                        </div>
                        <div id="images_{{ $c }}" class="tw-flex tw-flex-wrap tw-gap-4">
                            @foreach($images as $imgUrl)
                            @if(\Illuminate\Support\Str::startsWith($imgUrl, ['/', 'http://', 'https://']))
                            <div class="tw-w-32 tw-h-32 tw-rounded-2xl tw-overflow-hidden tw-relative tw-group tw-border tw-border-slate-100 tw-shadow-sm">
                                <input type="hidden" name="day_images_{{ $c }}[]" value="{{ $imgUrl }}">
                                <img src="{{ $imgUrl }}" class="tw-w-full tw-h-full tw-object-cover tw-transition-transform tw-duration-500 group-hover:tw-scale-110">
                                <div class="tw-absolute tw-inset-0 tw-bg-black/40 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity tw-flex tw-items-center tw-justify-center">
                                    <button type="button" onclick="this.closest('.tw-relative').remove();" class="tw-text-white tw-bg-rose-500/80 tw-w-8 tw-h-8 tw-rounded-xl hover:tw-bg-rose-600 tw-transition-colors"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
                </div>
            </div>
        </div>

        <div class="qtp-tab-panel qtp-price-panel" id="qtpPanelPrice" style="display:none;">
            <section class="ev-panel qtp-price-card">
                <div class="ev-panel-head">
                    <div>
                        <div class="ev-panel-dots"><span></span><span></span><span></span></div>
                        <h3 class="ev-panel-title">Trip price</h3>
                    </div>
                    <button type="button" class="ev-panel-toggle" onclick="toggleEvPanel('qtp_price_body', this);">
                        <i class="fa fa-chevron-up"></i>
                    </button>
                </div>
                <div id="qtp_price_body">
                    <div class="ev-important-box">
                        <div>
                            <b>Important</b>
                            <p>If you need to adjust the number of pax please go to Request Manager. Please refresh this page when you've done the modifications in Request Manager.</p>
                        </div>
                        <a href="{{ route('admin.request-manager') }}">Go to Request<br>Manager</a>
                    </div>
                    <div class="qtp-price-total-row">
                        <div class="ev-field" style="width:120px;">
                            <label>Currency</label>
                            <select class="ev-select" id="qtp_currency" onchange="recalcQtpPrice();">
                                <option value="JOD" selected>JOD</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                        <div style="text-align:right;">
                            <div class="qtp-price-total-label">Total price including all taxes</div>
                            <div class="qtp-price-total-value"><span id="qtp_currency_label">JOD</span> <span id="qtp_total_display">{{ number_format($qtpClientTotal, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="qtp-price-actions">
                        <button type="button" class="qtp-green-link" onclick="applyQtpPriceToAll();">Apply to all <i class="fa fa-info-circle"></i></button>
                        <button type="button" class="qtp-green-link" onclick="syncQtpPriceFromCost();"><i class="fa fa-refresh"></i> Recalculate from services</button>
                    </div>
                    @for($paxIndex = 1; $paxIndex <= $qtpPax; $paxIndex++)
                    <div class="qtp-price-row">
                        <div class="qtp-check"><i class="fa fa-check-circle"></i></div>
                        <div class="ev-field">
                            <label>Pax type</label>
                            <select class="ev-select">
                                <option value="adult">adult</option>
                                <option value="child">child</option>
                            </select>
                        </div>
                        <div class="ev-field">
                            <label>Price details</label>
                            <input type="text" class="ev-input" value="Traveller {{ $paxIndex }}">
                        </div>
                        <div class="ev-field">
                            <label>Price for this traveller</label>
                            <input type="number" class="ev-input qtp-pax-price" value="{{ number_format($qtpPerPerson, 2, '.', '') }}" step="0.01" min="0" oninput="recalcQtpPrice();">
                        </div>
                    </div>
                    @endfor
                    <input type="hidden" id="qtp_base_cost" value="{{ number_format($qtpBaseCost, 2, '.', '') }}">
                    <input type="hidden" id="qtp_profit_amount" value="{{ number_format($qtpProfit, 2, '.', '') }}">
                    <button type="button" class="btn orange !tw-px-8 !tw-py-3" onclick="saveQtpPrice();">
                        <i class="fa fa-save"></i> Save Pricing
                    </button>
                </div>
            </section>
            <section class="ev-panel qtp-price-card">
                <div class="ev-panel-head">
                    <div>
                        <div class="ev-panel-dots"><span></span><span></span><span></span></div>
                        <h3 class="ev-panel-title">Price details</h3>
                    </div>
                    <button type="button" class="ev-panel-toggle" onclick="toggleEvPanel('qtp_price_details_body', this);">
                        <i class="fa fa-chevron-up"></i>
                    </button>
                </div>
                <div id="qtp_price_details_body" class="ev-form-grid">
                    <div class="ev-field">
                        <label>Total services cost</label>
                        <input type="text" class="ev-input" value="JOD {{ number_format($qtpBaseCost, 2) }}" readonly>
                    </div>
                    <div class="ev-field">
                        <label>Profit amount</label>
                        <input type="text" class="ev-input" id="qtp_profit_display" value="JOD {{ number_format($qtpProfit, 2) }}" readonly>
                    </div>
                    <div class="ev-field">
                        <label>Booking conditions</label>
                        <textarea class="ev-input" style="height:110px; padding-top:18px;" readonly>Payment and booking conditions will be shared with the traveller after quotation confirmation.</textarea>
                    </div>
                </div>
            </section>
        </div>

        <input type="hidden" name="step" value="save_quotation">
        
        <div class="tw-flex tw-justify-center tw-pb-20">
            <button type="submit" class="btn orange !tw-px-10 !tw-py-4 !tw-text-lg">
                <i class="fa fa-save"></i> Synchronize and Update Quotation
            </button>
        </div>
    </form>

    <div class="qtp-canned-overlay" id="qtpCannedModal" onclick="if(event.target === this) closeQtpCannedModal();">
        <div class="qtp-canned-modal">
            <div class="qtp-canned-head">
                <div>
                    <h3>Add another day</h3>
                    <div class="qtp-canned-day-meta">
                        <span id="qtpCannedDayBadge">D1</span>
                        <span id="qtpCannedDayDate"></span>
                    </div>
                </div>
                <button type="button" class="qtp-canned-create" onclick="createBlankQtpDay(); closeQtpCannedModal();">
                    <i class="fa fa-plus"></i> Create another day
                </button>
            </div>
            <div class="qtp-canned-toolbar">
                <label class="qtp-canned-search">
                    <i class="fa fa-search"></i>
                    <input type="text" id="qtpCannedSearch" placeholder="Search for a saved day" oninput="filterQtpCannedDays(this.value);">
                </label>
                <select class="qtp-canned-lang">
                    <option>🇬🇧 English</option>
                    <option>🇫🇷 Français</option>
                    <option>🇮🇹 Italiano</option>
                    <option>🇪🇸 Español</option>
                    <option>🇯🇴 Arabic</option>
                </select>
            </div>
            <div class="qtp-canned-list" id="qtpCannedList">
                @foreach($qtpCannedDays as $canned)
                <button type="button" class="qtp-canned-card" data-canned-id="{{ $canned['id'] }}" data-title="{{ strtolower($canned['title']) }}" data-desc="{{ strtolower(strip_tags($canned['description'])) }}" onclick="useQtpCannedDay({{ $canned['id'] }});" style="background-image:url('{{ $canned['image'] }}');">
                    <span class="qtp-canned-more"><i class="fa fa-ellipsis-v"></i></span>
                    <span class="qtp-canned-card-body">
                        <span class="qtp-canned-locations">
                            <span><i class="fa fa-map-marker"></i> Jordan</span>
                        </span>
                        <span class="qtp-canned-title">{{ $canned['title'] }}</span>
                    </span>
                </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="qtp-service-overlay" id="qtpServiceModal" onclick="if(event.target === this) closeQtpServiceModal();">
    <div class="qtp-service-modal">
        <div class="qtp-service-modal-head">
            <div>
                <h3>Add a service:</h3>
                <div class="qtp-service-type-label" id="qtpServiceTypeLabel">Service</div>
            </div>
            <button type="button" class="qtp-service-create" onclick="createQtpCustomService();">
                <i class="fa fa-plus"></i> <span id="qtpServiceCreateLabel">Create service</span>
            </button>
        </div>
        <div class="qtp-service-toolbar" style="display:flex; gap:14px; margin-bottom:22px; align-items:stretch;">
            <label class="qtp-service-search" style="flex:1;">
                <i class="fa fa-search"></i>
                <input type="text" id="qtpServiceSearch" placeholder="Search for service" oninput="filterQtpServices(this.value);" style="width:100%; border:none; outline:none; background:transparent;">
            </label>
            <span id="qtpFilterContainer"></span>
            <button type="button" id="qtpFilterBtn" style="width:auto; padding:0 15px; background: white; border: 1px solid #f97316; cursor:pointer; border-radius: 4px; color: #4b5563; font-size: 14px; height: 38px; display:flex; align-items:center; gap:6px;" onclick="document.getElementById('qtpServiceFilter').focus();">
                <i class="fa fa-filter" style="color:#f97316;"></i> Filter
            </button>
            <select class="qtp-service-lang" style="width: 150px;">
                <option>🇬🇧 English</option>
                <option>🇫🇷 Français</option>
                <option>🇮🇹 Italiano</option>
                <option>🇪🇸 Español</option>
                <option>🇯🇴 Arabic</option>
            </select>
        </div>
        <div class="qtp-service-list" id="qtpServiceResults"></div>
    </div>
</div>

{{-- Expense Service Modal --}}
<div class="modal" id="expense_modal">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[1000px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-orange-400"></i> Browse Expense Resources
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <div class="tw-flex tw-h-[650px] tw-max-h-[80vh]">
            <div class="tw-w-[350px] tw-flex tw-flex-col tw-bg-slate-50 tw-border-r tw-border-slate-100">
                <div class="tw-p-6 tw-flex tw-flex-col tw-gap-4 tw-bg-white tw-border-b tw-border-slate-100">
                    <div class="tw-relative">
                        <i class="fa fa-search tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
                        <input type="text" id="modal_search" placeholder="Search resources..." onkeyup="filterModalItems();" class="!tw-pl-11">
                    </div>
                </div>
                <div id="modal_left_panel" class="tw-flex-1 tw-overflow-y-auto tw-p-6"></div>
            </div>
            <div class="tw-flex-1 tw-flex tw-flex-col tw-bg-white">
                <div id="modal_right_header" class="tw-px-8 tw-py-4 tw-bg-orange-50/50 tw-border-b tw-border-orange-100"></div>
                <div id="modal_right_vendor" class="tw-px-8 tw-py-4 tw-bg-slate-50/50 tw-border-b tw-border-slate-100"></div>
                <div class="tw-flex-1 tw-overflow-y-auto">
                    <div id="modal_right_table">
                        <table class="tw-w-full">
                            <thead>
                                <tr class="tw-bg-slate-50 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest"><th class="tw-py-3 tw-px-8 tw-text-left">Details</th><th class="tw-py-3 tw-px-4">Cost</th><th class="tw-py-3 tw-px-8 tw-text-right">Action</th></tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="expense_day_number">
    </div>
</div>

{{-- Inclusion Modal --}}
<div class="modal" id="add_inclusion">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[700px] !tw-max-w-[90vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0"><i class="fa fa-paperclip tw-text-orange-400"></i> Manage Checklist</h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <div class="tw-p-8">
            <input type="text" id="inclusion_search" placeholder="Filter list..." onkeyup="filterInclusions();">
            <input type="hidden" id="inclusion_day">
            <div id="inclusion_items_list" class="tw-max-height-[400px] tw-overflow-y-auto tw-mt-6 tw-flex tw-flex-col tw-gap-2">
                @foreach($inclusions as $inc)
                <div class="inclusion-row tw-flex tw-items-center tw-justify-between tw-p-3 tw-rounded-xl tw-border tw-border-slate-50 hover:tw-bg-slate-50" data-name="{{ strtolower($inc->name) }}">
                    <span class="tw-text-sm tw-font-bold tw-text-slate-700">{{ $inc->name }}</span>
                    <div class="tw-flex tw-gap-2">
                        <button onclick="addInclusionItem(this, 'included', '{{ addslashes($inc->name) }}');" class="tw-px-3 tw-py-1.5 tw-bg-emerald-50 tw-text-emerald-600 tw-text-[11px] tw-font-black tw-rounded-lg">Include</button>
                        <button onclick="addInclusionItem(this, 'excluded', '{{ addslashes($inc->name) }}');" class="tw-px-3 tw-py-1.5 tw-bg-rose-50 tw-text-rose-600 tw-text-[11px] tw-font-black tw-rounded-lg">Exclude</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="/assets/admin/tinymce/tinymce.min.js"></script>
<script src="/assets/admin/gogiesfm/image_selector.js"></script>
<script>
// Reuse logic from create but adjusted for existing data
var expenseCategories = @json($expenseCategories);
var expenseCountries = @json($expenseCountries);
var qtpProfitUrl = '{{ route('admin.quotations.profit', $quotation->id) }}';
var qtpCsrf = '{{ csrf_token() }}';
var qtpCannedDays = @json($qtpCannedDays->keyBy('id'));
var qtpServiceSearchUrl = '{{ route('admin.request-manager.search-library') }}';

function toggleEvPanel(panelId, button) {
    var panel = document.getElementById(panelId);
    if (!panel) return;
    var icon = button ? button.querySelector('i') : null;
    panel.classList.toggle('ev-hidden');
    if (icon) {
        icon.classList.toggle('fa-chevron-up');
        icon.classList.toggle('fa-chevron-down');
    }
}

function triggerFirstDayImageSelector() {
    var selector = document.querySelector('.image_selector[data-input-name="day_images_1"]');
    if (selector) {
        selector.click();
    }
}

function showQtpTab(tab, button) {
    var map = { quote: 'qtpPanelQuote', day: 'qtpPanelDay', price: 'qtpPanelPrice' };
    Object.keys(map).forEach(function(key) {
        var panel = document.getElementById(map[key]);
        if (panel) panel.style.display = key === tab ? '' : 'none';
    });
    document.querySelectorAll('.qtp-editor-tabs button').forEach(function(btn) {
        btn.classList.remove('active');
    });
    if (button) button.classList.add('active');
    if (tab === 'day') {
        var active = document.querySelector('.qtp-day-item.active') || document.querySelector('.qtp-day-item');
        if (active) selectQtpDay(active.getAttribute('data-qtp-day-nav'), active);
    }
    if (tab === 'price') {
        recalcQtpPrice();
    }
}

function selectQtpDay(dayNumber, button) {
    dayNumber = String(dayNumber);
    document.querySelectorAll('.qtp-day-item').forEach(function(btn) {
        btn.classList.toggle('active', btn === button);
    });
    document.querySelectorAll('.qtp-day-edit').forEach(function(panel) {
        var isActive = panel.getAttribute('data-qtp-day') === dayNumber;
        panel.style.display = isActive ? '' : 'none';
        if (isActive) {
            panel.querySelectorAll('.tinymce').forEach(initQuotationTinyMce);
        }
    });
}

function addQtpDay() {
    openQtpCannedModal();
}

function openQtpCannedModal() {
    var modal = document.getElementById('qtpCannedModal');
    var nextDay = (parseInt(document.getElementById('qtp_days_input').value || '0', 10) || 0) + 1;
    document.getElementById('qtpCannedDayBadge').textContent = 'D' + nextDay;
    var travelDateInput = document.querySelector('input[name="travel_date"]');
    var dateLabel = '';
    if (travelDateInput && travelDateInput.value) {
        var d = new Date(travelDateInput.value);
        if (!isNaN(d.getTime())) {
            d.setDate(d.getDate() + nextDay - 1);
            dateLabel = d.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
    }
    document.getElementById('qtpCannedDayDate').textContent = dateLabel;
    document.getElementById('qtpCannedSearch').value = '';
    filterQtpCannedDays('');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeQtpCannedModal() {
    var modal = document.getElementById('qtpCannedModal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
}

function filterQtpCannedDays(query) {
    var q = (query || '').toLowerCase();
    document.querySelectorAll('.qtp-canned-card').forEach(function(card) {
        var title = card.getAttribute('data-title') || '';
        var desc = card.getAttribute('data-desc') || '';
        card.style.display = (!q || title.indexOf(q) !== -1 || desc.indexOf(q) !== -1) ? 'block' : 'none';
    });
}

function useQtpCannedDay(id) {
    var item = qtpCannedDays[id];
    if (!item) return;
    createBlankQtpDay(item);
    closeQtpCannedModal();
    qtpToast('Canned day added');
}

function qtpEscapeHtml(value) {
    return String(value || '').replace(/[&<>"']/g, function(ch) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[ch];
    });
}

function qtpImageItemsHtml(dayNumber, images) {
    return (images || []).map(function(url) {
        return '<div class="tw-w-32 tw-h-32 tw-rounded-2xl tw-overflow-hidden tw-relative tw-group tw-border tw-border-slate-100 tw-shadow-sm">' +
            '<input type="hidden" name="day_images_' + dayNumber + '[]" value="' + qtpEscapeHtml(url) + '">' +
            '<img src="' + qtpEscapeHtml(url) + '" class="tw-w-full tw-h-full tw-object-cover tw-transition-transform tw-duration-500 group-hover:tw-scale-110">' +
            '<div class="tw-absolute tw-inset-0 tw-bg-black/40 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity tw-flex tw-items-center tw-justify-center">' +
                '<button type="button" onclick="this.parentElement.parentElement.remove();" class="tw-text-white tw-bg-rose-500/80 tw-w-8 tw-h-8 tw-rounded-xl hover:tw-bg-rose-600 tw-transition-colors"><i class="fa fa-trash"></i></button>' +
            '</div>' +
        '</div>';
    }).join('');
}

function qtpExpenseItemsHtml(dayNumber, expenses) {
    return (expenses || []).map(function(exp, index) {
        var key = Date.now() + '_' + index;
        var desc = exp && exp.desc ? exp.desc : '';
        var expId = exp && exp.id ? exp.id : '';
        return '<div class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-bg-white tw-border tw-border-slate-100 tw-rounded-xl tw-shadow-sm tw-group">' +
            '<div class="tw-flex tw-flex-col tw-gap-0.5"><span class="tw-text-xs tw-font-bold tw-text-slate-700">' + qtpEscapeHtml(desc) + '</span><span class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase">Service Resource</span></div>' +
            '<div class="tw-flex tw-items-center tw-gap-3">' +
                '<input type="number" name="expenses_qty_' + dayNumber + '[' + key + ']" value="1" min="1" class="!tw-w-14 !tw-h-8 !tw-px-2 !tw-py-0 !tw-text-center !tw-text-xs !tw-font-bold">' +
                '<input type="hidden" name="expenses_day_' + dayNumber + '[' + key + ']" value="' + qtpEscapeHtml(expId) + '">' +
                '<input type="hidden" name="expenses_name_' + dayNumber + '[' + key + ']" value="' + qtpEscapeHtml(desc) + '">' +
                '<button type="button" onclick="this.parentElement.parentElement.remove();" class="tw-w-8 tw-h-8 tw-bg-rose-50 tw-text-rose-500 tw-rounded-lg hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all"><i class="fa fa-trash"></i></button>' +
            '</div>' +
        '</div>';
    }).join('');
}

function qtpChecklistItemsHtml(dayNumber, items, type) {
    var prefix = type === 'included' ? 'day_inc_' : 'day_exc_';
    var icon = type === 'included' ? 'fa-check tw-text-emerald-500' : 'fa-times tw-text-rose-500';
    return (items || []).map(function(text, index) {
        var key = Date.now() + '_' + index;
        return '<div class="tw-flex tw-items-center tw-justify-between tw-p-2 tw-bg-white tw-rounded-lg tw-border tw-border-slate-100 tw-group">' +
            '<span class="tw-text-[11px] tw-font-medium tw-text-slate-600 tw-flex tw-items-center tw-gap-2"><i class="fa ' + icon + '"></i> ' + qtpEscapeHtml(text) + '</span>' +
            '<input type="hidden" name="' + prefix + dayNumber + '[' + key + ']" value="' + qtpEscapeHtml(text) + '">' +
            '<button type="button" onclick="this.parentElement.remove();" class="tw-text-rose-400 group-hover:tw-text-rose-600"><i class="fa fa-times-circle"></i></button>' +
        '</div>';
    }).join('');
}

function qtpSitesHtml(dayNumber, sites) {
    return (sites && sites.length ? sites : ['Jordan']).map(function(site) {
        return '<span class="qtp-tag">' + qtpEscapeHtml(site) +
            '<input type="hidden" name="day_sites_' + dayNumber + '[]" value="' + qtpEscapeHtml(site) + '">' +
            '<button type="button" onclick="this.parentElement.remove();">&times;</button></span>';
    }).join('');
}

function qtpServicesChipsHtml(dayNumber, services) {
    return (services || []).map(function(service) {
        var svcObj = null;
        try { svcObj = JSON.parse(service); } catch(e) {}
        var name = svcObj ? (svcObj.name || service) : service;
        var cost = svcObj ? (svcObj.cost || '') : '';
        var image = svcObj ? (svcObj.image || '') : '';
        var category = svcObj ? (svcObj.category || 'Jordan') : 'Jordan';
        var desc = svcObj ? (svcObj.description || '') : '';
        var svcType = svcObj ? (svcObj.type || '') : '';
        var type = qtpDetectServiceType(name, svcType);
        var colorMap = {Transport:'#8b1553', Activity:'#e65100', Guide:'#d97706', Restaurant:'#c05621', Accommodation:'#ea580c', Other:'#ea580c'};
        var iconMap = {Transport:'fa-car', Activity:'fa-camera', Guide:'fa-user', Restaurant:'fa-cutlery', Accommodation:'fa-bed', Other:'fa-plus'};
        var color = colorMap[type] || '#ea580c';
        var icon = iconMap[type] || 'fa-plus';
        var safeVal = qtpEscapeHtml(typeof service === 'string' ? service : JSON.stringify(svcObj));
        var mediaHtml;
        if(type === 'Transport' || !image) {
            mediaHtml = '<div class="qtp-svc-card-icon" style="border-color:' + color + ';"><i class="fa ' + icon + '" style="color:' + color + ';"></i></div>';
        } else {
            mediaHtml = '<img class="qtp-svc-card-img" src="' + qtpEscapeHtml(image) + '" alt="">';
        }
        var costHtml = cost ? '<span style="font-size:12px;font-weight:700;color:#1e293b;">JOD ' + qtpEscapeHtml(cost) + '</span>' : '';
        var descHtml = desc ? '<div style="font-size:11px;color:#6b7280;margin-top:4px;line-height:1.4;overflow:hidden;max-height:32px;">' + qtpEscapeHtml(desc).slice(0,120) + '</div>' : '';
        return '<div class="qtp-svc-card">' +
            '<input type="hidden" name="day_services_' + dayNumber + '[]" value="' + safeVal + '">' +
            mediaHtml +
            '<div class="qtp-svc-card-body">' +
                '<div class="qtp-svc-card-title" style="color:' + color + ';">' + qtpEscapeHtml(name) + '</div>' +
                '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;"><span class="qtp-svc-card-loc"><i class="fa fa-map-marker"></i> ' + qtpEscapeHtml(category) + '</span>' + costHtml + '</div>' +
                descHtml +
                '<button type="button" class="qtp-svc-card-alt"><i class="fa fa-plus" style="font-size:9px;"></i> Add an alternative</button>' +
            '</div>' +
            '<button type="button" class="qtp-svc-card-close" onclick="this.closest(\'.qtp-svc-card\').remove();">&times;</button>' +
        '</div>';
    }).join('');
}

function qtpDetectServiceType(name, svcType) {
    if(svcType) {
        var st = svcType.toLowerCase();
        if(st.indexOf('transport')!==-1) return 'Transport';
        if(st.indexOf('activ')!==-1) return 'Activity';
        if(st.indexOf('accommod')!==-1||st.indexOf('hotel')!==-1) return 'Accommodation';
        if(st.indexOf('guide')!==-1) return 'Guide';
        if(st.indexOf('restaurant')!==-1) return 'Restaurant';
    }
    var s = name.toLowerCase();
    if(s.indexOf('transfer')!==-1||s.indexOf('transport')!==-1||s.indexOf('car')!==-1) return 'Transport';
    if(s.indexOf('hotel')!==-1||s.indexOf('accommodation')!==-1||s.indexOf('resort')!==-1) return 'Accommodation';
    if(s.indexOf('visit')!==-1||s.indexOf('tour')!==-1||s.indexOf('activity')!==-1) return 'Activity';
    if(s.indexOf('guide')!==-1) return 'Guide';
    if(s.indexOf('restaurant')!==-1||s.indexOf('dinner')!==-1) return 'Restaurant';
    return 'Other';
}

function qtpAlternativesHtml(dayNumber, alternatives) {
    return (alternatives || []).map(function(alt) {
        return '<input type="text" class="qtp-alt-input" name="day_accommodation_alt_' + dayNumber + '[]" value="' + qtpEscapeHtml(alt) + '" placeholder="Alternative accommodation">';
    }).join('');
}

function syncQtpDayTitle(dayNumber, value) {
    var navTitle = document.querySelector('[data-qtp-day-nav="' + dayNumber + '"] .qtp-day-title');
    if (navTitle) {
        navTitle.textContent = value || ('Day ' + dayNumber);
    }
}

function updateQtpCharCount(input, counterId) {
    var counter = document.getElementById(counterId);
    if (counter) {
        counter.textContent = '(' + String(input.value || '').length + '/255)';
    }
}

function showQtpSiteInput(dayNumber) {
    var input = document.getElementById('qtp_site_input_' + dayNumber);
    if (input) input.focus();
}

var qtpSiteTimer = null;
var qtpSiteActiveDay = 0;
var qtpSiteActiveIdx = -1;

function qtpSiteAutocomplete(dayNumber, query) {
    clearTimeout(qtpSiteTimer);
    qtpSiteActiveDay = dayNumber;
    qtpSiteActiveIdx = -1;
    var dropdown = document.getElementById('qtp_site_dropdown_' + dayNumber);
    if (!dropdown) return;
    if (!query || query.length < 2) {
        dropdown.style.display = 'none';
        return;
    }
    qtpSiteTimer = setTimeout(function() {
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&addressdetails=1&limit=6&accept-language=en')
        .then(function(r) { return r.json(); })
        .then(function(results) {
            dropdown.innerHTML = '';
            if (!results || !results.length) {
                dropdown.style.display = 'none';
                return;
            }
            results.forEach(function(place, idx) {
                var addr = place.address || {};
                var city = addr.city || addr.town || addr.village || addr.hamlet || addr.county || '';
                var state = addr.state || '';
                var country = addr.country || '';
                var displayParts = [];
                if (city) displayParts.push(city);
                if (state && state !== city) displayParts.push(state);
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'qtp-site-dropdown-item';
                btn.setAttribute('data-idx', idx);
                var html = '<i class="fa fa-map-marker" style="color:#9ca3af;font-size:13px;"></i> ';
                html += '<span>';
                if (displayParts.length) html += '<span class="qtp-site-city">' + qtpEscapeHtml(displayParts.join(', ')) + '</span> ';
                if (country) html += '<span class="qtp-site-country">' + qtpEscapeHtml(country) + '</span>';
                html += '</span>';
                btn.innerHTML = html;
                btn.onclick = function() {
                    var label = city || state || country;
                    qtpAddSiteTag(dayNumber, label);
                    dropdown.style.display = 'none';
                };
                dropdown.appendChild(btn);
            });
            dropdown.style.display = 'block';
        })
        .catch(function() {
            dropdown.style.display = 'none';
        });
    }, 300);
}

function qtpSiteInputKey(event, dayNumber) {
    var dropdown = document.getElementById('qtp_site_dropdown_' + dayNumber);
    var items = dropdown ? dropdown.querySelectorAll('.qtp-site-dropdown-item') : [];
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        qtpSiteActiveIdx = Math.min(qtpSiteActiveIdx + 1, items.length - 1);
        items.forEach(function(el, i) { el.classList.toggle('active', i === qtpSiteActiveIdx); });
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        qtpSiteActiveIdx = Math.max(qtpSiteActiveIdx - 1, 0);
        items.forEach(function(el, i) { el.classList.toggle('active', i === qtpSiteActiveIdx); });
    } else if (event.key === 'Enter') {
        event.preventDefault();
        if (qtpSiteActiveIdx >= 0 && items[qtpSiteActiveIdx]) {
            items[qtpSiteActiveIdx].click();
        } else {
            var input = document.getElementById('qtp_site_input_' + dayNumber);
            if (input && input.value.trim()) {
                qtpAddSiteTag(dayNumber, input.value.trim());
                if (dropdown) dropdown.style.display = 'none';
            }
        }
    } else if (event.key === 'Escape') {
        if (dropdown) dropdown.style.display = 'none';
    }
}

function qtpAddSiteTag(dayNumber, label) {
    var row = document.getElementById('qtp_sites_' + dayNumber);
    var input = document.getElementById('qtp_site_input_' + dayNumber);
    if (!row || !label) return;
    var tag = document.createElement('span');
    tag.className = 'qtp-tag';
    tag.innerHTML = qtpEscapeHtml(label) +
        '<input type="hidden" name="day_sites_' + dayNumber + '[]" value="' + qtpEscapeHtml(label) + '">' +
        '<button type="button" onclick="this.parentElement.remove();">&times;</button>';
    row.insertBefore(tag, input);
    if (input) {
        input.value = '';
        input.focus();
    }
    // Also update sidebar day location
    var navLoc = document.querySelector('[data-qtp-day-nav="' + dayNumber + '"] .qtp-day-loc');
    if (navLoc) navLoc.innerHTML = '<i class="fa fa-map-marker"></i> ' + qtpEscapeHtml(label);
}

function addQtpSite(dayNumber) {
    var input = document.getElementById('qtp_site_input_' + dayNumber);
    if (!input) return;
    var value = input.value.trim();
    if (!value) return;
    qtpAddSiteTag(dayNumber, value);
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.qtp-site-row')) {
        document.querySelectorAll('.qtp-site-dropdown').forEach(function(dd) { dd.style.display = 'none'; });
    }
});

function toggleQtpMeal(dayNumber) {
    var included = document.querySelector('input[name="meal_type_' + dayNumber + '"][value="included"]');
    var checks = document.getElementById('qtp_meal_checks_' + dayNumber);
    if (!included || !checks) return;

    var enabled = included.checked;
    checks.style.opacity = enabled ? '1' : '.55';
    checks.querySelectorAll('input[type="checkbox"]').forEach(function(box) {
        box.disabled = !enabled;
    });
}


var qtpServiceModalState = {
    dayNumber: null,
    type: 'Activity',
    endpointType: 'activities',
    items: [],
    timer: null
};

var qtpServiceTypes = {
    Activity: {
        endpointType: 'activities',
        title: 'Activity',
        search: 'Search for activity',
        create: 'Create activity',
        icon: 'fa-map-signs'
    },
    Transport: {
        endpointType: 'transport',
        title: 'Transport',
        search: 'Search for transport',
        create: 'Create transport type',
        icon: 'fa-car'
    },
    Accommodation: {
        endpointType: 'accommodations',
        title: 'Accommodation',
        search: 'Search for accommodation',
        create: 'Create accommodation',
        icon: 'fa-bed'
    },
    Restaurant: {
        endpointType: 'restaurants',
        title: 'Restaurant',
        search: 'Search for restaurant',
        create: 'Create restaurant',
        icon: 'fa-cutlery'
    },
    Guide: {
        endpointType: 'guides',
        title: 'Guide',
        search: 'Search for guide',
        create: 'Create guide',
        icon: 'fa-user'
    },
    Other: {
        endpointType: 'other',
        title: 'Other',
        search: 'Search for other service',
        create: 'Create other service',
        icon: 'fa-plus'
    }
};

function addQtpService(dayNumber, type) {
    openQtpServiceModal(dayNumber, type);
}

function openQtpServiceModal(dayNumber, type) {
    var config = qtpServiceTypes[type] || qtpServiceTypes.Other;
    qtpServiceModalState.dayNumber = dayNumber;
    qtpServiceModalState.type = type;
    qtpServiceModalState.endpointType = config.endpointType;
    qtpServiceModalState.items = [];

    document.getElementById('qtpServiceTypeLabel').textContent = config.title;
    document.getElementById('qtpServiceCreateLabel').textContent = config.create;
    document.getElementById('qtpServiceSearch').value = '';
    document.getElementById('qtpServiceSearch').placeholder = config.search;
    document.getElementById('qtpServiceResults').innerHTML = '<div class="qtp-service-empty"><i class="fa fa-spinner fa-spin"></i> Loading services...</div>';

    var filterContainer = document.getElementById('qtpFilterContainer');
    var filterBtn = document.getElementById('qtpFilterBtn');
    
    var filterOptions = '';
    if (type === 'Accommodation') {
        filterOptions = '<option value="Hotel">Hotel</option>' +
            '<option value="Camps">Camps</option>' +
            '<option value="Homestay">Homestay</option>' +
            '<option value="Mobile Camp">Mobile Camp</option>' +
            '<option value="Wild Jordan RSCN">Wild Jordan RSCN</option>';
    } else if (type === 'Activity') {
        filterOptions = '<option value="Jeep Tour">Jeep Tour</option>' +
            '<option value="Camel ride">Camel ride</option>' +
            '<option value="Horse Ride in Petra">Horse Ride in Petra</option>' +
            '<option value="Horse Ride in Wadi Rum">Horse Ride in Wadi Rum</option>' +
            '<option value="Diving - Aqaba">Diving - Aqaba</option>' +
            '<option value="Lunch">Lunch</option>' +
            '<option value="Petra">Petra</option>';
    } else if (type === 'Transport') {
        filterOptions = '<option value="Ismael cars">Ismael cars</option>' +
            '<option value="Al Raha bus">Al Raha bus</option>' +
            '<option value="Arena rent a car">Arena rent a car</option>' +
            '<option value="Car">Car</option>' +
            '<option value="Bus">Bus</option>';
    } else if (type === 'Restaurant') {
        filterOptions = '<option value="Amman">Amman</option>' +
            '<option value="Aqaba">Aqaba</option>' +
            '<option value="Petra">Petra</option>' +
            '<option value="Dead Sea">Dead Sea</option>' +
            '<option value="Madaba">Madaba</option>' +
            '<option value="Jarash">Jarash</option>' +
            '<option value="Karak">Karak</option>' +
            '<option value="Um Qais">Um Qais</option>' +
            '<option value="On the way">On the way</option>';
    } else if (type === 'Guide') {
        filterOptions = '<option value="English">English Guide</option>' +
            '<option value="French">French Guide</option>' +
            '<option value="Spanish">Spanish Guide</option>' +
            '<option value="Italian">Italian Guide</option>';
    }

    if (filterOptions) {
        filterContainer.innerHTML = '<select id="qtpServiceFilter" onchange="filterQtpServices(document.getElementById(\'qtpServiceSearch\').value);" style="width: 150px; background: white; border: 1px solid #f97316; padding: 0 10px; border-radius: 4px; outline: none; color: #4b5563; font-size: 14px; height: 38px;">' +
            '<option value="">All Categories</option>' + filterOptions + '</select>';
        filterContainer.style.display = '';
        filterBtn.style.display = '';
    } else {
        filterContainer.innerHTML = '';
        filterContainer.style.display = 'none';
        filterBtn.style.display = 'none';
    }

    document.getElementById('qtpServiceModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    fetchQtpServices('');
}

function closeQtpServiceModal() {
    var modal = document.getElementById('qtpServiceModal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
}

function filterQtpServices(query) {
    clearTimeout(qtpServiceModalState.timer);
    qtpServiceModalState.timer = setTimeout(function() {
        fetchQtpServices(query || '');
    }, 250);
}

function fetchQtpServices(query) {
    var subCat = document.getElementById('qtpServiceFilter') ? document.getElementById('qtpServiceFilter').value : '';
    var url = qtpServiceSearchUrl + '?type=' + encodeURIComponent(qtpServiceModalState.endpointType) + '&q=' + encodeURIComponent(query || '') + '&subCat=' + encodeURIComponent(subCat);
    fetch(url)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            qtpServiceModalState.items = data.items || [];
            renderQtpServices(qtpServiceModalState.items);
        })
        .catch(function() {
            document.getElementById('qtpServiceResults').innerHTML = '<div class="qtp-service-empty">Services load nahi ho paayi.</div>';
        });
}

function renderQtpServices(items) {
    var list = document.getElementById('qtpServiceResults');
    var config = qtpServiceTypes[qtpServiceModalState.type] || qtpServiceTypes.Other;
    list.innerHTML = '';

    if (!items || !items.length) {
        list.innerHTML = '<div class="qtp-service-empty">Is category me service nahi mili. Upar create button se custom service add kar sakte hain.</div>';
        return;
    }

    items.forEach(function(item) {
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'qtp-service-result';
        
        var displayTitle = item.title || 'Untitled service';
        var displayMeta = item.arrival || item.category || 'Jordan';
        var costStr = item.cost ? 'JOD ' + item.cost : '';
        var displayDesc = item.description || '';

        if (qtpServiceModalState.type === 'Accommodation') {
            displayTitle = item.vendor || item.category || 'Unknown Hotel';
            var place = item.arrival || 'Jordan';
            displayMeta = place + (item.title ? ' - ' + item.title : '');
        }

        button.innerHTML =
            '<span class="qtp-service-result-icon"><i class="fa ' + config.icon + '"></i></span>' +
            '<span>' +
                '<span class="qtp-service-result-title">' + qtpEscapeHtml(displayTitle) + '</span>' +
                '<span class="qtp-service-result-meta"><i class="fa fa-map-marker"></i> ' + qtpEscapeHtml(displayMeta) + (costStr ? ' &nbsp; <b>' + qtpEscapeHtml(costStr) + '</b>' : '') + '</span>' +
                (displayDesc ? '<p class="qtp-service-result-desc">' + qtpEscapeHtml(displayDesc).slice(0, 190) + '</p>' : '') +
            '</span>';
        button.onclick = function() {
            selectQtpService(item);
        };
        list.appendChild(button);
    });
}

function selectQtpService(item) {
    var title = item && item.title ? item.title : '';
    if (!title) return;
    var svcData = {
        name: title,
        cost: item.cost || '',
        image: item.image || '',
        category: item.category || 'Jordan',
        description: item.description || '',
        type: qtpServiceModalState.type || 'Other'
    };
    addQtpServiceChip(qtpServiceModalState.dayNumber, svcData);



    closeQtpServiceModal();
    qtpToast('Service added');
}

function createQtpCustomService() {
    var label = window.prompt('Service name', qtpServiceModalState.type || 'Service');
    if (!label || !label.trim()) return;
    var svcData = {
        name: label.trim(),
        cost: '',
        image: '',
        category: 'Jordan',
        description: '',
        type: qtpServiceModalState.type || 'Other'
    };
    addQtpServiceChip(qtpServiceModalState.dayNumber, svcData);
    closeQtpServiceModal();
    qtpToast('Service added');
}

function addQtpServiceChip(dayNumber, svcData) {
    var row = document.getElementById('qtp_services_' + dayNumber);
    if (!row) return;

    var name, cost, image, category, desc, svcType;
    if(typeof svcData === 'object') {
        name = svcData.name || '';
        cost = svcData.cost || '';
        image = svcData.image || '';
        category = svcData.category || 'Jordan';
        desc = svcData.description || '';
        svcType = svcData.type || '';
    } else {
        name = svcData;
        cost = ''; image = ''; category = 'Jordan'; desc = ''; svcType = '';
    }

    var type = qtpDetectServiceType(name, svcType || qtpServiceModalState.type);
    var colorMap = {Transport:'#8b1553', Activity:'#e65100', Guide:'#d97706', Restaurant:'#c05621', Accommodation:'#ea580c', Other:'#ea580c'};
    var iconMap = {Transport:'fa-car', Activity:'fa-camera', Guide:'fa-user', Restaurant:'fa-cutlery', Accommodation:'fa-bed', Other:'fa-plus'};
    var color = colorMap[type] || '#ea580c';
    var icon = iconMap[type] || 'fa-plus';

    var jsonVal = JSON.stringify({name:name, cost:cost, image:image, category:category, description:desc, type:type});
    var safeVal = qtpEscapeHtml(jsonVal);

    var mediaHtml;
    if(type === 'Transport' || !image) {
        mediaHtml = '<div class="qtp-svc-card-icon" style="border-color:' + color + ';"><i class="fa ' + icon + '" style="color:' + color + ';"></i></div>';
    } else {
        mediaHtml = '<img class="qtp-svc-card-img" src="' + qtpEscapeHtml(image) + '" alt="">';
    }
    var costHtml = cost ? '<span style="font-size:12px;font-weight:700;color:#1e293b;">JOD ' + qtpEscapeHtml(String(cost)) + '</span>' : '';
    var descHtml = desc ? '<div style="font-size:11px;color:#6b7280;margin-top:4px;line-height:1.4;overflow:hidden;max-height:32px;">' + qtpEscapeHtml(desc).slice(0,120) + '</div>' : '';

    var card = document.createElement('div');
    card.className = 'qtp-svc-card';
    card.innerHTML =
        '<input type="hidden" name="day_services_' + dayNumber + '[]" value="' + safeVal + '">' +
        mediaHtml +
        '<div class="qtp-svc-card-body">' +
            '<div class="qtp-svc-card-title" style="color:' + color + ';">' + qtpEscapeHtml(name) + '</div>' +
            '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;"><span class="qtp-svc-card-loc"><i class="fa fa-map-marker"></i> ' + qtpEscapeHtml(category) + '</span>' + costHtml + '</div>' +
            descHtml +
            '<button type="button" class="qtp-svc-card-alt"><i class="fa fa-plus" style="font-size:9px;"></i> Add an alternative</button>' +
        '</div>' +
        '<button type="button" class="qtp-svc-card-close" onclick="this.closest(\'.qtp-svc-card\').remove();">&times;</button>';
    row.appendChild(card);
}

function createBlankQtpDay(prefill) {
    prefill = prefill || {};
    var daysInput = document.getElementById('qtp_days_input');
    var nightsInput = document.getElementById('qtp_nights_input');
    var nextDay = (parseInt(daysInput.value || '0', 10) || 0) + 1;
    daysInput.value = nextDay;
    nightsInput.value = Math.max(0, nextDay - 1);

    var sidebar = document.getElementById('qtp_day_sidebar');
    var addButton = sidebar.querySelector('.qtp-add-day');
    var nav = document.createElement('button');
    nav.type = 'button';
    nav.className = 'qtp-day-item';
    nav.setAttribute('data-qtp-day-nav', nextDay);
    nav.onclick = function() { selectQtpDay(nextDay, nav); };
    var navTitle = prefill.title || 'New day';
    var navImage = prefill.image || '';
    var navThumb = navImage ? '<span class="qtp-day-thumb"><img src="' + qtpEscapeHtml(navImage) + '" alt=""></span>' : '<span class="qtp-day-thumb qtp-day-thumb-empty"><i class="fa fa-image"></i></span>';
    nav.innerHTML = navThumb + '<span class="qtp-day-meta"><span class="qtp-day-number">Day ' + nextDay + '</span><span class="qtp-day-title">' + qtpEscapeHtml(navTitle) + '</span><span class="qtp-day-loc"><i class="fa fa-map-marker"></i> Jordan</span></span>';
    sidebar.insertBefore(nav, addButton);

    var container = document.getElementById('day_sections_container');
    var panel = document.createElement('div');
    panel.className = 'box !tw-p-0 !tw-overflow-hidden qtp-day-edit';
    panel.setAttribute('data-qtp-day', nextDay);
    panel.style.display = 'none';
    var rawTitle = prefill.title || 'New day';
    var safeTitle = qtpEscapeHtml(rawTitle);
    var safeLocation = qtpEscapeHtml((prefill.accommodation && prefill.accommodation.location) || 'Jordan');
    var safeAccommodation = qtpEscapeHtml((prefill.accommodation && prefill.accommodation.name) || '');
    var safeAccommodationImage = qtpEscapeHtml((prefill.images && prefill.images[0]) || '/uploads/filemanager/Photos/Petra/Kahzneh.jpg');
    panel.innerHTML =
        '<div class="tw-px-8 tw-py-5 tw-bg-slate-900 tw-text-white tw-flex tw-justify-between tw-items-center shadow-lg">' +
            '<div class="tw-flex tw-items-center tw-gap-4"><div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-lg tw-font-black" data-day-label="' + nextDay + '">' + String(nextDay).padStart(2, '0') + '</div>' +
            '<div><span class="tw-text-orange-400 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest">Itinerary Details</span><h4 class="tw-text-sm tw-font-extrabold !tw-m-0">Day ' + nextDay + '</h4></div></div>' +
        '</div>' +
        '<div class="tw-p-8 tw-grid tw-grid-cols-1 lg:tw-grid-cols-12 tw-gap-8">' +
            '<div class="qtp-day-title-field tw-col-span-full"><div class="qtp-floating-field"><input type="text" name="day_title_' + nextDay + '" value="' + safeTitle + '" maxlength="255" oninput="syncQtpDayTitle(' + nextDay + ', this.value); updateQtpCharCount(this, &quot;day_title_count_' + nextDay + '&quot;);"><label>Day title</label></div><div class="qtp-char-count" id="day_title_count_' + nextDay + '">(' + rawTitle.length + '/255)</div></div>' +
            '<div class="qtp-day-description-panel tw-col-span-full tw-flex tw-flex-col tw-gap-3"><label class="tw-flex tw-items-center tw-gap-2 tw-text-sm tw-font-bold tw-text-slate-900">Description</label><textarea class="tinymce" name="desc_day_' + nextDay + '" style="min-height:300px;"></textarea></div>' +
            '<div class="qtp-sites-section tw-col-span-full"><div class="qtp-section-title">Site(s)</div><div class="qtp-site-row" id="qtp_sites_' + nextDay + '">' + qtpSitesHtml(nextDay, prefill.sites || ['Jordan']) + '<input type="text" class="qtp-site-input" id="qtp_site_input_' + nextDay + '" placeholder="Type destination..." autocomplete="off" oninput="qtpSiteAutocomplete(' + nextDay + ', this.value)" onkeydown="qtpSiteInputKey(event, ' + nextDay + ')"><div class="qtp-site-dropdown" id="qtp_site_dropdown_' + nextDay + '"></div></div></div>' +
            '<div class="qtp-meal-section tw-col-span-full"><div class="qtp-section-title">Meal</div><label class="qtp-meal-option"><input type="radio" name="meal_type_' + nextDay + '" value="included" onchange="toggleQtpMeal(' + nextDay + ');"><span>Meals included</span><span class="qtp-meal-checks" id="qtp_meal_checks_' + nextDay + '"><label><input type="checkbox" name="meal_options_' + nextDay + '[]" value="breakfast"> breakfast</label><label><input type="checkbox" name="meal_options_' + nextDay + '[]" value="lunch"> lunch</label><label><input type="checkbox" name="meal_options_' + nextDay + '[]" value="dinner"> dinner</label></span></label><label class="qtp-meal-option"><input type="radio" name="meal_type_' + nextDay + '" value="none" checked onchange="toggleQtpMeal(' + nextDay + ');"><span>No meals</span></label></div>' +

            '<div class="qtp-services-section tw-col-span-full"><div class="qtp-section-title">Add a service:</div><div id="qtp_services_' + nextDay + '">' + qtpServicesChipsHtml(nextDay, prefill.services || []) + '</div><div class="qtp-service-buttons"><button type="button" onclick="addQtpService(' + nextDay + ', &quot;Activity&quot;);">Activity</button><button type="button" onclick="addQtpService(' + nextDay + ', &quot;Transport&quot;);">Transport</button><button type="button" onclick="addQtpService(' + nextDay + ', &quot;Accommodation&quot;);">Accommodation</button><button type="button" onclick="addQtpService(' + nextDay + ', &quot;Restaurant&quot;);">Restaurant</button><button type="button" onclick="addQtpService(' + nextDay + ', &quot;Guide&quot;);">Guide</button><button type="button" onclick="addQtpService(' + nextDay + ', &quot;Other&quot;);">Other</button></div></div>' +
            '<div class="qtp-day-side-panel tw-col-span-full tw-flex tw-flex-col tw-gap-8">' +
                '<div id="expense_list_' + nextDay + '" class="tw-hidden"></div>' +
                '<div class="tw-grid tw-grid-cols-2 tw-gap-4">' +
                    '<div class="tw-p-4 tw-bg-emerald-50/50 tw-rounded-2xl tw-border tw-border-emerald-100/50 tw-min-h-[100px]"><span class="tw-text-[11px] tw-font-black tw-text-emerald-600 tw-uppercase tw-tracking-widest tw-block tw-mb-2">Included</span><div id="day_inc_' + nextDay + '" class="tw-flex tw-flex-col tw-gap-1"></div></div>' +
                    '<div class="tw-p-4 tw-bg-rose-50/50 tw-rounded-2xl tw-border tw-border-rose-100/50 tw-min-h-[100px]"><span class="tw-text-[11px] tw-font-black tw-text-rose-600 tw-uppercase tw-tracking-widest tw-block tw-mb-2">Excluded</span><div id="day_exc_' + nextDay + '" class="tw-flex tw-flex-col tw-gap-1"></div></div>' +
                '</div>' +
            '</div>' +
            '<div class="qtp-day-photos-panel tw-col-span-full tw-pt-8 tw-border-t tw-border-slate-100"><div class="tw-flex tw-justify-between tw-items-center tw-mb-4"><label class="tw-flex tw-items-center tw-gap-3 tw-text-sm tw-font-bold tw-text-slate-900"><span>Photos</span><a href="javascript:void(0);" class="tw-text-emerald-700 tw-font-black tw-no-underline">How to choose the right photos?</a></label><button type="button" class="image_selector tw-px-3 tw-py-1.5 tw-bg-white tw-text-slate-500 tw-border tw-border-slate-200 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wider tw-rounded-lg" data-input-name="day_images_' + nextDay + '"><i class="fa fa-camera tw-mr-1"></i> Add photo</button></div><div id="images_' + nextDay + '" class="tw-flex tw-flex-wrap tw-gap-4"></div></div>' +
        '</div>';
    container.appendChild(panel);
    if (prefill.description) {
        panel.querySelector('textarea.tinymce').value = prefill.description;
    }
    var imageBox = document.getElementById('images_' + nextDay);
    if (imageBox) imageBox.innerHTML = qtpImageItemsHtml(nextDay, prefill.images || []);
    var expBox = document.getElementById('expense_list_' + nextDay);
    if (expBox) expBox.innerHTML = qtpExpenseItemsHtml(nextDay, prefill.expenses || []);
    var incBox = document.getElementById('day_inc_' + nextDay);
    if (incBox) incBox.innerHTML = qtpChecklistItemsHtml(nextDay, prefill.included || [], 'included');
    var excBox = document.getElementById('day_exc_' + nextDay);
    if (excBox) excBox.innerHTML = qtpChecklistItemsHtml(nextDay, prefill.excluded || [], 'excluded');
    toggleQtpMeal(nextDay);
    selectQtpDay(nextDay, nav);
    if (window.initImageSelector) window.initImageSelector();
    qtpToast((prefill.title ? 'Canned day added' : 'New day added') + '. Save quotation to keep it.');
}

function recalcQtpPrice() {
    var currency = document.getElementById('qtp_currency') ? document.getElementById('qtp_currency').value : 'JOD';
    var total = 0;
    document.querySelectorAll('.qtp-pax-price').forEach(function(input) {
        total += parseFloat(input.value || '0') || 0;
    });
    var base = parseFloat(document.getElementById('qtp_base_cost').value || '0') || 0;
    var profit = total - base;
    document.getElementById('qtp_total_display').textContent = total.toFixed(2);
    document.getElementById('qtp_currency_label').textContent = currency;
    document.getElementById('qtp_profit_amount').value = profit.toFixed(2);
    document.getElementById('qtp_profit_display').value = currency + ' ' + profit.toFixed(2);
}

function applyQtpPriceToAll() {
    var first = document.querySelector('.qtp-pax-price');
    if (!first) return;
    document.querySelectorAll('.qtp-pax-price').forEach(function(input) {
        input.value = first.value;
    });
    recalcQtpPrice();
}

function syncQtpPriceFromCost() {
    var base = parseFloat(document.getElementById('qtp_base_cost').value || '0') || 0;
    var inputs = document.querySelectorAll('.qtp-pax-price');
    var perPax = inputs.length ? base / inputs.length : base;
    inputs.forEach(function(input) {
        input.value = perPax.toFixed(2);
    });
    recalcQtpPrice();
}

function saveQtpPrice() {
    recalcQtpPrice();
    fetch(qtpProfitUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': qtpCsrf },
        body: JSON.stringify({ profit_amount: document.getElementById('qtp_profit_amount').value })
    }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.success) {
            qtpToast('Pricing saved');
        } else {
            qtpToast('Pricing save failed');
        }
    }).catch(function() {
        qtpToast('Pricing save failed');
    });
}

function qtpToast(message) {
    var old = document.querySelector('.qtp-toast');
    if (old) old.remove();
    var toast = document.createElement('div');
    toast.className = 'qtp-toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(function() { toast.remove(); }, 2200);
}

function toggleCatDropdown(btn) {
    var submenu = btn.nextElementSibling;
    var isOpen = !submenu.classList.contains('tw-hidden');
    closeCatDropdowns();
    if (!isOpen) submenu.classList.remove('tw-hidden');
}

function closeCatDropdowns() {
    document.querySelectorAll('.cat-submenu').forEach(function(m) { m.classList.add('tw-hidden'); });
}

function toggleCountryDropdown(dayNumber) {
    var dd = document.getElementById('country_dropdown_' + dayNumber);
    if (dd) dd.classList.toggle('tw-hidden');
}

function loadCountryCategories(dayNumber, countryId, countryName) {
    var dd = document.getElementById('country_dropdown_' + dayNumber);
    if (dd) dd.classList.add('tw-hidden');
    document.getElementById('expense_day_number').value = dayNumber;
    window.location.hash = 'expense_modal';
    document.getElementById('modal_left_panel').innerHTML = '<div class="tw-p-10 tw-text-center"><i class="fa fa-spinner fa-spin tw-text-orange-500"></i></div>';
    
    fetch('/admin/ajax/get-country-categories?country_id=' + countryId)
        .then(function(r) { return r.json(); })
        .then(function(categories) {
            var html = '';
            categories.forEach(function(cat) {
                html += '<div class="modal-cat-item tw-mb-2" data-name="' + (cat.name || '').toLowerCase() + '">';
                html += '<div class="tw-flex tw-items-center tw-gap-3"><label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer"><input type="radio" name="modal_cat_radio" value="' + cat.id + '" onclick="loadModalServices(' + cat.id + ');" class="!tw-w-4 !tw-h-4"> <span class="tw-text-sm tw-font-medium tw-text-slate-600">' + cat.name + '</span></label></div></div>';
            });
            document.getElementById('modal_left_panel').innerHTML = html;
        });
}

function loadSubCategoryServices(dayNumber, categoryId, categoryName) {
    document.getElementById('expense_day_number').value = dayNumber;
    window.location.hash = 'expense_modal';
    loadModalServices(categoryId);
}

function loadModalServices(categoryId, venderFilter) {
    var url = '/admin/expenses/services?category=' + categoryId;
    if (venderFilter) url += '&vender=' + venderFilter;
    document.getElementById('modal_right_table').querySelector('tbody').innerHTML = '<tr><td colspan="3" class="tw-py-20 tw-text-center"><i class="fa fa-spinner fa-spin tw-text-3xl tw-text-orange-500"></i></td></tr>';
    
    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var tHtml = '';
            data.services.forEach(function(s) {
                tHtml += '<tr class="tw-border-b tw-border-slate-50"><td class="tw-py-4 tw-px-8 tw-text-xs tw-font-bold">' + s.description + '</td><td class="tw-py-4"><span class="tw-text-xs tw-font-bold tw-text-emerald-600">' + s.cost + ' JOD</span></td><td class="tw-py-4 tw-px-8 tw-text-right"><button type="button" class="btn orange !tw-text-[11px] !tw-px-3 !tw-py-1.5 modal-select-btn" data-sid="' + s.id + '" data-sdesc="' + (s.description || '').replace(/"/g, '&quot;') + '">Select</button></td></tr>';
            });
            document.getElementById('modal_right_table').querySelector('tbody').innerHTML = tHtml;
            document.querySelectorAll('.modal-select-btn').forEach(function(btn) {
                btn.onclick = function() {
                    addServiceExpense(document.getElementById('expense_day_number').value, this.dataset.sid, this.dataset.sdesc);
                    this.innerHTML = 'Added'; this.classList.replace('orange', 'emerald'); this.disabled = true;
                };
            });
        });
}

function addServiceExpense(dayNumber, serviceId, description) {
    var expList = document.getElementById('expense_list_' + dayNumber);
    var key = Date.now();
    var div = document.createElement('div');
    div.className = 'tw-flex tw-items-center tw-justify-between tw-p-3 tw-bg-white tw-border tw-border-slate-100 tw-rounded-xl tw-shadow-sm tw-group';
    div.innerHTML = `<div class="tw-flex tw-flex-col"><span class="tw-text-xs tw-font-bold tw-text-slate-700">${description}</span></div><div class="tw-flex tw-items-center tw-gap-3"><input type="number" name="expenses_qty_${dayNumber}[${key}]" value="1" min="1" class="!tw-w-14 !tw-h-8 !tw-px-2 !tw-py-0 !tw-text-center !tw-text-xs !tw-font-bold"><input type="hidden" name="expenses_day_${dayNumber}[${key}]" value="${serviceId}"><input type="hidden" name="expenses_name_${dayNumber}[${key}]" value="${description}"><button type="button" onclick="this.closest('.tw-flex').remove();" class="tw-text-rose-500"><i class="fa fa-trash"></i></button></div>`;
    expList.appendChild(div);
}

function addInclusion(day) { document.getElementById('inclusion_day').value = day; window.location.hash = 'add_inclusion'; }
function filterInclusions() {
    var q = document.getElementById('inclusion_search').value.toLowerCase();
    document.querySelectorAll('.inclusion-row').forEach(function(r) { r.style.display = r.dataset.name.indexOf(q) !== -1 ? 'flex' : 'none'; });
}

function addInclusionItem(btn, type, text) {
    var day = document.getElementById('inclusion_day').value;
    var isInc = type === 'included';
    var container = document.getElementById(isInc ? 'day_inc_' + day : 'day_exc_' + day);
    var key = Date.now() + Math.floor(Math.random()*100);
    var div = document.createElement('div');
    div.className = 'tw-flex tw-items-center tw-justify-between tw-p-2 tw-bg-white tw-rounded-lg tw-border tw-border-slate-100 tw-group';
    div.innerHTML = `<span class="tw-text-[11px] tw-font-medium tw-text-slate-600 tw-flex tw-items-center tw-gap-2"><i class="fa ${isInc ? 'fa-check tw-text-emerald-500' : 'fa-times tw-text-rose-500'}"></i> ${text}</span><input type="hidden" name="${isInc?'day_inc_':'day_exc_'}${day}[${key}]" value="${text}"><button type="button" onclick="this.parentElement.remove();" class="tw-text-rose-400 group-hover:tw-text-rose-600"><i class="fa fa-times-circle"></i></button>`;
    container.appendChild(div);
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.cat-btn') && !e.target.closest('.cat-submenu')) closeCatDropdowns();
    if (!e.target.closest('.country-add-wrapper')) document.querySelectorAll('.country-dropdown').forEach(function(dd) { dd.classList.add('tw-hidden'); });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.qtp-meal-checks').forEach(function(group) {
        toggleQtpMeal(group.id.replace('qtp_meal_checks_', ''));
    });
});

function initQuotationTinyMce(el) {
    if (!el || el.dataset.tinymceReady) return;
    el.dataset.tinymceReady = '1';
    tinymce.init({
        target: el,
        plugins: ["advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker", "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking", "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"],
        toolbar1: "bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | code fullscreen",
        menubar: false, toolbar_items_size: 'small', height: 350,
        content_style: "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'); body { font-family: 'Inter', sans-serif; font-size: 14px; padding: 20px; }"
    });
}

document.getElementById('quotation_form').addEventListener('submit', function() { tinymce.triggerSave(); });
</script>
@endsection
