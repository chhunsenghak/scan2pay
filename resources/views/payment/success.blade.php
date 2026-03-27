@extends('layouts.payment')

@section('title', 'Success')
@section('body_class', 'success')

@section('content')
<div class="payment-card success">
    <div class="check-wrap">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4caf7d" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>

    <div class="eyebrow success">Confirmed</div>
    <h2 class="payment-title">Payment Successful</h2>
    <p class="payment-subtitle">Your transaction has been processed<br>and confirmed securely.</p>

    <div class="divider"></div>

    <a href="/" class="pill-btn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Make another payment
    </a>

    <p class="footer-note">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        Encrypted &amp; secure transaction
    </p>
</div>
@endsection