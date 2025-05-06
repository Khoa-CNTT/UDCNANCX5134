<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Violation;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $status = $request->input('status');
        $date = $request->input('violation_date');

        $violations = Violation::query();

        if ($query) {
            $violations->where(function ($q) use ($query) {
                $q->where('plate_number', 'like', "%$query%")
                ->orWhere('id', 'like', "%$query%");
            });
        }

        if ($status) {
            $violations->where('status', $status);
        }

        if ($date) {
            $violations->whereDate('violation_time', $date);
        }

        $violations = $violations->latest()->paginate(20)->withQueryString();

        return view('web.index', compact('violations'));
    }
}
