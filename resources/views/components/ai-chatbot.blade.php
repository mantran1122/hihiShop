<!-- resources/views/components/ai-chatbot.blade.php -->
<div id="hihi-ai-chatbot" class="fixed bottom-5 right-5 z-50 font-['Montserrat',sans-serif]">

    <!-- Floating Toggle Button -->
    <button type="button" 
            id="chat-toggle-btn"
            onclick="toggleHihiChat()"
            class="relative flex items-center gap-2.5 px-4 py-3.5 rounded-full bg-gradient-to-r from-rose-600 to-rose-500 text-white shadow-[0_10px_25px_-5px_rgba(225,29,72,0.5)] hover:shadow-[0_15px_30px_-5px_rgba(225,29,72,0.65)] hover:scale-105 active:scale-95 transition-all duration-300 group cursor-pointer border border-rose-400/30">
        <!-- Pulse ring -->
        <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-slate-900"></span>
        </span>

        <!-- AI Dynamic Animated Icon (Replaced emoji) -->
        <span class="w-7 h-7 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-rose-100 shadow-inner group-hover:scale-110 transition-transform">
            <svg class="w-4 h-4 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </span>
        <span class="text-xs sm:text-sm font-bold tracking-wide leading-none">
            Chat với AI
        </span>
    </button>

    <!-- Chat Window Popup (Hidden by default) -->
    <div id="chat-window" 
         class="hidden fixed bottom-5 right-5 w-[92vw] sm:w-[410px] h-[560px] max-h-[85vh] rounded-3xl bg-slate-900/95 backdrop-blur-2xl border border-slate-800 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.8)] flex-col overflow-hidden transition-all duration-300 z-50">
        
        <!-- Chat Header -->
        <div class="px-5 py-4 bg-gradient-to-r from-slate-900 via-rose-950/60 to-slate-900 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-rose-600 to-rose-400 text-white flex items-center justify-center text-lg font-black shadow-md shadow-rose-600/30">
                        H
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-slate-900"></span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-1.5 leading-snug">
                        <span>HIHI AI Assistant</span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 leading-none">AI RAG</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 flex items-center gap-1.5 leading-normal mt-0.5">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Dữ liệu CSDL thời gian thực</span>
                    </p>
                </div>
            </div>

            <!-- Minimize / Close Buttons -->
            <button type="button" 
                    onclick="toggleHihiChat()" 
                    class="w-8 h-8 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition border border-slate-700/50 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Quick Prompts Chips (Replaced emojis with crisp vector SVG icons) -->
        <div class="px-4 py-2.5 bg-slate-950/40 border-b border-slate-800/60 flex items-center gap-2 overflow-x-auto no-scrollbar text-[11px]">
            <button type="button" onclick="sendQuickPrompt('Làm sao để đăng ký nhanh bằng Google hoặc FB?')" class="whitespace-nowrap px-3 py-1.5 rounded-full bg-slate-800/90 text-slate-300 hover:text-rose-300 hover:bg-slate-800 border border-slate-700/80 transition flex items-center gap-1.5 leading-none cursor-pointer">
                <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                <span>Đăng ký Google/FB</span>
            </button>
            <button type="button" onclick="sendQuickPrompt('Tư vấn nước hoa bán chạy nhất')" class="whitespace-nowrap px-3 py-1.5 rounded-full bg-slate-800/90 text-slate-300 hover:text-rose-300 hover:bg-slate-800 border border-slate-700/80 transition flex items-center gap-1.5 leading-none cursor-pointer">
                <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <span>Nước hoa hot</span>
            </button>
            <button type="button" onclick="sendQuickPrompt('Chính sách giao hàng và bảo hành như thế nào?')" class="whitespace-nowrap px-3 py-1.5 rounded-full bg-slate-800/90 text-slate-300 hover:text-rose-300 hover:bg-slate-800 border border-slate-700/80 transition flex items-center gap-1.5 leading-none cursor-pointer">
                <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Chính sách bảo hành</span>
            </button>
        </div>

        <!-- Message History Body -->
        <div id="chat-messages-container" class="flex-1 p-4 overflow-y-auto space-y-3.5 text-xs">
            <!-- Welcome message (Replaced emoji with Vector Icon) -->
            <div class="flex items-start gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-rose-600/30 border border-rose-500/30 text-rose-300 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="max-w-[85%] p-3.5 rounded-2xl rounded-tl-sm bg-slate-800/90 border border-slate-700/80 text-slate-200 leading-5 space-y-1.5 shadow-sm">
                    <p class="font-semibold text-rose-300 text-[11px] uppercase tracking-wide leading-none">Trợ lý HIHI Shop</p>
                    <p class="leading-5">Chào bạn! Mình là <strong>AI Assistant</strong> hỗ trợ 24/7. Bạn có thể hỏi mình về tư vấn chọn sản phẩm, cách đăng ký tài khoản hoặc chính sách của shop nhé!</p>
                </div>
            </div>
        </div>

        <!-- Typing Indicator (Hidden by default) -->
        <div id="chat-typing" class="hidden px-5 py-2 flex items-center gap-2 text-[11px] text-slate-400 bg-slate-900/80 border-t border-slate-800/60 leading-none">
            <span class="inline-flex gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-bounce"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-bounce" style="animation-delay: 0.2s"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-rose-300 animate-bounce" style="animation-delay: 0.4s"></span>
            </span>
            <span>AI đang tra cứu dữ liệu...</span>
        </div>

        <!-- Chat Input Footer -->
        <form id="chat-input-form" onsubmit="handleChatSubmit(event)" class="p-3 bg-slate-950/90 border-t border-slate-800 flex items-center gap-2">
            <input type="text" 
                   id="chat-user-input" 
                   placeholder="Nhập câu hỏi hoặc tên sản phẩm..." 
                   autocomplete="off"
                   class="flex-1 px-4 py-2.5 rounded-2xl bg-slate-900 border border-slate-800 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-rose-500/50 focus:ring-2 focus:ring-rose-500/20 leading-normal transition">
            
            <button type="submit" 
                    id="chat-send-btn"
                    class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-rose-600 to-rose-500 text-white flex items-center justify-center shrink-0 hover:brightness-110 active:scale-95 transition shadow-md shadow-rose-600/30 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </form>

    </div>
