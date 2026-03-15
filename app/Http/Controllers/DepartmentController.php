<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('users')->paginate(15);
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:departments,name', 'description' => 'nullable|string']);
        Department::create($request->only(['name', 'description', 'is_active']));
        return redirect()->route('departments.index')->with('success', 'Department created.');
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate(['name' => 'required|unique:departments,name,' . $department->id]);
        $department->update($request->only(['name', 'description', 'is_active']));
        return redirect()->route('departments.index')->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        if ($department->users()->exists()) {
            return back()->with('error', 'Cannot delete a department with assigned users.');
        }
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted.');
    }
}
