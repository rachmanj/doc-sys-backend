<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\AdditionalDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class InvoiceController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Invoice::with(['supplier', 'createdBy', 'invoiceType']);

        if ($request->has('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }
        if ($request->has('po_no')) {
            $query->where('po_no', 'like', '%' . $request->po_no . '%');
        }
        if ($request->has('receive_project')) {
            $query->where('receive_project', 'like', '%' . $request->receive_project . '%');
        }
        if ($request->has('invoice_project')) {
            $query->where('invoice_project', 'like', '%' . $request->invoice_project . '%');
        }
        if ($request->has('payment_project')) {
            $query->where('payment_project', 'like', '%' . $request->payment_project . '%');
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->has('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        $invoices = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'message' => 'Invoices retrieved successfully',
            'data' => $invoices
        ]);
    }

    public function getById()
    {
        $invoice_id = request()->query('invoice_id');
        $invoice = Invoice::with('additionalDocuments')->find($invoice_id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice retrieved successfully',
            'data' => $invoice
        ]);
    }

    /**
     * Store a newly created invoice.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'invoice_number' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request) {
                        if (Invoice::where('invoice_number', $value)
                            ->where('supplier_id', $request->supplier_id)
                            ->exists()
                        ) {
                            $fail('The invoice number already exists for this supplier.');
                        }
                    },
                ],
                'supplier_id' => 'required|exists:suppliers,id',
                'invoice_date' => 'required|date',
                'receive_date' => 'required|date',
                'receive_project' => 'required|string|max:255',
                'invoice_project' => 'required|string|max:255',
                'payment_project' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'type_id' => 'required|exists:invoice_types,id',
                'po_no' => 'nullable|string|max:255',
                'currency' => 'required|string|max:10',
                'remarks' => 'nullable|string',
                'selected_documents' => 'nullable|array',
                'selected_documents.*' => 'exists:additional_documents,id',
                'user_id' => 'required|exists:users,id'
            ]);

            $user = User::find($validatedData['user_id']);;
            $locationCode = $user->department->location_code ?? null;

            if (!$locationCode) {
                throw new \Exception('User department location code not found');
            }

            $invoice = Invoice::create([
                'invoice_number' => $validatedData['invoice_number'],
                'supplier_id' => $validatedData['supplier_id'],
                'invoice_date' => $validatedData['invoice_date'],
                'receive_date' => $validatedData['receive_date'],
                'po_no' => $validatedData['po_no'],
                'currency' => $validatedData['currency'],
                'amount' => str_replace(',', '', $validatedData['amount']),
                'type_id' => $validatedData['type_id'],
                'remarks' => $validatedData['remarks'],
                'receive_project' => $validatedData['receive_project'],
                'invoice_project' => $validatedData['invoice_project'],
                'payment_project' => $validatedData['payment_project'],
                'cur_loc' => $locationCode,
                'created_by' => $validatedData['user_id']
            ]);

            if ($request->has('selected_documents')) {
                AdditionalDocument::whereIn('id', $request->selected_documents)
                    ->update(['invoice_id' => $invoice->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified invoice
     *
     * @param Request $request
     * @param Invoice $invoice
     * @return JsonResponse
     */
    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'invoice_number' => [
                    'sometimes',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($request, $invoice) {
                        if ($value !== $invoice->invoice_number) {
                            $exists = Invoice::where('invoice_number', $value)
                                ->where('supplier_id', $request->supplier_id ?? $invoice->supplier_id)
                                ->where('id', '!=', $invoice->id)
                                ->exists();
                            if ($exists) {
                                $fail('The invoice number already exists for this supplier.');
                            }
                        }
                    },
                ],
                'supplier_id' => 'sometimes|exists:suppliers,id',
                'invoice_date' => 'sometimes|date',
                'receive_date' => 'sometimes|date',
                'receive_project' => 'sometimes|string|max:255',
                'invoice_project' => 'sometimes|string|max:255',
                'payment_project' => 'sometimes|string|max:255',
                'amount' => 'sometimes|numeric|min:0',
                'type_id' => 'sometimes|exists:invoice_types,id',
                'po_no' => 'nullable|string|max:255',
                'currency' => 'sometimes|string|max:10',
                'remarks' => 'nullable|string',
                'selected_documents' => 'nullable|array',
                'selected_documents.*' => 'exists:additional_documents,id',
                'cur_loc' => 'sometimes|string|max:255',
            ]);

            // Remove selected_documents from the data to be updated in invoices table
            $invoiceData = collect($validatedData)
                ->except(['selected_documents'])
                ->toArray();

            // Store original values before update
            $originalValues = $invoice->getAttributes();

            // Update the invoice with filtered data
            $invoice->update($invoiceData);

            // Get only the changed attributes
            $changes = array_intersect_key(
                $invoice->getChanges(),
                $invoiceData
            );

            // Handle document associations separately
            if ($request->has('selected_documents')) {
                // First, clear any existing document associations
                AdditionalDocument::where('invoice_id', $invoice->id)
                    ->update(['invoice_id' => null]);

                // Then set the new associations
                AdditionalDocument::whereIn('id', $request->selected_documents)
                    ->update(['invoice_id' => $invoice->id]);

                $changes['selected_documents'] = $request->selected_documents;
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully',
                'data' => [
                    'id' => $invoice->id,
                    'changes' => $changes
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update invoice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    public function checkDuplication(Request $request)
    {
        $invoiceNumber = $request->query('invoice_number');
        $supplierId = $request->query('supplier_id');
        $exists = Invoice::where('invoice_number', $invoiceNumber)
            ->where('supplier_id', $supplierId)
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}
