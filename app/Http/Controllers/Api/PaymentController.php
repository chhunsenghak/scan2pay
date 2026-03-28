<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Str;
use KHQR\BakongKHQR;
use KHQR\Models\IndividualInfo;
use KHQR\Helpers\KHQRData;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payment.index');
    }

    public function create(Request $request)
    {
        $amount = (float) $request->input('amount');
        $currencyStr = $request->input('currency', 'USD');
        $currency = ($currencyStr === 'KHR') ? KHQRData::CURRENCY_KHR : KHQRData::CURRENCY_USD;

        $bakongId = env('MERCHANT_BAKONG_ID') ?: 'test@aba';
        $merchantName = env('MERCHANT_NAME') ?: 'Scan2Pay';
        $merchantCity = env('MERCHANT_CITY') ?: 'Phnom Penh';

        \Illuminate\Support\Facades\Log::info('Generating QR with:', [
            'bakongId' => $bakongId,
            'merchantName' => $merchantName,
            'merchantCity' => $merchantCity,
            'amount' => $amount,
            'currency' => $currency
        ]);

        try {
            $individualInfo = new IndividualInfo(
                bakongAccountID: $bakongId,
                merchantName: $merchantName,
                merchantCity: $merchantCity,
                acquiringBank: env('ACQUIRING_BANK') ?: 'ABA',
                currency: $currency,
                amount: $amount
            );

            $response = BakongKHQR::generateIndividual($individualInfo);
            $qrData = $response->data;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('QR Generation Failed', [
                'message' => $e->getMessage(),
                'bakongId' => $bakongId
            ]);
            return back()->with('error', 'Failed to generate QR: ' . $e->getMessage());
        }

        $payment = Payment::create([
            'order_id' => Str::uuid(),
            'amount' => $amount,
            'currency' => $currencyStr,
            'qr_code' => $qrData['qr'],
            'md5' => $qrData['md5'],
            'status' => 'pending',
        ]);

        return redirect("/payment/{$payment->id}");
    }

    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return view('payment.show', compact('payment'));
    }

    public function status($id)
    {
        $payment = Payment::findOrFail($id);
        
        if ($payment->status === 'paid') {
            return response()->json(['status' => 'paid']);
        }

        $token = env('BAKONG_TOKEN');
        if (!$token) {
            return response()->json(['status' => $payment->status]);
        }

        $bakong = new BakongKHQR($token);
        try {
            $isTest = env('APP_ENV') === 'local';
            $response = $bakong->checkTransactionByMD5($payment->md5, $isTest);
            
            if (isset($response['responseCode']) && $response['responseCode'] == 0) {
                $payment->update([
                    'status' => 'paid',
                    'raw_response' => json_encode($response)
                ]);
            }
        } catch (\Exception $e) {
            // Log error
        }

        return response()->json(['status' => $payment->status]);
    }
}
