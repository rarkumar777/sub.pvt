<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Your Trip - PVT Travels</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;min-height:100vh;background:#f5f5f0;}
.top-bar{background:#f97316;padding:14px 30px;display:flex;align-items:center;justify-content:space-between;}
.top-bar .logo{color:#fff;font-size:20px;font-weight:800;letter-spacing:-0.5px;}
.top-bar .logo span{color:#e8b445;}
.top-bar .close-btn{color:#fff;font-size:20px;cursor:pointer;opacity:.7;text-decoration:none;transition:opacity .2s;}
.top-bar .close-btn:hover{opacity:1}
.hero{background:url('/images/jordan_bg.png') center/cover no-repeat;height:200px;display:flex;align-items:center;justify-content:center;position:relative;}
.hero::before{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,0.3) 0%,rgba(0,0,0,0.5) 100%);}
.hero h1{position:relative;z-index:1;color:#fff;font-size:32px;font-weight:800;text-shadow:0 2px 12px rgba(0,0,0,0.4);letter-spacing:-0.3px;}
.wizard-container{max-width:760px;margin:-40px auto 40px;position:relative;z-index:2;}
.wizard-card{background:#fff;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.1);overflow:hidden;}
.stepper{display:flex;align-items:flex-start;justify-content:center;padding:20px 30px 16px;gap:0;}
.stepper-step{display:flex;flex-direction:column;align-items:center;position:relative;z-index:1;min-width:60px;}
.stepper-label{font-size:11px;font-weight:600;color:#aaa;margin-bottom:6px;text-align:center;max-width:100px;line-height:1.2;transition:color .3s;order:-1;}
.stepper-circle{width:28px;height:28px;border-radius:50%;border:2px solid #ccc;background:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#999;transition:all .35s ease;}
.stepper-step.active .stepper-circle{border-color:#f97316;background:#f97316;color:#fff;}
.stepper-step.active .stepper-label{color:#333;font-weight:700;}
.stepper-step.completed .stepper-circle{border-color:#f97316;background:#f97316;color:#fff;}
.stepper-step.completed .stepper-label{color:#f97316;}
.stepper-line{flex:1;height:0;border-top:2px dotted #ccc;min-width:30px;margin-top:32px;transition:border-color .4s ease;}
.stepper-line.done{border-color:#f97316;}
.wizard-body{padding:0 40px 20px;}
.step-header{background:#f5f5f0;border-radius:8px;padding:20px 24px;margin:24px 0 20px;}
.step-header h2{font-size:20px;font-weight:700;color:#1a1a1a;margin-bottom:4px;}
.step-header .sub{font-size:14px;color:#666;margin-bottom:0;line-height:1.5;}
.step{display:none}.step.active{display:block;animation:slideIn .35s ease}
@keyframes slideIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
@keyframes shake{0%,100%{transform:translateX(0)}20%,60%{transform:translateX(-6px)}40%,80%{transform:translateX(6px)}}
.shake{animation:shake .4s ease}
h2{font-size:20px;font-weight:700;color:#1a1a1a;margin-bottom:4px;}
.sub{font-size:14px;color:#666;margin-bottom:24px;line-height:1.6;}
.question{font-size:15px;font-weight:700;color:#333;margin-bottom:12px;margin-top:20px;}
/* Card radio options */
.radio-group{display:flex;flex-direction:column;gap:12px;margin-bottom:20px;}
.radio-card{display:flex;align-items:center;gap:14px;padding:16px;cursor:pointer;font-size:15px;color:#333;border:1.5px solid #eee;border-radius:8px;background:#fff;transition:all .2s;}
.radio-card:hover{border-color:#ccc;background:#fafafa;}
.radio-card.selected{border-color:#f97316;background:#fff7ed;}
.radio-card input[type=radio]{width:20px;height:20px;accent-color:#f97316;cursor:pointer;margin:0;flex-shrink:0;}
.radio-opt input[type=radio]{width:20px;height:20px;accent-color:#f97316;cursor:pointer;margin:0;flex-shrink:0;}
/* Pill options */
.pills-group{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;}
.pill-opt{border:1px solid #ccc;border-radius:24px;padding:10px 18px;font-size:14px;font-weight:600;color:#333;cursor:pointer;display:flex;align-items:center;gap:6px;background:#fff;transition:all .2s;}
.pill-opt:hover{border-color:#999;}
.pill-opt.selected{border-color:#f97316;color:#f97316;background:#fff7ed;}
.pill-opt input[type=radio]{display:none;}
/* Side by side buttons */
.side-buttons{display:flex;gap:16px;margin-bottom:20px;}
.side-btn{flex:1;text-align:center;border:1px solid #ccc;border-radius:8px;padding:14px;font-size:14px;font-weight:600;cursor:pointer;color:#333;background:#fff;transition:all .2s;}
.side-btn:hover{border-color:#999;}
.side-btn.selected{border-color:#f97316;color:#f97316;background:#fff7ed;}
.side-btn input[type=radio]{display:none;}
/* Plain checkbox options with separator */
.check-group{display:flex;flex-direction:column;gap:0;margin-bottom:20px;}
.check-opt{display:flex;align-items:center;gap:12px;cursor:pointer;font-size:15px;color:#333;padding:14px 4px;border:none;border-bottom:1px solid #eee;background:none;transition:color .2s;}
.check-opt:last-child{border-bottom:none;}
.check-opt:hover{color:#f97316;}
.check-opt input[type=checkbox]{width:20px;height:20px;accent-color:#f97316;cursor:pointer;flex-shrink:0;}
.counters{display:flex;gap:80px;margin-bottom:18px;justify-content:center;}
.counter-block{display:flex;flex-direction:column;align-items:center;}
.counter-block .cl{font-size:14px;font-weight:700;color:#333;margin-bottom:10px;text-align:center;}
.counter-block .cl small{font-weight:400;color:#999;font-size:12px;}
.counter-ctrl{display:flex;align-items:center;gap:12px;}
.counter-ctrl button{width:40px;height:40px;border-radius:50%;border:1.5px solid #ccc;background:#fff;cursor:pointer;font-size:20px;color:#555;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.counter-ctrl button:hover{border-color:#f97316;color:#f97316;background:#fff7ed;}
.counter-ctrl input{width:60px;height:40px;text-align:center;border:1px solid #ccc;border-radius:6px;font-size:16px;font-weight:600;}
.child-ages{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;}
.child-ages select{padding:8px 12px;border:1.5px solid #ddd;border-radius:6px;font-size:13px;background:#fff;transition:border-color .2s;}
.child-ages select:focus{border-color:#f97316;outline:none;}
.guide-sub{margin-left:28px;display:flex;flex-direction:column;gap:6px;margin-top:4px;margin-bottom:4px;}
.guide-sub .check-opt{padding:8px 12px;border:1px solid #ddd;}
.field{margin-bottom:16px;}
.field label{display:block;font-size:13px;font-weight:700;color:#333;margin-bottom:5px;}
.field label small{font-weight:400;color:#999;}
.field .hint{font-size:11px;color:#aaa;margin-bottom:6px;line-height:1.4;}
.field input,.field select,.field textarea{width:100%;padding:10px 12px;border:1.5px solid #ddd;border-radius:6px;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s;}
.field input:focus,.field select:focus,.field textarea:focus{border-color:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.08);}
.field textarea{min-height:100px;resize:vertical;}
.field .error-msg{color:#c0392b;font-size:11px;margin-top:4px;display:none;}
.budget-row{display:flex;gap:16px;margin-bottom:14px;}
.budget-field{flex:1;position:relative;}
.budget-field input{width:100%;padding:10px 12px 10px 30px;border:1.5px solid #ddd;border-radius:6px;font-size:15px;font-weight:600;transition:border-color .2s;}
.budget-field input:focus{border-color:#f97316;}
.budget-field .cur{position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:14px;color:#888;}
.budget-total{background:#f8f8f5;border-radius:8px;padding:12px 16px;font-size:13px;color:#666;margin-bottom:14px;border:1px solid #eee;}
.budget-total strong{color:#f97316;}
.personal-title{font-size:18px;font-weight:700;color:#333;text-align:center;margin-bottom:24px;line-height:1.4;}
.social-btns{display:flex;flex-direction:column;align-items:center;gap:10px;margin-bottom:16px;}
.social-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:320px;max-width:100%;padding:11px;border:1.5px solid #ddd;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;background:#fff;color:#333;transition:all .2s;}
.social-btn:hover{background:#f8f8f8;border-color:#999;box-shadow:0 2px 8px rgba(0,0,0,.06);}
.social-btn img{width:18px;height:18px;}
/* Or-divider with lines */
.or-divider{display:flex;align-items:center;gap:14px;margin:18px 0;color:#aaa;font-size:12px;font-weight:500;}
.or-divider::before,.or-divider::after{content:'';flex:1;height:1px;background:#ddd;}
/* Email row with validation icon */
.email-row{display:flex;gap:8px;margin-bottom:20px;position:relative;}
.email-row input{flex:1;padding:10px 36px 10px 12px;border:1.5px solid #ddd;border-radius:6px;font-size:14px;outline:none;transition:border-color .2s;}
.email-row input:focus{border-color:#f97316;}
.email-row .email-check{position:absolute;right:90px;top:50%;transform:translateY(-50%);font-size:16px;color:#f97316;opacity:0;transition:opacity .3s;}
.email-row .email-check.visible{opacity:1;}
.email-row button{padding:10px 22px;background:#e8b445;color:#fff;border:none;border-radius:6px;font-weight:700;font-size:14px;cursor:pointer;transition:all .2s;white-space:nowrap;}
.email-row button:hover{background:#d4a23c;box-shadow:0 2px 8px rgba(232,180,69,.3);}
.benefits{margin-top:24px;}
.benefits h4{font-size:14px;font-weight:700;color:#333;margin-bottom:14px;}
.benefits-grid{display:flex;gap:12px;}
.benefit-card{flex:1;background:#fff7ed;border-radius:10px;padding:18px 14px;text-align:center;transition:transform .2s;}
.benefit-card:hover{transform:translateY(-2px);}
.benefit-card i{font-size:26px;color:#f97316;margin-bottom:10px;display:block;}
.benefit-card p{font-size:11px;color:#555;line-height:1.4;}
.wizard-footer{display:flex;justify-content:space-between;align-items:center;padding:0 40px 24px;}
.btn-prev{padding:10px 18px;border:none;background:transparent;color:#f97316;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .2s;}
.btn-prev:hover{text-decoration:underline}
.btn-next{padding:11px 30px;border:none;border-radius:6px;background:#e8b445;color:#fff;font-size:13px;font-weight:700;cursor:pointer;transition:all .25s;box-shadow:0 2px 8px rgba(232,180,69,.2);}
.btn-next:hover{background:#d4a23c;box-shadow:0 4px 12px rgba(232,180,69,.35);transform:translateY(-1px);}
.btn-next:active{transform:translateY(0);}
.btn-next:disabled{opacity:.6;cursor:not-allowed;transform:none;box-shadow:none;}
.btn-next .spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;margin-right:6px;vertical-align:middle;}
@keyframes spin{to{transform:rotate(360deg)}}
.success-screen{text-align:center;padding:50px 30px;}
.success-screen .check-circle{width:64px;height:64px;border-radius:50%;background:#fff7ed;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;}
.success-screen .check-circle i{font-size:32px;color:#f97316;}
.success-screen h2{color:#f97316;margin-bottom:8px;font-size:24px;}
.success-screen .sub{font-size:14px;color:#666;margin-bottom:20px;}
/* Consent checkboxes - no card border */
.consent-opt{display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:12px;color:#555;line-height:1.5;padding:0;border:none;background:none;}
.consent-opt input[type=checkbox]{margin-top:2px;flex-shrink:0;width:18px;height:18px;accent-color:#f97316;cursor:pointer;}
/* Inline civility radios - no card border */
.civility-group{display:flex;gap:20px;margin-top:4px;}
.civility-group .radio-opt{border:none;padding:0;background:none;}
.civility-group .radio-opt:hover{background:none;}
@media(max-width:600px){.wizard-body{padding:20px 16px 12px;}.wizard-footer{padding:0 16px 16px;}.budget-row,.counters{flex-direction:column;gap:12px;}.hero h1{font-size:22px;}.benefits-grid{flex-direction:column;}.social-btn{width:100%;}.email-row{flex-direction:column;}.email-row .email-check{right:12px;}}
</style>
</head>
<body>
<div class="top-bar">
    <div class="logo">PVT <span>Travels</span></div>
    <a href="/" class="close-btn">✕</a>
</div>
<div class="hero"><h1>Create your trip to Jordan</h1></div>
<div class="wizard-container">
    <div class="wizard-card">
        <div class="stepper" id="wizardStepper">
            <div class="stepper-step active" data-step="1"><div class="stepper-circle">1</div><div class="stepper-label">Planning</div></div>
            <div class="stepper-line" data-line="1"></div>
            <div class="stepper-step" data-step="2"><div class="stepper-circle">2</div><div class="stepper-label">Participants and dates</div></div>
            <div class="stepper-line" data-line="2"></div>
            <div class="stepper-step" data-step="3"><div class="stepper-circle">3</div><div class="stepper-label">Travel plan</div></div>
            <div class="stepper-line" data-line="3"></div>
            <div class="stepper-step" data-step="4"><div class="stepper-circle">4</div><div class="stepper-label">Budget</div></div>
            <div class="stepper-line" data-line="4"></div>
            <div class="stepper-step" data-step="5"><div class="stepper-circle">5</div><div class="stepper-label">Personal Space</div></div>
        </div>
        <div class="wizard-body">
            <!-- STEP 1 -->
            <div class="step active" id="step1">
                <div class="step-header">
                    <h2>Let's go!</h2>
                    <p class="sub">We will forward your request to the local agency that will handle your trip. To do this, you simply need to answer a few questions.</p>
                </div>
                <div class="question">Where are you in your project?</div>
                <div class="radio-group" id="projectStage">
                    <label class="radio-card"><input type="radio" name="project_stage" value="looking" onchange="formData.project_stage=this.value; updateRadioCards('projectStage')"> I'm just looking for ideas and inspiration</label>
                    <label class="radio-card"><input type="radio" name="project_stage" value="planning" onchange="formData.project_stage=this.value; updateRadioCards('projectStage')"> I want to start planning my trip</label>
                    <label class="radio-card"><input type="radio" name="project_stage" value="booking" onchange="formData.project_stage=this.value; updateRadioCards('projectStage')"> I'd like to book my trip soon</label>
                </div>
            </div>
            <!-- STEP 2 -->
            <div class="step" id="step2">
                <div class="step-header">
                    <h2>Tell us a little more...</h2>
                    <p class="sub">Your answers will allow the local agency to offer you a tailor-made trip.</p>
                </div>
                <div class="question">Who are you going with?</div>
                <div class="pills-group" id="partGroup">
                    <label class="pill-opt"><input type="radio" name="participant" value="Solo" onchange="setPart(this.value); updatePills('partGroup')"> 👤 Solo</label>
                    <label class="pill-opt"><input type="radio" name="participant" value="Couple" onchange="setPart(this.value); updatePills('partGroup')"> 💑 Couple</label>
                    <label class="pill-opt"><input type="radio" name="participant" value="Family" onchange="setPart(this.value); updatePills('partGroup')"> 👨‍👩‍👧 Family</label>
                    <label class="pill-opt"><input type="radio" name="participant" value="Friends" onchange="setPart(this.value); updatePills('partGroup')"> 👥 Friends</label>
                    <label class="pill-opt"><input type="radio" name="participant" value="Group" onchange="setPart(this.value); updatePills('partGroup')"> 🌍 Group</label>
                </div>
                <div id="participantError" style="display:none;color:#c0392b;font-size:13px;font-weight:600;margin-top:-10px;margin-bottom:12px;">⚠️ Please select who you are going with.</div>
                <div id="groupTypeBlock" style="display:none;margin-bottom:18px;">
                    <div class="question">What type of group?</div>
                    <div class="radio-group">
                        <label class="radio-opt"><input type="radio" name="group_type" value="Club / Association" onchange="formData.group_type=this.value"> Club / Association</label>
                        <label class="radio-opt"><input type="radio" name="group_type" value="Works council" onchange="formData.group_type=this.value"> Works council</label>
                        <label class="radio-opt"><input type="radio" name="group_type" value="Seminar" onchange="formData.group_type=this.value"> Seminar</label>
                        <label class="radio-opt"><input type="radio" name="group_type" value="Other" onchange="formData.group_type=this.value"> Other</label>
                    </div>
                </div>
                <div id="honeymoonBlock" style="display:none;margin-bottom:18px;">
                    <div class="question">For what occasion?</div>
                    <label class="check-opt" style="border-bottom:none;"><input type="checkbox" id="honeymoonCheck" value="honeymoon" onchange="formData.is_honeymoon=this.checked?1:0"> It's a honeymoon.</label>
                </div>
                <div class="counters" id="countersBlock" style="display:none;">
                    <div class="counter-block"><div class="cl">Adults</div><div class="counter-ctrl"><button type="button" onclick="changeCount('adults',-1)">−</button><input type="text" id="adultsCount" value="1" readonly><button type="button" onclick="changeCount('adults',1)">+</button></div></div>
                    <div class="counter-block"><div class="cl">Children <small>(under 18)</small></div><div class="counter-ctrl"><button type="button" onclick="changeCount('children',-1)">−</button><input type="text" id="childrenCount" value="0" readonly><button type="button" onclick="changeCount('children',1)">+</button></div></div>
                </div>
                <div class="child-ages" id="childAges"></div>
                <div class="question">Do you know the exact dates of your trip?</div>
                <div class="side-buttons" id="exactDatesGroup">
                    <label class="side-btn"><input type="radio" name="exactDates" value="yes" onchange="toggleDates('yes'); updateSideBtns('exactDatesGroup')"> Yes, exact dates</label>
                    <label class="side-btn"><input type="radio" name="exactDates" value="no" onchange="toggleDates('no'); updateSideBtns('exactDatesGroup')"> No, I'm flexible</label>
                </div>
                <div id="exactDateFields" style="display:none;margin-bottom:14px;">
                    <div style="display:flex;gap:16px;align-items:flex-end;">
                        <div class="field" style="flex:1"><label>Departure date</label><input type="date" id="depDate"></div>
                        <div style="font-size:18px;color:#999;padding-bottom:12px;">→</div>
                        <div class="field" style="flex:1"><label>Return date</label><input type="date" id="retDate"></div>
                    </div>
                </div>
                <div id="approxDateFields" style="display:none;margin-bottom:14px;">
                    <div class="question" style="margin-bottom:10px;">When do you want to leave?</div>
                    <div style="display:flex;gap:16px;">
                        <div class="field" style="flex:1"><select id="departurePeriod" onchange="formData.departure_period=this.value">
                            <option value="">Departure period</option>
                            <option value="advise_me">Advise me</option>
                        </select></div>
                        <div class="field" style="flex:1"><select id="approxDuration" onchange="formData.approx_duration=this.value">
                            <option value="">Approximate duration</option>
                            <option value="advise_me">Advise me</option>
                            <option value="less_than_week">Less than a week</option>
                            <option value="1 week">1 week</option>
                            <option value="2 weeks">2 weeks</option>
                            <option value="3 weeks">3 weeks</option>
                            <option value="more_than_3_weeks">More than 3 weeks</option>
                        </select></div>
                    </div>
                </div>
            </div>
            <!-- STEP 3 -->
            <div class="step" id="step3">
                <div class="question" style="margin-top:0;font-size:18px;">What type of accommodation?</div>
                <div style="font-size:13px;color:#999;margin-bottom:16px;">Select your preferences (you can choose multiple)</div>
                <div class="check-group" id="accPrefs">
                    @foreach($accommodationCategories as $cat)
                        @php
                            $icon = '🏨';
                            $nameLower = strtolower($cat->name);
                            if (str_contains($nameLower, 'camp')) $icon = '🏕️';
                            elseif (str_contains($nameLower, 'homestay') || str_contains($nameLower, 'guest')) $icon = '🏡';
                            elseif (str_contains($nameLower, 'wild')) $icon = '🌿';
                            elseif (str_contains($nameLower, 'luxury') || str_contains($nameLower, 'star')) $icon = '✨';
                        @endphp
                        <label class="check-opt"><input type="checkbox" value="{{ $cat->name }}" onchange="toggleAcc(this)"> {{ $icon }} {{ $cat->name }}</label>
                    @endforeach
                </div>
                <div class="field" style="margin-top:24px;">
                    <label style="text-transform:uppercase;font-size:11px;color:#888;">Your travel plan in a nutshell (optional)</label>
                    <div class="hint" style="color:#aaa;">E.g. the desired stages and activities, what you like, what you don't like, your questions about this trip...</div>
                    <textarea id="travelPlan" placeholder="Tell us about your dream trip..."></textarea>
                </div>
                <div class="question" style="margin-top:24px;font-size:15px;">During your stay</div>
                <div style="font-size:13px;color:#999;margin-bottom:16px;">Select your preferences (you can choose multiple)</div>
                <div class="check-group" id="duringStay">
                    <label class="check-opt"><input type="checkbox" value="car-driver" onchange="toggleGuide(this)"> 🚗 Car with a local English-speaking driver</label>
                    <label class="check-opt"><input type="checkbox" value="rental-car" onchange="toggleGuide(this)"> 🚙 Rental car</label>
                    <label class="check-opt"><input type="checkbox" value="join-group" onchange="toggleGuide(this)"> 👥 Would agree to join an already formed group</label>
                    <label class="check-opt"><input type="checkbox" value="guided-tour" onchange="toggleGuide(this)"> 🧭 Guided tour</label>
                    <label class="check-opt"><input type="checkbox" value="local-guides" onchange="toggleGuide(this)"> 🗺️ Local guides on site</label>
                </div>
            </div>
            <!-- STEP 4 -->
            <div class="step" id="step4">
                <div class="question" style="margin-top:0;font-size:18px;">What's your budget? <small style="font-size:13px;color:#999;font-weight:400">(optional)</small></div>
                <p style="font-size:12px;color:#aaa;margin-bottom:20px;">Per person, excluding international flights</p>
                <div style="display:flex;gap:16px;margin-bottom:16px;">
                    <div class="budget-field" style="flex:1;">
                        <span class="cur" style="color:#666;font-weight:600;left:14px;">$</span>
                        <input type="number" id="idealBudget" placeholder="Ideal budget" oninput="calcTotal()" style="padding-left:32px;font-weight:400;font-size:14px;height:44px;">
                    </div>
                    <div class="budget-field" style="flex:1;">
                        <span class="cur" style="color:#666;font-weight:600;left:14px;">$</span>
                        <input type="number" id="maxBudget" placeholder="Maximum budget" oninput="calcTotal()" style="padding-left:32px;font-weight:400;font-size:14px;height:44px;">
                    </div>
                </div>
                <div id="budgetTotal" style="background:#f9f9f9;padding:14px 16px;border-radius:8px;font-size:12px;color:#666;display:none;">
                    Estimated total for <strong id="personsCount" style="color:#f97316">1</strong> persons: <strong id="totalAmount" style="color:#f97316">$0</strong>
                </div>
            </div>
            <!-- STEP 5 -->
            <div class="step" id="step5">
                @auth
                <!-- Logged-in user: skip sign-in, show direct submit -->
                <div class="personal-title">You are logged in as <strong>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong></div>
                <p style="text-align:center;color:#666;font-size:14px;margin-bottom:24px;">Click below to submit your travel request.</p>
                <button type="button" class="btn-next" id="submitBtn" style="width:100%;padding:14px;font-size:15px;" onclick="submitForm()">Send my request</button>
                @else
                <!-- Guest user: show sign-in form -->
                <div class="personal-title">Create your personal space to send your travel request to the local agency</div>
                <div class="social-btns">
                    <button class="social-btn" type="button"><img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="G"> Sign in with Google</button>
                    <button class="social-btn" type="button"><i class="fa fa-apple" style="font-size:18px"></i> Sign in with Apple</button>
                </div>
                <div class="or-divider">or</div>
                <div class="field"><label>Continue with an email address<span style="color:#c0392b">*</span></label></div>
                <div class="email-row">
                    <input type="email" id="email" placeholder="your@email.com" oninput="checkEmailIcon()">
                    <span class="email-check" id="emailCheck"><i class="fa fa-check-circle"></i></span>
                    <button type="button" id="emailOkBtn" onclick="showRegForm()">Ok</button>
                </div>
                <div id="emailConfirmRow" style="display:none;margin-bottom:16px;">
                    <div class="field" style="margin-bottom:0;">
                        <label>Confirm email address<span style="color:#c0392b">*</span></label>
                        <input type="email" id="emailConfirm" placeholder="">
                        <div id="emailMatchErr" style="color:#c0392b;font-size:11px;margin-top:4px;display:none;">Email addresses do not match.</div>
                    </div>
                </div>
                <!-- Registration form (hidden until email Ok) -->
                <div id="regFormBlock" style="display:none;">
                    <div class="field">
                        <label>Create a password<span style="color:#c0392b">*</span></label>
                        <div style="display:flex;border:1.5px solid #ddd;border-radius:6px;overflow:hidden;transition:border-color .2s;" id="pwdWrap1">
                            <input type="password" id="regPassword" placeholder="" style="flex:1;border:none;outline:none;padding:10px 12px;font-size:14px;font-family:inherit;background:transparent;" onfocus="document.getElementById('pwdWrap1').style.borderColor='#f97316'" onblur="document.getElementById('pwdWrap1').style.borderColor='#ddd'">
                            <button type="button" onclick="togglePwd('regPassword','eyeIcon1')" id="eyeIcon1" style="border:none;background:none;padding:0 12px;cursor:pointer;color:#aaa;font-size:16px;flex-shrink:0;line-height:1;"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="field">
                        <label>Confirm password<span style="color:#c0392b">*</span></label>
                        <div style="display:flex;border:1.5px solid #ddd;border-radius:6px;overflow:hidden;transition:border-color .2s;" id="pwdWrap2">
                            <input type="password" id="regPasswordConfirm" placeholder="" style="flex:1;border:none;outline:none;padding:10px 12px;font-size:14px;font-family:inherit;background:transparent;" onfocus="document.getElementById('pwdWrap2').style.borderColor='#f97316'" onblur="document.getElementById('pwdWrap2').style.borderColor='#ddd'">
                            <button type="button" onclick="togglePwd('regPasswordConfirm','eyeIcon2')" id="eyeIcon2" style="border:none;background:none;padding:0 12px;cursor:pointer;color:#aaa;font-size:16px;flex-shrink:0;line-height:1;"><i class="fa fa-eye"></i></button>
                        </div>
                        <div id="pwdMatchErr" style="color:#c0392b;font-size:11px;margin-top:4px;display:none;">⚠ Passwords do not match.</div>
                    </div>
                    <div class="field">
                        <label>Civility<span style="color:#c0392b">*</span></label>
                        <div class="civility-group">
                            <label class="radio-opt"><input type="radio" name="civility" value="Mrs" onchange="formData.civility=this.value"> Mrs.</label>
                            <label class="radio-opt"><input type="radio" name="civility" value="Mr" onchange="formData.civility=this.value"> Mr.</label>
                        </div>
                    </div>
                    <div style="display:flex;gap:16px;">
                        <div class="field" style="flex:1"><label>First name<span style="color:#c0392b">*</span></label><input type="text" id="regFirstName" placeholder=""></div>
                        <div class="field" style="flex:1"><label>Last name<span style="color:#c0392b">*</span></label><input type="text" id="regLastName" placeholder=""></div>
                    </div>
                    <div style="display:flex;gap:16px;">
                        <div class="field" style="flex:1">
                            <label>Country of residence</label>
                            <select id="regCountry">
                                <option value="">Select</option>
                                <option value="US">United States</option>
                                <option value="UK">United Kingdom</option>
                                <option value="FR">France</option>
                                <option value="DE">Germany</option>
                                <option value="IN">India</option>
                                <option value="JO">Jordan</option>
                                <option value="AE">UAE</option>
                                <option value="SA">Saudi Arabia</option>
                                <option value="CA">Canada</option>
                                <option value="AU">Australia</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="field" style="flex:1"><label>Phone<span style="color:#c0392b">*</span></label><input type="tel" id="regPhone" placeholder=""></div>
                    </div>
                    <div class="field">
                        <label>Date of birth<span style="color:#c0392b">*</span></label>
                        <div style="display:flex;gap:8px;margin-top:4px;">
                            <input type="text" id="dobDay" placeholder="DD" maxlength="2" style="width:60px;text-align:center;">
                            <input type="text" id="dobMonth" placeholder="MM" maxlength="2" style="width:60px;text-align:center;">
                            <input type="text" id="dobYear" placeholder="YYYY" maxlength="4" style="width:80px;text-align:center;">
                        </div>
                    </div>
                    <div style="margin:14px 0;">
                        <label class="consent-opt">
                            <input type="checkbox" id="marketingConsent">
                            I agree to receive communications by email, SMS and WhatsApp: Personalized advice, notifications related to the follow-up of my travel projects, alternative destinations & news.
                        </label>
                    </div>
                    <div style="margin-bottom:18px;">
                        <label class="consent-opt">
                            <input type="checkbox" id="termsConsent">
                            I have read and accept the <a href="#" style="color:#f97316;text-decoration:underline;">Terms and Conditions</a>.<span style="color:#c0392b">*</span>
                        </label>
                    </div>
                    <button type="button" class="btn-next" id="submitBtn" style="width:100%;padding:14px;font-size:15px;" onclick="submitForm()">Send my request</button>
                </div>
                <div class="benefits" id="benefitsBlock">
                    <h4>Why create my personal space?</h4>
                    <div class="benefits-grid">
                        <div class="benefit-card"><i class="fa fa-comments-o"></i><p>Communicate directly with your local agency</p></div>
                        <div class="benefit-card"><i class="fa fa-info-circle"></i><p>Consult your documents, quotes, payments...</p></div>
                        <div class="benefit-card"><i class="fa fa-mobile"></i><p>Access these services on the mobile app</p></div>
                    </div>
                </div>
                @endauth
            </div>
            <!-- Success -->
            <div class="step" id="stepSuccess">
                <div class="success-screen">
                    <div class="check-circle"><i class="fa fa-check"></i></div>
                    <h2>Thank you!</h2>
                    <p class="sub" id="successMsg">Your trip request has been submitted successfully.<br>Our local agency will contact you shortly to start planning your perfect trip.</p>
                    <a href="/" class="btn-next" style="display:inline-block;margin-top:8px;text-decoration:none;">Back to Home</a>
                </div>
            </div>
        </div>
        <div class="wizard-footer" id="wizardFooter">
            <button class="btn-prev" id="btnPrev" onclick="prevStep()" style="visibility:hidden"><i class="fa fa-chevron-left"></i> Previous</button>
            <button class="btn-next" id="btnNext" onclick="nextStep()">Next step</button>
        </div>
    </div>
</div>
<script>
var currentStep=1,totalSteps=5,formData={project_stage:'',participant_type:'',adults:1,children:0,children_ages:[],is_honeymoon:0,has_exact_dates:0,departure_date:'',return_date:'',departure_period:'',approx_duration:'',travel_styles:[],accommodation_prefs:[],travel_plan:'',guide_type:'',guide_languages:[],ideal_budget:'',max_budget:'',currency:'USD',civility:'',first_name:'',last_name:'',email:'',phone:'',password:'',country:'',dob:'',marketing_consent:0,terms_consent:0};

// Email validation icon
function checkEmailIcon(){
    var e=document.getElementById('email').value.trim();
    var chk=document.getElementById('emailCheck');
    if(e&&/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e)){chk.classList.add('visible');}
    else{chk.classList.remove('visible');}
}

function updateRadioCards(groupId) {
    document.querySelectorAll('#'+groupId+' .radio-card').forEach(function(l){ l.classList.remove('selected'); });
    var chk = document.querySelector('#'+groupId+' input:checked');
    if(chk) chk.parentElement.classList.add('selected');
}
function updatePills(groupId) {
    document.querySelectorAll('#'+groupId+' .pill-opt').forEach(function(l){ l.classList.remove('selected'); });
    var chk = document.querySelector('#'+groupId+' input:checked');
    if(chk) chk.parentElement.classList.add('selected');
}
function updateSideBtns(groupId) {
    document.querySelectorAll('#'+groupId+' .side-btn').forEach(function(l){ l.classList.remove('selected'); });
    var chk = document.querySelector('#'+groupId+' input:checked');
    if(chk) chk.parentElement.classList.add('selected');
}

function goToStep(n){
    document.querySelectorAll('.step').forEach(function(s){s.classList.remove('active')});
    document.getElementById('step'+n).classList.add('active');
    document.getElementById('btnPrev').style.visibility=n>1?'visible':'hidden';
    document.querySelectorAll('.stepper-step').forEach(function(s){
        var sn=parseInt(s.dataset.step);s.classList.remove('active','completed');
        if(sn<n){s.classList.add('completed');s.querySelector('.stepper-circle').innerHTML='✓';}
        else if(sn===n){s.classList.add('active');s.querySelector('.stepper-circle').textContent=sn;}
        else{s.querySelector('.stepper-circle').textContent=sn;}
    });
    document.querySelectorAll('.stepper-line').forEach(function(l){var ln=parseInt(l.dataset.line);if(ln<n)l.classList.add('done');else l.classList.remove('done');});
    if(n===totalSteps){document.getElementById('btnNext').style.display='none';}else{document.getElementById('btnNext').style.display='';document.getElementById('btnNext').textContent='Next step';}
    // Scroll to top of wizard
    document.querySelector('.wizard-card').scrollIntoView({behavior:'smooth',block:'start'});
}
function nextStep(){
    // Validation for step 2: participant must be selected
    if(currentStep===2){
        var selected=document.querySelector('#partGroup input:checked');
        var errEl=document.getElementById('participantError');
        if(!selected){
            errEl.style.display='block';
            document.getElementById('partGroup').classList.add('shake');
            setTimeout(function(){document.getElementById('partGroup').classList.remove('shake');},500);
            return;
        } else {
            errEl.style.display='none';
        }
    }
    if(currentStep<totalSteps){currentStep++;goToStep(currentStep);}
}
function prevStep(){if(currentStep>1){currentStep--;goToStep(currentStep);}}
function setPart(v){
    formData.participant_type=v;
    var hb=document.getElementById('honeymoonBlock');
    var cb=document.getElementById('countersBlock');
    var gb=document.getElementById('groupTypeBlock');
    if(v==='Couple'){hb.style.display='block';}else{hb.style.display='none';formData.is_honeymoon=0;var hc=document.getElementById('honeymoonCheck');if(hc)hc.checked=false;}
    if(v==='Family'||v==='Friends'||v==='Group'){cb.style.display='flex';}else{cb.style.display='none';}
    if(v==='Group'){gb.style.display='block';}else{gb.style.display='none';}
    if(v==='Alone'){formData.adults=1;formData.children=0;document.getElementById('adultsCount').value='1';document.getElementById('childrenCount').value='0';}
    else if(v==='Couple'){formData.adults=2;formData.children=0;document.getElementById('adultsCount').value='2';document.getElementById('childrenCount').value='0';}
    else if(v==='Family'){formData.adults=1;document.getElementById('adultsCount').value='1';}
    renderChildAges();
}
function changeCount(type,d){var c=formData[type]+d;if(type==='adults'&&c<1)return;if(type==='children'&&c<0)return;if(c>10)return;formData[type]=c;document.getElementById(type+'Count').value=c;if(type==='children')renderChildAges(); calcTotal(); }
function renderChildAges(){var cont=document.getElementById('childAges');cont.innerHTML='';for(var i=0;i<formData.children;i++){var sel=document.createElement('select');sel.innerHTML='<option value="">Age child '+(i+1)+'</option>';for(var a=1;a<=18;a++)sel.innerHTML+='<option value="'+a+'">'+a+' years</option>';cont.appendChild(sel);}}
function toggleDates(val){
    formData.has_exact_dates=val==='yes'?1:0;
    document.getElementById('exactDateFields').style.display=val==='yes'?'block':'none';
    document.getElementById('approxDateFields').style.display=val==='no'?'block':'none';
}
function toggleAcc(el){var v=el.value;var idx=formData.accommodation_prefs.indexOf(v);if(idx>-1)formData.accommodation_prefs.splice(idx,1);else formData.accommodation_prefs.push(v);}
function toggleStyle(el){var v=el.value;if(!formData.travel_styles)formData.travel_styles=[];var idx=formData.travel_styles.indexOf(v);if(idx>-1)formData.travel_styles.splice(idx,1);else formData.travel_styles.push(v);}
function toggleGuide(el){var v=el.value;if(!Array.isArray(formData.guide_languages))formData.guide_languages=[];var idx=formData.guide_languages.indexOf(v);if(idx>-1)formData.guide_languages.splice(idx,1);else formData.guide_languages.push(v);}
function calcTotal(){
    var ideal=parseFloat(document.getElementById('idealBudget').value)||0;
    var maxB=parseFloat(document.getElementById('maxBudget').value)||0;
    var amt = ideal > 0 ? ideal : (maxB > 0 ? maxB : 0);
    var persons = formData.adults + formData.children;
    var total=amt*persons;
    var el=document.getElementById('budgetTotal');
    if(amt>0){
        el.style.display='block';
        document.getElementById('personsCount').textContent=persons;
        document.getElementById('totalAmount').textContent='$'+total.toLocaleString();
    }else{
        el.style.display='none';
    }
}
// Populate departure period dropdown with dynamic months
(function(){
    var sel=document.getElementById('departurePeriod');
    var months=['January','February','March','April','May','June','July','August','September','October','November','December'];
    var now=new Date();
    var startMonth=now.getMonth()+1;
    var startYear=now.getFullYear();
    if(startMonth>11){startMonth=0;startYear++;}
    for(var i=0;i<24;i++){
        var m=(startMonth+i)%12;
        var y=startYear+Math.floor((startMonth+i)/12);
        var opt=document.createElement('option');
        opt.value=months[m]+' '+y;
        opt.textContent=months[m]+' '+y;
        sel.appendChild(opt);
    }
})();
// Step 5: Show registration form after email Ok
function showRegForm(){
    var emailVal=document.getElementById('email').value.trim();
    if(!emailVal||!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)){alert('Please enter a valid email address');return;}
    formData.email=emailVal;
    document.getElementById('email').readOnly=true;
    document.getElementById('email').style.background='#f5f5f0';
    document.getElementById('emailOkBtn').style.display='none';
    document.getElementById('emailConfirmRow').style.display='block';
    document.getElementById('benefitsBlock').style.display='none';
    document.getElementById('regFormBlock').style.display='block';
    document.getElementById('emailConfirm').focus();
}
function togglePwd(fieldId, iconId){
    var inp=document.getElementById(fieldId);
    var ico=document.getElementById(iconId).querySelector('i');
    if(inp.type==='password'){inp.type='text';ico.className='fa fa-eye-slash';}
    else{inp.type='password';ico.className='fa fa-eye';}
}

function submitForm(){
    var btn=document.getElementById('submitBtn');
    var isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

    // Collect trip data (common for both logged-in and guest users)
    formData.departure_date=document.getElementById('depDate')?document.getElementById('depDate').value:'';
    formData.return_date=document.getElementById('retDate')?document.getElementById('retDate').value:'';
    formData.departure_period=document.getElementById('departurePeriod')?document.getElementById('departurePeriod').value:'';
    formData.approx_duration=document.getElementById('approxDuration')?document.getElementById('approxDuration').value:'';
    formData.travel_plan=document.getElementById('travelPlan').value;
    formData.ideal_budget=document.getElementById('idealBudget').value;
    formData.max_budget=document.getElementById('maxBudget').value;
    var ages=[];document.querySelectorAll('#childAges select').forEach(function(s){ages.push(s.value)});formData.children_ages=ages;

    if(isLoggedIn){
        // Logged-in user: no registration fields needed
        formData.is_logged_in = true;
    } else {
        // Guest user: validate registration fields
        formData.email=document.getElementById('email').value;
        if(!formData.email){alert('Please enter your email address');return;}
        // Validate email confirmation
        var emailConf=document.getElementById('emailConfirm').value.trim();
        if(!emailConf){alert('Please confirm your email address');return;}
        if(formData.email!==emailConf){
            document.getElementById('emailMatchErr').style.display='block';
            document.getElementById('emailConfirm').focus();
            return;
        }
        document.getElementById('emailMatchErr').style.display='none';
        var pwd=document.getElementById('regPassword').value;
        if(!pwd){alert('Please enter a password');return;}
        // Validate password confirmation
        var pwdConf=document.getElementById('regPasswordConfirm').value;
        if(!pwdConf){alert('Please confirm your password');return;}
        if(pwd!==pwdConf){
            document.getElementById('pwdMatchErr').style.display='block';
            document.getElementById('regPasswordConfirm').focus();
            return;
        }
        document.getElementById('pwdMatchErr').style.display='none';
        formData.password=pwd;
        formData.first_name=document.getElementById('regFirstName').value.trim();
        formData.last_name=document.getElementById('regLastName').value.trim();
        if(!formData.first_name||!formData.last_name){alert('Please enter your first and last name');return;}
        if(!formData.civility){alert('Please select your civility');return;}
        formData.phone=document.getElementById('regPhone').value.trim();
        if(!formData.phone){alert('Please enter your phone number');return;}
        var dd=document.getElementById('dobDay').value,mm=document.getElementById('dobMonth').value,yy=document.getElementById('dobYear').value;
        if(!dd||!mm||!yy){alert('Please enter your date of birth');return;}
        formData.dob=dd+'/'+mm+'/'+yy;
        formData.country=document.getElementById('regCountry').value;
        formData.marketing_consent=document.getElementById('marketingConsent').checked?1:0;
        if(!document.getElementById('termsConsent').checked){alert('Please accept the Terms and Conditions');return;}
        formData.terms_consent=1;
    }

    // Show loading
    btn.disabled=true;
    btn.innerHTML='<span class="spinner"></span>Sending...';
    fetch('/create-trip',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},body:JSON.stringify(formData)}).then(function(r){return r.json()}).then(function(d){
        if(d.success){
            document.getElementById('wizardFooter').style.display='none';
            document.querySelectorAll('.step').forEach(function(s){s.classList.remove('active')});
            document.getElementById('stepSuccess').classList.add('active');
            document.querySelector('.stepper').style.display='none';
            if(d.already_registered){
                document.getElementById('successMsg').innerHTML='This email is already registered.<br>Your trip request has been submitted successfully.<br><br><a href="/en/users/login/" style="color:#f97316;font-weight:600;text-decoration:underline;">Please login with your existing password →</a>';
            }
        } else {
            btn.disabled=false;btn.textContent='Send my request';
            alert(d.message||'Something went wrong. Please try again.');
        }
    }).catch(function(e){btn.disabled=false;btn.textContent='Send my request';alert('Error submitting. Please try again.');});
}
</script>
</body>
</html>
