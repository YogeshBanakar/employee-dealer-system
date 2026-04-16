<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', 'unique:users,email'],
            'password'   => 'required|string|min:6|confirmed',
            'user_type'  => 'required|in:employee,dealer',
        ], [
            'email.regex' => 'Please enter a complete email address (e.g. user@example.com).',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password' => $request->password,
            'user_type'  => $request->user_type,
        ]);

        $successMessage = 'Registration successful! Please login.';

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $successMessage,
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login')->with('success', $successMessage);
    }

    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'],
            'password' => 'required|string',
        ], [
            'email.regex' => 'Please enter a complete email address (e.g. user@example.com).',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // If dealer on first login, redirect to complete profile
            if ($user->isDealer() && $user->is_first_login) {
                $redirectUrl = route('dealer.complete-profile');

                if ($request->ajax()) {
                    return response()->json([
                        'success'  => true,
                        'message'  => 'Please complete your profile.',
                        'redirect' => $redirectUrl,
                    ]);
                }

                return redirect($redirectUrl);
            }

            $successMessage = 'Welcome back, ' . $user->full_name . '!';

            if ($request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => $successMessage,
                    'redirect' => route('dashboard'),
                ]);
            }

            return redirect()->route('dashboard')->with('success', $successMessage);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
                'errors'  => ['email' => ['Invalid email or password.']],
            ], 422);
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    /**
     * Show dealer complete-profile form.
     */
    public function showCompleteProfile()
    {
        $user = Auth::user();

        if (!$user->isDealer() || !$user->is_first_login) {
            return redirect()->route('dashboard');
        }

        return view('auth.dealer-info', compact('user'));
    }

    /**
     * Handle dealer profile completion.
     */
    public function completeProfile(Request $request)
    {
        $request->validate([
            'city'     => 'required|string|max:255',
            'state'    => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $user->update([
            'city'           => $request->city,
            'state'          => $request->state,
            'zip_code'       => $request->zip_code,
            'is_first_login' => false,
        ]);

        $successMessage = 'Profile completed! Welcome, ' . $user->full_name . '!';

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $successMessage,
                'redirect' => route('dashboard'),
            ]);
        }

        return redirect()->route('dashboard')->with('success', $successMessage);
    }

    /**
     * Check if an email is already registered (AJAX).
     */
    public function checkEmail(Request $request)
    {
        $query = User::where('email', $request->email);

        if ($request->filled('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }

        return response()->json(['exists' => $query->exists()]);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function dashboard()
    {
        $user = session('user');

        if($user->user_type=='Dealer' && $user->is_first_login){
            return view('dealer_profile');
        }

        return view('dashboard');
    }
}
