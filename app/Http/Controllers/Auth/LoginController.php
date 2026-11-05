<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login (fallback).
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the post login redirect path.
     */
    public function redirectTo()
    {
        if (auth()->check() && auth()->user()->role === 'driver') {
            return route('driver.dashboard');
        }

        return route('dashboard');
    }

    /**
     * Redirect users based on their role after login.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'driver') {
            return redirect()->route('driver.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
