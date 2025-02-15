<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Http\Resources\SupplierResource;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index()
    {
        // $suppliers = Supplier::paginate(10);
        $suppliers = Supplier::select('id', 'sap_code', 'name', 'type', 'created_by', 'is_active', 'payment_project')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Suppliers retrieved successfully',
            'data' => $suppliers
        ]);
    }

    public function all()
    {
        $suppliers = Supplier::all();

        return response()->json([
            'status' => 'success',
            'message' => 'This from All suppliers retrieved successfully',
            'data' => SupplierResource::collection($suppliers)
        ]);
    }

    public function store(Request $request)
    {
        $supplier = Supplier::create($request->all());
        return response()->json([
            'status' => 'success',
            'message' => 'Supplier created successfully',
            'data' => $supplier
        ], 201);
    }

    public function show(Supplier $supplier)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Supplier retrieved successfully',
            'data' => $supplier
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {
        if ($request->has('sap_code')) {
            if ($supplier->sap_code !== $request->sap_code) {
                //    check for existing sap_code
                $sap_code = Supplier::where('sap_code', $request->sap_code)->first();
                if ($sap_code) {
                    return response()->json([
                        'error' => true,
                        'message' => 'SAP Code already exists',
                        'data' => null
                    ], 200);
                }
            }
        }

        $supplier->update($request->only($supplier->getTableColumns()));

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully',
            'data' => $supplier
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Supplier deleted successfully',
            'data' => null
        ], 204);
    }

    public function cek_target()
    {
        try {
            // $url = 'http://192.168.32.17/payreq-x-v3/api/customers';
            $url = 'http://payreq-one.local/api/customers';

            $client = new Client();
            $response = $client->get($url);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode >= 400) {
                Log::error('Failed to fetch suppliers from external API', [
                    'status' => $statusCode,
                    'body' => $body,
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch suppliers from external API',
                    'data' => null
                ], 500);
            }

            $data = json_decode($body, true);

            if (!isset($data['customers'])) {
                Log::error('External API response missing "data" key', [
                    'response' => $data,
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'External API response missing "data" key',
                    'data' => null
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Suppliers fetched successfully from external API',
                'data' => [
                    'customer_count' => $data['customer_count'],
                    'vendor_count' => $data['vendor_count']
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch suppliers from external API', ['exception' => $e]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch suppliers from external API',
                'data' => null
            ], 500);
        }
    }

    public function import()
    {
        try {
            $url = 'http://payreq-one.local/api/customers';
            $client = new Client();
            $response = $client->get($url);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($statusCode >= 400) {
                Log::error('Failed to fetch suppliers from external API', [
                    'status' => $statusCode,
                    'body' => $body,
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch suppliers from external API',
                    'data' => null
                ], 500);
            }

            $suppliers = json_decode($body, true);
            $createdCount = 0;

            // Insert data to suppliers table
            foreach ($suppliers['customers'] as $supplier) {
                $existingSupplier = DB::table('suppliers')->where('sap_code', $supplier['code'])->first();
                if (!$existingSupplier) {
                    DB::table('suppliers')->insert([
                        'sap_code' => $supplier['code'],
                        'name' => $supplier['name'],
                        'type' => $supplier['type'],
                        'created_by' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $createdCount++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Data suppliers berhasil diimport. Total created: $createdCount",
                'data' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to import suppliers', ['exception' => $e]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to import suppliers',
                'data' => null
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Supplier::query();

        if ($request->has('sap_code')) {
            $query->where('sap_code', 'like', '%' . $request->sap_code . '%');
        }

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('payment_project')) {
            $query->where('payment_project', $request->payment_project);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $suppliers = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'status' => 'success',
            'message' => 'Suppliers retrieved successfully',
            'data' => $suppliers
        ]);
    }
}
