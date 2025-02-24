<?php

namespace App\Http\Controllers\Deliveries;

use App\Http\Controllers\Controller;
use App\Http\Resources\LpdSearchResource;
use App\Http\Resources\LpdEditResource;
use App\Models\ActivityLog;
use App\Models\Lpd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LpdController extends Controller
{
    public function search(Request $request)
    {
        try {
            $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $query = Lpd::with([
                'originDepartment', 
                'destinationDepartment', 
                'createdBy', 
                'receivedBy'
            ]);

            // Apply filters
            $filters = [
                'nomor' => 'like',
                'status' => 'exact',
                'origin_department' => 'exact',
                'destination_department' => 'exact',
            ];

            foreach ($filters as $field => $type) {
                if ($request->has($field)) {
                    if ($type === 'like') {
                        $query->where($field, 'like', '%' . $request->input($field) . '%');
                    } else {
                        $query->where($field, $request->input($field));
                    }
                }
            }

            $perPage = $request->input('per_page', 10);
            $lpds = $query->latest()->paginate($perPage);

            return LpdSearchResource::collection($lpds)
                ->additional([
                    'success' => true,
                    'message' => 'LPDs retrieved successfully',
                ]);
        } catch (\Exception $e) {
            Log::error('LPD search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error searching LPDs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getById(Lpd $lpd)
    {
        return new LpdEditResource($lpd);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor' => 'required|string|unique:lpds',
            'date' => 'required|date',
            'origin_department' => 'required|exists:departments,id',
            'destination_department' => 'required|exists:departments,id',
            'attention_person' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        $lpd = Lpd::create($validated);

        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'model_name' => 'Lpd',
            'model_id' => $lpd->id,
            'activity' => 'Created LPD ' . $validated['nomor'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'LPD created successfully',
            'data' => $lpd
        ]);
    }

    public function update(Request $request, Lpd $lpd)
    {
        try {
            $validated = $request->validate([
                'nomor' => 'sometimes|string|unique:lpds,nomor,' . $lpd->id,
                'date' => 'sometimes|date',
                'origin_department' => 'sometimes|exists:departments,id',
                'destination_department' => 'sometimes|exists:departments,id',
                'attention_person' => 'nullable|string',
                'notes' => 'nullable|string',
                'status' => 'sometimes|string',
                'sent_at' => 'nullable|date',
                'received_at' => 'nullable|date',
                'received_by' => 'nullable|exists:users,id',
            ]);

            $lpd->update($validated);

            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'model_name' => 'Lpd',
                'model_id' => $lpd->id,
                'activity' => 'Updated LPD ' . $lpd->nomor,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'LPD updated successfully',
                'data' => $lpd
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating LPD: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Lpd $lpd)
    {
        $lpd->delete();
        return response()->json(['message' => 'LPD deleted successfully']);
    }

    public function all(Request $request)
    {
        $query = Lpd::with(['originDepartment', 'destinationDepartment', 'createdBy']);

        $lpds = $query->orderBy('nomor', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'LPDs retrieved successfully',
            'data' => LpdSearchResource::collection($lpds)
        ]);
    }

    public function checkDuplication(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomor' => 'required|string',
            ]);

            $exists = Lpd::where('nomor', $validated['nomor'])->exists();

            return response()->json([
                'success' => true,
                'exists' => $exists
            ]);
        } catch (\Exception $e) {
            Log::error('Error in checkDuplication: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking duplication: ' . $e->getMessage()
            ], 500);
        }
    }
}
