<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabSetupForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'traffic_source_id',

        // 01. School Basics
        'school_name',
        'board_affiliation',
        'address',
        'grades_offered',
        'student_strength',

        // 02. Existing Lab, Room & Space
        'existing_lab',
        'room_size',
        'seating_capacity',
        'furniture',

        // 03. Power, Network & Internet
        'power_points',
        'backup_power',
        'internet_availability',
        'internet_speed',
        'school_devices',

        // 04. Students & Grades to Enroll
        'enroll_grades',
        'expected_students',
        'session_frequency',

        // 05. Timeline, Budget & Procurement
        'start_date',
        'annual_budget',
        'procurement_process',
        'lab_goals',

        // 06. Point of Contact
        'contact_name',
        'designation',
        'phone',
        'email',

        // Sign-off
        'signature',
        'sig_date',

        // LMS sync
        'synced_to_lms',
        'lms_reference_id',
    ];

    protected $casts = [
        'furniture'      => 'array',
        'sig_date'       => 'date',
        'synced_to_lms'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trafficSource()
    {
        return $this->belongsTo(TrafficSource::class);
    }
}