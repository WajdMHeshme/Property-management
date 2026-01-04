@extends('dashboard.layout')

@section('content')

<div class="lg:ml-64 px-6 py-8">

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">
            Booking Details
        </h2>

        <p class="text-sm text-gray-500 mt-1">
            Full booking information & management actions
        </p>
    </div>


    {{-- Card --}}
    <div class="bg-white border rounded-xl shadow-sm p-6">

        {{-- Booking Status --}}
        <div class="flex justify-between items-center mb-4">

            <h3 class="text-lg font-semibold">
                Booking #{{ $booking->id }}
            </h3>

            <span class="px-3 py-1 rounded-lg text-xs border
                @if($booking->status == 'pending')
                    bg-yellow-50 text-yellow-700 border-yellow-300
                @elseif($booking->status == 'approved')
                    bg-green-50 text-green-700 border-green-300
                @elseif($booking->status == 'rejected')
                    bg-red-50 text-red-700 border-red-300
                @elseif($booking->status == 'cancelled')
                    bg-gray-100 text-gray-700 border-gray-300
                @elseif($booking->status == 'completed')
                    bg-blue-50 text-blue-700 border-blue-300
                @endif">
                {{ ucfirst($booking->status) }}
            </span>

        </div>


        {{-- Customer --}}
        <div class="mb-4">
            <h4 class="font-medium text-gray-800 mb-1">Customer</h4>

            <div class="flex items-start gap-2">

                {{-- Filled User Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6 text-black"
                     viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M12 2.25a4.5 4.5 0 014.5 4.5v.75a4.5 4.5 0 11-9 0V6.75a4.5 4.5 0 014.5-4.5zm-7.5 17.1a7.5 7.5 0 0115 0v.15A2.25 2.25 0 0117.25 21h-10.5A2.25 2.25 0 014.5 19.5v-.15z"
                          clip-rule="evenodd" />
                </svg>

                <div>
                    <p class="text-sm text-gray-700 font-medium">
                        {{ $booking->customer->name ?? 'N/A' }}
                    </p>

                    <p class="text-xs text-gray-500">
                        {{ $booking->customer->email ?? '' }}
                    </p>
                </div>

            </div>
        </div>


        {{-- Property --}}
        <div class="mb-4">
            <h4 class="font-medium text-gray-800 mb-1">Property</h4>

            <p class="text-sm font-semibold">
                {{ $booking->property->title ?? 'Unknown Property' }}
            </p>

            <p class="text-xs text-gray-500">
                {{ $booking->property->city ?? '' }}
                — {{ $booking->property->address ?? '' }}
            </p>
        </div>


        {{-- Schedule --}}
        <div class="mb-4">
            <h4 class="font-medium text-gray-800 mb-1">Scheduled Visit</h4>

            <div class="flex items-center gap-2">

                {{-- Filled Calendar Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6 text-black"
                     viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M6.75 2.25a.75.75 0 01.75.75V4.5h9V3a.75.75 0 011.5 0v1.5h.75A2.25 2.25 0 0121 6.75v12A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75v-12A2.25 2.25 0 015.25 4.5H6V3a.75.75 0 01.75-.75zM3.75 9h16.5v9.75a.75.75 0 01-.75.75H4.5a.75.75 0 01-.75-.75V9z"
                          clip-rule="evenodd" />
                </svg>

                <p class="text-sm text-gray-700">
                    {{ $booking->scheduled_at }}
                </p>

            </div>
        </div>


        {{-- Notes --}}
        @if($booking->notes)
        <div class="mb-4">
            <h4 class="font-medium text-gray-800 mb-1">Notes</h4>

            <p class="text-sm text-gray-600">
                {{ $booking->notes }}
            </p>
        </div>
        @endif


        {{-- Employee --}}
        @if($booking->employee)
        <div class="mb-4">
            <h4 class="font-medium text-gray-800 mb-1">Assigned Employee</h4>

            <div class="flex items-center gap-2">

                {{-- Filled Briefcase / Worker Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6 text-black"
                     viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 2.25h6A2.25 2.25 0 0117.25 4.5V6H6.75V4.5A2.25 2.25 0 019 2.25z" />
                    <path fill-rule="evenodd"
                          d="M3.75 7.5A2.25 2.25 0 016 5.25h12A2.25 2.25 0 0120.25 7.5v9A2.25 2.25 0 0118 18.75H6A2.25 2.25 0 013.75 16.5v-9z"
                          clip-rule="evenodd" />
                </svg>

                <p class="text-sm text-gray-700">
                    {{ $booking->employee->name }}
                </p>

            </div>
        </div>
        @endif


        {{-- Action Buttons --}}
        <div class="mt-4 flex gap-2">

            @if($booking->status == 'pending')

                <form method="POST" action="{{ route('employee.bookings.approve', $booking->id) }}">
                    @csrf @method('PATCH')
                    <button class="px-3 py-1.5 text-xs rounded-lg bg-green-600 text-white">
                        Approve
                    </button>
                </form>

                <form method="POST" action="{{ route('employee.bookings.reject', $booking->id) }}">
                    @csrf @method('PATCH')
                    <button class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white">
                        Reject
                    </button>
                </form>

            @endif


            @if($booking->status == 'approved')

                <form method="POST" action="{{ route('employee.bookings.complete', $booking->id) }}">
                    @csrf @method('PATCH')
                    <button class="px-3 py-1.5 text-xs rounded-lg bg-blue-600 text-white">
                        Mark as Completed
                    </button>
                </form>

                <form method="POST" action="{{ route('employee.bookings.cancel', $booking->id) }}">
                    @csrf @method('PATCH')
                    <button class="px-3 py-1.5 text-xs rounded-lg bg-gray-700 text-white">
                        Cancel
                    </button>
                </form>

            @endif


            <a href="{{ route('employee.bookings.index') }}"
               class="px-3 py-1.5 text-xs rounded-lg border hover:bg-gray-50">
                Back to list
            </a>

        </div>

    </div>

</div>

@endsection
