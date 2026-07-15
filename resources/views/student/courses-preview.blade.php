@extends('layouts.app')

@section('title', 'My Courses | Academic Mantra Services')

@section('meta_description', 'Access your enrolled courses, track your learning progress, watch course sessions, complete assignments, take quizzes, and continue your professional training with Academic Mantra Services.')

@section('meta_keywords', 'my courses, enrolled courses, LMS dashboard, online courses, learning management system, Academic Mantra Services, professional training, digital marketing course, AI course, IT courses, HR training, graphic designing, content writing, student learning portal')

@section('content')
    <livewire:student.course-preview/>
@endsection