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
        //Nếu plate_number không có giá trị hoặc rỗng thì gắn bằng null
        if (empty($request->plate_number)) {
            $request->merge(['plate_number' => null]);
        }
        $violation->update($request->all());
        // Trả về json cập nhật thành công
        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật vi phạm thành công'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $status = $request->input('status');
        $date = $request->input('violation_date');

        $hasFilter = $query || $status || $date;

        $violations = collect(); // mặc định là collection rỗng

        if ($hasFilter) {
            $violationsQuery = Violation::query();

            if ($query) {
                $violationsQuery->where(function ($q) use ($query) {
                    $q->where('plate_number', 'like', "%$query%")
                        ->orWhere('id', 'like', "%$query%");
                });
            }

            if ($status) {
                $violationsQuery->where('status', $status);
            }

            if ($date) {
                $violationsQuery->whereDate('violation_time', $date);
            }
            
            $violations = $violationsQuery->latest()->paginate(10)->withQueryString();
        }

        return view('admin.violations.search', compact('violations', 'hasFilter'));
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
