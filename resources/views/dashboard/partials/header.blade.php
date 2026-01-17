<header class="fixed top-0 inset-x-0 z-40 bg-white/95 backdrop-blur-sm border-b border-gray-100 shadow-sm"
    dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

        {{-- Left Section: Logo --}}
        <div class="flex items-center gap-3">
            <a href="{{ url('dashboard') }}" class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col leading-tight">
                    <span class="text-2xl font-bold text-indigo-600">RealEstate</span>
                    <span class="text-xs text-gray-500">Dashboard</span>
                </div>
                <img src="/logo.png" alt="logo" class="h-10 w-10 object-contain">
            </a>
        </div>

        {{-- Center (optional empty space for future search or breadcrumbs) --}}
        <div class="flex-1 hidden md:flex items-center justify-center"></div>

        {{-- Right Section --}}
        <div class="flex items-center gap-3">

{{-- Notifications Dropdown (improved positioning & styling + nicer arrow) --}}
<div class="relative" x-data="{
        open: false,
        left: '0px',
        top: '0px',
        calculate() {
            const btn = this.$refs.btn;
            const panel = this.$refs.panel;
            if (!btn || !panel) return;

            const btnRect = btn.getBoundingClientRect();
            const panelWidth = panel.offsetWidth;
            const margin = 12; // gap from edges

            // center panel under button
            let left = btnRect.left + (btnRect.width / 2) - (panelWidth / 2);
            const maxLeft = window.innerWidth - panelWidth - margin;
            const minLeft = margin;
            if (left < minLeft) left = minLeft;
            if (left > maxLeft) left = maxLeft;

            // vertical position just below button (fixed to viewport)
            const top = btnRect.bottom + 12; // 12px gap

            this.left = left + 'px';
            this.top = top + 'px';
        },
        initListeners() {
            // recalc on resize & scroll
            this._resizeHandler = () => this.open && this.calculate();
            window.addEventListener('resize', this._resizeHandler);
            window.addEventListener('scroll', this._resizeHandler, true);
        },
        destroyListeners() {
            window.removeEventListener('resize', this._resizeHandler);
            window.removeEventListener('scroll', this._resizeHandler, true);
        }
    }"
    x-init="initListeners()"
    x-on:keydown.escape.window="open = false; $nextTick(()=>{})"
    x-cloak
    @click.outside="open = false"
>
    <button
        x-ref="btn"
        @click="open = !open; if(open){ $nextTick(()=> calculate()) }"
        class="relative p-2 rounded-full hover:bg-indigo-50 transition focus:outline-none"
        aria-haspopup="true" aria-expanded="false"
    >
        {{-- Bell Icon --}}
        <svg xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-indigo-600"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                a6.002 6.002 0 00-4-5.659V5
                a2 2 0 10-4 0v.341
                C7.67 6.165 6 8.388 6 11v3.159
                c0 .538-.214 1.055-.595 1.436L4 17h5m6 0
                a3 3 0 11-6 0h6z" />
        </svg>

        {{-- Unread badge --}}
        @php
            $userNotifications = auth()->user()->unreadNotifications;
            if(auth()->user()->hasRole('employee')) {
                $userNotifications = $userNotifications->where('data.type', 'booking');
            }
        @endphp

        @if($userNotifications->count())
            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold px-1.5 rounded-full animate-pulse">
                {{ $userNotifications->count() }}
            </span>
        @endif
    </button>

    {{-- Floating dropdown panel (positioned via JS) --}}
    <div
        x-ref="panel"
        x-show="open"
        x-transition:enter="transition ease-out duration-150 transform"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100 transform"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="position: fixed; left: 0; top: 0;"
        :style="{ left: left, top: top }"
        class="w-96 max-w-[90vw] bg-white border border-gray-100 rounded-2xl shadow-2xl z-50 overflow-hidden ring-1 ring-indigo-100"
    >
        {{-- Elegant arrow (SVG) --}}
        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 z-50 pointer-events-none">
            <svg class="h-6 w-6 filter drop-shadow-sm" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <!-- white triangle background with soft border -->
                <path d="M3 10L12 3l9 7H3z" fill="white" stroke="#E6E9F0" stroke-width="1"/>
                <!-- subtle indigo accent line -->
                <path d="M6.5 9.2L12 4.4l5.5 4.8" stroke="rgba(79,70,229,0.08)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        {{-- Header --}}
        <div class="px-6 py-3 font-semibold text-gray-700 border-b border-gray-100 bg-indigo-50 flex items-center justify-between">
            <span>Notifications</span>
            @if($userNotifications->count())
                <form method="POST" action="{{ route('notifications.read') }}">
                    @csrf
                    <button type="submit" class="text-indigo-600 text-xs hover:underline">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 bg-white">
            @forelse($userNotifications->take(6) as $notification)
                @php $isUnread = is_null($notification->read_at); @endphp
                <div class="px-4 py-3 flex items-start gap-3 hover:bg-indigo-50 transition cursor-pointer group">
                    {{-- Unread indicator --}}
                    <span class="h-3 w-3 mt-1 rounded-full {{ $isUnread ? 'bg-indigo-600' : 'bg-gray-300' }}"></span>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate group-hover:underline">
                            {{ $notification->data['message'] ?? 'No message' }}
                        </p>
                        <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                            <span>by <span class="text-gray-700 font-medium">{{ $notification->data['by'] ?? 'System' }}</span></span>
                            <span class="text-gray-300">•</span>
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        @if($notification->data['extra'] ?? false)
                            <div class="mt-2 text-xs text-gray-500">
                                {{ Str::limit($notification->data['extra'], 120) }}
                            </div>
                        @endif
                    </div>

                    {{-- subtle action chevron --}}
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-gray-300 group-hover:text-indigo-400 transition" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-gray-500">
                    No new notifications
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if($userNotifications->count() > 6)
            <div class="px-6 py-2 text-center text-xs text-indigo-600 hover:bg-indigo-50 cursor-pointer transition">
                View all notifications
            </div>
        @endif
    </div>
