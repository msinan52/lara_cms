@extends('site.layouts.base')
@section('title',$site->title)
@section('header')

    <title>{{ $site->title }}</title>
    <meta name="description" content="{{ $site->spot }}"/>
    <meta name="keywords" content="{{ $site->keywords }}"/>
    <meta property="og:type" content="website "/>
    <meta property="og:url" content="{{ $site->domain }}"/>
    <meta property="og:title" content="{{ $site->title }}"/>
    <meta property="og:image" content="{{ $site->domain.'/logo.png'}}"/>
    <meta name="twitter:card" content="website"/>
    <meta name="twitter:site" content="@siteadi"/>
    <meta name="twitter:creator" content="@siteadi"/>
    <meta name="twitter:title" content="{{ $site->title }}"/>
    <meta name="twitter:description" content="{{ $site->spot }}"/>
    <meta name="twitter:image:src" content="{{ $site->domain.'/logo.png'}}"/>
    <meta name="twitter:domain" content="{{$site->domain}}"/>
    <link rel="canonical" href="{{ $site->domain }}"/>

@endsection
@section('content')

    <main class="main">
        @include('site.layouts.partials.messages')
        <div class="mb-2"></div><!-- margin -->

        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="home-slider owl-carousel owl-carousel-lazy owl-theme owl-theme-light">
                        @foreach($banners as $b)
                            <div class="home-slide">
                                <div class="owl-lazy slide-bg" data-src="{{config('constants.image_paths.banner_image_folder_path').$b->image}}"></div>
                                <div class="home-slide-content text-white">
                                    {!! $b->title !!}
                                    <p>{!! $b->sub_title !!}.</p>
                                    @if($b->link)
                                        <a href="{{ $b->link }}" class="btn btn-dark">İncele</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div><!-- End .home-slider -->
                    <div class="mb-3"></div><!-- margin -->
                    <div class="info-boxes-container">
                        <div class="container">
                            <div class="info-box">
                                <i class="fa fa-credit-card"></i>

                                <div class="info-box-content">
                                    <h4>Güvenli Ödeme Sistemi</h4>
                                    <p>Kredi kartı bilgileriniz sistemde kaydedilmez</p>
                                </div><!-- End .info-box-content -->
                            </div><!-- End .info-box -->

                            <div class="info-box">
                                <i class="fa fa-users"></i>

                                <div class="info-box-content">
                                    <h4>Mutlu Müşteriler</h4>
                                    <p>Yüksek Müşteri Memnuniyeti</p>
                                </div><!-- End .info-box-content -->
                            </div><!-- End .info-box -->

                            <div class="info-box">
                                <i class="icon-support"></i>

                                <div class="info-box-content">
                                    <h4> 7/24 Destek</h4>
                                    <p>Takıldığınız her konuda yanınızdayız</p>
                                </div><!-- End .info-box-content -->
                            </div><!-- End .info-box -->
                        </div><!-- End .container -->
                    </div><!-- End .info-boxes-container -->
                    <h2 class="carousel-title">Çok Satanlar</h2>

                    <div class="home-featured-products owl-carousel owl-theme owl-dots-top">
                        @foreach($bestSellers as $bs)
                            <div class="product-default">
                                <figure>
                                    <a href="{{route('productDetail',$bs->slug)}}">
                                        <img src="{{config('constants.image_paths.product270x250_folder_path').''.$bs->image}}" style="height: 240px;width: 380px">
                                    </a>
                                </figure>
                                <div class="product-details">
                                    <h2 class="product-title">
                                        <a href="{{route('productDetail',$bs->slug)}}" title="{{ $bs->title }}">{{str_limit($bs->title,25)}}</a>
                                    </h2>
                                    <div class="price-box">
                                        @if($bs->discount_price)
                                            <span class="old-price" title="ürün fiyatı">  {{$bs->price}} ₺</span>
                                            <span class="product-price">  {{$bs->discount_price}} ₺</span>
                                        @else
                                            <span class="product-price" title="ürün fiyatı">  {{$bs->price}} ₺</span>
                                        @endif
                                    </div><!-- End .price-box -->
                                    <div class="product-action">
                                        <a href="javascript:void(0);" class="btn-icon-wish"><i class="icon-heart" onclick="return addToFavorites({{$bs->id}})"></i></a>
                                        <a class="btn-icon btn-add-cart" data-toggle="modal" data-target="#addCartModal" href="javascript:void(0)"
                                           onclick="return addItemToBasket({{$bs->id}},{{is_array($bs->detail) ? (count($bs->detail)> 0 ? 1 : 0) : !is_null($bs->detail) ? 1 : 0 }})"><i
                                                class="icon-bag"></i>Sepete Ekle</a>
                                        <a href="{{route('product.quickView',$bs->slug)}}" class="btn-quickview" title="Önizleme" id="productQuickView{{$bs->id}}"><i
                                                class="fas fa-external-link-alt"></i></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div><!-- End .featured-proucts -->

                    <div class="mb-6"></div><!-- margin -->

                    <div class="row">
                        @foreach($featuredProducts->chunk(3) as $index=>$chunk)
                            <div class="col-md-4 col-sm-12">
                                <div class="product-column">
                                    <h3 class="title">{{ $bestSellersTitles[$index] }}</h3>
                                    @foreach($chunk as $bestItem)
                                        <div class="product-default left-details product-widget">
                                            <figure>
                                                <a href="{{ route('productDetail',$bestItem->slug) }}">
                                                    <img src="{{ config('constants.image_paths.product270x250_folder_path').''.$bestItem->image }}" class="featuredProductsImageSize">
                                                </a>
                                            </figure>
                                            <div class="product-details">
                                                <h2 class="product-title">
                                                    <a href="{{ route('productDetail',$bestItem->slug) }}">{{$bestItem->title}}</a>
                                                </h2>
                                                <div class="price-box">
                                                    @if($bestItem->discount_price)
                                                        <span class="old-price" title="ürün fiyatı">  {{$bestItem->price}} ₺</span><br>
                                                        <span class="product-price">  {{$bestItem->discount_price}} ₺</span>
                                                    @else
                                                        <span class="product-price" title="ürün fiyatı">  {{$bestItem->price}} ₺</span>
                                                    @endif
                                                </div><!-- End .price-box -->
                                            </div><!-- End .product-details -->
                                        </div>
                                    @endforeach
                                </div><!-- End .product-column -->
                            </div><!-- End .col-md-4 -->
                        @endforeach
                    </div><!-- End .row -->

                    <div class="mb-3"></div><!-- margin -->

                </div><!-- End .col-lg-9 -->

                <aside class="sidebar-home col-lg-3 order-lg-first">
                    <div class="side-menu-container">
                        <h2>Kategoriler</h2>

                        <nav class="side-nav">
                            <ul class="menu menu-vertical sf-arrows">
                                @foreach($categories as $cat)
                                    <li>
                                        <a href="{{ route('categoryDetail',$cat->slug) }}" class="{{ count($cat->sub_categories) > 0 ?'sf-with-ul' : '' }}"><i class="icon-briefcase"></i>
                                            {{ $cat->title }}</a>
                                        @if(count($cat->sub_categories) > 0)
                                            <div class="megamenu megamenu-fixed-width">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="row">
                                                            @foreach($cat->sub_categories as $sub)
                                                                <div class="col-lg-6">
                                                                    <div class="menu-title">
                                                                        <a href="{{ route('categoryDetail',$sub->slug) }}">{{ $sub->title }}</a>
                                                                    </div>
                                                                    <ul>
                                                                        @foreach($sub->sub_categories as $sub2)
                                                                            <li><a href="{{ route('categoryDetail',$sub2->slug) }}">{{ $sub2->title }}</a></li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endforeach
                                                        </div><!-- End .row -->
                                                    </div><!-- End .col-lg-8 -->
                                                </div>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach

                            </ul>
                        </nav>
                    </div><!-- End .side-menu-container -->

                    <div class="widget">
                        @foreach($camps as $camp)
                            <div class="banner banner-image">
                                <a href="{{ route('campaigns.detail',[$camp->slug,null]) }}">
                                    <img src="{{ config('constants.image_paths.campaign_image_folder_path'). $camp->image }}" alt="{{ $camp->title }}">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </aside><!-- End .col-lg-3 -->
            </div><!-- End .row -->
        </div><!-- End .container -->

        <div class="mb-4"></div><!-- margin -->
    </main><!-- End .main -->
@endsection
@section('footer')
    <script type="application/ld+json">{
      "@context": "https://schema.org",
      "@type": "WebSite",
      "url": "{{  $site->domain }}",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{{  route('searchView') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
}


    </script>
@endsection
