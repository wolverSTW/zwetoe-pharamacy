<x-filament-widgets::widget>
    <style>
        .zw-welcome-card {
            background: linear-gradient(135deg, #0f172a 0%, #0c1a2e 100%);
            border-radius: 1.5rem;
            position: relative;
            overflow: hidden;
            padding: 2.25rem 2.5rem;
            color: white;
            font-family: inherit;
            border: 1px solid rgba(16, 185, 129, 0.12);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255,255,255,0.04);
        }
        .zw-glow-1 {
            position: absolute; top: -80px; right: -60px;
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.14) 0%, transparent 65%);
            filter: blur(50px); z-index: 0;
        }
        .zw-glow-2 {
            position: absolute; bottom: -80px; left: -60px;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.08) 0%, transparent 65%);
            filter: blur(50px); z-index: 0;
        }
        .zw-glow-3 {
            position: absolute; top: 50%; left: 40%;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.06) 0%, transparent 65%);
            filter: blur(40px); z-index: 0;
        }
        .zw-content {
            position: relative; z-index: 1;
            display: flex; align-items: center;
            justify-content: space-between; gap: 2rem;
        }
        .zw-left { flex: 1; min-width: 0; }
        .zw-eyebrow {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 3px 12px; border-radius: 99px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            font-size: 0.6rem; font-weight: 700; letter-spacing: 0.12em;
            text-transform: uppercase; color: #10b981;
            margin-bottom: 0.75rem;
        }
        .zw-live-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #10b981; animation: zw-live-pulse 2s infinite;
        }
        @keyframes zw-live-pulse {
            0%, 100% { opacity: 1; } 50% { opacity: 0.3; }
        }
        .zw-title {
            font-size: 1.75rem; font-weight: 800;
            margin-bottom: 0.35rem; letter-spacing: -0.025em; line-height: 1.2;
        }
        .zw-highlight { color: #10b981; }
        .zw-subtitle {
            color: #64748b; font-size: 0.8rem; font-weight: 500;
        }
        .zw-time-tag { color: #10b981; opacity: 0.8; }
        .zw-pills {
            display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1.75rem;
        }
        .zw-pill {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.07);
            padding: 0.65rem 1.1rem; border-radius: 0.875rem;
            display: flex; align-items: center; gap: 0.65rem;
            transition: all 0.2s ease; cursor: default;
        }
        .zw-pill:hover {
            border-color: rgba(16, 185, 129, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -8px rgba(16, 185, 129, 0.2);
        }
        .zw-pill-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .zw-pill-icon svg { width: 16px; height: 16px; }
        .icon-emerald { background: rgba(16,185,129,0.15); color: #10b981; }
        .icon-amber   { background: rgba(245,158,11,0.15);  color: #f59e0b; }
        .icon-sky     { background: rgba(56,189,248,0.15);  color: #38bdf8; }
        .icon-rose    { background: rgba(244,63,94,0.15);   color: #f43f5e; }
        .icon-violet  { background: rgba(139,92,246,0.15);  color: #8b5cf6; }
        .zw-pill-val {
            font-size: 1.05rem; font-weight: 800; line-height: 1;
        }
        .zw-pill-lbl {
            font-size: 0.58rem; text-transform: uppercase; letter-spacing: 0.09em;
            color: #475569; font-weight: 700; line-height: 1.3;
        }
        .zw-right {
            display: none; align-items: center; justify-content: center;
            width: 110px; height: 110px; border-radius: 50%;
            background: rgba(16,185,129,0.05); border: 1px solid rgba(16,185,129,0.15);
            position: relative; flex-shrink: 0;
        }
        @media (min-width: 768px) { .zw-right { display: flex; } }
        .zw-ring {
            position: absolute; inset: -8px; border-radius: 50%;
            border: 1px dashed rgba(16,185,129,0.2);
            animation: zw-spin 20s linear infinite;
        }
        @keyframes zw-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .zw-ring-dot {
            position: absolute; top: 4px; left: 50%; transform: translateX(-50%);
            width: 6px; height: 6px; border-radius: 50%; background: #10b981;
        }
    </style>

    <div class="zw-welcome-card">
        <div class="zw-glow-1"></div>
        <div class="zw-glow-2"></div>
        <div class="zw-glow-3"></div>

        <div class="zw-content">
            <div class="zw-left">
                <div class="zw-eyebrow">
                    <div class="zw-live-dot"></div>
                    Live Dashboard · Auto-Sync @ {{ $lastUpdate }}
                </div>

                <div class="zw-title">
                    Welcome back, <span class="zw-highlight">{{ $userName }}</span> 👋
                </div>
                <div class="zw-subtitle">
                    ZweToe Pharmacy Management System
                    <span class="zw-time-tag">· {{ now()->format('D, M d Y') }}</span>
                </div>

                <div class="zw-pills">
                    {{-- Today Revenue --}}
                    <div class="zw-pill">
                        <div class="zw-pill-icon icon-emerald">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="zw-pill-val" style="color:#10b981;">{{ number_format($todayRevenue) }}</div>
                            <div class="zw-pill-lbl">Today's Revenue (MMK)</div>
                        </div>
                    </div>

                    {{-- Pending Orders --}}
                    <div class="zw-pill">
                        <div class="zw-pill-icon {{ $pendingOrders > 0 ? 'icon-amber' : 'icon-emerald' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div>
                            <div class="zw-pill-val" style="color:{{ $pendingOrders > 0 ? '#f59e0b' : '#10b981' }};">{{ $pendingOrders }}</div>
                            <div class="zw-pill-lbl">Pending Orders</div>
                        </div>
                    </div>

                    {{-- Total Orders --}}
                    <div class="zw-pill">
                        <div class="zw-pill-icon icon-sky">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <div class="zw-pill-val" style="color:#38bdf8;">{{ $totalOrders }}</div>
                            <div class="zw-pill-lbl">Total Orders</div>
                        </div>
                    </div>

                    {{-- Low Stock --}}
                    <div class="zw-pill">
                        <div class="zw-pill-icon {{ $lowStockCount > 0 ? 'icon-rose' : 'icon-emerald' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <div class="zw-pill-val" style="color:{{ $lowStockCount > 0 ? '#f43f5e' : '#10b981' }};">{{ $lowStockCount }}</div>
                            <div class="zw-pill-lbl">Low Stock Items</div>
                        </div>
                    </div>

                    {{-- Pending Customers --}}
                    @if($pendingCustomers > 0)
                    <div class="zw-pill">
                        <div class="zw-pill-icon icon-violet">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <div class="zw-pill-val" style="color:#8b5cf6;">{{ $pendingCustomers }}</div>
                            <div class="zw-pill-lbl">Pending Customers</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Right Side Graphic --}}
            <div class="zw-right">
                <div class="zw-ring"><div class="zw-ring-dot"></div></div>
                <svg style="width:52px; height:52px; color:#10b981; opacity:0.85;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3H15V9H21V15H15V21H9V15H3V9H9V3Z" />
                </svg>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
