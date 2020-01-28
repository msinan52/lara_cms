@extends('site.layouts.base')
@section('title','Adreslerim')

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('homeView')}}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Adres Bilgilerim</li>
            </ol>
        </div><!-- End .container -->
    </nav>
    <div class="container">
        @include('site.layouts.partials.messages')
        <div class="row">
            <div class="col-lg-3">
                @include('site.kullanici.partials.myAccountLeftSidebar')
            </div>
            <div class="col-lg-8">
                <ul class="checkout-steps">
                    <li>
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link active {{ request()->get('type',1) == \App\Models\KullaniciAdres::TYPE_DELIVERY ? 'h3' : '' }}"
                                   href="{{route('kullanici.address')}}?type={{\App\Models\KullaniciAdres::TYPE_DELIVERY}}">Teslimat Adreslerim</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->get('type',1) == \App\Models\KullaniciAdres::TYPE_INVOICE ? 'h3' : '' }}"
                                   href="{{route('kullanici.address')}}?type={{\App\Models\KullaniciAdres::TYPE_INVOICE}}">Fatura Adreslerim</a>
                            </li>
                        </ul>
                        <div class="shipping-step-addresses">
                            @if(request()->get('type',1) == 1)
                                @foreach($address as $ad)
                                    <div class="shipping-address-box {{ $userDefaultAddress == $ad->id  ? 'active':'' }}">
                                        <address>
                                            {{$ad->title}} <br>
                                            {{ $ad->adres }}<br>
                                            {{$ad->City->title . ' - '. $ad->Town->title }} <br>
                                            {{$ad->phone}} <br>
                                        </address>
                                        <div class="address-box-action clearfix">
                                            @if($userDefaultAddress !== $ad->id)

                                                <form action="{{route('kullanici.address.setDefault',$ad->id)}}" method="post" id="formSetDefaultPage{{$ad->id}}">

                                                    @csrf
                                                    <a href="javascript:void(0)" onclick='document.getElementById("formSetDefaultPage{{$ad->id}}").submit()'
                                                       class="btn btn-sm btn-outline-secondary float-right">
                                                        Varsayılan Yap
                                                    </a>
                                                </form>
                                            @endif
                                            <a href="{{route('kullanici.address.edit',$ad->id)}}?type={{request()->get('type',1)}}" class="btn-quickview" title="Adres Detay"><i
                                                    class="fas fa-edit"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif(request()->get('type',1) == 2)
                                @foreach($address as $ad)
                                    <div class="shipping-address-box {{ $userDefaultInvoiceAddress == $ad->id  ? 'active':'' }}">
                                        <address>
                                            {{$ad->title}} <br>
                                            {{ $ad->adres }}<br>
                                            {{$ad->City->title . ' - '. $ad->Town->title }} <br>
                                            {{$ad->phone}} <br>
                                        </address>
                                        <div class="address-box-action clearfix">
                                            @if($userDefaultInvoiceAddress !== $ad->id)
                                                <form action="{{route('kullanici.address.setDefaultInvoiceAddress',$ad->id)}}" method="post" id="formSetDefaultInvoicePage{{$ad->id}}">
                                                    @csrf
                                                    <a href="javascript:void(0)" onclick='document.getElementById("formSetDefaultInvoicePage{{$ad->id}}").submit()'
                                                       class="btn btn-sm btn-outline-secondary float-right">
                                                        Varsayılan Yap
                                                    </a>
                                                </form>
                                            @endif
                                            <a href="{{route('kullanici.address.edit',$ad->id)}}?type={{request()->get('type',2)}}" class="btn-quickview" title="Adres Detay"><i
                                                    class="fas fa-edit"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                        </div><!-- End .shipping-step-addresses -->
                        <a href="{{route('kullanici.address.edit',0)}}?type={{request()->get('type',1)}}" class="btn btn-quickview btn-sm btn-outline-secondary btn-new-address"
                           data-toggle="modal"
                           data-target="#addressModal">+
                            Yeni Adres</a>
                    </li>
                </ul>
            </div><!-- End .col-lg-8 -->
        </div><!-- End .row -->
    </div>
@endsection

