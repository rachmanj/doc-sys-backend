<?php

namespace App\Http\Controllers\Master;

use App\Models\AdditionalDocumentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdditionalDocumentTypeController extends Controller
{

    public function search(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = AdditionalDocumentType::query()->select('type_name');

        if ($request->has('type_name')) {
            $query->where('type_name', 'like', '%' . $request->type_name . '%');
        }

        $types = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'Document types retrieved successfully',
            'data' => $types
        ]);
    }

    public function all()
    {
        $types = AdditionalDocumentType::select('id', 'type_name')->orderBy('type_name', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Document types retrieved successfully',
            'data' => $types
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type_name' => 'required|string|max:255|unique:additional_document_types'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $type = AdditionalDocumentType::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Document type created successfully',
            'data' => $type
        ], 201);
    }

    public function show(AdditionalDocumentType $additionalDocumentType)
    {
        return response()->json([
            'success' => true,
            'message' => 'Document type retrieved successfully',
            'data' => $additionalDocumentType
        ]);
    }

    public function update(Request $request, AdditionalDocumentType $additionalDocumentType)
    {
        try {
            $validated = $request->validate([
                'type_name' => 'required|string|max:255|unique:additional_document_types,type_name,' . $additionalDocumentType->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $additionalDocumentType->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Document type updated successfully',
            'data' => $additionalDocumentType
        ]);
    }

    public function destroy(AdditionalDocumentType $additionalDocumentType)
    {
        $additionalDocumentType->delete();
        return response()->json([
            'success' => true,
            'message' => 'Document type deleted successfully',
            'data' => null
        ], 204);
    }
}
