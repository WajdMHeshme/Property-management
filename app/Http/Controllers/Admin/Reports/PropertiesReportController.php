<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyReportRequest; // ✅ التعديل هنا
use App\Models\Property;
use Barryvdh\DomPDF\Facade\PDF;

class PropertiesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check.active', 'role:admin']);
    }

    public function index(PropertyReportRequest $request)
    {
        $report = $this->getReportData($request->validated());

        return view('dashboard.reports.properties', compact('report'));
    }

    public function export(PropertyReportRequest $request)
    {
        $report = $this->getReportData($request->validated());

        $pdf = PDF::loadView('dashboard.reports.properties-export', compact('report'));

        $fileName = 'properties_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($fileName);
    }

    private function getReportData(array $filters): array
    {
        $query = Property::query();

        $query->when($filters['status'] ?? null, fn ($q, $status) =>
            $q->where('status', $status)
        );

        $query->when($filters['city'] ?? null, fn ($q, $city) =>
            $q->where('city', $city)
        );

        $query->when(
            !empty($filters['from']) && !empty($filters['to']),
            fn ($q) =>
                $q->whereBetween('created_at', [
                    $filters['from'],
                    $filters['to'],
                ])
        );

        return [
            'total_properties' => $query->count(),

            'by_status' => [
                'available' => Property::where('status', 'available')->count(),
                'booked'    => Property::where('status', 'booked')->count(),
                'rented'    => Property::where('status', 'rented')->count(),
                'hidden'    => Property::where('status', 'hidden')->count(),
            ],

            'properties' => $query->latest()->get(),
        ];
    }
}
