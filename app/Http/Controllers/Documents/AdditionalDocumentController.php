<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\AdditionalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdditionalDocumentController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = AdditionalDocument::with(['type', 'createdBy', 'invoice']);

        if ($request->has('document_number')) {
            $query->where('document_number', 'like', '%' . $request->document_number . '%');
        }
        if ($request->has('po_no')) {
            $query->where('po_no', 'like', '%' . $request->po_no . '%');
        }
        if ($request->has('project')) {
            $query->where('project', 'like', '%' . $request->project . '%');
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('type_id')) {
            $query->where('type_id', $request->type_id);
        }
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }
        if ($request->has('cur_loc')) {
            $query->where('cur_loc', 'like', '%' . $request->cur_loc . '%');
        }

        $documents = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'Additional documents retrieved successfully',
            'data' => $documents
        ]);
    }

    public function getById(AdditionalDocument $additionalDocument)
    {
        return $additionalDocument->load(['type', 'createdBy', 'invoice']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_id' => 'required|exists:additional_document_types,id',
            'document_number' => 'required|string',
            'document_date' => 'required|date',
            'po_no' => 'nullable|string',
            'invoice_id' => 'nullable|exists:invoices,id',
            'project' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        return AdditionalDocument::create($validated);
    }

    public function update(Request $request, AdditionalDocument $additionalDocument)
    {
        $validated = $request->validate([
            'type_id' => 'sometimes|exists:additional_document_types,id',
            'document_number' => 'sometimes|string',
            'document_date' => 'sometimes|date',
            'po_no' => 'nullable|string',
            'invoice_id' => 'nullable|exists:invoices,id',
            'project' => 'nullable|string',
            'status' => 'sometimes|string',
        ]);

        $additionalDocument->update($validated);
        return $additionalDocument->refresh();
    }

    public function destroy(AdditionalDocument $additionalDocument)
    {
        $additionalDocument->delete();
        return response()->json(['message' => 'Document deleted successfully']);
    }

    public function all()
    {
        $documents = AdditionalDocument::orderBy('document_number', 'asc')->get();

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
}
