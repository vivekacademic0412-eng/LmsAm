<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingDocument extends Model
{
    //
    protected $fillable = [
    'user_id',
    'doc_type',
    'file_path',
    'original_name',
    'file_size',
    'uploaded_at',
];
}
