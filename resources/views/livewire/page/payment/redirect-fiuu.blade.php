<!DOCTYPE html>
<html>
    <head>
        <title>Redirecting to Fiuu Payment Gateway...</title>
    </head>
    <body>
        <div style="text-align: center; padding: 50px;">
            <h2>Redirecting to payment gateway...</h2>
            <p>Please wait while we redirect you to the secure payment page.</p>
        </div>

            <!-- Add this temporarily to debug -->
        {{-- <div style="padding: 20px; background: #f0f0f0;">
            <h3>Debug Info:</h3>
            <pre>{{ print_r($paymentData, true) }}</pre>
        </div> --}}

        <form id="fiuu-payment-form" method="POST" action="{{ config('fiuu.payment_url') . config('fiuu.merchantId') . '/'}}">
            @foreach($paymentData as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                <!-- Show form fields for debugging -->
                {{-- <div>{{ $key }}: {{ $value }}</div> --}}
            @endforeach
        </form>

        <script>
            document.getElementById('fiuu-payment-form').submit();
        </script>
    </body>
</html>