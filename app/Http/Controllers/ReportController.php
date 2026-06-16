<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reports = $request->user()
            ->reports()
            ->with('testResults')
            ->latest()
            ->get();

        return response()->json($reports);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'report_ref'            => 'nullable|string',
            'client_request_ref'    => 'nullable|string',
            'date_reported'         => 'nullable|string',
            'date_received'         => 'nullable|string',
            'client_name'           => 'nullable|string',
            'project'               => 'nullable|string',
            'project_client'        => 'nullable|string',
            'contractor'            => 'nullable|string',
            'consultant'            => 'nullable|string',
            'concrete_supplier'     => 'nullable|string',
            'mix_grade'             => 'nullable|string',
            'slump_mm'              => 'nullable|string',
            'air_content'           => 'nullable|string',
            'sampling_method'       => 'nullable|string',
            'other_information'     => 'nullable|string',
            'pour_location'         => 'nullable|string',
            'total_cubes'           => 'nullable|integer',
            'date_of_casting'       => 'nullable|string',
            'sampled_by'            => 'nullable|string',
            'sampling_location'     => 'nullable|string',
            'sampling_cert_ref'     => 'nullable|string',
            'compaction_method'     => 'nullable|string',
            'lab_curing_method'     => 'nullable|string',
            'test_method'           => 'nullable|string',
            'density_method'        => 'nullable|string',
            'volume_determination'  => 'nullable|string',
            'test_location'         => 'nullable|string',
            'method_variation'      => 'nullable|string',
            'cubes_delivered_by'    => 'nullable|string',
            'dimensions'            => 'nullable|string',
            'nominal_size'          => 'nullable|string',
            'removal_of_fins'       => 'nullable|string',
            'curing_at_lab'         => 'nullable|string',
            'tested_by'             => 'nullable|string',
            'report_type'           => 'nullable|in:final,interim',
            'structural_reference'  => 'nullable|string',
            'test_results'          => 'nullable|array',
            'test_results.*.client_ref'            => 'nullable|string',
            'test_results.*.lab_sample_ref'        => 'nullable|string',
            'test_results.*.req_test_age'          => 'nullable|numeric',
            'test_results.*.act_test_age'          => 'nullable|numeric',
            'test_results.*.date_of_test'          => 'nullable|string',
            'test_results.*.condition_upon_receipt'=> 'nullable|string',
            'test_results.*.condition_at_test'     => 'nullable|string',
            'test_results.*.dim_l'                 => 'nullable|numeric',
            'test_results.*.dim_w'                 => 'nullable|numeric',
            'test_results.*.dim_h'                 => 'nullable|numeric',
            'test_results.*.mass_kg'               => 'nullable|numeric',
            'test_results.*.density'               => 'nullable|numeric',
            'test_results.*.max_load_kn'           => 'nullable|numeric',
            'test_results.*.comp_strength'         => 'nullable|numeric',
            'test_results.*.type_of_fracture'      => 'nullable|string',
        ]);

        $report = $request->user()->reports()->create(
            collect($data)->except('test_results')->toArray()
        );

        foreach ($data['test_results'] ?? [] as $i => $row) {
            $report->testResults()->create(array_merge($row, ['sort_order' => $i]));
        }

        return response()->json($report->load('testResults'), 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $report = $request->user()->reports()->with('testResults')->findOrFail($id);
        return response()->json($report);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $report = $request->user()->reports()->findOrFail($id);

        $data = $request->validate([
            'report_ref'            => 'nullable|string',
            'client_request_ref'    => 'nullable|string',
            'date_reported'         => 'nullable|string',
            'date_received'         => 'nullable|string',
            'client_name'           => 'nullable|string',
            'project'               => 'nullable|string',
            'project_client'        => 'nullable|string',
            'contractor'            => 'nullable|string',
            'consultant'            => 'nullable|string',
            'concrete_supplier'     => 'nullable|string',
            'mix_grade'             => 'nullable|string',
            'slump_mm'              => 'nullable|string',
            'air_content'           => 'nullable|string',
            'sampling_method'       => 'nullable|string',
            'other_information'     => 'nullable|string',
            'pour_location'         => 'nullable|string',
            'total_cubes'           => 'nullable|integer',
            'date_of_casting'       => 'nullable|string',
            'sampled_by'            => 'nullable|string',
            'sampling_location'     => 'nullable|string',
            'sampling_cert_ref'     => 'nullable|string',
            'compaction_method'     => 'nullable|string',
            'lab_curing_method'     => 'nullable|string',
            'test_method'           => 'nullable|string',
            'density_method'        => 'nullable|string',
            'volume_determination'  => 'nullable|string',
            'test_location'         => 'nullable|string',
            'method_variation'      => 'nullable|string',
            'cubes_delivered_by'    => 'nullable|string',
            'dimensions'            => 'nullable|string',
            'nominal_size'          => 'nullable|string',
            'removal_of_fins'       => 'nullable|string',
            'curing_at_lab'         => 'nullable|string',
            'tested_by'             => 'nullable|string',
            'report_type'           => 'nullable|in:final,interim',
            'structural_reference'  => 'nullable|string',
            'test_results'          => 'nullable|array',
            'test_results.*.client_ref'            => 'nullable|string',
            'test_results.*.lab_sample_ref'        => 'nullable|string',
            'test_results.*.req_test_age'          => 'nullable|numeric',
            'test_results.*.act_test_age'          => 'nullable|numeric',
            'test_results.*.date_of_test'          => 'nullable|string',
            'test_results.*.condition_upon_receipt'=> 'nullable|string',
            'test_results.*.condition_at_test'     => 'nullable|string',
            'test_results.*.dim_l'                 => 'nullable|numeric',
            'test_results.*.dim_w'                 => 'nullable|numeric',
            'test_results.*.dim_h'                 => 'nullable|numeric',
            'test_results.*.mass_kg'               => 'nullable|numeric',
            'test_results.*.density'               => 'nullable|numeric',
            'test_results.*.max_load_kn'           => 'nullable|numeric',
            'test_results.*.comp_strength'         => 'nullable|numeric',
            'test_results.*.type_of_fracture'      => 'nullable|string',
        ]);

        $report->update(collect($data)->except('test_results')->toArray());

        $report->testResults()->delete();
        foreach ($data['test_results'] ?? [] as $i => $row) {
            $report->testResults()->create(array_merge($row, ['sort_order' => $i]));
        }

        return response()->json($report->load('testResults'));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $report = $request->user()->reports()->findOrFail($id);
        $report->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
