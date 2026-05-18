<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    private function isSafeRelativeRedirect(?string $redirect): bool
    {
        return is_string($redirect) && str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//');
    }

    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {
        $redirect = $request->query('redirect');
        $safeRedirect = $this->isSafeRelativeRedirect($redirect) ? $redirect : null;

        if ($safeRedirect) {
            $request->session()->put('url.intended', $safeRedirect);
        }

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'redirect' => $safeRedirect,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Store user_type in session for persistent access
        $user = Auth::user();
        $request->session()->put('user_type', $user->user_type ?? 'user');
        $request->session()->put('user_id', $user->id);
        $request->session()->put('user_name', $user->name);

        \Illuminate\Support\Facades\Log::info('Login successful', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_type' => $user->user_type ?? 'user',
            'session_id' => $request->session()->getId(),
        ]);

        $redirect = $request->input('redirect');
        if ($this->isSafeRelativeRedirect($redirect)) {
            $request->session()->put('url.intended', $redirect);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        // Regenerate token BEFORE invalidating session
        // This ensures CSRF token is available in response headers
        $request->session()->regenerateToken();

        $request->session()->invalidate();

        return redirect('/');
    }
}
