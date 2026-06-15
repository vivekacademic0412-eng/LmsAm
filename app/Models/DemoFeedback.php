<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoFeedback extends Model
{
    protected  $table = 'demo_feedbacks';
    protected $fillable = [
        'user_id',
        'course_id',
        'demo_user_id',
        'emoji_reaction',
        'emoji_label',
        'rating',
        'content_rating',
        'clarity_rating',
        'support_rating',
        'message',
        'liked_tags',
        'improve_tags',
        'would_recommend',
    ];

    protected $casts = [
        'liked_tags'       => 'array',
        'improve_tags'     => 'array',
        'would_recommend'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
