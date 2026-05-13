<x-filament-widgets::widget>
    <style>
        .zw-welcome-card {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            color: #1e293b;
            font-family: inherit;
            border: 1px solid #e2e8f0;
        }
        .zw-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
        }
        .zw-left { flex: 1; min-width: 0; }
        .zw-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 99px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            font-size: 0.7rem;
            font-weight: 600;
            color: #059669;
            margin-bottom: 0.5rem;
        }
        .zw-live-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #10b981;
            animation: zw-pulse 2s infinite;
        }
        @keyframes zw-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        .zw-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }
        .zw-highlight { color: #059669; }
        .zw-subtitle {
            color: #64748b;
            font-size: 0.8rem;
        }
        .zw-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.25rem;
        }
        .zw-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            transition: border-color 0.2s;
        }
        .zw-pill:hover {
            border-color: #a7f3d0;
        }
        .zw-pill-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .zw-pill-icon svg { width: 16px; height: 16px; }
        .icon-emerald { background: #ecfdf5; color: #059669; }
        .icon-amber   { background: #fffbeb; color: #d97706; }
        .icon-sky     { background: #f0f9ff; color: #0284c7; }
        .icon-rose    { background: #fff1f2; color: #e11d48; }
        .icon-violet  { background: #f5f3ff; color: #7c3aed; }
        .zw-pill-val {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1;
        }
        .zw-pill-lbl {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            font-weight: 600;
        }
        .zw-right {
            display: none;
            align-items: center;
            justify-content: center;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: #ecfdf5;
            flex-shrink: 0;
        }
        @media (min-width: 768px) { .zw-right { display: flex; } }
    </style>

    <div class="zw-welcome-card">
        <div class="zw-content">
            <div class="zw-left">
                <div class="zw-badge">
                    <div class="zw-live-dot"></div>
                    Dashboard · {{ $lastUpdate }}
                </div>

                <div class="zw-title">
                    Welcome back, <span class="zw-highlight">{{ $userName }}</span> 👋
                </div>
                <div class="zw-subtitle">
                    ZweToe Pharmacy Management
                    <span style="color:#94a3b8;">· {{ now()->format('D, M d Y') }}</span>
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
                            <div class="zw-pill-val" style="color:#059669;">{{ number_format($todayRevenue) }}</div>
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
                            <div class="zw-pill-val" style="color:{{ $pendingOrders > 0 ? '#d97706' : '#059669' }};">{{ $pendingOrders }}</div>
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
                            <div class="zw-pill-val" style="color:#0284c7;">{{ $totalOrders }}</div>
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
                            <div class="zw-pill-val" style="color:{{ $lowStockCount > 0 ? '#e11d48' : '#059669' }};">{{ $lowStockCount }}</div>
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
                            <div class="zw-pill-val" style="color:#7c3aed;">{{ $pendingCustomers }}</div>
                            <div class="zw-pill-lbl">Pending Customers</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Right Side Icon --}}
            <div class="zw-right">
                <svg style="width:40px; height:40px; color:#059669;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3H15V9H21V15H15V21H9V15H3V9H9V3Z" />
                </svg>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
