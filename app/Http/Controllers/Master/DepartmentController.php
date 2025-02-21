<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;

class DepartmentController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Department::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('project')) {
            $query->where('project', 'like', '%' . $request->project . '%');
        }
        if ($request->has('location_code')) {
            $query->where('location_code', 'like', '%' . $request->location_code . '%');
        }
        if ($request->has('transit_code')) {
            $query->where('transit_code', 'like', '%' . $request->transit_code . '%');
        }
        if ($request->has('akronim')) {
            $query->where('akronim', 'like', '%' . $request->akronim . '%');
        }
        if ($request->has('sap_code')) {
            $query->where('sap_code', 'like', '%' . $request->sap_code . '%');
        }

        $departments = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'Departments retrieved successfully',
            'data' => $departments
        ]);
    }

    public function all()
    {
        try {
            $departments = Department::orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => DepartmentResource::collection($departments)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
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
                'success' => true,
                'message' => 'Department created successfully',
                'data' => new DepartmentResource($department)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to create department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Department $department)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:100',
                'project' => 'nullable|string|max:10',
                'location_code' => 'nullable|string|max:30',
                'transit_code' => 'nullable|string|max:30',
                'akronim' => 'nullable|string|max:10',
                'sap_code' => 'nullable|string|max:10'
            ]);

            $department->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Department updated successfully',
                'data' => new DepartmentResource($department)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
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
                    'error' => true,
                    'message' => 'Cannot delete department with associated users'
                ], 422);
            }

            $department->delete();

            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to delete department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCurLocs()
    {
        $cur_locs = Department::select('location_code')
            ->whereNotNull('location_code')
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cur_locs->pluck('location_code')
        ]);
    }
}
