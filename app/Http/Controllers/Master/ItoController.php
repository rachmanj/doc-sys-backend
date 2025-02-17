<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\ItoImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class ItoController extends Controller
{
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $import = new ItoImport(false);
            Excel::import($import, $request->file('file'));

            return response()->json([
                'status' => 'success',
                'message' => 'Data imported successfully',
                'data' => [
                    'imported' => $import->getSuccessCount(),
                    'skipped' => $import->getSkippedCount()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $import = new ItoImport(true);
            Excel::import($import, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Import check completed',
                'data' => [
                    'importable' => $import->getSuccessCount(),
                    'duplicates' => $import->getSkippedCount()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error checking import: ' . $e->getMessage()
            ], 500);
        }
    }
}
