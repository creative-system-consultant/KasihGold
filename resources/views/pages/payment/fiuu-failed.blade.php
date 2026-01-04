<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h2>
                <p class="text-gray-600 mb-6">{{ $message ?? 'Your payment could not be processed.' }}</p>
                
                @if(isset($orderid))
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="font-semibold">{{ $orderid }}</span>
                    </div>
                    @if(isset($status))
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status Code:</span>
                        <span class="font-semibold">{{ $status }}</span>
                    </div>
                    @endif
                </div>
                @endif
                
                <div class="space-y-3">
                    <a href="{{ route('cart') }}" class="inline-block w-full bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                        Try Again
                    </a>
                    <a href="{{ route('home') }}" class="inline-block w-full bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-300 transition duration-200">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Fiuu IPN Iframe (Required even for failed transactions) -->
    @if(isset($merchantId) && isset($ipnUrl))
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
    @endif
</body>
</html>