<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function all()
    {
        $projects = Project::select('code')->orderBy('code')->get();

        return response()->json($projects);
    }

    public function search(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Project::query();

        if ($request->has('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }
        if ($request->has('owner')) {
            $query->where('owner', 'like', '%' . $request->owner . '%');
        }
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $projects = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'Projects retrieved successfully',
            'data' => $projects
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:projects',
            'owner' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $project = Project::create($validated);
        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|unique:projects,code,' . $project->id,
                'owner' => 'nullable|string',
                'location' => 'nullable|string',
            ]);

            $project->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'data' => $project
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to update project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Project $project)
    {
        try {
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to delete project',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
