<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json(Employee::latest()->get());
    }

    public function addEmployee(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
        ]);

        $employee = Employee::create($validated);

        $employee->employee_id = 'EMP-' . str_pad($employee->id, 5, '0', STR_PAD_LEFT);
        $employee->save();

        return response()->json([
            'message' => 'Employee created successfully',
            'data' => $employee
        ], 201);
    }
    public function showEmployee(Request $request)
    {
        $request->validate([
            'employeeId' => 'required|string',
        ]);

        $employee = Employee::where('employee_id', $request->employeeId)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json($employee);
    }

    public function updateEmployee(Request $request)
    {
        $employee = Employee::find($request->id);

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }

        $requestedEmployeeId = $request->input('employee_id', $request->input('employeeId'));

        if ($requestedEmployeeId && $requestedEmployeeId !== $employee->employee_id) {
            return response()->json([
                'message' => 'Employee ID cannot be changed'
            ], 422);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
        ]);

        // Fill the new values first
        $employee->fill($validated);

        // Check if there are changes
        if (!$employee->isDirty()) {
            return response()->json([
                'message' => 'Nothing Changed'
            ], 200);
        }

        // Save changes
        $employee->save();

        return response()->json([
            'message' => 'Employee updated successfully',
            'data' => $employee
        ]);
    }

    public function deleteEmployee(Request $request)
    {
        $request->validate([
            'employeeId' => 'required|string',
        ]);

        $employee = Employee::where('employee_id', $request->employeeId)->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found'
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully'
        ]);
    }
}
