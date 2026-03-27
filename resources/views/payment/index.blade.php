@extends('layouts.payment')
@section('title', 'Pay')
@section('content')

<div class="payment-card">
    <div class="eyebrow">Secure Payment</div>
    <h2 class="payment-title">Pay Now</h2>
    <p class="payment-subtitle">Choose or enter an amount to pay</p>

    <div class="currency-toggle" id="currency-toggle">
        <button type="button" class="active" data-currency="USD">USD ($)</button>
        <button type="button" data-currency="KHR">KHR (៛)</button>
    </div>
    <form method="POST" action="/pay" id="payment-form">
        @csrf
        <input type="hidden" name="currency" id="currency-input" value="USD">
        
        <div id="error-msg" class="error-msg">Please select or enter an amount.</div>

        <div class="amount-grid" id="amount-grid">
            <!-- Suggested amounts will be injected here -->
        </div>

        <div class="custom-amount-wrap" id="custom-amount-wrap">
            <div class="amount-input-wrap">
                <span class="currency-symbol" id="currency-symbol">$</span>
                <input
                    type="number"
                    name="amount"
                    id="amount-input"
                    placeholder="0.00"
                    step="0.01"
                    min="0"
                    class="amount-input"
                >
            </div>
        </div>

        <button type="submit" class="submit-btn" style="margin-top: 24px;">
            <span class="submit-btn-inner">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="3" height="3" rx="0.5"/>
                    <rect x="18" y="14" width="3" height="3" rx="0.5"/>
                    <rect x="14" y="18" width="3" height="3" rx="0.5"/>
                    <rect x="18" y="18" width="3" height="3" rx="0.5"/>
                </svg>
                Generate QR Code
            </span>
        </button>
    </form>

    <p class="footer-note">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        Encrypted &amp; secure transaction
    </p>
</div>

@endsection

@section('scripts')
<script>
    const suggestions = {
        USD: [1, 5, 10, 20, 50, 'Custom'],
        KHR: [4000, 10000, 20000, 40000, 100000, 'Custom']
    };

    const currencyToggle = document.getElementById('currency-toggle');
    const currencyInput  = document.getElementById('currency-input');
    const currencySymbol = document.getElementById('currency-symbol');
    const amountGrid     = document.getElementById('amount-grid');
    const amountInput    = document.getElementById('amount-input');
    const customWrap     = document.getElementById('custom-amount-wrap');
    const form           = document.getElementById('payment-form');
    const errorMsg       = document.getElementById('error-msg');

    function updateGrid(currency) {
        amountGrid.innerHTML = '';
        suggestions[currency].forEach(val => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'amount-btn';
            
            if (val === 'Custom') {
                btn.innerHTML = `<span style="font-size: 14px; opacity: 0.8;">${val}</span>`;
            } else {
                btn.innerHTML = `
                    <span class="btn-currency">${currency}</span>
                    <span class="btn-value">${val.toLocaleString()}</span>
                `;
            }
            
            btn.onclick = () => selectAmount(val, btn);
            amountGrid.appendChild(btn);
        });
    }

    function selectAmount(val, btn) {
        errorMsg.classList.remove('visible');
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        if (val === 'Custom') {
            customWrap.classList.add('visible');
            amountInput.value = '';
            amountInput.focus();
            amountInput.required = true;
        } else {
            customWrap.classList.remove('visible');
            amountInput.value = val;
            amountInput.required = false;
        }
    }

    currencyToggle.querySelectorAll('button').forEach(btn => {
        btn.onclick = () => {
            errorMsg.classList.remove('visible');
            currencyToggle.querySelectorAll('button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const currency = btn.dataset.currency;
            currencyInput.value = currency;
            currencySymbol.textContent = currency === 'USD' ? '$' : '៛';
            amountInput.placeholder = currency === 'USD' ? '0.00' : '0';
            
            customWrap.classList.remove('visible');
            amountInput.value = '';

            updateGrid(currency);
        };
    });

    amountInput.oninput = () => {
        errorMsg.classList.remove('visible');
    };

    // Initial grid
    updateGrid('USD');

    form.onsubmit = (e) => {
        if (!amountInput.value || amountInput.value <= 0) {
            errorMsg.classList.add('visible');
            e.preventDefault();
        }
    };
</script>
@endsection