@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/buy.css') }}">
@endsection

@section('content')
    <div class="buy-container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <h1>商品購入</h1>
        <div class="content-container">
            <div class="left-container">
                <div class="item-image">
                    <img src="{{ asset('storage/' . $item->image_url) }}" alt="Item Image" class="item-image-element">
                </div>
                <div class="item-info">
                    <h2>商品名: {{ $item->name }}</h2>
                    <p>価格: ￥{{ $item->price }}</p>
                </div>
                <div class="payment-method">
                    <div class="form-group">
                        <label for="payment_method">支払い方法を選択してください</label>
                        <select name="payment_method" id="payment_method" class="form-control" onchange="updatePaymentMethod()">
                            <option value="" disabled selected>支払い方法を選択してください</option>
                            <option value="credit_card">クレジットカード</option>
                            <option value="convenience_store">コンビニ</option>
                            <option value="bank_transfer">銀行振込</option>
                        </select>
                        @error('payment_method')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="shipping-address">
                    <div class="form-group">
                        <label for="shipping_address">配送先を選択してください</label>
                        <select name="shipping_address" id="shipping_address" class="form-control" onchange="updateShippingAddress()">
                            <option value="{{ $profile['post_code'] . ' ' . $profile['address'] . ' ' . $profile['building'] }}">
                                郵便番号: {{ $profile['post_code'] }} - 住所: {{ $profile['address'] }} - 建物名: {{ $profile['building'] }}
                            </option>
                            @foreach($shippingAddresses as $shipping)
                                <option value="{{ $shipping->post_code . ' ' . $shipping->address . ' ' . $shipping->building }}">
                                    郵便番号: {{ $shipping->post_code }} - 住所: {{ $shipping->address }} - 建物名: {{ $shipping->building }}
                                </option>
                            @endforeach
                        </select>
                        @error('shipping_address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <form action="{{ route('shipping.address.show', $item->id) }}" method="GET">
                        @csrf
                        <button type="submit" class="btn btn-secondary">配送先を追加する</button>
                    </form>
                </div>
            </div>
            <div class="confirmation-container">
                <h2 class="form-list">確認</h2>
                <p>商品代金: ￥{{ $item->price }}</p>
                <p>選択された支払い方法: <br>
                    <span id="selected_payment_method"></span>
                </p>
                <p>選択された配送先:</p>
                <p>〒: <span id="selected_shipping_postcode"></span></p>
                <p>住所: <span id="selected_shipping_address"></span></p>
                <p>建物名: <span id="selected_shipping_building"></span></p>
                <p>合計金額: ￥{{ $item->price }}</p>
                <form method="POST" action="{{ route('items.purchase', $item->id) }}" id="purchase-form">
                    @csrf
                    <input type="hidden" name="payment_method" id="confirmation_payment_method">
                    <input type="hidden" name="shipping_address" id="confirmation_shipping_address">
                </form>
                <div class="container mt-5">
                    @if (session('success_message'))
                        <div class="alert alert-success">
                            {{ session('success_message') }}
                        </div>
                    @endif
                    @if (session('error_message'))
                        <div class="alert alert-danger">
                            {{ session('error_message') }}
                        </div>
                    @endif
                    <form action="{{ route('charge') }}" class="credit-card-form" method="post" id="payment-form">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <div class="form-row">
                            <div id="card-errors" role="alert"></div>
                        </div>
                        <div id="card-element">
                            <label for="card-number">カード番号</label>
                            <div id="card-number-element" class="form-control"></div>
                            <div id="card-expiry-element" class="form-control"></div>
                            <div id="card-cvc-element" class="form-control"></div>
                            <div id="postal-code-element" class="form-control"></div>
                            <!-- エラーメッセージ表示 -->
                            <div id="card-errors" role="alert"></div>
                        </div>
                        <button class="btn btn-primary mt-3" id="credit-card-button">購入する</button>
                    </form>
                    <button id="konbini-button" class="btn btn-primary mt-3">購入する</button>
                    <button id="bank-transfer-button" class="btn btn-primary mt-3">購入する</button>
                    <div id="payment-message" class="alert alert-info" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        function updatePaymentMethod() {
            var paymentMethod = document.getElementById('payment_method').value;
            var paymentMethodName = '';

            switch(paymentMethod) {
                case 'credit_card':
                    paymentMethodName = 'クレジットカード';
                    break;
                case 'convenience_store':
                    paymentMethodName = 'コンビニ';
                    break;
                case 'bank_transfer':
                    paymentMethodName = '銀行振込';
                    break;
                default:
                    paymentMethodName = '未選択';
                    break;
            }

            document.getElementById('selected_payment_method').innerText = paymentMethodName;
            document.getElementById('confirmation_payment_method').value = paymentMethod;
        }

        function updateShippingAddress() {
            var shippingAddress = document.getElementById('shipping_address').value.split(' ');
            document.getElementById('selected_shipping_postcode').innerText = shippingAddress[0];
            document.getElementById('selected_shipping_address').innerText = shippingAddress[1];
            document.getElementById('selected_shipping_building').innerText = shippingAddress[2];
            document.getElementById('confirmation_shipping_address').value = shippingAddress.join(' ');
        }

        document.addEventListener('DOMContentLoaded', function() {
            updatePaymentMethod();
            updateShippingAddress();
        });

        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();

        var style = {
            base: {
                fontSize: '16px',
                color: '#32325d',
                padding: '10px 12px' // パディングを追加
            },
        };

        var cardNumberElement = elements.create('cardNumber', {
            style: style,
        });
        cardNumberElement.mount('#card-number-element');

        var cardExpiryElement = elements.create('cardExpiry', {
            style: style,
        });
        cardExpiryElement.mount('#card-expiry-element');

        var cardCvcElement = elements.create('cardCvc', {
            style: style,
        });
        cardCvcElement.mount('#card-cvc-element');

        var postalCodeElement = elements.create('postalCode', {
            style: style,
        });
        postalCodeElement.mount('#postal-code-element');

        var displayError = document.getElementById('card-errors');
        cardNumberElement.on('change', function(event) {
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            stripe.createToken(cardNumberElement, {
                address_zip: postalCodeElement.value,
            }).then(function(result) {
                if (result.error) {
                    displayError.textContent = result.error.message;
                } else {
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);
            form.submit();
        }

        document.getElementById('konbini-button').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('purchase-form').submit();
        });

        document.getElementById('bank-transfer-button').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('purchase-form').submit();
        });
    </script>
@endsection
