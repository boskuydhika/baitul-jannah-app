<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show login page.
     */
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $user = $this->authService->attemptLogin($request->phone, $request->password);

            if ($user) {
                Auth::login($user, true); // Remember me enabled by default for convenience
                $request->session()->regenerate();

                return redirect()->intended(route('dashboard'));
            }

            return back()->withErrors([
                'phone' => 'Nomor HP atau password salah.',
            ]);

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $this->authService->logout(Auth::user());
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
