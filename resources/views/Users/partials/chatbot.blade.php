<button id="chat-toggle" class="chat-icon">
    <img src="{{ asset('images/logos/chatbot.png')}}" class="w-8">

</button>

<div id="chat-window" class="chat-window hidden">
    <div class="chat-header">
        <span class="title">Lyn & Spa AI</span>
        <button id="chat-close" class="close-btn">×</button>
    </div>

    <div id="chat-body" class="chat-body">
        <div class="msg bot">
            <div class="bubble">
                👋 <b>Xin chào!</b> Mình có thể giúp bạn:
                <br>- Tư vấn sản phẩm theo loại da  
                <br>- Đặt lịch spa & dịch vụ  
                <br>- Tra cứu đơn hàng (#Mã đơn)
            </div>
        </div>
    </div>

    <div class="chat-suggest">
    
        <button class="sug" data-q="Đặt lịch spa">Đặt lịch spa</button>
        <button class="sug" data-q="Tra đơn hàng #1234">Tra đơn</button>
    </div>

    <form id="chat-form" class="chat-form">
        @csrf
        <input id="chat-input" placeholder="Nhập tin nhắn của bạn..." autocomplete="off">
        <button class="send-btn" type="submit">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.chat-icon{
    position: fixed;
    right: 30px;
    bottom:85px;
    background: #1e40af;
    color:white;
    border-radius:50%;
    width:60px;
    height:60px;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    box-shadow:0 8px 20px rgba(0,0,0,.2);
    z-index:99999;
}
.chat-window{
    position:fixed;
    right:30px;
    bottom:150px;
    width:380px;
    height:500px;
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.25);
    z-index:9999;
}
.hidden{ display:none; }

.chat-header{
    background:#1e40af;
    color:white;
    padding:15px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.close-btn{
    background:none;
    border:none;
    color:white;
    font-size:22px;
}
.chat-body{
    padding:15px;
    height:280px;
    overflow-y:auto;
    background:#f9fafb;
}
.msg{
    margin-bottom:12px;
    display:flex;
}
.msg.user{ justify-content:flex-end; }
.msg.user .bubble{ background:#1e40af; color:white; }
.msg.bot .bubble{ background:white; border:1px solid #e5e7eb; }

.bubble{
    max-width:75%;
    padding:10px 14px;
    border-radius:14px;
}

.chat-suggest{
    padding:10px;
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    background:white;
    border-top:1px solid #eee;
}
.sug{
    background:#e0ecff;
    color:#1e3a8a;
    padding:6px 12px;
    border-radius:16px;
    border:1px solid #c7ddff;
}

.chat-form{
    display:flex;
    padding:10px;
    gap:10px;
    border-top:1px solid #ddd;
    background:white;
}
.chat-form input{
    flex:1;
    padding:10px;
    border-radius:10px;
    border:1px solid #ddd;
}
.send-btn{
    background:#1e40af;
    color:white;
    width:45px;
    border:none;
    border-radius:10px;
}
</style>

<script>
const panel = document.getElementById('chat-window');
const toggle = document.getElementById('chat-toggle');
const closeBtn = document.getElementById('chat-close');

const input = document.getElementById('chat-input');
const chatBody = document.getElementById('chat-body');
const form = document.getElementById('chat-form');

// Open / close chatbot
toggle.onclick = () => panel.classList.remove('hidden');
closeBtn.onclick = () => panel.classList.add('hidden');

// Add message
function addMsg(role, text){
    const div = document.createElement('div');
    div.className = 'msg ' + role;
    div.innerHTML = `<div class="bubble">${text}</div>`;
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Quick suggestions
document.querySelectorAll('.sug').forEach(b=>{
    b.onclick = ()=>{
        input.value = b.dataset.q;
        form.dispatchEvent(new Event('submit',{cancelable:true}));
    }
});

// Form submit
form.onsubmit = async(e)=>{
    e.preventDefault();
    let text = input.value.trim();
    if(!text) return;

    addMsg('user', text);
    input.value = "";

    // typing
    let typing = document.createElement('div');
    typing.className = "msg bot";
    typing.innerHTML = `<div class="bubble">...</div>`;
    chatBody.appendChild(typing);

    let res = await fetch("{{ route('users.chat.send') }}", {
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body:JSON.stringify({ message: text })
    });

    let data = await res.json();
    typing.remove();

    addMsg('bot', data.assistant);
};
</script>
