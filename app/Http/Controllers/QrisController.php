<?php
// app/Http/Controllers/QrisController.php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class QrisController extends Controller
{
    /**
     * Show the QRIS scanner page (for kasir)
     */
    public function scanner(Order $order)
    {
        $order->load('user', 'items');
        return view('pages.cashier.qris-scanner', compact('order'));
    }

    /**
     * Verify a scanned QR code and mark payment
     */
    public function verify(Request $request, Order $order)
    {
        $request->validate(['qr_data' => 'required|string']);

        // In production, integrate with actual QRIS payment gateway
        // For now, we simulate: any non-empty QR data is accepted
        $qrData = $request->qr_data;

        // Basic QRIS format check (starts with 000201 - standard QRIS EMVCo)
        $isValidQris = str_starts_with($qrData, '000201') || strlen($qrData) > 10;

        if (!$isValidQris) {
            return response()->json(['error' => 'QR Code tidak valid atau bukan QRIS'], 422);
        }

        $order->update([
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'qris_ref'       => substr($qrData, 0, 50), // store ref
            'paid_at'        => now(),
            'kasir_id'       => auth()->id(),
            'status'         => 'processing',
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Pembayaran QRIS berhasil dikonfirmasi!',
            'receipt_url'  => route('orders.receipt', $order),
        ]);
    }
}
