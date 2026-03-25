<footer class="bg-gray-900 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-medical text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-white">MediTax Connect</span>
                </div>
                <p class="text-gray-400 mb-4 max-w-md">
                    Connecting healthcare professionals with certified accountants for seamless tax management and financial peace of mind.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook text-xl"></i></a>
                </div>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">Platform</h4>
                <ul class="space-y-2">
                    <li><a href="/#how-it-works" class="text-gray-400 hover:text-white transition">How It Works</a></li>
                    <li><a href="/#pricing" class="text-gray-400 hover:text-white transition">Pricing</a></li>
                    <li><a href="/#benefits" class="text-gray-400 hover:text-white transition">Benefits</a></li>
                    <li><a href="/register" class="text-gray-400 hover:text-white transition">Get Started</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">Support</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Help Center</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Contact Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> MediTax Connect. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- ══════════════════════════════════════════════════════════
     MEDITAX SUPPORT CHATBOT
     ══════════════════════════════════════════════════════════ -->
<style>
    /* Chat widget container */
    #chat-widget { position: fixed; bottom: 24px; right: 24px; z-index: 9999; font-family: inherit; }

    /* Toggle button */
    #chat-btn {
        width: 56px; height: 56px; border-radius: 50%;
        background: linear-gradient(135deg, #0284c7 0%, #22c55e 100%);
        color: white; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 20px rgba(2,132,199,0.4);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    #chat-btn:hover { transform: scale(1.08); box-shadow: 0 6px 24px rgba(2,132,199,0.5); }
    #chat-btn .badge {
        position: absolute; top: -4px; right: -4px;
        width: 18px; height: 18px; border-radius: 50%;
        background: #ef4444; color: white; font-size: 10px;
        font-weight: 700; display: flex; align-items: center; justify-content: center;
        border: 2px solid white;
    }
    #chat-btn .badge.hidden { display: none; }

    /* Chat panel */
    #chat-panel {
        position: absolute; bottom: 68px; right: 0;
        width: 360px; max-height: 520px;
        background: white; border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        display: flex; flex-direction: column;
        overflow: hidden;
        transition: opacity 0.2s ease, transform 0.2s ease;
        transform-origin: bottom right;
    }
    #chat-panel.hidden { opacity: 0; pointer-events: none; transform: scale(0.92); }

    /* Dark mode for chat panel */
    html.dark #chat-panel { background: #1e293b; border: 1px solid #334155; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
    html.dark #chat-messages { background: #0f172a; }
    html.dark .chat-msg-bot .bubble { background: #1e293b; color: #e2e8f0; border: 1px solid #334155; }
    html.dark .chat-msg-user .bubble { background: linear-gradient(135deg, #0284c7, #22c55e); color: white; }
    html.dark #chat-input-area { background: #1e293b; border-top: 1px solid #334155; }
    html.dark #chat-input { background: #0f172a; color: #e2e8f0; border: 1px solid #334155; }
    html.dark #chat-input::placeholder { color: #475569; }
    html.dark .quick-btn { background: #1e293b; color: #94a3b8; border: 1px solid #334155; }
    html.dark .quick-btn:hover { background: #334155; color: #e2e8f0; border-color: #475569; }
    html.dark #chat-suggestions { background: #1e293b; border-top: 1px solid #334155; }

    /* Header */
    #chat-header {
        padding: 14px 16px;
        background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #22c55e 100%);
        color: white;
        display: flex; align-items: center; justify-content: space-between;
        flex-shrink: 0;
    }
    #chat-header .bot-info { display: flex; align-items: center; gap: 10px; }
    #chat-header .avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
    }
    #chat-header .title { font-weight: 700; font-size: 14px; line-height: 1.2; }
    #chat-header .subtitle { font-size: 11px; opacity: 0.85; }
    #chat-header .online-dot { width: 8px; height: 8px; border-radius: 50%; background: #4ade80; display: inline-block; margin-right: 4px; }
    #chat-close { background: rgba(255,255,255,0.2); border: none; color: white; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.15s; }
    #chat-close:hover { background: rgba(255,255,255,0.35); }

    /* Messages */
    #chat-messages {
        flex: 1; overflow-y: auto; padding: 14px 14px 8px;
        background: #f8fafc;
        display: flex; flex-direction: column; gap: 10px;
        scroll-behavior: smooth;
    }
    .chat-msg-bot, .chat-msg-user { display: flex; align-items: flex-end; gap: 8px; }
    .chat-msg-user { flex-direction: row-reverse; }
    .chat-msg-bot .av {
        width: 28px; height: 28px; border-radius: 50%;
        background: linear-gradient(135deg, #0284c7, #22c55e);
        color: white; font-size: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .bubble {
        max-width: 80%; padding: 10px 13px; border-radius: 14px;
        font-size: 13.5px; line-height: 1.5;
    }
    .chat-msg-bot .bubble { background: white; color: #1e293b; border: 1px solid #e2e8f0; border-bottom-left-radius: 4px; }
    .chat-msg-user .bubble { background: linear-gradient(135deg, #0284c7, #0ea5e9); color: white; border-bottom-right-radius: 4px; }

    /* Quick suggestion buttons */
    #chat-suggestions {
        padding: 8px 12px; background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex; flex-wrap: wrap; gap: 6px;
        flex-shrink: 0;
    }
    .quick-btn {
        padding: 5px 10px; font-size: 12px; border-radius: 20px;
        border: 1px solid #e2e8f0; background: white; color: #64748b;
        cursor: pointer; transition: all 0.15s;
        white-space: nowrap;
    }
    .quick-btn:hover { background: #f0f9ff; color: #0284c7; border-color: #bae6fd; }

    /* Input area */
    #chat-input-area {
        padding: 10px 12px; background: white;
        border-top: 1px solid #f1f5f9;
        display: flex; gap: 8px; align-items: center;
        flex-shrink: 0;
    }
    #chat-input {
        flex: 1; padding: 8px 12px; border-radius: 20px;
        border: 1px solid #e2e8f0; outline: none;
        font-size: 13.5px; background: #f8fafc; color: #1e293b;
        transition: border-color 0.15s;
    }
    #chat-input:focus { border-color: #0ea5e9; background: white; }
    #chat-send {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, #0284c7, #22c55e);
        color: white; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; transition: transform 0.15s;
    }
    #chat-send:hover { transform: scale(1.08); }

    /* Typing dots */
    .typing-dots { display: flex; gap: 4px; padding: 4px 0; }
    .typing-dots span { width: 7px; height: 7px; border-radius: 50%; background: #94a3b8; animation: bounce 1.2s infinite; }
    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes bounce { 0%,60%,100% { transform: translateY(0); } 30% { transform: translateY(-6px); } }

    @media (max-width: 400px) {
        #chat-panel { width: calc(100vw - 32px); right: 0; }
    }
</style>

<div id="chat-widget">
    <div id="chat-panel" class="hidden">
        <!-- Header -->
        <div id="chat-header">
            <div class="bot-info">
                <div class="avatar"><i class="fas fa-robot"></i></div>
                <div>
                    <div class="title">MediTax Assistant</div>
                    <div class="subtitle"><span class="online-dot"></span>Always here to help</div>
                </div>
            </div>
            <button id="chat-close" onclick="toggleChat()"><i class="fas fa-times"></i></button>
        </div>

        <!-- Messages -->
        <div id="chat-messages"></div>

        <!-- Quick suggestions -->
        <div id="chat-suggestions"></div>

        <!-- Input -->
        <div id="chat-input-area">
            <input id="chat-input" type="text" placeholder="Ask me anything…" autocomplete="off" />
            <button id="chat-send" onclick="sendMessage()"><i class="fas fa-paper-plane" style="font-size:13px"></i></button>
        </div>
    </div>

    <button id="chat-btn" onclick="toggleChat()" title="Chat with support">
        <i class="fas fa-comments" style="font-size:20px"></i>
        <span class="badge" id="chat-badge">1</span>
    </button>
</div>

<script>
(function() {
    // ── Knowledge base ───────────────────────────────────────────────────────
    var KB = [
        // Platform basics
        { k: ['what is meditax','what does meditax','about meditax','what is this'],
          a: "MediTax Connect is a platform that connects healthcare professionals — doctors, dentists, and pharmacies — with certified accountants. You can manage your taxes, upload financial documents, and get expert help all in one place." },
        { k: ['how does it work','get started','how to start','how do i use'],
          a: "It's simple! <b>3 steps:</b><br>1️⃣ Create your profile<br>2️⃣ Upload your financial documents<br>3️⃣ Get matched with a verified accountant<br><br>You can <a href='/register' style='color:#0284c7'>register here</a> to begin." },
        // Pricing
        { k: ['price','pricing','cost','how much','free','subscription','$80','monthly'],
          a: "Here's the pricing:<br><br>🏥 <b>Healthcare Professionals</b> — <b>FREE</b> platform access (you pay accountant fees directly)<br><br>📊 <b>Accountants</b> — <b>$80/month</b> for unlimited client management + a 12% platform commission per contract." },
        // Registration
        { k: ['register','sign up','create account','join'],
          a: "You can register as either a healthcare professional or an accountant:<br><br>→ <a href='/register?role=healthcare' style='color:#0284c7'>Join as Healthcare Pro</a><br>→ <a href='/register?role=accountant' style='color:#0284c7'>Join as Accountant</a>" },
        // Login
        { k: ['login','log in','sign in','forgot password'],
          a: "You can log in from our <a href='/login' style='color:#0284c7'>login page</a>. If you've forgotten your password, please contact support — password reset is coming soon!" },
        // Documents
        { k: ['document','upload','file','invoice','receipt','pdf'],
          a: "You can upload <b>income documents, expenses, and invoices</b> in PDF, JPG, PNG, DOC, or Excel format. Go to the <a href='/documents' style='color:#0284c7'>Documents</a> section in your dashboard after logging in." },
        // Accountants
        { k: ['find accountant','accountant','cpa','tax professional','verified accountant'],
          a: "All accountants on our platform are credential-verified. Healthcare pros can browse and select an accountant from the <a href='/accountants' style='color:#0284c7'>Find Accountants</a> page. Once selected, the accountant gets access to your uploaded documents." },
        // Tax / financial
        { k: ['tax','taxes','tax report','tax calculation','tax rate','deduction'],
          a: "The platform automatically estimates your taxes based on your uploaded income and expenses using a 25% base rate (adjustable). You can view your financial summary and download full tax reports from the <a href='/reports' style='color:#0284c7'>Reports</a> section." },
        // Dashboard
        { k: ['dashboard','where am i','my account','my profile','home page'],
          a: "Your <a href='/dashboard' style='color:#0284c7'>dashboard</a> is your hub — it shows your financial summary, assigned accountant, recent documents, and quick actions tailored to your role." },
        // Security
        { k: ['secure','security','safe','privacy','hipaa','data protection'],
          a: "Your data is protected with:<br>🔒 256-bit encryption in transit & at rest<br>✅ HIPAA-compliant document storage<br>🛡️ Role-based access control<br>🔑 Password hashing (bcrypt)" },
        // Roles
        { k: ['doctor','dentist','pharmacy','pharmacist','healthcare professional'],
          a: "MediTax Connect supports <b>doctors</b>, <b>dentists</b>, and <b>pharmacies</b>. All healthcare professionals get free platform access and can connect with a verified accountant for tax management." },
        { k: ['accountant','cpa','accounting firm'],
          a: "Accountants subscribe for <b>$80/month</b> and get unlimited client management. After being verified, you'll appear in client searches and can access client documents to prepare tax reports." },
        // Commission
        { k: ['commission','12%','platform fee'],
          a: "The platform charges a <b>12% commission</b> on contracts between accountants and clients. This is tracked automatically in your financial records." },
        // AI reports
        { k: ['ai report','ai analysis','artificial intelligence','ai financial'],
          a: "The <a href='/ai-reports' style='color:#0284c7'>AI Reports</a> feature generates comprehensive financial analysis for your practice — including profit margins, tax breakdowns, and recommendations — automatically from your financial data." },
        // Contact / support
        { k: ['contact','support','help','problem','issue','bug','error'],
          a: "Need more help? You can reach us through the Help Center or Contact Us links in the footer. For urgent issues, describe your problem here and we'll do our best to guide you! 🙂" },
        // Subscription
        { k: ['how to activate','activate subscription','payment','pay','trial'],
          a: "Accountants can activate their subscription from the <a href='/subscription' style='color:#0284c7'>Subscription</a> page. A 14-day free trial is available. The flat rate is $80/month with no per-client fees." },
    ];

    var QUICK = [
        "How does it work?",
        "Pricing?",
        "Upload documents",
        "Find an accountant",
        "Security & privacy",
    ];

    var isOpen = false;
    var greeted = false;

    function botMessage(html, delay) {
        delay = delay || 0;
        var msgs = document.getElementById('chat-messages');
        var sugg = document.getElementById('chat-suggestions');

        // Show typing indicator
        var typing = document.createElement('div');
        typing.className = 'chat-msg-bot';
        typing.innerHTML = '<div class="av"><i class="fas fa-robot" style="font-size:12px"></i></div>' +
            '<div class="bubble"><div class="typing-dots"><span></span><span></span><span></span></div></div>';
        msgs.appendChild(typing);
        msgs.scrollTop = msgs.scrollHeight;

        setTimeout(function() {
            typing.remove();
            var wrap = document.createElement('div');
            wrap.className = 'chat-msg-bot';
            wrap.innerHTML = '<div class="av"><i class="fas fa-robot" style="font-size:12px"></i></div>' +
                '<div class="bubble">' + html + '</div>';
            msgs.appendChild(wrap);
            msgs.scrollTop = msgs.scrollHeight;
        }, delay + 700);
    }

    function userMessage(text) {
        var msgs = document.getElementById('chat-messages');
        var wrap = document.createElement('div');
        wrap.className = 'chat-msg-user';
        wrap.innerHTML = '<div class="bubble">' + escHtml(text) + '</div>';
        msgs.appendChild(wrap);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function buildQuickSuggestions() {
        var sugg = document.getElementById('chat-suggestions');
        sugg.innerHTML = '';
        QUICK.forEach(function(q) {
            var btn = document.createElement('button');
            btn.className = 'quick-btn';
            btn.textContent = q;
            btn.onclick = function() { processInput(q); };
            sugg.appendChild(btn);
        });
    }

    function findAnswer(text) {
        var lower = text.toLowerCase();
        for (var i = 0; i < KB.length; i++) {
            for (var j = 0; j < KB[i].k.length; j++) {
                if (lower.indexOf(KB[i].k[j]) !== -1) {
                    return KB[i].a;
                }
            }
        }
        return null;
    }

    function processInput(text) {
        if (!text.trim()) return;
        userMessage(text);
        document.getElementById('chat-input').value = '';

        var answer = findAnswer(text);
        if (answer) {
            botMessage(answer, 200);
        } else {
            botMessage("I'm not sure about that specific question, but I can help with topics like <b>pricing</b>, <b>documents</b>, <b>finding an accountant</b>, <b>tax reports</b>, or <b>security</b>. What would you like to know?", 200);
        }
    }

    window.sendMessage = function() {
        var val = document.getElementById('chat-input').value.trim();
        if (val) processInput(val);
    };

    window.toggleChat = function() {
        isOpen = !isOpen;
        var panel = document.getElementById('chat-panel');
        var badge = document.getElementById('chat-badge');
        var icon  = document.querySelector('#chat-btn > i');

        if (isOpen) {
            panel.classList.remove('hidden');
            icon.className = 'fas fa-times';
            badge.classList.add('hidden');

            if (!greeted) {
                greeted = true;
                buildQuickSuggestions();
                botMessage("👋 Hi! I'm the <b>MediTax Assistant</b>. I can help you with questions about the platform, pricing, documents, tax reports, and more.<br><br>What can I help you with today?", 300);
            }
        } else {
            panel.classList.add('hidden');
            icon.className = 'fas fa-comments';
        }
    };

    // Send on Enter key
    document.getElementById('chat-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') window.sendMessage();
    });
})();
</script>

</body>
</html>
