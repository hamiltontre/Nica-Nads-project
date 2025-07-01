<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Maneja el proceso de autenticación
    public function handleAuth(Request $request)
    {
        // Si es una solicitud POST (envío de formulario)
        if ($request->isMethod('post')) {
            // Valida credenciales
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Intenta autenticar al usuario
            if (Auth::attempt($credentials)) {
                return redirect()->route('atletas.index');
            }

            // Retorna con error si falla
            return back()->withErrors([
                'email' => 'Credenciales incorrectas',
            ]);
        }

        // Muestra el formulario de login si es GET
        return view('auth.login');
    }

    // Cierra la sesión del usuario
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}