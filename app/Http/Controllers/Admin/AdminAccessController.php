<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminAccessController extends Controller
{
    /**
     * Show the admin access gate page
     * 
     * If user is already authenticated as admin, redirect to dashboard.
     * Otherwise, show the creative access gate landing page.
     */
    public function index()
    {
        // If user is authenticated and has admin role, redirect to dashboard
        if (Auth::check()) {
            $user = Auth::user();
            
            if (in_array($user->role ?? null, ['admin', 'super_admin'])) {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $user->name);
            }
        }

        // Otherwise, show the access gate
        return view('admin.access-gate');
    }
}
