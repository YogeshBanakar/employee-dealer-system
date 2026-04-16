<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DealerController extends Controller
{
    /**
     * Display a list of dealers (Employee only).
     * Supports filtering/searching by zip code.
     */
    public function index(Request $request)
    {
        if (!Auth::user()->isEmployee()) {
            abort(403, 'Only employees can view the dealer list.');
        }

        $query = User::where('user_type', 'dealer');

        if ($request->filled('zip_code')) {
            $query->where('zip_code', 'like', '%' . $request->zip_code . '%');
        }

        $dealers = $query->latest()->paginate(10)->withQueryString();

        return view('dealers.index', compact('dealers'));
    }

    /**
     * Show form to edit a dealer's location (City, State, Zip).
     * Accessible by any Employee, or by the Dealer themselves.
     */
    public function editLocation(User $dealer)
    {
        if (!$dealer->isDealer()) {
            abort(404, 'User is not a dealer.');
        }

        $this->authorizeLocationEdit($dealer);

        return view('dealers.edit-location', compact('dealer'));
    }

    /**
     * Update a dealer's location (City, State, Zip).
     */
    public function updateLocation(Request $request, User $dealer)
    {
        if (!$dealer->isDealer()) {
            abort(404, 'User is not a dealer.');
        }

        $this->authorizeLocationEdit($dealer);

        $request->validate([
            'city'     => 'required|string|max:255',
            'state'    => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
        ]);

        $dealer->update([
            'city'     => $request->city,
            'state'    => $request->state,
            'zip_code' => $request->zip_code,
        ]);

        // Redirect based on who is editing
        $isEmployee    = Auth::user()->isEmployee();
        $successMessage = $isEmployee ? 'Dealer location updated successfully.' : 'Your location has been updated.';
        $redirectUrl   = $isEmployee ? route('dealers.index') : route('dashboard');

        if ($request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $successMessage,
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect($redirectUrl)->with('success', $successMessage);
    }

    /**
     * Check that the current user is allowed to edit the dealer's location.
     */
    private function authorizeLocationEdit(User $dealer): void
    {
        $user = Auth::user();

        // Employees can edit any dealer's location
        if ($user->isEmployee()) {
            return;
        }

        // Dealers can only edit their own location
        if ($user->isDealer() && $user->id === $dealer->id) {
            return;
        }

        abort(403, 'You are not authorized to edit this dealer\'s location.');
    }
}
