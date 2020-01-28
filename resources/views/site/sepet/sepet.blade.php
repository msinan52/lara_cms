@extends('site.layouts.base')
@section('title','Sepetim')
@section('header')
    <meta name="csrf-token" content="{{csrf_token()}}">
@endsection
@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('homeView')}}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Sepet</li>
            </ol>
        </div><!-- End .container -->
    </nav>
    <div class="container">
        @include('site.layouts.partials.messages')
        <div class="row">
            <div class="col-lg-9">
                <div class="cart-table-container">
                    <table class="table table-cart">
                        <thead>
                        <tr>
                            <th class="product-col">Ürün</th>
                            <th class="">Özellikler</th>
                            <th class="price-col">Fiyat</th>
                            <th class="qty-col">Adet</th>
                            <th>Toplam</th>
                        </tr>
                        </thead>
                        <tbody id="sepetItemsContainer">
                        @if(count(Cart::content()) > 0)
                            @foreach(Cart::content() as $cartItem)
                                <tr class="product-row basketCartItem" data-value="{{$cartItem->rowId}}">
                                    <input type="hidden" value="{{$cartItem->rowId}}" data-type="cartItemRow">
                                    <td class="product-col">
                                        <figure class="product-image-container">
                                            <a href="{{ route('productDetail',$cartItem->options->slug) }}" class="product-image">
                                                <img class="cartItem" src="{{ config('constants.image_paths.product_image_folder_path').''.$cartItem->options->image }}"
                                                     alt="{{$cartItem->title}}" width="180" height="160">
                                            </a>
                                        </figure>
                                        <h2 class="product-title">
                                            <a href="{{ route('productDetail',$cartItem->options->slug) }}">{{$cartItem->name}}</a>
                                        </h2>
                                    </td>
                                    <td>{{ $cartItem->options->attributeText }}</td>
                                    <td>
                                        @if($cartItem->options->old_price)
                                            <span class="old-price">{{$cartItem->options->old_price}} ₺</span>
                                            <span class="product-price"><span class="price">{{$cartItem->price}}</span> ₺</span>
                                        @else
                                            <span class="product-price"><span class="price">{{$cartItem->price}}</span> ₺</span>
                                        @endif

                                    </td>
                                    <td>
                                        <input class="vertical-quantity form-control" type="text" id="{{$cartItem->rowId}}" value="{{$cartItem->qty}}">
                                    </td>
                                    <td><span class="itemTotalPrice">{{ $cartItem->price * $cartItem->qty }}</span> ₺</td>
                                </tr>
                                <tr class="product-action-row">
                                    <td colspan="5" class="clearfix">
                                        <div class="float-left">
                                            <a href="javascript:void(0);" onclick="return addToFavorites({{$cartItem->id}})" class="btn-move">Favorilere Ekle</a>
                                        </div><!-- End .float-left -->

                                        <div class="float-right">
                                            <form action="{{ route('basket.remove',$cartItem->rowId) }}" id="deleteForm{{$cartItem->id}}" method="post" class="mb-0">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <a href="javascript:void(0);" onclick="document.getElementById('deleteForm{{$cartItem->id}}').submit()" title="Sepetten Kaldır"
                                                   class="btn-remove"><span
                                                        class="sr-only">Kaldır</span></a>
                                            </form>

                                        </div><!-- End .float-right -->
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center"><h4>Henüz sepette ürün yok</h4></td>
                            </tr>
                        @endif

                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="5" class="clearfix">
                                <div class="float-left">
                                    <a href="{{route('homeView')}}" class="btn btn-outline-secondary">Alışverişe Devam Et</a>
                                </div><!-- End .float-left -->

                                <div class="float-right">
                                    <form action="{{ route('basket.removeAllItems') }}" id="formRemoveAllItems" method="post" class="mb-0">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                    </form>
                                    @if(Cart::count() > 0)
                                        <a href="javascript:void(0);" onclick="document.getElementById('formRemoveAllItems').submit()"
                                           class="btn btn-outline-secondary btn-clear-cart">Sepeti Temizle</a>
                                        <a href="javascript:void(0)" onclick="return updateBasket()" class="btn btn-outline-secondary btn-update-cart">Sepeti Güncelle</a>
                                    @endif

                                </div><!-- End .float-right -->
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div><!-- End .cart-table-container -->


            </div><!-- End .col-lg-8 -->

            <div class="col-lg-3">
                @if(is_null($basketCoupon))
                    <div class="cart-discount">
                        <h4>Kupon Kodu Uygula</h4>
                        <form action="{{ route('coupon.apply') }}" method="post">
                            @csrf
                            <div class="input-group">
                                @if(auth()->check())
                                    <input type="text" class="form-control form-control-sm" value="{{ old('code') }}" placeholder="Kupon Kodunu Giriniz" required name="code">
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-primary" type="submit">Uygula</button>
                                    </div>
                                @else
                                    <div>
                                        <a href="{{ route('kullaniciLoginView') }}" class="btn btn-primary text-white">Kupon Girmek için Giriş yapınız</a>
                                    </div>
                                @endif
                            </div><!-- End .input-group -->
                        </form>
                    </div>
                @endif
                <div class="cart-summary">
                    <h3>Sepet Özeti</h3>
                    <table class="table table-totals">
                        <tbody>
                        <tr>
                            <td>Ara Toplam</td>
                            <td>₺<span class="cartSubTotal">{{Cart::subTotal()}}</span></td>
                        </tr>
                        <tr>
                            <td>Kargo</td>
                            <td>₺<span>{{$cargoPrice}}</span></td>
                        </tr>
                        @if(!is_null($basketCoupon))
                            <tr>
                                <td>Kupon - {{$basketCoupon->code}}</td>
                                <td style="color: green">- ₺<span class="text-green bg-green">{{$basketCoupon->discount_price}}</span></td>
                            </tr>
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>Genel Toplam</td>
                            <td>₺<span class="cartTotal">{{$totalPrice}}</span></td>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="checkout-methods">
                        @if(Cart::count() > 0)
                            <a href="{{route('odemeView')}}" class="btn btn-block btn-sm btn-primary">Ödeme Yap</a>
                        @endif
                    </div><!-- End .checkout-methods -->
                </div><!-- End .cart-summary -->
            </div><!-- End .col-lg-4 -->
        </div><!-- End .row -->
    </div><!-- End .container -->
    <div class="mb-6"></div>
@endsection

@section('footer')
    <script src="/js/basketPage.js"></script>
@endsection
