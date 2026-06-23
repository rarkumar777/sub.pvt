@extends('frontend.layout')
@section('title', 'My Messages')

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
                                <a href="/{{ $lang }}/users/account/my-bookings/" style="display:block; padding:12px 20px; color:#444; font-weight:600; text-decoration:none; border-left:4px solid transparent; transition:all 0.3s;" onmouseover="this.style.background='#f9f9f9'; this.style.color='#eb9950';" onmouseout="this.style.background='transparent'; this.style.color='#444';">
                                    <i class="fa-list-ul" style="width:25px; color:#bbb;"></i> My Bookings
                                </a>
                            </li>
                            <li>
                                <a href="/{{ $lang }}/users/account/my-messages/" style="display:block; padding:12px 20px; color:#eb9950; font-weight:600; text-decoration:none; background:#fff5eb; border-left:4px solid #eb9950;">
                                    <i class="fa-comments" style="width:25px; color:#eb9950;"></i> My Messages
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
                                <i class="fa-comments"></i> My Messages
                            </h2>
                        </div>

                    <div class="sd-12 pad">
                        @if($tripRequests->isEmpty())
                            <div style="text-align:center; padding:50px 0;">
                                <i class="fa-comments-o" style="font-size:60px; color:#ddd; margin-bottom:20px; display:block;"></i>
                                <h3 style="color:#999;">No messages yet.</h3>
                                <p style="color:#bbb; margin-top:10px;">When you submit a trip request, your conversations will appear here.</p>
                            </div>
                        @else
                            @foreach($tripRequests as $tripRequest)
                                <div style="border:1px solid #eee; border-radius:8px; margin-bottom:20px; overflow:hidden;">
                                    {{-- Trip Request Header --}}
                                    <div style="background:#f8f8f8; padding:12px 16px; border-bottom:1px solid #eee; cursor:pointer; display:flex; justify-content:space-between; align-items:center;"
                                         onclick="toggleChat({{ $tripRequest->id }})">
                                        <div>
                                            <strong style="color:#333;">Trip Request #{{ $tripRequest->id }}</strong>
                                            <span style="color:#999; font-size:12px; margin-left:8px;">
                                                {{ $tripRequest->created_at->format('d M Y') }}
                                            </span>
                                            @if($tripRequest->messages->where('sender_type', 'agent')->count() > 0)
                                                <span style="background:#eb9950; color:#fff; font-size:10px; padding:2px 8px; border-radius:10px; margin-left:6px;">
                                                    {{ $tripRequest->messages->count() }} messages
                                                </span>
                                            @endif
                                        </div>
                                        <i class="fa-chevron-down" id="arrow-{{ $tripRequest->id }}" style="color:#999; transition:transform 0.2s;"></i>
                                    </div>

                                    {{-- Chat Area --}}
                                    <div id="chat-{{ $tripRequest->id }}" style="display:none;">
                                        <div id="messages-{{ $tripRequest->id }}" style="max-height:400px; overflow-y:auto; padding:16px; background:#fafafa;">
                                            @if($tripRequest->messages->isEmpty())
                                                <p style="text-align:center; color:#bbb; padding:20px 0;">No messages in this conversation yet. Send the first message!</p>
                                            @else
                                                @foreach($tripRequest->messages->sortBy('created_at') as $msg)
                                                    @if($msg->sender_type === 'agent')
                                                        {{-- Admin message - left --}}
                                                        <div style="display:flex; margin-bottom:12px;">
                                                            <div style="max-width:75%; background:#fff; border:1px solid #e5e5e5; border-radius:12px 12px 12px 2px; padding:10px 14px;">
                                                                <div style="font-size:11px; color:#eb9950; font-weight:600; margin-bottom:4px;">
                                                                    {{ $msg->sender_name ?? 'Admin' }}
                                                                </div>
                                                                <div style="font-size:14px; color:#333; line-height:1.5;">
                                                                    {!! nl2br(e($msg->message)) !!}
                                                                </div>
                                                                @if($msg->attachment)
                                                                    <div style="margin-top:6px;">
                                                                        <a href="{{ $msg->attachment }}" target="_blank" style="color:#eb9950; font-size:12px; text-decoration:underline;">
                                                                            📎 View Attachment
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <div style="font-size:10px; color:#bbb; margin-top:4px; text-align:right;">
                                                                    {{ $msg->created_at->format('d M, h:i A') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{-- User message - right --}}
                                                        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
                                                            <div style="max-width:75%; background:#eb9950; border-radius:12px 12px 2px 12px; padding:10px 14px;">
                                                                <div style="font-size:14px; color:#fff; line-height:1.5;">
                                                                    {!! nl2br(e($msg->message)) !!}
                                                                </div>
                                                                @if($msg->attachment)
                                                                    <div style="margin-top:6px;">
                                                                        <a href="{{ $msg->attachment }}" target="_blank" style="color:#fff; font-size:12px; text-decoration:underline;">
                                                                            📎 View Attachment
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                                <div style="font-size:10px; color:rgba(255,255,255,0.7); margin-top:4px; text-align:right;">
                                                                    {{ $msg->created_at->format('d M, h:i A') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>

                                        {{-- Send Message Form --}}
                                        <div style="padding:12px 16px; border-top:1px solid #eee; background:#fff;">
                                            <form onsubmit="sendMessage(event, {{ $tripRequest->id }})" style="display:flex; gap:8px; align-items:center;">
                                                <input type="text" id="msg-input-{{ $tripRequest->id }}" placeholder="Type your message..."
                                                       style="flex:1; padding:10px 14px; border:1.5px solid #ddd; border-radius:20px; font-size:14px; outline:none; transition:border-color 0.2s;"
                                                       onfocus="this.style.borderColor='#eb9950'" onblur="this.style.borderColor='#ddd'">
                                                <button type="submit" id="send-btn-{{ $tripRequest->id }}"
                                                        style="background:#eb9950; color:#fff; border:none; border-radius:50%; width:40px; height:40px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:16px; transition:background 0.2s;"
                                                        onmouseover="this.style.background='#d4872e'" onmouseout="this.style.background='#eb9950'">
                                                    <i class="fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleChat(id) {
    var chat = document.getElementById('chat-' + id);
    var arrow = document.getElementById('arrow-' + id);
    if (chat.style.display === 'none') {
        chat.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
        // Scroll to bottom of messages
        var msgBox = document.getElementById('messages-' + id);
        msgBox.scrollTop = msgBox.scrollHeight;
    } else {
        chat.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}

function sendMessage(e, tripId) {
    e.preventDefault();
    var input = document.getElementById('msg-input-' + tripId);
    var btn = document.getElementById('send-btn-' + tripId);
    var message = input.value.trim();
    if (!message) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-spinner fa-spin"></i>';

    fetch('/{{ $lang }}/users/account/my-messages/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            trip_request_id: tripId,
            message: message
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            // Add message to chat
            var msgBox = document.getElementById('messages-' + tripId);
            // Remove "no messages" text if present
            var noMsg = msgBox.querySelector('p');
            if (noMsg && noMsg.textContent.indexOf('No messages') > -1) {
                noMsg.remove();
            }
            var div = document.createElement('div');
            div.style.cssText = 'display:flex; justify-content:flex-end; margin-bottom:12px;';
            var now = new Date();
            var timeStr = now.getDate() + ' ' + now.toLocaleString('en', {month:'short'}) + ', ' + now.toLocaleString('en', {hour:'numeric', minute:'2-digit', hour12:true});
            div.innerHTML = '<div style="max-width:75%; background:#eb9950; border-radius:12px 12px 2px 12px; padding:10px 14px;">' +
                '<div style="font-size:14px; color:#fff; line-height:1.5;">' + message.replace(/\n/g, '<br>') + '</div>' +
                '<div style="font-size:10px; color:rgba(255,255,255,0.7); margin-top:4px; text-align:right;">' + timeStr + '</div>' +
                '</div>';
            msgBox.appendChild(div);
            msgBox.scrollTop = msgBox.scrollHeight;
            input.value = '';
        } else {
            alert(data.message || 'Failed to send message');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-paper-plane"></i>';
    })
    .catch(function() {
        alert('Error sending message. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-paper-plane"></i>';
    });
}

// Auto-open first chat if there are messages
document.addEventListener('DOMContentLoaded', function() {
    @if($tripRequests->isNotEmpty())
        var firstId = {{ $tripRequests->first()->id }};
        toggleChat(firstId);
    @endif

    // Auto-refresh polling for messages
    setInterval(function() {
        var openChats = document.querySelectorAll('div[id^="chat-"]');
        var isAnyOpen = false;
        openChats.forEach(function(chat) {
            if (chat.style.display !== 'none') {
                isAnyOpen = true;
            }
        });

        if (isAnyOpen) {
            fetch(window.location.href)
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    openChats.forEach(function(chat) {
                        if (chat.style.display !== 'none') {
                            var tripId = chat.id.replace('chat-', '');
                            var newMessages = doc.getElementById('messages-' + tripId);
                            var oldMessages = document.getElementById('messages-' + tripId);
                            if (newMessages && oldMessages && newMessages.innerHTML !== oldMessages.innerHTML) {
                                var isAtBottom = oldMessages.scrollHeight - oldMessages.scrollTop <= oldMessages.clientHeight + 50;
                                oldMessages.innerHTML = newMessages.innerHTML;
                                if (isAtBottom) {
                                    oldMessages.scrollTop = oldMessages.scrollHeight;
                                }
                            }
                        }
                    });
                })
                .catch(function(err) { console.error('Polling error', err); });
        }
    }, 5000);
});
</script>
@endsection
