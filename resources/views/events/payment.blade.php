@extends('layouts.app')

@section('title', 'Complete Payment - ' . $event->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Payment Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">Complete Payment</h1>

                    <!-- Tabs -->
                    <div class="flex border-b mb-6">
                        <button class="payment-tab active py-2 px-4 font-semibold border-b-2 border-blue-600 text-blue-600"
                            data-tab="stripe">
                            Credit Card (Stripe)
                        </button>
                        <button class="payment-tab py-2 px-4 font-semibold text-gray-600 hover:text-gray-900"
                            data-tab="sslcommerz">
                            SSLCommerz (bKash, Nagad)
                        </button>
                    </div>

                    <!-- Stripe Form -->
                    <form id="stripeForm" class="payment-form" action="{{ route('registrations.payment.callback', $registration) }}" method="POST">
                        @csrf
                        <input type="hidden" name="provider" value="stripe">

                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">Card Holder Name</label>
                            <input type="text" name="card_holder" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">Card Number</label>
                            <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Expiry Date</label>
                                <input type="text" name="expiry" placeholder="MM/YY" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">CVV</label>
                                <input type="text" name="cvv" placeholder="123" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Pay ৳{{ number_format($event->price, 2) }}
                        </button>
                    </form>

                    <!-- SSLCommerz Form -->
                    <form id="sslcommerzForm" class="payment-form hidden" action="{{ route('registrations.payment.callback', $registration) }}" method="POST">
                        @csrf
                        <input type="hidden" name="provider" value="sslcommerz">

                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">Phone Number</label>
                            <input type="tel" name="phone" placeholder="01xxxxxxxxx" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600">
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">Select Payment Method</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="bkash" class="mr-2" required>
                                    <span class="text-gray-700">bKash</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="nagad" class="mr-2" required>
                                    <span class="text-gray-700">Nagad</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="rocket" class="mr-2" required>
                                    <span class="text-gray-700">Rocket</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Proceed to {{ ucfirst('Payment') }}
                        </button>
                    </form>

                    <!-- Security Notice -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm text-blue-700"><strong>Your payment is secure.</strong> We use industry-standard SSL encryption to protect your information.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h3>

                    <!-- Event Details -->
                    <div class="mb-6 pb-6 border-b">
                        <p class="text-sm text-gray-600">EVENT</p>
                        <p class="font-semibold text-gray-900">{{ $event->title }}</p>
                        @if($event->start_datetime)
                        <p class="text-sm text-gray-600 mt-1">{{ $event->start_datetime->format('M d, Y') }}</p>
                        @endif
                    </div>

                    <!-- Registration Code -->
                    <div class="mb-6 pb-6 border-b bg-gray-50 p-3 rounded">
                        <p class="text-xs text-gray-600 uppercase tracking-wide">Registration Code</p>
                        <code class="font-mono font-bold text-gray-900 text-lg">{{ $registration->registration_code }}</code>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-2 mb-6 pb-6 border-b">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Event Fee</span>
                            <span class="font-semibold text-gray-900">৳{{ number_format($event->price, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Processing Fee</span>
                            @php $fee = $event->price * 0.05; @endphp
                            <span class="font-semibold text-gray-900">৳{{ number_format($fee, 2) }}</span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between mb-6">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        @php $total = $event->price + ($event->price * 0.05); @endphp
                        <span class="text-2xl font-bold text-blue-600">৳{{ number_format($total, 2) }}</span>
                    </div>

                    <!-- Info -->
                    <div class="bg-blue-50 p-3 rounded text-sm text-blue-700">
                        <p class="mb-2"><strong>After payment:</strong></p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>You'll receive a confirmation email</li>
                            <li>Your QR ticket will be generated</li>
                            <li>You can print or screenshot your ticket</li>
                        </ul>
                    </div>

                    <!-- Back Link -->
                    <a href="{{ route('events.show', $event) }}" class="block mt-4 text-center text-gray-600 hover:text-gray-900 text-sm">
                        Back to Event
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.payment-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        
        // Update active tab
        document.querySelectorAll('.payment-tab').forEach(t => {
            t.classList.remove('active', 'border-b-2', 'border-blue-600', 'text-blue-600');
            t.classList.add('text-gray-600', 'hover:text-gray-900');
        });
        this.classList.add('active', 'border-b-2', 'border-blue-600', 'text-blue-600');
        this.classList.remove('text-gray-600', 'hover:text-gray-900');

        // Update forms
        document.querySelectorAll('.payment-form').forEach(form => form.classList.add('hidden'));
        document.getElementById(tabName + 'Form').classList.remove('hidden');
    });
});
</script>
@endsection
