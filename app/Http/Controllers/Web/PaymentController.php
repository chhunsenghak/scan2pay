<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Transaction;
use KHQR\Helpers\KHQRData;
use KHQR\BakongKHQR;
use KHQR\Models\IndividualInfo;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payment.index');
    }

    public function create(Request $request)
    {
        $request->validate([
            'amount'   => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,KHR',
        ]);

        $transactionId = (string) Str::uuid();

        $amount = (float) $request->amount;
        $currency = $request->currency;

        // Format amount
        $formattedAmount = $currency === 'KHR'
            ? (int) $amount
            : number_format($amount, 2, '.', '');

        // Save transaction first
        Transaction::create([
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
        ]);

        // Generate KHQR
        $merchant = new IndividualInfo(
            bakongAccountID: env('MERCHANT_BAKONG_ID'),
            merchantName: env('MERCHANT_NAME'),
            merchantCity: env('MERCHANT_CITY'),
            currency: $currency === 'KHR'
                ? KHQRData::CURRENCY_KHR
                : KHQRData::CURRENCY_USD,
            amount: $formattedAmount,
        );

        $qrResponse = BakongKHQR::generateIndividual($merchant);

        $qr = $qrResponse->data['qr'] ?? null;
        $md5 = $qrResponse->data['md5'] ?? null;

        // Optional: store md5
        Transaction::where('transaction_id', $transactionId)->update([
            
            'md5' => $md5
        ]);

        // Generate deeplink by QR
        $deeplink = $this->generateDeeplinkByQR([ 'qr' => $qr]);

        // Generate deeplink By Acount ID
        // $deeplink = $this->generateDeeplinkByQR([
        //     'accountId' => env('MERCHANT_BAKONG_ID'),
        //     'merchantName' => env('MERCHANT_NAME'),
        //     'amount' => $formattedAmount,
        //     'currency' => $currency,
        //     'transaction_id' => $transactionId,
        // ]);

    
        session([
            'payment' => [
                'qr' => $qr,
                'md5' => $md5,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'currency' => $currency,
                'deeplink' => $deeplink,
            ]
        ]);

        return redirect()->route('payment.show');
    }

    public function show()
    {
        $payment = session('payment');

        if (!$payment) {
            return redirect('/')->with('error', 'No payment found.');
        }

        return view('payment.show', compact('payment'));
    }

    public function status(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string'
        ]);

        $transaction = Transaction::where('transaction_id', $request->transaction_id)->first();

        return response()->json([
            'status' => $transaction?->status ?? 'pending'
        ]);
    }

    public function webhook(Request $request)
    {
        Log::info('Webhook received', $request->all());

        $data = $request->all();

        // Try common fields (depends on Bakong payload)
        $transactionId =
            $data['transactionId'] ??
            $data['data']['transactionId'] ??
            null;

        if (!$transactionId) {
            return response()->json(['error' => 'No transactionId'], 400);
        }

        $transaction = Transaction::where('transaction_id', $transactionId)->first();

        if ($transaction && $transaction->status !== 'paid') {
            $transaction->update([
                'status' => 'paid',
                'payload' => json_encode($data),
            ]);
        }

        return response()->json(['message' => 'OK']);
    }

    private function generateDeeplinkByQR(array $payload): ?string
    {
        $token = env('BAKONG_TOKEN');

        if (!$token) return null;

        try {
            $response = Http::withToken($token)->post(
                'https://api-bakong.nbc.gov.kh/v1/generate_deeplink_by_qr',
                [
                    'qr' => $payload['qr'],
                    'sourceInfo' => [
                        "appIconUrl"=> "https://bakong.nbc.gov.kh/images/logo.svg",
                        "appName"=> "Bakong", 
                        "appDeepLinkCallback"=> "https://bakong.nbc.gov.kh/"  
                    ]
                ]
            );

            if ($response->successful()) {
                $data = $response->json();

                return $data['data']['deeplink']
                    ?? $data['data']['shortLink']
                    ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Deeplink error', ['error' => $e->getMessage()]);
        }

        return null;
    }

    private function generateDeeplinkByAccoundID(array $payload): ?string
    {
        $token = env('BAKONG_TOKEN');

        if (!$token) return null;

        try {
            $response = Http::withToken($token)->post(                
                'https://api-bakong.nbc.gov.kh/v1/generate_deeplink_by_account_id_in_store',
                [
                    'accountId' => env('MERCHANT_BAKONG_ID'),
                    'merchantName' => env('MERCHANT_NAME'),
                    'transactionId' => $payload['transaction_id'],
                    'currency' => $payload['currency'],
                    'amount' => (string) $payload['amount'],
                ]
            );

            if ($response->successful()) {
                $data = $response->json();

                return $data['data']['deeplink']
                    ?? $data['data']['shortLink']
                    ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Deeplink error', ['error' => $e->getMessage()]);
        }

        return null;
    }
}