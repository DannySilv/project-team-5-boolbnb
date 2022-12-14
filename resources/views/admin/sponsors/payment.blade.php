@extends('layouts.dashboard')

@section('content')
  @if (session('success_message'))
    <div class="alert alert-success">
      {{ session('success_message') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  <div class="container">
    <h1>Riepilogo Ordine:</h1>
    <h4>Sponsor per l'alloggio: </h4>
    <span>{{$accomodation->name}}</span>
    <h4>Sponsor Level: </h4>
    <span>{{$sponsor->name}}</span>
    <form method="post" id="payment-form" action="{{ route('admin.sponsors.checkout', [$accomodation, $sponsor]) }}">
      @csrf
      @method('post')
      <section>
        <label for="amount">
          <h4>Prezzo: </h4>
          <div class="input-wrapper amount-wrapper">
            <input id="amount" name="amount" type="tel" placeholder="importo"
                  value="{{$sponsor->price}}" readonly>
          </div>
        </label>

        <div class="bt-drop-in-wrapper">
          <div id="bt-dropin"></div>
        </div>
      </section>

      <input id="nonce" name="payment_method_nonce" type="hidden" />
      <button class="button" type="submit"><span>Test Transaction</span></button>
    </form>
  </div>

  <script src="https://js.braintreegateway.com/web/dropin/1.33.4/js/dropin.min.js"></script>
  <script>
    let form = document.querySelector('#payment-form');
    let client_token = '{{ $token }}';
    braintree.dropin.create({
      authorization: client_token,
      selector: '#bt-dropin',
      paypal: {
          flow: 'vault'
      }
    }, function(createErr, instance) {
      if (createErr) {
        console.log('Create Error', createErr);
        return;
      }
      form.addEventListener('submit', function(event) {
        event.preventDefault();
        instance.requestPaymentMethod(function(err, payload) {
          if (err) {
            console.log('Request Payment Method Error', err);
            return;
          }
          document.querySelector('#nonce').value = payload.nonce;
          form.submit();
          });
        });
      });
  </script>
@endsection