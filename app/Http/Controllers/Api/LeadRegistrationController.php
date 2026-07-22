<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\StudentThankYouMail;
use App\Models\Lead;
use App\Models\User;
use App\Models\TrafficSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeadRegistrationController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'track' => 'required',
            'source' => 'nullable'
        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Save Traffic Source
            |--------------------------------------------------------------------------
            */

            $traffic = TrafficSource::create(
                TrafficSource::attributesFromRequest($request)
            );

            /*
            |--------------------------------------------------------------------------
            | Save Lead
            |--------------------------------------------------------------------------
            */

            // $lead = Lead::create([
            //     'name'=>$request->name,
            //     'email'=>$request->email,
            //     'phone'=>$request->phone,
            //     'track'=>$request->track,
            //     'source_page'=>$request->source,
            //     'traffic_source_id'=>$traffic->id
            // ]);

            /*
            |--------------------------------------------------------------------------
            | User Exists?
            |--------------------------------------------------------------------------
            */

            $user = User::whereEmail($request->email)->first();

            if (!$user) {

                $password = Str::random(10);

                $user = User::create([

                    'name' => $request->name,

                    'email' => $request->email,

                    'contact' => $request->phone,

                    'password' => Hash::make($password),

                    'role' => User::ROLE_STUDENT,

                    'is_active' => true

                ]);

                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addDays(7),
                    [
                        'id' => $user->id,
                        'hash' => sha1($user->email)
                    ]
                );

                Mail::to($user)->send(
                    new StudentThankYouMail(
                        $user,
                        $verificationUrl
                    )
                );
            }

            DB::commit();

            return response()->json([

                'success' => true,

                'user_id' => $user->id,

                'message' => 'Registration Successful'

            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }
}