</div>

<style>
@keyframes spinSlow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.animate-spin-slow {
    animation: spinSlow 10s linear infinite;
}
</style>

<script>
function toggleHihiChat() {
    const chatWin = document.getElementById("chat-window");
    const toggleBtn = document.getElementById("chat-toggle-btn");
    if (!chatWin) return;

    if (chatWin.classList.contains("hidden")) {
        chatWin.classList.remove("hidden");
        chatWin.classList.add("flex");
        toggleBtn.classList.add("hidden");
        document.getElementById("chat-user-input")?.focus();
        loadChatHistory();
    } else {
        chatWin.classList.add("hidden");
        chatWin.classList.remove("flex");
        toggleBtn.classList.remove("hidden");
    }
}

function sendQuickPrompt(promptText) {
    const input = document.getElementById("chat-user-input");
    if (!input) return;
    input.value = promptText;
    handleChatSubmit(new Event('submit'));
}

async function handleChatSubmit(e) {
    if (e && e.preventDefault) e.preventDefault();
    const input = document.getElementById("chat-user-input");
    const container = document.getElementById("chat-messages-container");
    const typing = document.getElementById("chat-typing");
    const sendBtn = document.getElementById("chat-send-btn");

    if (!input) return;
    const userText = input.value.trim();
    if (!userText) return;

    // Append User Message to UI
    appendUserMessage(userText);
    input.value = "";
    input.disabled = true;
    sendBtn.disabled = true;
    typing.classList.remove("hidden");
    container.scrollTop = container.scrollHeight;

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch("{{ route('chat.message') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": token || ""
            },
            body: JSON.stringify({ message: userText })
        });

        const data = await res.json();
        typing.classList.add("hidden");
        input.disabled = false;
        sendBtn.disabled = false;
        input.focus();

        if (data && data.reply) {
            appendBotMessage(data.reply, data.products || [], data.message_id || null);
        } else {
            appendBotMessage("Hệ thống AI đang bận xử lý, vui lòng thử lại sau giây lát.");
        }
    } catch (err) {
        console.error("Chat error:", err);
        typing.classList.add("hidden");
        input.disabled = false;
        sendBtn.disabled = false;
        appendBotMessage("Không thể kết nối đến máy chủ AI. Vui lòng kiểm tra lại kết nối mạng.");
    }

    container.scrollTop = container.scrollHeight;
}

function appendUserMessage(text) {
    const container = document.getElementById("chat-messages-container");
    const msgDiv = document.createElement("div");
    msgDiv.className = "flex justify-end";
    msgDiv.innerHTML = `
        <div class="max-w-[80%] p-3.5 rounded-2xl rounded-tr-sm bg-gradient-to-r from-rose-600 to-rose-700 text-white leading-5 shadow-md shadow-rose-950/30">
            ${escapeHtml(text)}
        </div>
    `;
    container.appendChild(msgDiv);
    container.scrollTop = container.scrollHeight;
}

