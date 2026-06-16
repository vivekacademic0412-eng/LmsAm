<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Models\CourseLevel;
use App\Models\CourseType;
use App\Models\DemoFeatureVideo;
use App\Models\DemoFeedback;
use App\Models\EducationLevel;
use App\Models\DemoUser;
use App\Models\SubmittedDemos;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;
use Exception;

class LmsController extends Controller
{
    //Landing Page 
    public function Landing(){
         $courses = Course::get();
           $categories = CourseCategory::get();
            $feedbacks = DemoFeedback::with([
                'user',
                'course'
            ])
                ->whereNotNull('message')
                ->latest()
                ->take(10)
                ->get();
            return view('demo.lms.landing', compact('categories', 'courses', 'feedbacks') + [
                    'currentStep' => 1
                ]);
    }



    // ──────────────────────────────────────────
    // STEP 1 — Welcome & Onboarding
    // ──────────────────────────────────────────


    public function step1()
    {
        try {
            Log::info(__METHOD__ . ' started');

            $categories = CourseCategory::get();
            $feedbacks = DemoFeedback::with([
                'user',
                'course'
            ])
                ->whereNotNull('message')
                ->latest()
                ->take(10)
                ->get();

            $educationLevels = EducationLevel::where('status', 1)
                ->orderBy('sort_order')
                ->get();

            Log::info(__METHOD__ . ' completed');

            return view(
                'demo.lms.step1',
                compact('categories', 'educationLevels', 'feedbacks') + [
                    'currentStep' => 2
                ]
            );
        } catch (Exception $e) {

            Log::error(__METHOD__, [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Something went wrong.');
        }
    }
     public function courseTypes(Request $request)
    {
        $types = CourseType::where('status', 1)
            ->select('id', 'name')
            ->orderBy('id')
            ->get();
 
        return response()->json($types);
    }
  public function courseLevels(Request $request)
    {
        $typeId = $request->query('type_id');
 
        // CourseType name lookup to decide available levels
        $type = CourseType::find($typeId);
 
        $query = CourseLevel::where('status', 1)->orderBy('id');
 
        // Fundamentals / Basic courses have no level — return a synthetic "All levels"
        if ($type && strtolower($type->name) === 'basic') {
            return response()->json([
                ['id' => 'all', 'name' => 'All levels']
            ]);
        }
 
        return response()->json($query->select('id', 'name')->get());
    }
     public function courses(Request $request)
    {
        $categoryId = $request->query('category_id');
        $typeId     = $request->query('type_id');
        $levelId    = $request->query('level_id'); // may be 'all'
 
        $query = Course::query()
            ->select('id', 'title', 'duration_hours', 'course_type_id', 'course_level_id', 'category_id')
            ->where('category_id', $categoryId)
            ->where('course_type_id', $typeId);
 
        // 'all' means Basic/Fundamentals — level_id is null in seeder
        if ($levelId && $levelId !== 'all') {
            $query->where('course_level_id', $levelId);
        } else {
            $query->whereNull('course_level_id');
        }
 
        $courses = $query->orderBy('title')->get();
 
        return response()->json($courses);
    }
    public function storeStep1(Request $request)
    {
        try {

            Log::info(__METHOD__ . ' started', [
                'request' => $request->all()
            ]);
            $data = $request->validate([

                'full_name' => [
                    'required',
                    'string',
                    'max:100'
                ],

                'email' => [
                    'required',
                    'email',
                    'max:100'
                ],

                'contact' => [
                    'required',
                    'string',
                    'regex:/^[0-9+\s-]{10,15}$/'
                ],

                'education_level' => [
                    'required',
                    'integer',
                    'exists:education_levels,id'
                ],

                'interest_area' => [
                    'required',
                    'integer',
                    'exists:course_categories,id'
                ],

                'preferred_course' => [
                    'required',
                    'integer',
                    'exists:courses,id'
                ]

            ]);

            $demoUser = DemoUser::create([
                'user_id'             => auth()->id(),
                'full_name'           => $data['full_name'],
                'email'         => $data['email'],
                'phone'         => $data['contact'],
                'education_level_id'  => $data['education_level'],
                'interest_area_id'    => $data['interest_area'],
                'preferred_course_id' => $data['preferred_course'],
                'ip_address'          => $request->ip(),
            ]);

            Log::info('Demo user created', [
                'demo_user_id' => $demoUser->id
            ]);
            $course = CourseCategory::find($data['interest_area']);

            $video = DemoFeatureVideo::where('course_id', $data['preferred_course'])
                ->where('status', 1)
                ->first();

            session([
                'lms_full_name'        => $data['full_name'],
                'lms_email_phone'      => $data['email'],
                'lms_contact'      => $data['contact'],
                'lms_education'        => $data['education_level'],
                'lms_interest'         => $data['interest_area'],
                'lms_preferred_course' => $data['preferred_course'],
                'demo_user_id'        => $demoUser->id,
                'demo_video_id'       => $video?->id,
                'lms_course_id' => $course->id,
                'lms_course_label' => $course->name,
                'lms_course_slug' => $course->slug,
            ]);
            return redirect()->route('lms.step2');
        } catch (Exception $e) {

            Log::error(__METHOD__, [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
            ]);

            return back()->with('error', 'Something went wrong.');
        }
    }


    // ──────────────────────────────────────────
    // STEP 2 — Demo Video Session
    // ──────────────────────────────────────────
    public function step2()
    {
        if (!session('lms_full_name')) {
            return redirect()->route('lms.step1')
                ->with('info', 'Please complete step 1 first.');
        }

        $video = DemoFeatureVideo::find(session('demo_video_id'));

        $demoUser = DemoUser::find(session('demo_user_id'));
        return view('demo.lms.step2', [
            'currentStep' => 3,
            'video' => $video,
            'video_details' => $demoUser
        ]);
    }
    public function storeStep2(Request $request)
    {
        try {

            Log::info(__METHOD__ . ' started', [
                'request' => $request->all()
            ]);
            $request->validate([
                'video_watched' => 'required|numeric|min:70',
            ], [
                'video_watched.min' => 'Please watch at least 70% of the demo video before continuing.',
            ]);

            $demoUser = DemoUser::find(session('demo_user_id'));

            if ($demoUser) {

                $demoUser->update([
                    // 'demo_feature_video_id' => session('demo_video_id'),
                    'progress_demo'         => (int) $request->video_watched,
                ]);
            }

            session([
                'lms_video_watched' => $request->video_watched
            ]);



            return redirect()->route('lms.step3');
        } catch (Exception $e) {

            Log::error(__METHOD__, [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
            ]);

            return back()->with('error', 'Something went wrong.');
        }
    }

    // ──────────────────────────────────────────
    // STEP 3 — Create Your Demo
    // ──────────────────────────────────────────

    // public function step3()
    // {
    //     try {

    //         if (!session('lms_video_watched')) {
    //             return redirect()->route('lms.step2');
    //         }
    //         $course = CourseCategory::find(session('lms_course_id'));

    //         return view('demo.lms.step3', [
    //             'currentStep' => 3,
    //             'course' => $course
    //         ]);
    //     } catch (Exception $e) {

    //         Log::error(__METHOD__, [
    //             'message' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'request' => $request->all(),
    //         ]);

    //         return back()->with('error', $e->getMessage());
    //     }
    // }

    public function step3()
    {
        try {
            if (!session('lms_video_watched')) {
                return redirect()->route('lms.step2');
            }

            $course = CourseCategory::find(session('lms_course_id'));

            return view('demo.lms.step3', [
                'currentStep' => 4,
                'course'      => $course,
            ]);
        } catch (Exception $e) {
            Log::error(__METHOD__, [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                // ✅ REMOVED: 'request' => $request->all()  ← was causing fatal error
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    // public function storeStep3(Request $request)
    // {
    //     try {

    //         Log::info('Step3 Request Started', [
    //             'user_id' => auth()->id(),
    //             'request_data' => $request->except('demo_video'),
    //         ]);

    //         $request->validate([
    //             'demo_topic'       => 'required|string|max:200',
    //             'demo_description' => 'required|string|min:30|max:600',
    //             'demo_video'       => 'required|file|mimetypes:video/*|max:512000',
    //         ]);

    //         Log::info('Validation Passed');

    //         $userId   = auth()->id();
    //         $courseId = session('lms_course_id');

    //         Log::info('Session Data', [
    //             'user_id'   => $userId,
    //             'course_id' => $courseId,
    //         ]);

    //         // Check existing record
    //         $existing = SubmittedDemos::where('demo_user_id', $userId)
    //             ->where('course_id', $courseId)
    //             ->first();

    //         Log::info('Existing Demo Record', [
    //             'exists' => !empty($existing),
    //             'record' => $existing,
    //         ]);

    //         // Delete old video
    //         if ($existing && $existing->demo_video) {

    //             Log::info('Deleting Old Video', [
    //                 'path' => $existing->demo_video,
    //             ]);

    //             $deleted = Storage::disk('public')->delete($existing->demo_video);

    //             Log::info('Old Video Delete Result', [
    //                 'deleted' => $deleted,
    //             ]);
    //         }

    //         // Upload new video
    //         Log::info('Uploading New Video', [
    //             'original_name' => $request->file('demo_video')->getClientOriginalName(),
    //             'size' => $request->file('demo_video')->getSize(),
    //             'mime' => $request->file('demo_video')->getMimeType(),
    //         ]);

    //         $videoPath = $request->file('demo_video')->store('lms-demos', 'public');

    //         Log::info('Video Uploaded Successfully', [
    //             'video_path' => $videoPath,
    //         ]);

    //         // Store session
    //         session([
    //             'lms_demo_topic' => $videoPath
    //         ]);

    //         Log::info('Session Updated', [
    //             'lms_demo_topic' => session('lms_demo_topic'),
    //         ]);

    //         // Create/Update record
    //         $demo = SubmittedDemos::updateOrCreate(
    //             [
    //                 'demo_user_id' => $userId,
    //                 'course_id'    => $courseId,
    //             ],
    //             [
    //                 'user_id'           => $userId,
    //                 'demo_topic'        => $request->demo_topic,
    //                 'demo_description'  => $request->demo_description,
    //                 'demo_video'        => $videoPath,
    //                 'status'            => 'pending',
    //                 'completion_score'  => 0,
    //             ]
    //         );

    //         Log::info('Demo Record Saved Successfully', [
    //             'demo_id' => $demo->id,
    //             'record'  => $demo->toArray(),
    //         ]);

    //         Log::info('Step3 Completed Successfully');

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Demo submitted successfully!',
    //             'redirect_url' => route('lms.step4')
    //         ]);
    //     } catch (Exception $e) {

    //         Log::error('Step3 Failed', [
    //             'message' => $e->getMessage(),
    //             'file'    => $e->getFile(),
    //             'line'    => $e->getLine(),
    //             'trace'   => $e->getTraceAsString(),
    //             'request' => $request->except('demo_video'),
    //             'user_id' => auth()->id(),
    //         ]);

    //         return back()->with('error', 'Something went wrong.' . $e->getMessage());
    //     }
    // }
    public function storeStep3(Request $request)
    {
        try {
            // Make sure Laravel treats this as an AJAX/JSON request
            // even if Accept header gets stripped by a redirect
            $request->headers->set('Accept', 'application/json');

            Log::info('Step3 Request Started', [
                'user_id' => auth()->id(),
                'request_data' => $request->except('demo_video'),
            ]);

            $request->validate([
                'demo_topic'       => 'required|string|max:200',
                'demo_description' => 'required|string|min:30|max:600',
                'demo_video'       => 'required|file|mimetypes:video/*|max:512000',
            ]);

            $userId   = session('demo_user_id') ?? auth()->id();
            $courseId = session('lms_course_id');

            $existing = SubmittedDemos::where('demo_user_id', $userId)
                ->where('course_id', $courseId)
                ->first();

            if ($existing && $existing->demo_video) {
                Storage::disk('public')->delete($existing->demo_video);
            }

            $videoPath = $request->file('demo_video')->store('lms-demos', 'public');

            session(['lms_demo_topic' => $videoPath]);

            $demo = SubmittedDemos::updateOrCreate(
                [
                    'demo_user_id' => $userId,
                    'course_id'    => $courseId,
                ],
                [
                    'user_id'          => $userId,
                    'demo_topic'       => $request->demo_topic,
                    'demo_description' => $request->demo_description,
                    'demo_video'       => $videoPath,
                    'status'           => 'pending',
                    'completion_score' => 0,
                ]
            );

            Log::info('Step3 Completed', ['demo_id' => $demo->id]);

            // Always return JSON — never redirect from this method
            return response()->json([
                'status'       => true,
                'message'      => 'Demo submitted successfully!',
                'redirect_url' => route('lms.step4'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON explicitly
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Step3 Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
    // ──────────────────────────────────────────
    // STEP 4 — Submission Confirmation
    // ──────────────────────────────────────────



    public function step4()
    {
        try {

            Log::info('Step4 Request Started', [
                'auth_user_id' => auth()->id(),
                'session_data' => session()->all(),
            ]);

            $userId = session('demo_user_id');
            $courseId = session('lms_course_id');

            Log::info('Retrieved Session Values', [
                'demo_user_id' => $userId,
                'course_id' => $courseId,
            ]);



            // Check feedback
            $feedback = DemoFeedback::where('demo_user_id', $userId)
                ->where('course_id', $courseId)
                ->first();

            Log::info('Feedback Query Result', [
                'found' => !is_null($feedback),
                'feedback' => $feedback ? $feedback->toArray() : null,
            ]);

            Log::info('Loading Step4 View', [

                'feedback_exists' => !is_null($feedback),
            ]);

            return view('demo.lms.step4', [
                'currentStep' => 5,

                'feedback' => $feedback,
            ]);
        } catch (Exception $e) {

            Log::error('Step4 Failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'auth_user_id' => auth()->id(),
                'session_data' => session()->all(),
            ]);

            return back()->with('error', 'Something went wrong.');
        }
    }
    // ──────────────────────────────────────────
    // STEP 5 — Recommendations & Social Proof
    // ──────────────────────────────────────────
    public function step5()
    {
        // Look up the category id from the slug stored in session
        $category = CourseCategory::where('slug', session('lms_interest'))->first();

        $courses = Course::with('category')
            ->when($category, fn($q) => $q->where('category_id', $category->id))
            ->latest()
            ->take(4)
            ->get();

        $reviews = DemoFeedback::with('user', 'course')
            ->whereNotNull('message')
            ->latest()
            ->take(10)
            ->get();

        return view('demo.lms.step5', [
            'currentStep' => 6,
            'courses'     => $courses,   // comma, not semicolon
            'reviews'     => $reviews,
        ]);
    }

    // ──────────────────────────────────────────
    // DASHBOARD
    // ──────────────────────────────────────────

    public function dashboard()
    {
        return redirect()->route('lms.step5'); // or load a real dashboard view
    }
}
