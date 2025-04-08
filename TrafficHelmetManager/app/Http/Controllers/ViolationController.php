<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function live(){
        return view('admin.violations.live');
    }

    public function create(){
        return view('admin.violations.create');
    }
}
