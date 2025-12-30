<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h2>
                <p class="text-gray-600 mb-6">Your payment has been processed successfully.</p>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="font-semibold">{{ $orderid }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Transaction ID:</span>
                        <span class="font-semibold">{{ $tranID }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-semibold">RM {{ number_format($amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-semibold">{{ strtoupper($channel) }}</span>
                    </div>
                </div>
                
                <a href="{{ route('dashboard') }}" class="inline-block w-full bg-green-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-green-700 transition duration-200">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Fiuu IPN Iframe (Required by Fiuu documentation) -->
    <script type='text/javascript'>
        var sa = 'VFmerchantId{{ $merchantId }}';
        window.onload = function() {
            m = document.createElement('IFRAME');
            m.setAttribute('src', "{{ $ipnUrl }}?treq=0&sa=" + sa);
            m.setAttribute('seamless', 'seamless');
            m.setAttribute('width', 0);
            m.setAttribute('height', 0);
            m.setAttribute('frameborder', 0);
            m.setAttribute('scrolling', 'no');
            m.setAttribute('style', 'border:none !important;');
            document.body.appendChild(m);
        };
    </script>
</body>
</html>