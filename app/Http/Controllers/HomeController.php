<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $allUsers = User::all();

        return view('home', compact('currentUser', 'allUsers'));
    }
}