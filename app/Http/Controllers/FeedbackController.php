<?php
// FILE: app/Http/Controllers/FeedbackController.php
namespace App\Http\Controllers;

use App\Models\DemoFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    // POST /lms/feedback  — called via AJAX from step4
    public function store(Request $request)
    {
        $data = $request->validate([
            'emoji_reaction'   => 'required|string|max:10',
            'emoji_label'      => 'required|string|max:50',
            'rating'           => 'nullable|integer|min:1|max:5',
            'content_rating'   => 'nullable|integer|min:1|max:5',
            'clarity_rating'   => 'nullable|integer|min:1|max:5',
            'support_rating'   => 'nullable|integer|min:1|max:5',
            'message'          => 'nullable|string|max:1000',
            'liked_tags'       => 'nullable|array',
            'liked_tags.*'     => 'string|max:60',
            'improve_tags'     => 'nullable|array',
            'improve_tags.*'   => 'string|max:60',
            'would_recommend'  => 'nullable|boolean',
        ]);

        // Prevent duplicate per course
        DemoFeedback::updateOrCreate(
            [
                'user_id'   => Auth::id(),
                'demo_user_id'=>session('demo_user_id'),
                'course_id' => session('lms_course_id'),
            ],
            $data + [
                'user_id'   => Auth::id(),
                'course_id' => session('lms_course_id'),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Thank you for your feedback! 🙏']);
    }

    // GET /admin/feedbacks  — super admin view
    public function adminIndex()
    {
        $feedbacks = DemoFeedback::with(['user','course'])
            ->latest()->paginate(20);

        $stats = [
            'total'     => DemoFeedback::count(),
            'avg_rating'=> round(DemoFeedback::whereNotNull('rating')->avg('rating'), 1),
            'recommend' => DemoFeedback::where('would_recommend', true)->count(),
            'by_emoji'  => DemoFeedback::selectRaw('emoji_reaction, emoji_label, count(*) as count')
                            ->groupBy('emoji_reaction','emoji_label')->get(),
        ];

        return view('feedback.feedback', compact('feedbacks','stats'));
    }
}