<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoureManagerController extends Controller
{
    //
    public function weeks()
    {
        return view('backend.admin.course.week.index');
    }

    public function seats()
    {
        return view('backend.admin.course.seats.index');
    }
    public function certificates()
    {
        return view('backend.admin.course.certificates.index');
    }
    public function sessions()
    {
        return view('backend.admin.course.session.index');
    }
}
