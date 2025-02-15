<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $departments = Department::when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('project', 'like', "%{$search}%")
                        ->orWhere('akronim', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => DepartmentResource::collection($departments->items()),
                'meta' => [
                    'current_page' => $departments->currentPage(),
                    'last_page' => $departments->lastPage(),
                    'per_page' => $departments->perPage(),
                    'total' => $departments->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch departments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllDepartments()
    {
        try {
            $departments = Department::orderBy('name')->get();

            return response()->json([
                'status' => 'success',
                'data' => DepartmentResource::collection($departments)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch departments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'project' => 'nullable|string|max:10',
                'location_code' => 'nullable|string|max:30',
                'transit_code' => 'nullable|string|max:30',
                'akronim' => 'nullable|string|max:10',
                'sap_code' => 'nullable|string|max:10'
            ]);

            $department = Department::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Department created successfully',
                'data' => new DepartmentResource($department)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Department $department)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => new DepartmentResource($department)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Department $department)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'project' => 'nullable|string|max:10',
                'location_code' => 'nullable|string|max:30',
                'transit_code' => 'nullable|string|max:30',
                'akronim' => 'nullable|string|max:10',
                'sap_code' => 'nullable|string|max:10'
            ]);

            $department->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Department updated successfully',
                'data' => new DepartmentResource($department)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Department $department)
    {
        try {
            // Check if department has users
            if ($department->users()->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete department with associated users'
                ], 422);
            }

            $department->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Department deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete department',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 