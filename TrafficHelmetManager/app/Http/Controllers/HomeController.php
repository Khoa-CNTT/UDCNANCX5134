<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Violation;

class HomeController extends Controller
{
    public function index()
    {
        $violations = Violation::latest()->paginate(20);
        return view('web.index', compact('violations'));
    }
}
