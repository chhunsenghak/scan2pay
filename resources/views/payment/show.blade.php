@extends('layouts.payment')

@section('title', 'Payment Request')

@section('content')
<div class="payment-card">
    <div class="eyebrow">Scan to Pay</div>

    <h2 class="payment-title">Payment Request</h2>

    <p class="payment-subtitle">
        Amount due 
        <strong>
            {{ $payment['currency'] === 'KHR' ? '៛' : '$' }}
            {{ $payment['currency'] === 'KHR' 
                ? number_format($payment['amount'], 0) 
                : number_format($payment['amount'], 2) 
            }}
        </strong>
    </p>

    <div class="qr-wrap">
        <div class="qr-corner-tr"></div>
        <div class="qr-corner-bl"></div>

        {{-- Generate QR from session data --}}
        <img 
            src="data:image/png;base64,{{ base64_encode(
                QrCode::format('png')
                    ->size(250)
                    ->errorCorrection('H')
                    ->generate($payment['qr'])
            ) }}" 
            alt="Payment QR Code" 
        />
    </div>

    @if(isset($payment['deeplink']))
        <a href="{{ $payment['deeplink'] }}" class="pill-btn" target="_blank">
            Open in App
        </a>
    @endif

    <div class="divider"></div>

    <div class="status-wrap" id="status-wrap">
        <div class="pulse-dot"></div>
        <span id="status">Waiting for payment…</span>
    </div>

    <p class="footer-note">
        Encrypted & secure transaction
    </p>
</div>
@endsection


@section('scripts')
<script>
    const transactionId = "{{ $payment['transaction_id'] }}";
    
    setInterval(async () => {
        const res = await fetch("{{ route('payment.status') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ transaction_id: transactionId })
        });

        const data = await res.json();

        if (data.status === 'paid') {
            document.getElementById('status').textContent = 'Payment confirmed!';
            window.location.href = '/payment/success';
        }
    }, 3000);
</script>
@endsection