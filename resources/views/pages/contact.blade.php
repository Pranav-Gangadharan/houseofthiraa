@extends('layouts.shop')

@section('title', 'Contact | House of Thiraa')

@section('content')
    <div class="wrap page">

        <section class="story">
            <div>
                <h1 class="display">Say hello.</h1>
                <p class="lede">Sizing, an order, or a piece you can't decide on? Message us and a real person will answer.</p>
            </div>
        </section>

        @if($channels)
            <section class="channels" aria-label="Ways to reach us">
                @foreach($channels as $channel)
                    <a class="channel" href="{{ $channel['href'] }}" @unless($dummy || str_starts_with($channel['href'], 'mailto:')) target="_blank" rel="noopener" @endunless>
                        <div class="frame">
                            <div class="channel-body">
                                <span class="channel-label">{{ $channel['label'] }}</span>
                                <span class="channel-value">{{ $channel['value'] }}</span>
                                <span class="link">{{ $channel['cta'] }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </section>

            @if($dummy)
                <p class="quiet" style="margin-top:.9rem">Sample details. Add SHOP_WHATSAPP, SHOP_INSTAGRAM and SHOP_EMAIL to .env to show your own.</p>
            @endif
        @else
            <p class="quiet">Contact details will be added here soon.</p>
        @endif

        <div class="contact-split">
            <section class="panel" aria-label="Hours">
                <h2 class="sub-title">When we reply</h2>
                <dl class="hours">
                    <div><dt>Monday to Saturday</dt><dd>10 am to 6 pm</dd></div>
                    <div><dt>Sunday</dt><dd>Closed</dd></div>
                </dl>
                <p class="quiet" style="margin-top:1rem">Writing about an order? Include your order number, like HT-7K3QX9, so we can find it quickly.</p>
            </section>

            <section aria-label="Common questions">
                <h2 class="sub-title">Quick answers</h2>
                <div class="faq">
                    <details>
                        <summary>Where is my order?</summary>
                        <p>Once your order ships, the tracking number appears on your order page. Open the link you saw after paying.</p>
                    </details>
                    <details>
                        <summary>Do you ship across India?</summary>
                        <p>Yes. Shipping is free on every order, anywhere in India.</p>
                    </details>
                    <details>
                        <summary>Can I pay on delivery?</summary>
                        <p>No. We take online payment only: UPI, cards and netbanking.</p>
                    </details>
                    <details>
                        <summary>Can I return or exchange a piece?</summary>
                        <p>We don't accept returns or exchanges, so please check the size guide before you order. <a href="{{ route('policy') }}" class="link">Read the policy</a></p>
                    </details>
                </div>
            </section>
        </div>

    </div>
@endsection
