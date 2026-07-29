@extends('admin.layouts.app')
@section('content')
<div class="breadcrumb pad-t"><a class="active"><i class="fa-magic"></i> Tour Auto-Advisor</a></div>
<div class="sd-12"><h3><i class="fa-magic"></i> Tour Auto-Advisor</h3></div>
<div class="row"><div class="bordered pad">
    <p>Automatically recommend tours based on customer preferences and travel dates.</p>
    <div class="row pad"><label>Number of Travelers</label><input type="number" class="full-width" placeholder="e.g. 4"></div>
    <div class="row pad"><label>Travel Date</label><input type="date" class="full-width"></div>
    <div class="row pad"><label>Budget (USD)</label><input type="number" class="full-width" placeholder="e.g. 2000"></div>
    <div class="row pad"><label>Interests</label>
        <select class="full-width" multiple><option>Adventure</option><option>Cultural</option><option>Religious</option><option>Beach</option><option>Desert</option></select>
    </div>
    <hr><button class="btn blue gap-t"><i class="fa-search"></i> Find Tours</button>
    <div id="advisor_results" class="pad-t"></div>
</div></div>
@endsection
