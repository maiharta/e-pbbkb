<?php

namespace App\Http\Controllers\Api;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Log the incoming request for debugging and audit purposes
        Log::channel('payment_callbacks')->info('Payment callback received', [
            'payload' => $request->all(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all()
        ]);

        // Validate the incoming request
        try {
            $validated = $request->validate([
                'id_billing' => 'required|string',
                'record_id' => 'required|string',
                'kwitansi' => 'required|string',
                'payment_date_paid' => 'required|date_format:Y-m-d H:i:s',
                'payment_date_kasda' => 'required|date_format:Y-m-d',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('payment_callbacks')->error('Validation failed', [
                'errors' => $e->errors(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid payload format',
                'errors' => $e->errors()
            ], 422);
        }

        // Find the invoice by id_billing (invoice number from Sipay)
        $invoice = Invoice::where('invoice_number', $request->kwitansi)
            ->where('sipay_record_id', $request->record_id)
            ->where('sipay_invoice', $request->id_billing)
            ->where('payment_status', 'pending')
            ->first();

        if (!$invoice) {
            Log::channel('payment_callbacks')->warning('Invoice not found', [
                'record_id' => $request->record_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        // Use database transaction to ensure data consistency
        try {
            DB::beginTransaction();

            // Update invoice with payment information
            $invoice->payment_status = 'paid';
            $invoice->sipay_payment_date_paid = Carbon::createFromFormat('Y-m-d H:i:s', $request->payment_date_paid, 'GMT+8')->setTimezone('UTC');
            $invoice->sipay_payment_date_kasda = Carbon::createFromFormat('Y-m-d H:i:s', $request->payment_date_kasda, 'GMT+8')->setTimezone('UTC');
            $invoice->sipay_status_invoice = true;
            $invoice->sipay_status_bpd = true;
            $invoice->save();

            // Update pelaporan status if needed
            $pelaporan = $invoice->pelaporan;
            if ($pelaporan) {
                $pelaporan->is_paid = true;
                $pelaporan->save();
            }

            DB::commit();

            // Log successful update
            Log::channel('payment_callbacks')->info('Payment processed successfully', [
                'pelaporan_id' => $pelaporan->id ?? null,
                'invoice_id' => $invoice->id,
                'record_id' => $invoice->sipay_record_id,
                'kwitansi' => $invoice->no_invoice,
                'payment_date_paid' => $invoice->sipay_payment_date_paid,
                'payment_date_kasda' => $invoice->sipay_payment_date_kasda
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment notification processed successfully',
                'data' => [
                    'record_id' => $invoice->sipay_record_id,
                    'kwitansi' => $invoice->no_invoice,
                    'payment_date_paid' => $invoice->sipay_payment_date_paid->toIso8601String(),
                    'status' => $invoice->payment_status
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('payment_callbacks')->error('Error processing payment', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing payment notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
