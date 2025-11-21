@extends('Users.layouts.home')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.0/dist/katex.min.css">
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
    <style>
.typing {
    display: inline-block;
    font-size: 14px;
    background: #f3f4f6;
    padding: 8px 14px;
    border-radius: 12px;
    line-height: 20px;
}

.typing span {
    display: inline-block;
    animation: blink 1.4s infinite both;
}

.typing span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes blink {
    0% { opacity: .2; }
    20% { opacity: 1; }
    100% { opacity: .2; }
}
</style>

</style>

<div class="w-full h-[calc(100vh-80px)] flex flex-col">
    
    <!-- Header -->
    <div class="px-4 h-14 flex items-center border-b bg-white shadow-sm">
        <h2 class="font-semibold text-lg">Trợ lý AI – Lyn & Spa</h2>
    </div>

    <!-- Chat content -->
    <div id="chat-box" class="flex-1 overflow-y-auto p-4 space-y-6 bg-gray-50">
        @foreach($messages as $msg)
            @if($msg->role === 'user')
                <div class="flex justify-end">
                    <div class="max-w-[70%] bg-blue-600 text-white px-4 py-2 rounded-xl shadow">
                        {{ $msg->content }}
                    </div>
                </div>
            @else
                <div class="flex items-start space-x-3">
                    <img src="/ai.png" class="w-8 h-8 rounded-full">
                    <div class="max-w-[75%] bg-white px-4 py-3 rounded-xl shadow prose">
                        {!! nl2br(e($msg->content)) !!}
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Input -->
    <div class="p-4 border-t bg-white">
        <div class="flex items-center space-x-2">
            <textarea id="msg"
                      class="flex-1 border rounded-lg px-3 py-2 focus:ring w-full resize-none"
                      rows="1"
                      placeholder="Nhập tin nhắn…"></textarea>

            <button id="send"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
                Gửi
            </button>
        </div>
    </div>
</div>

<script>
    function showTyping() {
    let box = document.getElementById("chat-box");

    let html = `
    <div id="typing-indicator" class="flex items-start space-x-3">
        <img src="/ai.png" class="w-8 h-8 rounded-full">
        <div class="typing shadow">
            <span>•</span><span>•</span><span>•</span>
        </div>
    </div>
    `;

    box.insertAdjacentHTML('beforeend', html);
    box.scrollTop = box.scrollHeight;
}

function hideTyping() {
    let typing = document.getElementById("typing-indicator");
    if (typing) typing.remove();
}

function appendAI(content) {
    let box = document.getElementById("chat-box");

    let html = `
    <div class="flex items-start space-x-3">
        <img src="/ai.png" class="w-8 h-8 rounded-full">
        <div class="max-w-[75%] bg-white px-4 py-3 rounded-xl shadow prose">
            ${marked.parse(content)}
        </div>
    </div>`;

    box.insertAdjacentHTML('beforeend', html);
    box.scrollTop = box.scrollHeight;
}

function appendUser(content) {
    let box = document.getElementById("chat-box");

    let html = `
    <div class="flex justify-end">
        <div class="max-w-[70%] bg-blue-600 text-white px-4 py-2 rounded-xl shadow">
            ${content}
        </div>
    </div>`;

    box.insertAdjacentHTML('beforeend', html);
    box.scrollTop = box.scrollHeight;
}

document.getElementById("send").onclick = function () {
    let message = document.getElementById("msg").value.trim();
    if (!message) return;

    appendUser(message);
    document.getElementById("msg").value = "";

    fetch("{{ route('users.chat.send') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ message })
    })
        .then(res => res.json())
        .then(res => appendAI(res.assistant));
};
document.getElementById("send").onclick = function () {
    let message = document.getElementById("msg").value.trim();
    if (!message) return;

    appendUser(message);
    document.getElementById("msg").value = "";

    // ✨ Hiển thị AI đang gõ
    showTyping();

    fetch("{{ route('users.chat.send') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ message })
    })
        .then(res => res.json())
        .then(res => {
            hideTyping();        // ❌ Ẩn typing
            appendAI(res.assistant); // ✔ hiển thị trả lời
        });
};

</script>

@endsection
