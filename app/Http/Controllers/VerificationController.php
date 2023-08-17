<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VerificationController extends Controller
{
    public function success()
    {
        if (Session::has('success')) {
            return view('verification.success', ['message' => Session::get('success')]);
        }
        abort(404);
    }

    public function verify(Request $request)
    {
        $user = User::find($request->route('id'));

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('verification.success')->with('success', "Verifikasi Email Berhasil!");
    }
}
