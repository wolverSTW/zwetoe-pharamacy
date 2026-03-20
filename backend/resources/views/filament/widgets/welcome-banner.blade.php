<x-filament-widgets::widget>
    <style>
        .zw-welcome-card {
            background: #0f172a;
            border-radius: 1.5rem;
            position: relative;
            overflow: hidden;
            padding: 2.5rem;
            color: white;
            font-family: inherit;
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .zw-gradient-1 {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
            filter: blur(40px);
            z-index: 0;
        }
        .zw-gradient-2 {
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            filter: blur(40px);
            z-index: 0;
        }
        .zw-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }
        .zw-title {
            font-size: 1.875rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }
        .zw-highlight {
            color: #10b981;
        }
        .zw-subtitle {
            color: #94a3b8;
            font-weight: 500;
        }
        .zw-stats-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }
        .zw-stat-pill {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08);
            padding: 0.75rem 1.25rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }
        .zw-stat-pill:hover {
            border-color: rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
        }
        .zw-stat-value {
            font-size: 1.25rem;
            font-weight: 800;
        }
        .zw-stat-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            font-weight: 700;
        }
        .zw-icon-box {
            padding: 0.5rem;
            border-radius: 0.75rem;
        }
        .zw-icon-emerald { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .zw-icon-amber { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .zw-update-tag {
            font-size: 0.625rem;
            color: rgba(16, 185, 129, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-left: 0.5rem;
        }
        .zw-avatar-container {
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.05);
            border: 1px solid rgba(16, 185, 129, 0.2);
            display: none;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        @media (min-width: 768px) {
            .zw-avatar-container { display: flex; }
        }
        .zw-pulse {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #10b981;
            opacity: 0.1;
            animation: zw-pulse-anim 2s infinite;
        }
        @keyframes zw-pulse-anim {
            0% { transform: scale(1); opacity: 0.1; }
            50% { transform: scale(1.2); opacity: 0.05; }
            100% { transform: scale(1); opacity: 0.1; }
        }
    </style>

    <div class="zw-welcome-card">
        <div class="zw-gradient-1"></div>
        <div class="zw-gradient-2"></div>

        <div class="zw-content">
            <div style="flex: 1;">
                <div class="zw-title">
                    Welcome back, <span class="zw-highlight">{{ $userName }}</span>! 👋
                </div>
                <div class="zw-subtitle">
                    ZweToe Pharmacy Monitor <span class="zw-update-tag">Auto-Sync @ {{ $lastUpdate }}</span>
                </div>

                <div class="zw-stats-group">
                    <div class="zw-stat-pill">
                        <div class="zw-icon-box zw-icon-emerald">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div>
                            <div class="zw-stat-value">{{ $pendingOrders }}</div>
                            <div class="zw-stat-label">Pending Orders</div>
                        </div>
                    </div>

                    <div class="zw-stat-pill">
                        <div class="zw-icon-box zw-icon-amber">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <div class="zw-stat-value">{{ $lowStockCount }}</div>
                            <div class="zw-stat-label">Low Stock Items</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="zw-avatar-container">
                <div class="zw-pulse"></div>
                <svg style="width: 3rem; height: 3rem; color: #10b981; opacity: 0.8;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
