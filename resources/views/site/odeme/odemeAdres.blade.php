@extends('site.layouts.base')
@section('title','Sepetim')

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index-2.html"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </div><!-- End .container -->
    </nav>
    <div class="container">
        @include('site.layouts.partials.messages')
        <ul class="checkout-progress-bar">
            <li class="">
                <span>Adres Bilgileri</span>
            </li>
            <li>
                <span>Ödeme</span>
            </li>
        </ul>
        <div class="row">
            <div class="col-lg-8">
                <ul class="checkout-steps">
                    <li>
                        <h2 class="step-title">Teslimat Adreslerim</h2>
                        <div class="shipping-step-addresses">
                            @foreach($address as $ad)
                                <div class="shipping-address-box {{ $userDefaultAddress == $ad->id ? 'active':'' }}">
                                    <address>
                                        {{$ad->title}} <br>
                                        {{ $ad->adres }}<br>
                                        {{$ad->City->title . ' - '. $ad->Town->title }} <br>
                                        {{$ad->phone}} <br>
                                    </address>

                                    <div class="address-box-action clearfix">
                                        @if($userDefaultAddress !== $ad->id)

                                            <form action="{{route('odeme.address.setDefault',$ad->id)}}" method="post" id="formSetDefaultPage{{$ad->id}}">

                                                @csrf
                                                <a href="javascript:void(0)" onclick='document.getElementById("formSetDefaultPage{{$ad->id}}").submit()'
                                                   class="btn btn-sm btn-outline-secondary float-right">
                                                    Varsayılan Yap
                                                </a>
                                            </form>
                                        @endif
                                        <a href="{{route('kullanici.address.editWithRedirect',[$ad->id,"odeme.adres"])}}" class="btn-quickview" title="Adres Detay"><i
                                                class="fas fa-edit"></i></a>
                                    </div><!-- End .address-box-action -->
                                </div><!-- End .shipping-address-box -->
                            @endforeach
                        </div><!-- End .shipping-step-addresses -->
                        <a href="{{route('kullanici.address.editWithRedirect',[0,"odeme.adres"])}}" class="btn btn-quickview btn-sm btn-outline-secondary btn-new-address"
                           data-toggle="modal"
                           data-target="#addressModal">+
                            Yeni Adres</a>
                    </li>
                </ul>
                <ul class="checkout-steps">
                    <li>
                        <h2 class="step-title">Fatura Adreslerim</h2>
                        <div class="shipping-step-addresses">
                            @foreach($invoiceAddress as $adr)
                                <div class="shipping-address-box {{ $userDefaultInvoiceAddress == $adr->id  ? 'active':'' }}">
                                    <address>
                                        {{$adr->title}} <br>
                                        {{ $adr->adres }}<br>
                                        {{$adr->City->title . ' - '. $adr->Town->title }} <br>
                                        {{$adr->phone}} <br>
                                    </address>
                                    <div class="address-box-action clearfix">
                                        @if($userDefaultInvoiceAddress !== $adr->id)
                                            <form action="{{route('kullanici.address.setDefaultInvoiceAddressWithRedirect',[$adr->id,'odeme.adres'])}}" method="post"
                                                  id="formSetDefaultInvoicePage{{$adr->id}}">
                                                @csrf
                                                <a href="javascript:void(0)" onclick='document.getElementById("formSetDefaultInvoicePage{{$adr->id}}").submit()'
                                                   class="btn btn-sm btn-outline-secondary float-right">
                                                    Varsayılan Yap
                                                </a>
                                            </form>
                                        @endif
                                        <a href="{{route('kullanici.address.editWithRedirect',[$adr->id,"odeme.adres"])}}?type=2" class="btn-quickview" title="Adres Detay"><i
                                                class="fas fa-edit"></i></a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{route('kullanici.address.editWithRedirect',[0,"odeme.adres"])}}" class="btn btn-quickview btn-sm btn-outline-secondary btn-new-address"
                           data-toggle="modal"
                           data-target="#addressModal">+
                            Yeni Adres</a>
                    </li>
                </ul>
            </div><!-- End .col-lg-8 -->

            <div class="col-lg-4">
                @include('site.sepet.partials.summaryCard')
            </div><!-- End .col-lg-4 -->
        </div><!-- End .row -->

        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-steps-action">
                    <a href="{{route('odemeView')}}" class="btn btn-primary float-right">Ödeme</a>
                </div><!-- End .checkout-steps-action -->
            </div><!-- End .col-lg-8 -->
        </div><!-- End .row -->
    </div><!-- End .container -->

    <div class="mb-6"></div><!-- margin -->
@endsection
