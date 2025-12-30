{{-- <!DOCTYPE html>
<html>
    <head>
        <title>Redirecting to Fiuu Payment Gateway...</title>
    </head>
    <body>
        <div style="text-align: center; padding: 50px;">
            <h2>Redirecting to payment gateway...</h2>
            <p>Please wait while we redirect you to the secure payment page.</p>
        </div>

        <form id="fiuu-payment-form" method="POST" action="{{ config('fiuu.payment_url') . config('fiuu.merchant_id') }}">
            @foreach($paymentData as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>

        <script>
            document.getElementById('fiuu-payment-form').submit();
        </script>
    </body>
</html> --}}

<livewire:page.payment.redirect-fiuu :paymentData="$paymentData"/>