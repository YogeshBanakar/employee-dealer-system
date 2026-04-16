<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Only employees can access user management.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!$request->user() || !$request->user()->isEmployee()) {
                    return redirect()->route('dashboard')->with('error', 'Access denied. Only employees can manage users.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', 'unique:users,email'],
            'password'   => 'required|string|min:6|confirmed',
            'user_type'  => 'required|in:employee,dealer',
            'city'       => 'nullable|string|max:255',
            'state'      => 'nullable|string|max:255',
            'zip_code'   => 'nullable|string|max:20',
        ], [
            'email.regex' => 'Please enter a complete email address (e.g. user@example.com).',
        ]);

        User::create([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'email'          => $request->email,
            'password'       => $request->password,
            'user_type'      => $request->user_type,
            'city'           => $request->city,
            'state'          => $request->state,
            'zip_code'       => $request->zip_code,
            'is_first_login' => false,
        ]);

        $successMessage = 'User created successfully.';

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $successMessage,
                'redirect' => route('users.index'),
            ]);
        }

        return redirect()->route('users.index')->with('success', $successMessage);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', Rule::unique('users')->ignore($user->id)],
            'password'   => 'nullable|string|min:6|confirmed',
            'user_type'  => 'required|in:employee,dealer',
            'city'       => 'nullable|string|max:255',
            'state'      => 'nullable|string|max:255',
            'zip_code'   => 'nullable|string|max:20',
        ], [
            'email.regex' => 'Please enter a complete email address (e.g. user@example.com).',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'user_type'  => $request->user_type,
            'city'       => $request->city,
            'state'      => $request->state,
            'zip_code'   => $request->zip_code,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        $successMessage = 'User updated successfully.';

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $successMessage,
                'redirect' => route('users.index'),
            ]);
        }

        return redirect()->route('users.index')->with('success', $successMessage);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        $successMessage = 'User deleted successfully.';

        if (request()->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $successMessage,
                'redirect' => route('users.index'),
            ]);
        }

        return redirect()->route('users.index')->with('success', $successMessage);
    }
}
