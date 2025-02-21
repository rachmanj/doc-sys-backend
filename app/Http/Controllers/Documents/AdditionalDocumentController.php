<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdditionalDocumentSearchResource;
use App\Http\Resources\AdditionalDocumentEditResource;
use App\Models\ActivityLog;
use App\Models\AdditionalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdditionalDocumentController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = AdditionalDocument::with(['type', 'createdBy', 'invoice']);

        // Apply filters
        $filters = [
            'document_number' => 'like',
            'po_no' => 'like',
            'project' => 'like',
            'status' => 'exact',
            'type_id' => 'exact',
            'invoice_id' => 'exact',
            'cur_loc' => 'like',
            'cur_loc' => 'like',
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
        $documents = $query->latest()->paginate($perPage);

        return AdditionalDocumentSearchResource::collection($documents)
            ->additional([
                'success' => true,
                'message' => 'Additional documents retrieved successfully',
            ]);
    }

    public function getById(AdditionalDocument $additionalDocument)
    {
        return new AdditionalDocumentEditResource($additionalDocument);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_id' => 'required|exists:additional_document_types,id',
            'document_number' => 'required|string',
            'document_date' => 'required|date',
            'receive_date' => 'nullable|date',
            'po_no' => 'nullable|string',
            'cur_loc' => 'nullable|string',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        $validated['created_by'] = Auth::id();

        $additionalDocument = AdditionalDocument::create($validated);

        // save activity log
        ActivityLog::create([
            'user_id' => Auth::user()->id,
            'model_name' => 'AdditionalDocument',
            'model_id' => 0,
            'activity' => 'Created additional document ' . $validated['document_number'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document created successfully',
            'data' => $additionalDocument
        ]);
    }

    public function update(Request $request, AdditionalDocument $additionalDocument)
    {
        try {
            $validated = $request->validate([
                'type_id' => 'sometimes|exists:additional_document_types,id',
                'document_number' => 'sometimes|string',
                'document_date' => 'sometimes|date',
                'po_no' => 'nullable|string',
                'invoice_id' => 'nullable|exists:invoices,id',
                'project' => 'nullable|string',
                'status' => 'sometimes|string',
                'cur_loc' => 'nullable|string',
                'receive_date' => 'nullable|date',
                'remarks' => 'nullable|string',
            ]);

            $additionalDocument->update($validated);

            // Get only the changed attributes
            $changes = array_intersect_key(
                $additionalDocument->getChanges(),
                $validated
            );

            // save activity log
            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'model_name' => 'AdditionalDocument',
                'model_id' => $additionalDocument->id,
                'activity' => 'Updated additional document ' . $additionalDocument->document_number,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully',
                'data' => [
                    'id' => $additionalDocument->id,
                    'changes' => $changes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating document: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(AdditionalDocument $additionalDocument)
    {
        $additionalDocument->delete();
        return response()->json(['message' => 'Document deleted successfully']);
    }

    public function all(Request $request)
    {
        $query = AdditionalDocument::with(['type', 'invoice', 'createdBy']);

        // apply filters
        $filters = [
            'document_number' => 'like',
            'po_no' => 'like',
            'project' => 'like',
            'status' => 'exact',
            'type_id' => 'exact',
            'invoice_id' => 'exact',
            'cur_loc' => 'like',
            'cur_loc' => 'like',
        ];

        foreach ($filters as $field => $type) {
            if ($request->has($field)) {
                if ($type === 'like') {
                    $query = $query->where($field, 'like', '%' . $request->input($field) . '%');
                } else {
                    $query = $query->where($field, $request->input($field));
                }
            }
        }

        $documents = $query->orderBy('document_number', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Additional documents retrieved successfully',
            'data' => $documents
        ]);
    }

    public function getByPo(Request $request)
    {
        $po_no = $request->query('po_no');

        $documents = AdditionalDocument::where('po_no', $po_no)
            ->orderBy('document_number', 'asc')
            ->get();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Additional documents retrieved successfully',
        //     'data' => $documents
        // ]);

        return response()->json($documents);
    }

    public function checkDuplication(Request $request)
    {
        try {
            $validated = $request->validate([
                'docnum' => 'required|string',
                'type_id' => 'required|exists:additional_document_types,id',
            ]);

            $exists = AdditionalDocument::where('document_number', $validated['docnum'])
                ->where('type_id', $validated['type_id'])
                ->exists();

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
