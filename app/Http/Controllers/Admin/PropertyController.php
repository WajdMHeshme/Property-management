<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Services\AmenityService;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;
    protected AmenityService $amenityService;

    public function __construct(PropertyService $propertyService, AmenityService $amenityService)
    {
        $this->propertyService = $propertyService;
        $this->amenityService = $amenityService;
    }

    /**
     * Display a listing of properties with optional filters (paginated).
     *
     * @return View
     */
public function index(): View
{
    $filters = request()->only([
        'amenity_ids',
        'property_types',
        'type',           
        'city',
        'min_price',
        'max_price',
        'sort',
        'order',
        'limit'
    ]);

    // If legacy single `type` param exists but no `property_types`, map it to property_types[]
    if (!empty($filters['type']) && empty($filters['property_types'])) {
        // make sure it's an array (so service expects array)
        $filters['property_types'] = is_array($filters['type']) ? $filters['type'] : [$filters['type']];
    }

    // Ensure amenity_ids is array (when select multiple disabled or single selected)
    if (!empty($filters['amenity_ids']) && !is_array($filters['amenity_ids'])) {
        $filters['amenity_ids'] = [$filters['amenity_ids']];
    }

    $properties = $this->propertyService->getPaginated($filters);

    $amenities = $this->amenityService->getAll();
    $propertyTypes = \App\Models\PropertyType::all();

    return view('dashboard.properties.index', compact('properties', 'amenities', 'filters', 'propertyTypes'));
}



public function create(): View
{
    $amenities = $this->amenityService->getAll();
    $propertyTypes = \App\Models\PropertyType::all();

    return view('dashboard.properties.create', compact('amenities', 'propertyTypes'));
}

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $property = $this->propertyService->create($data);

        if ($request->hasFile('images')) {
            app(\App\Services\ImageService::class)->uploadPropertyImages(
                $property->id,
                $request->file('images'),
                null
            );
        }

        return redirect()->route('dashboard.properties.index')->with('success', 'Property created with images.');
    }

public function edit(Property $property): View
{
    $property->load('amenities');
    $amenities = $this->amenityService->getAll();
    $propertyTypes = \App\Models\PropertyType::all();

    return view('dashboard.properties.edit', compact('property', 'amenities', 'propertyTypes'));
}


    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $data = $request->validated();

        $this->propertyService->update($property, $data);

        return redirect()->route('dashboard.properties.index')->with('success', 'Property updated.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->propertyService->delete($property);

        return redirect()->route('dashboard.properties.index')->with('success', 'Property deleted.');
    }

    public function show(Property $property): View
    {
        $property->load(['images', 'amenities']);

        return view('dashboard.properties.show', compact('property'));
    }
}
