@props([
    'id',
    'title' => 'Confirm',
    'message' => '',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
])

<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    @open-modal.window="if($event.detail === '{{ $id }}') open = true"
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center"
>
    {{-- Overlay --}}
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-black/50"
        @click="open = false"
    ></div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-transition.scale.opacity
        @click.outside="open = false"
        class="relative bg-white w-full max-w-sm mx-4 rounded-2xl shadow-2xl p-6"
        role="dialog"
        aria-modal="true"
    >
        {{-- Icon --}}
        <div class="flex justify-center mb-4">
            <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" class="h-7 w-7 text-indigo-600" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
</svg>

            </div>
        </div>

        {{-- Content --}}
        <h2 class="text-center text-lg font-semibold text-indigo-600 mb-2">
            {{ $title }}
        </h2>

        <p class="text-center text-sm text-gray-600 mb-6">
            {{ $message }}
        </p>

        {{-- Actions --}}
        <div class="flex justify-center gap-3">
            <button
                type="button"
                @click="open = false"
                class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
            >
                {{ $cancelText }}
            </button>

            {{-- Slot for confirm button --}}
            <div class="inline-flex">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
