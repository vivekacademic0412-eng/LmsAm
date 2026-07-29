<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabSetupForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LabSetupFormController extends Controller
{
    /**
     * POST /api/lab-setup-forms
     * Stores a submitted Discovery Form into the LMS database.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'school_name'            => 'nullable|string|max:255',
            'board_affiliation'      => 'nullable|string|max:255',
            'address'                => 'nullable|string|max:500',
            'grades_offered'         => 'nullable|string|max:255',
            'student_strength'       => 'nullable|string|max:50',

            'existing_lab'           => 'nullable|in:dedicated_room,shared_partial,new_room',
            'room_size'              => 'nullable|string|max:100',
            'seating_capacity'       => 'nullable|string|max:100',
            'furniture'              => 'nullable|array',
            'furniture.*'            => 'string|in:work_tables,lockable_storage,display_board',

            'power_points'           => 'nullable|string|max:100',
            'backup_power'           => 'nullable|string|max:100',
            'internet_availability'  => 'nullable|in:wifi,lan,not_yet',
            'internet_speed'         => 'nullable|string|max:100',
            'school_devices'         => 'nullable|string|max:255',

            'enroll_grades'          => 'nullable|string|max:255',
            'expected_students'      => 'nullable|string|max:50',
            'session_frequency'      => 'nullable|in:weekly,twice_weekly,flexible',

            'start_date'             => 'nullable|string|max:100',
            'annual_budget'          => 'nullable|string|max:100',
            'procurement_process'    => 'nullable|in:principal,trust,tender',
            'lab_goals'              => 'nullable|string',

            'contact_name'           => 'required|string|max:255',
            'designation'            => 'nullable|string|max:255',
             'phone' => [
                'required',
                'regex:/^(?:\+91|91)?[6-9]\d{9}$/',
            ],
            'email'                  => 'required|email|max:255',
            'signature'              => 'nullable|string|max:255',
            'sig_date'               => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $form = LabSetupForm::create(array_merge(
            $validator->validated(),
            [
                'synced_to_lms'    => true,
                'lms_reference_id' => 'LMS-' . strtoupper(uniqid()),
            ]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Form submitted and stored in LMS successfully.',
            'data'    => $form,
        ], 201);
    }

    /**
     * GET /api/lab-setup-forms
     * Lists stored submissions (for LMS admin views / dashboards).
     */
    public function index(Request $request): JsonResponse
    {
        $forms = LabSetupForm::query()
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $forms,
        ]);
    }

    /**
     * GET /api/lab-setup-forms/{labSetupForm}
     */
    public function show(LabSetupForm $labSetupForm): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $labSetupForm,
        ]);
    }
}
