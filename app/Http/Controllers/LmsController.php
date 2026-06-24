<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Models\CourseLevel;
use App\Models\CourseType;
use App\Models\DemoFeatureVideo;
use App\Models\DemoFeedback;
use App\Models\EducationLevel;
use App\Models\DemoUser;
use App\Models\SubmittedDemos;
use App\Models\Course;
use App\Models\DemoAccessToken;
use App\Models\HeroSection;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Exception;

class LmsController extends Controller
{
    //Landing Page 
    public function Landing()
    {
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
            $hero = HeroSection::with(['stats', 'ratings'])
    ->where('is_active', 1)
    ->first();


        return view('demo.lms.landing', compact('categories', 'courses', 'feedbacks','hero') + [
            'currentStep' => 1
        ]);
    }



    // ──────────────────────────────────────────
    // STEP 1 — Welcome & Onboarding
    // ──────────────────────────────────────────



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


    public function step1()
    {
        try {
            Log::info(__METHOD__ . ' started');

            $categories      = CourseCategory::get();
            $feedbacks       = DemoFeedback::with(['user', 'course'])
                ->whereNotNull('message')
                ->latest()
                ->take(10)
                ->get();
            $educationLevels = EducationLevel::where('status', 1)
                ->orderBy('sort_order')
                ->get();

            // Load previously saved record so the form pre-fills on Back navigation.
            // Priority: session demo_user_id → auth user's latest record → null.
            $existingDemoUser = null;
            if (auth()->user()) {
                $existingDemoUser = User::find(auth()->user()->id);
            }
            if (!$existingDemoUser) {
                $existingDemoUser = DemoUser::where('user_id', session('demo_user_id'))
                    ->latest()
                    ->first();
            }

            Log::info(__METHOD__ . ' completed');

            return view(
                'demo.lms.step1',
                compact('categories', 'educationLevels', 'feedbacks', 'existingDemoUser') + [
                    'currentStep' => 1,
                ]
            );
        } catch (Exception $e) {
            Log::error(__METHOD__, [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Something went wrong.');
        }
    }

    public function storeStep1(Request $request)
    {
        try {
            Log::info(__METHOD__ . ' started', ['request' => $request->all()]);

            $data = $request->validate([
                'full_name'        => ['required', 'string', 'max:100'],
                'email'            => ['required', 'email', 'max:100'],
                'contact'          => ['required', 'string', 'regex:/^[0-9+\s-]{10,15}$/'],
                'education_level'  => ['required', 'integer', 'exists:education_levels,id'],
                'interest_area'    => ['required', 'integer', 'exists:course_categories,id'],
                'preferred_course' => ['required', 'integer', 'exists:courses,id'],
            ]);

            $payload = [
                'user_id'             => auth()->user()->id,
                'full_name'           => $data['full_name'],
                'email'               => $data['email'],
                'phone'               => $data['contact'],
                'education_level_id'  => $data['education_level'],
                'interest_area_id'    => $data['interest_area'],
                'preferred_course_id' => $data['preferred_course'],
                'ip_address'          => $request->ip(),
            ];

            // Update the existing record if the user is revising step 1;
            // otherwise create a fresh one. This prevents duplicate rows.
            $existingId  = session('demo_user_id');
            $demoUser    = $existingId
                ? tap(DemoUser::findOrFail($existingId))->update($payload)
                : DemoUser::create($payload);

            Log::info('Demo user saved', ['demo_user_id' => $demoUser->id, 'action' => $existingId ? 'updated' : 'created']);

            $course = CourseCategory::find($data['interest_area']);
            $video  = DemoFeatureVideo::where('course_id', $data['preferred_course'])
                ->where('status', 1)
                ->first();

            session([
                'lms_full_name'        => $data['full_name'],
                'lms_email_phone'      => $data['email'],
                'lms_contact'          => $data['contact'],
                'lms_education'        => $data['education_level'],
                'lms_interest'         => $data['interest_area'],
                'lms_preferred_course' => $data['preferred_course'],
                'demo_user_id'         => $demoUser->id,
                'demo_video_id'        => $video?->id,
                'lms_course_id'        => $course->id,
                'lms_course_label'     => $course->name,
                'lms_course_slug'      => $course->slug,
            ]);

            return redirect()->route('lms.step2');
        } catch (Exception $e) {
            Log::error(__METHOD__, [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'request' => $request->all(),
            ]);

            return back()->with('error', 'Something went wrong.');
        }
    }



    // ──────────────────────────────────────────
    // STEP 2 — Demo Video Session  (FIXED)
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
            // ✅ FIXED: was hardcoded to 3 — this is step 2, not step 3.
            // That bug made your stepper highlight the wrong circle
            // every time someone (re)loaded this page.
            'currentStep'   => 2,
            'video'         => $video,
            'video_details' => $demoUser,
        ]);
    }

    public function storeStep2(Request $request)
    {
        try {
            Log::info(__METHOD__ . ' started', [
                'request' => $request->all()
            ]);

            $request->validate([
                'video_watched' => 'required|numeric|min:0|max:100',
            ]);

            $demoUser = DemoUser::find(session('demo_user_id'));

            if ($demoUser) {

                // ✅ FIXED: previously this blindly overwrote progress_demo
                // with whatever the form sent. If a learner re-opened the
                // page, watched 10 seconds, then navigated away, that LOW
                // number would overwrite a previously-saved HIGH number
                // (e.g. they already hit 85% last session).
                //
                // Progress should only ever move forward — never backward —
                // so we take the max of what's already stored vs incoming.
                $incoming = (int) $request->video_watched;
                $existing = (int) ($demoUser->progress_demo ?? 0);
                $highest  = max($incoming, $existing);

                $demoUser->update([
                    'progress_demo' => $highest,
                ]);

                Log::info('Progress updated', [
                    'demo_user_id' => $demoUser->id,
                    'incoming'     => $incoming,
                    'existing'     => $existing,
                    'saved'        => $highest,
                ]);
            }

            // ✅ Block continuing unless the HIGHEST recorded progress is >= 70,
            // not just whatever was sent on this particular request.
            $finalProgress = $demoUser->progress_demo ?? (int) $request->video_watched;

            if ($finalProgress < 70) {
                return back()
                    ->withErrors(['video_watched' => 'Please watch at least 70% of the demo video before continuing.'])
                    ->withInput();
            }

            session([
                'lms_video_watched' => $finalProgress,
            ]);

            return redirect()->route('lms.step3');
        } catch (Exception $e) {

            Log::error(__METHOD__, [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'request' => $request->all(),
            ]);

            return back()->with('error', 'Something went wrong.');
        }
    }

    // ──────────────────────────────────────────
    // STEP 3 — also needs a small fix: when the learner clicks
    // "Back" from step 3 to step 2, step3()'s session('lms_video_watched')
    // check was the ONLY gate. Since storeStep2() now sets that session key
    // only on success, this still works correctly — no change needed here,
    // but shown for clarity that step3() should keep reading from session,
    // not from a value that resets.
    // ──────────────────────────────────────────



    // ──────────────────────────────────────────
    // STEP 3 — Create Your Demo  (FIXED)
    // ──────────────────────────────────────────

    public function step3()
    {
        try {
            if (!session('lms_video_watched')) {
                return redirect()->route('lms.step2');
            }

            $course = CourseCategory::find(session('lms_course_id'));

            // ✅ FIXED: load the learner's previously submitted demo (if any)
            // so going Back/Forward or refreshing this page doesn't wipe
            // their topic, description, or uploaded video info.
            $userId   = session('demo_user_id') ?? auth()->id();
            $courseId = session('lms_course_id');

            $existingDemo = SubmittedDemos::where('demo_user_id', $userId)
                ->where('course_id', $courseId)
                ->first();

            return view('demo.lms.step3', [
                // ✅ FIXED: was 4 — this is step 3, not step 4.
                'currentStep'  => 3,
                'course'       => $course,
                'existingDemo' => $existingDemo,
            ]);
        } catch (Exception $e) {
            Log::error(__METHOD__, [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    public function storeStep3(Request $request)
    {
        try {
            $request->headers->set('Accept', 'application/json');

            Log::info('Step3 Request Started', [
                // 'user_id'      => auth()->id(),
                'request_data' => $request->except('demo_video'),
            ]);

            // ✅ FIXED: custom messages per rule so the SweetAlert popup
            // says exactly what's wrong ("video too large", "description
            // too short") instead of Laravel's generic default text.
            $validated = $request->validate([
                'demo_topic'       => 'required|string|max:200',
                'demo_description' => 'required|string|min:30|max:600',
                'demo_video'       => 'required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:512000',
            ], [
                'demo_topic.required'        => 'Please enter a topic for your demo.',
                'demo_topic.max'             => 'Your topic is too long — keep it under 200 characters.',

                'demo_description.required'  => 'Please describe what you demonstrated.',
                'demo_description.min'       => 'Your description is too short — write at least 30 characters so we understand what you covered.',
                'demo_description.max'       => 'Your description is too long — keep it under 600 characters.',

                'demo_video.required'        => 'Please upload your demo video before submitting.',
                'demo_video.file'            => 'The uploaded file is not valid. Please try again.',
                'demo_video.mimetypes'       => 'That file format isn\'t supported. Please upload MP4, MOV, AVI, or WEBM.',
                'demo_video.max'             => 'Your video is too large — please keep it under 500MB.',
            ]);

            $userId   = session('demo_user_id');
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
                    'demo_topic'       => $validated['demo_topic'],
                    'demo_description' => $validated['demo_description'],
                    'demo_video'       => $videoPath,
                    'status'           => 'pending',
                    'completion_score' => 0,
                ]
            );
            DemoAccessToken::where('user_id', auth()->id())->latest()->update([
                'is_completed' => true
            ]);
            Log::info('Step3 Completed', ['demo_id' => $demo->id]);

            return response()->json([
                'status'       => true,
                'message'      => 'Demo submitted successfully! 🎉',
                'redirect_url' => route('lms.step4'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Returned as-is — the JS side now reads e.errors() per-field
            // AND fires a SweetAlert with the first message.
            return response()->json([
                'status'  => false,
                'message' => 'Please fix the highlighted fields and try again.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Step3 Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                // 'user_id' => auth()->id(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while uploading your demo. Please try again.',
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
                // 'auth_user_id' => auth()->id(),
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
    public function step6()
    {
        // Look up the category id from the slug stored in session
        $category = CourseCategory::where('slug', session('lms_interest'))->first();
        $courseId = session('lms_course_id');
        $course = Course::where('id', $courseId)
            ->first();
        $reviews = DemoFeedback::with('user', 'course')
            ->whereNotNull('message')
            ->latest()
            ->take(10)
            ->get();

        return view('demo.lms.step6', [
            'currentStep' => 6,
            'course'     => $course,   // comma, not semicolon
            'reviews'     => $reviews,
        ]);
    }
    public function Download()
    {
         $courseId = session('lms_course_id');

    $course = Course::findOrFail($courseId);

    $pdf = Pdf::loadView('demo.lms.certificate', [
        'course' => $course,
        'user'   => auth()->user(),
    ]);

    return $pdf->download('certificate.pdf');
    }


    // ──────────────────────────────────────────
    // DASHBOARD
    // ──────────────────────────────────────────

    public function dashboard()
    {
        return redirect()->route('lms.step5'); // or load a real dashboard view
    }
    public function show(string $slug)
    {

        $course = Course::with([
            'category',
            'courseType',
            'courseLevel',
            'weeks.sessions.items',
            'demoFeatureVideos' => fn($q) => $q->where('status', 1)->orderBy('position'),
        ])->where('category_id', $slug)->firstOrFail();



        return view('demo.lms.courses', compact('course'));
    }
}
