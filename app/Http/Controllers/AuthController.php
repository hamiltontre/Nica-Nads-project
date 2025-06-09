<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function handleAuth(Request $request)
    {
        if ($request->isMethod('post')) {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if (Auth::attempt($credentials)) {
                return redirect()->route('atletas.index');
            }

            return back()->withErrors([
                'email' => 'Credenciales incorrectas',
            ]);
        }

        return view('auth.login');
    }
}
//$2y$12$AgrXQNXsSF.bzGcmd5JwoOBG9Hw0TeJ5Dt3Qz1/NGRQbgKlUXT72u