</div>




            {{-- Language Switcher --}}
            <div class="flex items-center gap-2 {{ app()->getLocale() == 'ar' ? 'border-l pl-3' : 'border-r pr-3' }} border-gray-100">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-100 rounded-lg hover:bg-indigo-50 transition focus:outline-none">
                        <span>{{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}</span>
                        <svg class="h-3 w-3 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Language Options --}}
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-2 w-32 bg-white border border-gray-100 rounded-lg shadow-xl z-[60] overflow-hidden">
                        <a href="{{ url('lang/ar') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition {{ app()->getLocale() == 'ar' ? 'bg-indigo-50 font-bold' : '' }}">
                            {{ __('messages.header.arabic') }}
                        </a>
                        <a href="{{ url('lang/en') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition {{ app()->getLocale() == 'en' ? 'bg-indigo-50 font-bold' : '' }}">
                            {{ __('messages.header.english') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- New Property Button (Admin Only) --}}
            @role('admin')
                <a href="{{ url('dashboard/properties/create') }}"
                    class="hidden md:inline-flex items-center gap-2 px-3 py-2 rounded-full bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold shadow hover:scale-[1.02] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ __('messages.header.new_property') }}
                </a>
            @endrole

            {{-- User Menu --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" aria-haspopup="true" aria-expanded="false"
                    class="flex items-center gap-3 p-2 rounded-full hover:bg-indigo-50 transition focus:outline-none">
                    <div class="h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-semibold">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'G', 0, 1)) }}
                    </div>
                    <div class="hidden sm:flex flex-col {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        <span class="text-sm font-medium text-gray-700">{{ auth()->user()?->name ?? 'Guest' }}</span>
                    </div>
                    <svg class="h-4 w-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Dropdown --}}
                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-2 w-56 bg-white border border-gray-100 rounded-lg shadow-lg overflow-hidden z-50">

                    <div class="px-4 py-3">
                        {{-- Role Badge --}}
                        <div class="flex {{ app()->getLocale() == 'ar' ? 'justify-start' : 'justify-end' }} mb-1">
                            @role('admin')
                                <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700 font-semibold">
                                    {{ __('messages.header.admin_badge') }}
                                </span>
                            @elserole('employee')
                                <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-50 text-indigo-700 font-medium">
                                    {{ __('messages.header.employee_badge') }}
                                </span>
                            @endrole
                        </div>
                        <p class="text-sm font-medium text-gray-800">{{ auth()->user()?->name ?? 'Guest' }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()?->email ?? 'guest@example.com' }}</p>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    {{-- Navigation Links --}}
                    @role('admin')
                        <a href="{{ url('dashboard/properties') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-start">{{ __('messages.sidebar.properties') }}</a>
                        <a href="{{ url('dashboard/bookings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Bookings</a>
                    @elserole('employee')
                        <a href="{{ url('dashboard/my-own-bookings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Bookings</a>
                    @endrole

                    <div class="border-t border-gray-100"></div>

                    {{-- Logout --}}
                    <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'logout-modal' }))"
                        class="w-full text-center px-4 py-2 text-red-600 hover:bg-gray-50">
                        {{ __('messages.sidebar.logout') }}
                    </button>
                </div>
            </div>

        </div>
    </div>
</header>

<div class="h-16"></div>

{{-- cleanup listeners when Alpine component is destroyed --}}
<script>
    document.addEventListener('alpine:initialized', () => {
        // no-op: listeners managed inside component; this script kept for safety if needed
    });
</script>
