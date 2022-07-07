<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    //
    public function sendResetEmail(Request $request)
    {
        if (!$this->validateEmail($request->email)) {
            return $this->failedResponse();
        }
        $this->send($request->email);
        return $this->successResponse();
    }
    public function validateEmail($email)
    {
        return !!User::where('email', $email)->first();
    }
    public function send($email)
    {
        $token = $this->createToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }
    public function failedResponse()
    {
        return response()->json([
            'error' => 'Email does not found on our database'
        ], Response::HTTP_NOT_FOUND);
    }
    public function successResponse()
    {
        return response()->json([
            'data' => 'Reset email has been sent successfully, please check your inbox.'
        ], Response::HTTP_OK);
    }
    public function createToken($email)
    {
        $oldToken=DB::table('password_resets')->where('email',$email)->first();
        if($oldToken){
            return $oldToken;
        }
        $token = Str::random(60);
        $this->saveToken($token,$email);
        return $token;
    }
    public function saveToken($token, $email)
    {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now(),


        ]);
    }
}
