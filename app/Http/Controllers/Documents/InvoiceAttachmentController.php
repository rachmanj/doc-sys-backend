<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class InvoiceAttachmentController extends Controller
{
    public function uploadAttachments(Request $request, Invoice $invoice)
    {
        $request->validate([
            'attachments.*' => 'required|file|mimes:pdf,jpg,jpeg,png,gif|max:10240', // 10MB max
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('invoice-attachments', 'public');

                $invoice->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->id,
                ]);
            }

            // Log the activity for each uploaded attachment
            foreach ($request->file('attachments') as $file) {
                ActivityLog::create([
                    'user_id' => Auth::user()->id,
                    'model_name' => 'Invoice',
                    'model_id' => $invoice->id,
                    'activity' => sprintf(
                        'Uploaded attachment: %s (Size: %s, Type: %s)',
                        $file->getClientOriginalName(),
                        number_format($file->getSize() / 1024, 2) . ' KB',
                        $file->getMimeType()
                    )
                ]);
            }


            return response()->json([
                'success' => true,
                'message' => 'Attachments uploaded successfully'
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => 'No files were uploaded'
        ], 400);
    }

    public function deleteAttachment(InvoiceAttachment $attachment)
    {
        try {
            // Delete the file from storage
            Storage::disk('public')->delete($attachment->file_path);

            // Delete the database record
            $attachment->delete();

            // Log the activity
            ActivityLog::create([
                'user_id' => Auth::user()->id,
                'model_name' => 'Invoice',
                'model_id' => $attachment->invoice_id,
                'activity' => sprintf(
                    'Deleted attachment: %s (Size: %s, Type: %s)',
                    $attachment->original_name,
                    number_format($attachment->size / 1024, 2) . ' KB',
                    $attachment->mime_type
                )
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAttachments(Invoice $invoice)
    {
        try {
            $attachments = $invoice->attachments()
                ->select(['id', 'file_path', 'original_name', 'mime_type', 'size', 'created_at'])
                ->latest()
                ->get()
                ->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'original_name' => $attachment->original_name,
                        'mime_type' => $attachment->mime_type,
                        'size' => $attachment->size,
                        'file_url' => $attachment->file_url,
                        'created_at' => $attachment->created_at
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $attachments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving attachments: ' . $e->getMessage()
            ], 500);
        }
    }
}
