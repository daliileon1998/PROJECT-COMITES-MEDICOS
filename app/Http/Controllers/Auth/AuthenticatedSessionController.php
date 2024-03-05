<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $user = Auth::user();

        // Validar el estado del usuario
        if ($user->estado === 0) {
            Auth::logout();
            return redirect('/login')->withErrors('Tu cuenta ha sido desactivada.');
        }

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Si la solicitud es de Inertia, devolver una respuesta JSON
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Sesión cerrada exitosamente']);
        }

        // Si la solicitud no es de Inertia, redirigir a la página de inicio
        return redirect('/');
    }

    protected function authenticated(Request $request, $user)
    {
        // Validar el estado del usuario
        if ($user->estado === 0) {
            Auth::logout();

           // Si la solicitud no es de Inertia, redirigir con un mensaje de error
            return redirect('/login')->withErrors('error', 'Tu cuenta ha sido desactivada.');
        }

        // Si la solicitud no es de Inertia, redirigir a la página de inicio
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
