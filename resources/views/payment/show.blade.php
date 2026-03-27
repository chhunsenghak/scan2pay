@extends('layouts.payment')

@section('title', 'Payment Request')

@section('content')
<div class="payment-card">
    <div class="eyebrow">Scan to Pay</div>
    <h2 class="payment-title">Payment Request</h2>
    <p class="payment-subtitle">Amount due <strong>${{ $payment->amount }}</strong></p>

    <div class="qr-wrap">
        <div class="qr-corner-tr"></div>
        <div class="qr-corner-bl"></div>
        <img
            src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ session('deeplink') }}"
            alt="Payment QR Code"
        />
    </div>

    <div>
        <a href="{{ session('deeplink') }}" class="pill-btn">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
            Open in App
        </a>
    </div>

    <div class="divider"></div>

    <div class="status-wrap" id="status-wrap">
        <div class="pulse-dot"></div>
        <span id="status">Waiting for payment…</span>
    </div>

    <p class="footer-note">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        Encrypted &amp; secure transaction
    </p>
</div>
@endsection

@section('scripts')
<script>
    const paymentId = {{ $payment->id }};

    setInterval(async () => {
        const res  = await fetch(`/payment/${paymentId}/status`);
        const data = await res.json();

        if (data.status === 'paid') {
            document.getElementById('status').textContent = 'Payment confirmed!';
            document.getElementById('status').classList.add('paid');
            document.getElementById('status-wrap').classList.add('paid');
            setTimeout(() => { window.location.href = '/payment/success'; }, 1200);
        }
    }, 3000);
</script>
@endsection