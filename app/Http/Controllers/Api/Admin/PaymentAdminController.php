<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with('user')->orderByDesc('id');
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        $payments = $query->paginate($request->integer('per_page', 20));
        return response()->json(['status' => 'success', 'data' => $payments]);
    }

    public function show(int $id)
    {
        $payment = Payment::with('user')->findOrFail($id);
        return response()->json(['status' => 'success', 'data' => $payment]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => ['required','in:pending,paid,failed,refunded']]);
        $payment = Payment::findOrFail($id);
        $payment->status = $request->string('status');
        $payment->save();
        return response()->json(['status' => 'success', 'message' => 'Payment status updated', 'data' => $payment]);
    }

    public function refund(Request $request, int $id)
    {
        $request->validate(['amount' => ['nullable','numeric','min:0']]);
        $payment = Payment::findOrFail($id);
        // This is a stub. Integrate your real gateway here.
        $meta = $payment->meta ?? [];
        $meta['refunds'] = $meta['refunds'] ?? [];
        $meta['refunds'][] = [
            'amount' => $request->input('amount', $payment->amount),
            'at' => now()->toISOString(),
        ];
        $payment->meta = $meta;
        $payment->status = 'refunded';
        $payment->save();
        return response()->json(['status' => 'success', 'message' => 'Payment refunded', 'data' => $payment]);
    }
}
