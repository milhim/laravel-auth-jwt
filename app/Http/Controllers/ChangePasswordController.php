<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordController extends Controller
{
    //
    public function changePassword(ChangePasswordRequest $request)
    {
        return $this->getPasswordResetTableRow($request)->count() > 0 ? $this->change($request) : $this->tokenNotFound();
    }
    private function getPasswordResetTableRow($request)
    {
       return DB::table('password_resets')->where([
            'email' => $request->email,
            'token' => $request->resetToken,
        ]);
    }
    private function tokenNotFound()
    {
        return response()->json([
            'error' => 'Token or Email incorect'
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    private function change(ChangePasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => $request->password]);
        $this->getPasswordResetTableRow($request)->delete();
        return response()->json([
            'data' => 'password successfully changed',
        ], Response::HTTP_CREATED);
    }
}
