<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Violation;

class ViolationController extends Controller
{
    public function index(){
        $violations = Violation::latest()->paginate(10);
        return view('admin.violations.index', compact('violations'));
    }

    public function update(Request $request, $id){
        $violation = Violation::findOrFail($id);
        $violation->update($request->all());
        // Trả về json cập nhật thành công
        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật vi phạm thành công'
        ]);
    }

    public function live(){
        return view('admin.violations.live');
    }

    public function create(){
        return view('admin.violations.create');
    }

    public function delete($id){
        Violation::findOrFail($id)->delete();
        // Trả về json xóa thành công
        return response()->json([
            'status' => 'success',
            'message' => 'Xóa vi phạm thành công'
        ]);
    }
}
