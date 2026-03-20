<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-3xl bg-[#161b22] p-8 border border-white/5 shadow-2xl">
        <!-- Background Decoration -->
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-blue-500/5 blur-3xl"></div>

        <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex-1 space-y-4">
                <div class="space-y-1">
                    <h2 class="text-3xl font-black tracking-tight text-white">
                        Welcome back, <span class="text-emerald-500">{{ $userName }}</span>! 👋
                    </h2>
                    <p class="text-gray-400 font-medium">
                        Here's what's happening at <span class="text-white font-bold">ZweToe Pharmacy</span> today.
                    </p>
                </div>

                <div class="flex flex-wrap gap-4 pt-2">
                    <div class="flex items-center gap-3 bg-[#0d1117]/80 backdrop-blur-md border border-white/5 px-5 py-3 rounded-2xl">
                        <div class="p-2 bg-emerald-500/20 rounded-xl">
                            <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xl font-black text-white tabular-nums">{{ $pendingOrders }}</span>
                            <span class="text-[10px] uppercase tracking-widest font-black text-gray-500">Pending Orders</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-[#0d1117]/80 backdrop-blur-md border border-white/5 px-5 py-3 rounded-2xl">
                        <div class="p-2 bg-amber-500/20 rounded-xl">
                            <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xl font-black text-white tabular-nums">{{ $lowStockCount }}</span>
                            <span class="text-[10px] uppercase tracking-widest font-black text-gray-500">Low Stock Items</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden md:block">
                <div class="relative h-32 w-32">
                    <div class="absolute inset-0 animate-pulse rounded-full bg-emerald-500/10"></div>
                    <div class="flex h-full w-full items-center justify-center rounded-full border border-emerald-500/20 bg-[#0d1117]/50 backdrop-blur-xl">
                        <svg class="h-16 w-16 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
