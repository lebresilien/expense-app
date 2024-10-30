<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\{User, Password};
use App\Mail\SendCodeResetPassword;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        // Delete all old code that the user sent before.
        Password::where('email', $request->email)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = Password::create($data);

        // Send email to user
        Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

        return $this->sendResponse($data, 'Password reset code sent.');
    }

    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:password_reset_tokens',
        ]);

        // find the code
        $passwordReset = Password::firstWhere('code', $request->code);

        //Check if it has not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            $this->sendError('Unauthorised.', [ 'error'=> 'code expired' ]);
        }

        return $this->sendResponse([
            'code' => $passwordReset->code,
        ], 'valid code.');
    }

    public function reset(Request $request) {

        $request->validate([
            'code' => 'required|string|exists:password_reset_tokens',
            'password' => 'required|string|min:6|confirmed',
        ]);

         // find the code
         $passwordReset = Password::firstWhere('code', $request->code);

         //Check if it has not expired: the time is one hour
         if ($passwordReset->created_at > now()->addHour()) {
             $passwordReset->delete();
             $this->sendError('Unauthorised.', [ 'error'=> 'code expired' ]);
         }

         return $this->sendResponse($data, 'valid code.');

         // find user's email
         $user = User::firstWhere('email', $passwordReset->email);

         // update user password
         $user->update($request->only('password'));

         // delete current code
         $passwordReset->delete();

        return $this->sendResponse($data, 'Password reset.');
    }
}
