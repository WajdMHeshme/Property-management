@extends('dashboard.layout')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold text-indigo-600">Properties List</h1>
        <a href="{{ route('dashboard.properties.create') }}"
           class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-full font-semibold shadow-lg hover:scale-[1.03] transform transition">
            Add New Property
        </a>
    </div>

    {{-- flash --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="mb-8 bg-white p-5 rounded-2xl shadow grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- City --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">City</label>
            <input type="text"
                   name="city"
                   value="{{ $filters['city'] ?? '' }}"
                   placeholder="e.g. Damascus"
                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Min Price --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Min Price</label>
            <input type="number"
                   name="min_price"
                   value="{{ $filters['min_price'] ?? '' }}"
                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Max Price --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Max Price</label>
            <input type="number"
                   name="max_price"
                   value="{{ $filters['max_price'] ?? '' }}"
                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        {{-- Actions --}}
        <div class="flex items-end gap-2">
            <button type="submit"
                    class="w-full px-4 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-500 transition">
                Filter
            </button>
            <a href="{{ route('dashboard.properties.index') }}"
               class="px-4 py-2 bg-gray-200 rounded-full hover:bg-gray-300 transition">
                Reset
            </a>
        </div>

        {{-- Amenities --}}
        <div class="md:col-span-4">
            <label class="block mb-2 text-sm font-medium text-gray-700">Amenities</label>
            <div class="flex flex-wrap gap-2">
                @foreach($amenities as $amenity)
                    <label class="inline-flex items-center bg-gray-100 rounded-full px-3 py-1 cursor-pointer hover:bg-indigo-50 transition">
                        <input type="checkbox"
                               name="amenity_ids[]"
                               value="{{ $amenity->id }}"
                               {{ in_array($amenity->id, (array)($filters['amenity_ids'] ?? [])) ? 'checked' : '' }}
                               class="form-checkbox h-4 w-4 text-indigo-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">{{ $amenity->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </form>

    {{-- Properties Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($properties as $property)
            <div class="border rounded-2xl shadow-lg overflow-hidden bg-white hover:shadow-2xl transition">
                <div class="h-52 bg-gray-100">
                    @if($property->images->first())
                        <img src="{{ asset('storage/'.$property->images->first()->path) }}"
                             class="w-full h-52 object-cover">
                    @else
                        <div class="flex items-center justify-center h-52 text-gray-400">
                            No Image
                        </div>
                    @endif
                </div>

                <div class="p-4">
                    <h2 class="text-lg font-semibold">{{ $property->title }}</h2>
                    <p class="text-sm text-gray-500">{{ $property->city }}</p>
                    <p class="text-green-700 font-medium my-2">
                        ${{ number_format($property->price, 2) }}
                    </p>

                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($property->amenities as $a)
                            <span class="text-xs px-2 py-1 bg-gray-100 rounded-full">
                                {{ $a->name }}
                            </span>
                        @endforeach
                    </div>

                    <div class="flex gap-2 flex-wrap">
                        <a href="{{ route('dashboard.properties.show', $property->id) }}"
                           class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm">View</a>

                        <a href="{{ route('dashboard.properties.edit', $property->id) }}"
                           class="px-3 py-1 bg-yellow-50 text-yellow-700 rounded-full text-sm">Edit</a>

                        {{-- Delete (uses modal) --}}
                        <form action="{{ route('dashboard.properties.destroy', $property->id) }}"
                              method="POST"
                              onsubmit="event.preventDefault();
                                        window.currentDeleteForm = this;
                                        window.dispatchEvent(
                                            new CustomEvent('open-modal', { detail: 'delete-property' })
                                        );">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 p-6">
                No properties found.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $properties->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>

{{-- =====================
     Confirm Delete Modal
   ===================== --}}
<x-confirm-modal
    id="delete-property"
    title="Delete Property"
    message="Are you sure you want to delete this property? This action cannot be undone."
    confirmText="Delete"
    cancelText="Cancel"
>
    <button
        type="button"
        @click="if (window.currentDeleteForm) window.currentDeleteForm.submit(); open = false"
        class="px-5 py-2 rounded-full bg-indigo-600 text-white hover:bg-indigo-700 transition"
    >
        Delete
    </button>
</x-confirm-modal>


@endsection
