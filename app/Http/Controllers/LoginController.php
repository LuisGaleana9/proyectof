<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Mostrar el formulario
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesar el Login
    public function login(Request $request)
    {
        // Validar que enviaron datos
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Autenticacion
        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            // Verifico el rol del usuario
            $usuario = Auth::user();

            if ($usuario->rol === 'Admin') {
                return redirect()->intended('/admin');
            } elseif ($usuario->rol === 'Profesor') {
                return redirect()->intended('/profesor');
            } else {
                return redirect()->intended('/usuario');
            }
        }

        // Si falla el login
        return back()->withErrors([
            'email' => 'Correo o contraseña invalida.',
        ]);
    }

    // Cerrar Sesion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}