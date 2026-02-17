<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل اس‌ام‌اس بمبر | SMS Bomber</title>
    <!-- Google Fonts: Vazirmatn -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn-font@v33.003/dist/font-face.css" rel="stylesheet" type="text/css" />
    <style>
        /* Modern Reset & Base */
        * { box-sizing: border-box; outline: none; }
        
        /* Force font on all inputs & buttons explicitly */
        body, input, button, select, textarea, input[type="text"], input[type="number"] {
            font-family: 'Vazirmatn', 'Tahoma', sans-serif !important;
        }

        /* Persian Digits Support */
        @font-face {
            font-family: 'Vazirmatn';
            src: url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn-font@v33.003/dist/Vazirmatn-Regular.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        
        /* Class for Persian numbers if needed specifically, but font handles it usually if config is right */
        .persian-num {
            -moz-font-feature-settings: "ss01";
            -webkit-font-feature-settings: "ss01";
            font-feature-settings: "ss01";
        }
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #00C6FF 0%, #0072FF 100%); /* Vibrant Background */
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Layout - Two Columns (Restored) */
        .app-container {
            display: flex;
            width: 95%;
            max-width: 1200px;
            height: 85vh;
            gap: 20px;
        }

        /* Glassmorphism Effect for Panels */
        .sidebar, .log-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* Right Sidebar - Controls & Stats */
        .sidebar {
            width: 340px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
            position: relative;
            z-index: 2;
        }

        /* Left Content - Logs */
        .log-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }

        /* --- Components --- */

        /* Header & Status */
        .app-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .app-title {
            font-size: 1.4rem;
            font-weight: 900;
            color: #1a1a1a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Online/Offline Badge */
        .status-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            transition: all 0.3s;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            background: #00c853;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(0, 200, 83, 0.2);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 200, 83, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(0, 200, 83, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 200, 83, 0); }
        }
        .status-badge.offline {
            background: #ffebee;
            color: #c62828;
        }
        .status-badge.offline .status-dot {
            background: #f44336;
            animation: none;
        }

        /* Input Form */
        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .input-label {
            font-size: 0.9rem;
            color: #555;
            font-weight: 600;
        }
        .input-field {
            padding: 14px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1.1rem;
            text-align: center;
            transition: all 0.3s;
            background: #f8f9fa;
            width: 100%;
            direction: ltr; /* Phone number left-to-right */
            font-family: 'Vazirmatn', sans-serif;
            letter-spacing: 1px;
        }
        .input-field:focus {
            border-color: #0072FF;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0, 114, 255, 0.1);
        }

        /* Buttons Grid */
        .btn-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Vazirmatn', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-start {
            background: linear-gradient(135deg, #da22ff 0%, #9733ee 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(218, 34, 255, 0.3);
        }
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(218, 34, 255, 0.4);
        }
        .btn-start:active { transform: translateY(0); }

        .btn-check {
            background: #fff;
            color: #555;
            border: 2px solid #e0e0e0;
        }
        .btn-check:hover {
            background: #f1f3f5;
            border-color: #ccc;
            color: #333;
        }

        /* Stats Grid (Restored) */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 10px;
        }
        .stat-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            border: 1px solid #edf2f7;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        
        .stat-val {
            font-size: 1.4rem;
            font-weight: 900;
            display: block;
            margin-bottom: 4px;
            font-family: 'Vazirmatn';
        }
        .stat-lbl {
            font-size: 0.75rem;
            color: #888;
            font-weight: 500;
        }
        
        /* Color themes for stats */
        .s-total .stat-val { color: #607d8b; }
        .s-sent .stat-val { color: #2196f3; }
        .s-success .stat-val { color: #00c853; }
        .s-fail .stat-val { color: #ff1744; }

        /* Messages */
        .msg-box {
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            font-size: 0.9rem;
            margin-top: 10px;
            display: none;
            font-weight: bold;
            animation: fadeIn 0.3s;
        }
        .msg-error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .msg-done { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .msg-pending { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }

        /* Log Panel */
        .log-header-title {
            font-size: 1.1rem;
            color: #444;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 800;
        }
        .log-console {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px; /* Scrollbar space */
            position: relative;
        }
        /* Custom Scrollbar */
        .log-console::-webkit-scrollbar { width: 6px; }
        .log-console::-webkit-scrollbar-track { background: transparent; }
        .log-console::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        .log-console::-webkit-scrollbar-thumb:hover { background: #bbb; }

        /* Improved Log Entry */
        .log-entry {
            background: #fff;
            border-radius: 10px;
            margin-bottom: 10px;
            padding: 12px 16px;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            border: 1px solid #f0f0f0;
            border-right: 4px solid #ddd; /* Indicator */
            transition: all 0.2s;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }

        .log-entry:hover {
            transform: translateX(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .lx-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .lx-domain {
            font-weight: 700;
            color: #333;
            background: #f1f3f5;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
        }

        .lx-status {
            font-weight: 800;
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 6px;
        }

         /* Status Colors */
         .st-SUCCESS { border-right-color: #00c853 !important; }
         .st-SUCCESS .lx-status { color: #00c853; background: #e8f5e9; }
         
         .st-ALIVE { border-right-color: #00c853 !important; }
         .st-ALIVE .lx-status { color: #00c853; background: #e8f5e9; }

         .st-ERROR { border-right-color: #ff1744 !important; }
         .st-ERROR .lx-status { color: #ff1744; background: #ffebee; }
         
         .st-CRITICAL { border-right-color: #d50000 !important; }
         .st-CRITICAL .lx-status { color: #d50000; background: #ffcdd2; }

         .st-WARN { border-right-color: #ff9100 !important; }
         .st-WARN .lx-status { color: #ff9100; background: #fff8e1; }

        .lx-details {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
            display: none; /* Hidden by default */
            background: #f9f9f9;
            padding: 8px;
            border-radius: 6px;
            font-family: monospace;
            direction: ltr;
            text-align: left;
            word-break: break-all;
        }
        
        /* Show Log Toggle */
        .show-log-btn {
            font-size: 0.75rem;
            color: #999;
            cursor: pointer;
            text-decoration: underline;
            margin-top: 5px;
            display: inline-block;
        }

        /* Footer */
        .footer-link {
            text-align: center;
            margin-top: auto;
            opacity: 0.6;
            transition: opacity 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            color: #555;
            text-decoration: none;
        }
        .footer-link:hover { opacity: 1; color: #000;}
        .footer-link img { width: 22px; }

        /* Responsive */
        @media (max-width: 768px) {
            .app-container { flex-direction: column; height: auto; min-height: 100vh; padding: 20px 0;}
            .sidebar { width: 100%; order: 1; height: auto; }
            .log-panel { width: 100%; order: 2; height: 500px; }
        }
    </style>
</head>
<body>

<div class="app-container">
    
    <!-- RIGHT SIDEBAR: CONTROLS & STATS -->
    <div class="sidebar">
        
        <!-- Header -->
        <div class="app-header">
            <h1 class="app-title">SMS Bomber 💣</h1>
            <div id="connectionStatus" class="status-badge">
                <div class="status-dot"></div>
                <span id="statusText">آنلاین</span>
            </div>
        </div>

        <hr style="border:0; border-top:1px solid #eee; width:100%;">

        <!-- Form -->
        <div class="input-group">
            <label class="input-label">شماره موبایل هدف</label>
            <input type="text" id="phone" class="input-field persian-num" placeholder="09xxxxxxxxx" maxlength="11">
        </div>

        <div class="btn-grid">
            <button onclick="runCheck()" class="btn btn-check" id="checkBtn">
                <span>🛡️</span> بررسی وضعیت سرویس‌ها
            </button>
            <button onclick="startBomber(event)" class="btn btn-start" id="startBtn">
                شروع بمباران <span style="font-size:1.2rem;">🚀</span>
            </button>
        </div>

        <!-- Messages -->
        <div id="errorMsg" class="msg-box msg-error">فرمت شماره صحیح نیست!</div>
        <div id="pendingMsg" class="msg-box msg-pending">در حال ارسال...</div>
        <div id="doneMsg" class="msg-box msg-done">عملیات پایان یافت.</div>

        <!-- Stats Grid -->
        <div class="input-label" style="margin-top: 10px;">آمار لحظه‌ای: <span id="modeLabel" style="font-size:0.8rem; color:#da22ff; font-weight:normal;"></span></div>
        <div class="stats-grid">
            <div class="stat-card s-total">
                <span class="stat-val persian-num" id="stTotalServices">-</span>
                <span class="stat-lbl">کل سرویس‌ها</span>
            </div>
            <div class="stat-card s-sent">
                <span class="stat-val persian-num" id="stSent">0</span>
                <span class="stat-lbl">درخواست‌ها</span>
            </div>
            <div class="stat-card s-success">
                <span class="stat-val persian-num" id="stSuccess">0</span>
                <span class="stat-lbl">موفق / سالم</span>
            </div>
            <div class="stat-card s-fail">
                <span class="stat-val persian-num" id="stFail">0</span>
                <span class="stat-lbl">ناموفق / خراب</span>
            </div>
        </div>

        <!-- Footer -->
        <a href="https://github.com/amirmalek0" target="_blank" class="footer-link">
            <img src="assets/img/github.png" alt="GitHub">
            <span>صفحه گیت‌هاب پروژه</span>
        </a>

    </div>

    <!-- LEFT CONTENT: LOGS -->
    <div class="log-panel">
        <div class="log-header-title">
            <span>📜 گزارش عملیات</span>
            <span style="font-size:0.8rem; color:#888;">بروزرسانی زنده</span>
        </div>
        <div id="logContainer" class="log-console">
            <div style="text-align:center; color:#999; margin-top:50px; line-height:2;">
                برای شروع شماره را وارد کنید.<br>
                برای حذف سرویس‌های غیرفعال، دکمه «بررسی» را بزنید.
            </div>
        </div>
    </div>

</div>

<script>
    // --- Elements ---
    const phoneInput = document.getElementById('phone');
    const logContainer = document.getElementById('logContainer');
    const statusBadge = document.getElementById('connectionStatus');
    const statusText = document.getElementById('statusText');
    const modeLabel = document.getElementById('modeLabel');
    
    // Msgs
    const errorMsg = document.getElementById('errorMsg');
    const pendingMsg = document.getElementById('pendingMsg');
    const doneMsg = document.getElementById('doneMsg');

    // Stats
    const stTotalServices = document.getElementById('stTotalServices');
    const stSent = document.getElementById('stSent');
    const stSuccess = document.getElementById('stSuccess');
    const stFail = document.getElementById('stFail');

    let totalServicesCount = 0;
    let lastLogLength = 0;
    let isRunning = false;
    let logInterval = null;

    // --- Status Check ---
    function updateOnlineStatus() {
        if (navigator.onLine) {
            statusBadge.classList.remove('offline');
            statusText.innerText = 'آنلاین';
        } else {
            statusBadge.classList.add('offline');
            statusText.innerText = 'آفلاین';
        }
    }
    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);
    updateOnlineStatus();

    // --- Core Logic ---

    function resetUI(msgText = 'در حال ارسال...') {
        errorMsg.style.display = 'none';
        doneMsg.style.display = 'none';
        
        pendingMsg.innerText = msgText;
        pendingMsg.style.display = 'block';
        
        logContainer.innerHTML = '';
        
        totalServicesCount = 0;
        lastLogLength = 0;
        isRunning = true;
        
        stTotalServices.innerText = '-';
        stSent.innerText = '0';
        stSuccess.innerText = '0';
        stFail.innerText = '0';
        modeLabel.innerText = '';
    }

    function startLogReader() {
        if(logInterval) clearInterval(logInterval);
        
        logInterval = setInterval(() => {
            fetch('log.txt?t=' + Date.now())
                .then(res => res.text())
                .then(data => {
                    if(!data) return;
                    const lines = data.trim().split('\n');
                    
                    if (lines.length > lastLogLength) {
                        const newLines = lines.slice(lastLogLength);
                        lastLogLength = lines.length;

                        newLines.forEach(line => {
                            if (!line.trim()) return;
                            try {
                                const log = JSON.parse(line);
                                
                                // META TYPE
                                if (log.type === 'meta') {
                                    totalServicesCount = log.total;
                                    stTotalServices.innerText = totalServicesCount;
                                    modeLabel.innerText = (log.mode === 'check' ? '(حالت بررسی)' : '(حالت حمله)');
                                    return;
                                }

                                // SERVICE LOG
                                addLogCard(log);
                                updateStats(log);

                            } catch (e) { }
                        });

                        // Scroll to bottom
                        logContainer.scrollTop = logContainer.scrollHeight;
                    }
                });
        }, 800);
    }

    function updateStats(log) {
        let sent = parseInt(stSent.innerText) + 1;
        stSent.innerText = sent;

        // In check mode 'ALIVE' or 'WARN' is good. In Attack 'SUCCESS' is good.
        // We look at the label provided by backend.
        if (['SUCCESS', 'ALIVE', 'WARN'].includes(log.label)) {
             stSuccess.innerText = parseInt(stSuccess.innerText) + 1;
        } else {
             stFail.innerText = parseInt(stFail.innerText) + 1;
        }
    }

    // --- Action: Check Services ---
    function runCheck() {
        if(isRunning) return;
        if(!confirm("آیا از بررسی تمام سرویس‌ها اطمینان دارید؟\nاین عملیات ممکن است طول بکشد.")) return;

        resetUI('در حال بررسی و بروزرسانی لیست سیاه...');
        startLogReader();

        fetch('sms.php?action=check')
            .then(() => {
                isRunning = false;
                pendingMsg.style.display = 'none';
                doneMsg.innerText = "بررسی تمام شد. لیست سیاه بروز شد.";
                doneMsg.style.display = 'block';
            })
            .catch(() => {
                isRunning = false;
                pendingMsg.style.display = 'none';
                errorMsg.innerText = "خطا در ارتباط با سرور";
                errorMsg.style.display = 'block';
            });
    }

    // --- Action: Start Attack ---
    function startBomber(e) {
        if(e) e.preventDefault();
        if(isRunning) return;
        
        const phone = phoneInput.value;
        const regex = /^09[0-9]{9}$/;

        if (!regex.test(phone)) {
            errorMsg.innerText = "فرمت شماره صحیح نیست! (مثلا 09123456789)";
            errorMsg.style.display = 'block';
            setTimeout(() => errorMsg.style.display = 'none', 3000);
            return;
        }

        resetUI('در حال ارسال بمب پیامکی...');
        startLogReader();

        // Send Request
        const formData = new FormData();
        formData.append('phone', phone);
        formData.append('action', 'attack');

        fetch('sms.php', { method: 'POST', body: formData })
            .then(() => {
                isRunning = false;
                pendingMsg.style.display = 'none';
                doneMsg.innerText = "عملیات بمباران پایان یافت.";
                doneMsg.style.display = 'block';
            })
            .catch(err => {
                isRunning = false;
                pendingMsg.style.display = 'none';
                errorMsg.innerText = "خطا در ارتباط با سرور";
                errorMsg.style.display = 'block';
            });
    }

    function addLogCard(log) {
        const div = document.createElement('div');
        div.className = 'log-entry';
        
        let label = log.label || 'UNKNOWN';
        div.classList.add('st-' + label);
        
        // Response Text Logic
        let displayResp = log.response || '';
        if (displayResp.includes('[HTML Hidden]')) {
             displayResp = '<i style="color:#aaa">محتوای HTML نادیده گرفته شد</i>';
        } else {
            displayResp = displayResp.replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }

        // Generate ID for toggle
        const toggleId = 'det-' + Math.random().toString(36).substr(2, 9);

        div.innerHTML = `
            <div class="lx-header">
                <span class="lx-domain">${log.domain || log.service}</span>
                <span class="lx-status">${label} ${log.status != 0 ? log.status : ''}</span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span class="lx-meta">${log.timestamp}</span>
                <span class="show-log-btn" onclick="document.getElementById('${toggleId}').style.display = document.getElementById('${toggleId}').style.display === 'block' ? 'none' : 'block'">نمایش جزئیات</span>
            </div>
            <div id="${toggleId}" class="lx-details">
                <div>URL: ${log.url}</div>
                <div style="margin-top:4px;">RESP: ${displayResp}</div>
                ${log.error ? '<div style="color:#d32f2f; margin-top:4px;">ERR: ' + log.error + '</div>' : ''}
            </div>
        `;
        logContainer.appendChild(div);
    }

</script>

</body>
</html>