function appendBotMessage(text, products = [], messageId = null, feedback = 'none') {
    const container = document.getElementById("chat-messages-container");
    const msgDiv = document.createElement("div");
    msgDiv.className = "flex items-start gap-2.5";

    let formattedText = formatMarkdownText(text);

    let productsHtml = "";
    if (products && products.length > 0) {
        productsHtml = `
            <div class="mt-2.5 pt-2.5 border-t border-slate-700/80 space-y-2">
                <p class="text-[11px] font-bold text-rose-300 leading-none">Sản phẩm đề xuất thời gian thực:</p>
                <div class="space-y-1.5">
                    ${products.map(p => `
                        <a href="${p.url}" class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-900/80 hover:bg-slate-950 border border-slate-700/60 hover:border-rose-500/50 transition group block">
                            <img src="${p.image}" alt="${escapeHtml(p.name)}" class="w-10 h-10 object-cover rounded-lg shrink-0 bg-slate-800">
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-semibold text-slate-200 group-hover:text-rose-300 truncate leading-snug">${escapeHtml(p.name)}</p>
                                <p class="text-[10px] font-bold text-rose-400 leading-none mt-0.5">${p.price}</p>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-rose-400 group-hover:translate-x-0.5 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    `).join('')}
                </div>
            </div>
        `;
    }

    const feedbackHtml = messageId ? `
        <div class="mt-2.5 pt-2.5 border-t border-slate-700/80 flex items-center gap-2" data-feedback-box>
            <span class="text-[10px] text-slate-500">Hữu ích?</span>
            <button type="button" data-feedback="thumbs_up" onclick="sendChatFeedback(this, ${messageId}, 'thumbs_up')" aria-label="Hữu ích"
                    class="w-7 h-7 rounded-lg bg-slate-900/80 border border-slate-700/60 flex items-center justify-center hover:border-emerald-500/60 transition ${feedback === 'thumbs_up' ? 'text-emerald-400' : 'text-slate-400'}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>
            </button>
            <button type="button" data-feedback="thumbs_down" onclick="sendChatFeedback(this, ${messageId}, 'thumbs_down')" aria-label="Chưa hữu ích"
                    class="w-7 h-7 rounded-lg bg-slate-900/80 border border-slate-700/60 flex items-center justify-center hover:border-rose-500/60 transition ${feedback === 'thumbs_down' ? 'text-rose-400' : 'text-slate-400'}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 15v4a3 3 0 003 3l4-9V2H5.72a2 2 0 00-2 1.7l-1.38 9a2 2 0 002 2.3zM17 2h2.67A2.31 2.31 0 0122 4v7a2.31 2.31 0 01-2.33 2H17"/></svg>
            </button>
            <span data-thanks class="hidden text-[10px] text-emerald-400">Đã ghi nhận, cảm ơn bạn!</span>
        </div>
    ` : '';

    msgDiv.innerHTML = `
        <div class="w-7 h-7 rounded-xl bg-rose-600/30 border border-rose-500/30 text-rose-300 flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div class="max-w-[85%] p-3.5 rounded-2xl rounded-tl-sm bg-slate-800/90 border border-slate-700/80 text-slate-200 leading-5 shadow-sm">
            <p class="font-semibold text-rose-300 text-[10px] uppercase tracking-wide leading-none">HIHI AI</p>
            <div class="space-y-1.5 leading-5 mt-1">${formattedText}</div>
            ${productsHtml}
            ${feedbackHtml}
        </div>
    `;
    container.appendChild(msgDiv);
    container.scrollTop = container.scrollHeight;
}

async function sendChatFeedback(btn, messageId, value) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        const res = await fetch("{{ route('chat.feedback') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": token || ""
            },
            body: JSON.stringify({ message_id: messageId, feedback: value })
        });

        if (!res.ok) throw new Error("HTTP " + res.status);

        const box = btn.closest('[data-feedback-box]');
        if (box) {
            box.querySelectorAll('button[data-feedback]').forEach(b => {
                b.classList.remove('text-emerald-400', 'text-rose-400');
                b.classList.add('text-slate-400');
            });
            btn.classList.remove('text-slate-400');
            btn.classList.add(value === 'thumbs_up' ? 'text-emerald-400' : 'text-rose-400');
            box.querySelector('[data-thanks]')?.classList.remove('hidden');
        }
    } catch (e) {
        console.warn("Không gửi được phản hồi", e);
    }
}

let historyLoaded = false;
async function loadChatHistory() {
    if (historyLoaded) return;
    try {
        const res = await fetch("{{ route('chat.history') }}");
        const data = await res.json();
        if (data && data.messages && data.messages.length > 0) {
            data.messages.forEach(m => {
                if (m.sender === 'user') {
                    appendUserMessage(m.content);
                } else if (m.sender === 'bot') {
                    appendBotMessage(m.content, [], m.id || null, m.feedback || 'none');
                }
            });
            historyLoaded = true;
        }
    } catch(e) {
        console.warn("Could not load chat history", e);
    }
}

function formatMarkdownText(txt) {
    if (!txt) return "";
    return txt
        .replace(/\n\n/g, '<br><br>')
        .replace(/\n/g, '<br>')
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/•/g, '&bull;');
}

function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>
