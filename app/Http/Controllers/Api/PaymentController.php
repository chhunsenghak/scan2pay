<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentController extends Controller
{

    public function index()
    {
        return view('payment.index');
    }

    public function create(Request $request)
    {
        $payment = Payment::create([
            'order_id' => Str::uuid(),
            'amount' => $request->input('amount'),
            'status' => 'pending',
        ]);

        // Call your external API (mock for now)
        $deeplink = "https://pay.example.com/" . $payment->id;

        return redirect("/payment/{$payment->id}")
            ->with('deeplink', $deeplink);
    }

    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return view('payment.show', compact('payment'));
    }

    public function status($id)
    {
        $payment = Payment::findOrFail($id);
        if ($payment->status === 'pending') {
            $payment->status = 'paid';
            $payment->save();
        }
        return response()->json(['status' => $payment->status]);
    }


    public function requestToken(Request $request)
    {
        // Logic to request a payment token
    }

    public function verifyToken(Request $request)
    {
        // Logic to process the payment
    }

    public function renewToken(Request $request)
    {
        // Logic to renew a payment token
    }

    public function generateDeeplink(Request $request)
    {
        $payment = Payment::create([
            'order_id' => Str::uuid(),
            'amount' => $request->input('amount'),
            'status' => 'pending',
        ]);

        return response()->json([
            'deeplink' => url("/payment/{$payment->id}/deeplink"),
            'payment_id' => $payment->id,
        ]);
    }

    public function checkMD5(Request $request)
    {
        // Logic to check MD5 hash
    }

    public function checkHash(Request $request)
    {
        // Logic to check hash
    }

    public function checkShortHash(Request $request)
    {
        // Logic to check short hash
    }
}
