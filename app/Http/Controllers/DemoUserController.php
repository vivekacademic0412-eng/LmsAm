<?php

namespace App\Http\Controllers;

use App\Models\DemoUser;
use Illuminate\Http\Request;

class DemoUserController extends Controller
{
    public function adminIndex(){
   
        return view('backend.demo-stages.index');
    }
    public function View(){
        return view('backend.demo-stages.submission-stage');
    }
}
