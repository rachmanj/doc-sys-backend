<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\InvoiceType;
use Illuminate\Http\Request;

class InvoiceTypeController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = InvoiceType::query()->select('id', 'type_name');

        if ($request->has('type_name')) {
            $query->where('type_name', 'like', '%' . $request->type_name . '%');
        }

        $invoiceTypes = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'Invoice types retrieved successfully',
            'data' => $invoiceTypes
        ]);
    }

    public function all()
    {
        try {
            $invoiceTypes = InvoiceType::select('id', 'type_name')->orderBy('type_name')->get();

            return response()->json([
                'success' => true,
                'data' => $invoiceTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to fetch invoice types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'type_name' => 'required|string|max:100|unique:invoice_types'
            ]);

            $invoiceType = InvoiceType::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Invoice type created successfully',
                'data' => $invoiceType
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to create invoice type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, InvoiceType $invoiceType)
    {
        try {
            $request->validate([
                'type_name' => 'required|string|max:100|unique:invoice_types,type_name,' . $invoiceType->id
            ]);

            $invoiceType->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Invoice type updated successfully',
                'data' => $invoiceType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to update invoice type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(InvoiceType $invoiceType)
    {
        try {
            $invoiceType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Invoice type deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to delete invoice type',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
