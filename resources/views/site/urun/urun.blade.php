@extends('site.layouts.base')
@section('header')
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>{{$urun->title}} | {{ $site->title }}</title>

    <meta name="description"
          content="{{ is_null($urun->spot) ? $site->title.' sayesinde '. $urun->categories[0]->title .' kategorisinde bulunan ' . $urun->title .' ürünlerine indirimlerle sahip  olabilirsiniz' : $urun->spot }}"/>
    <meta name="keywords" content="{{ $urun->categories[0]->title.','.@$urun->categories[1]->title.','.@$urun->categories[2]->title }}"/>
    <meta property="og:type" content="product"/>
    <meta property="og:url" content="{{ route('productDetail',$urun->slug)  }}"/>
    <meta property="og:title" content="{{ $urun->title .' | '. $site->title }}"/>
    <meta name="og:description" content="{{ $urun->title }} {{ $urun->spot }}"/>
    <meta property="og:image" content="{{ $site->domain }}/uploads/products/{{ $urun->image }}"/>
    <meta name="twitter:card" content="product"/>
    <meta name="twitter:site" content="@siteadi"/>
    <meta name="twitter:creator" content="@siteadi"/>
    <meta name="twitter:title" content="{{ $urun->title .' | '. $site->title }}"/>
    <meta name="twitter:description" content="{{ $urun->title }} {{ $urun->spot }}"/>
    <meta name="twitter:image:src" content="{{ $site->domain }}/uploads/products/{{ $urun->image }}"/>
    <meta name="twitter:data1" content="{{ !is_null($urun->discount_price) ? $urun->discount_price : $urun->price }}"/>
    <meta name="twitter:label1" content="{{ $site->title }} Fiyati"/>
    <meta name="twitter:domain" content="{{$site->domain}}"/>
    <link rel="canonical" href="{{ route('productDetail',$urun->slug)  }}"/>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": "{{ $urun->title }}",
      "image": "{{ $site->domain }}/uploads/products/{{ $urun->image }}",
      "description": "{{ is_null($urun->spot) ? $site->title.' sayesinde '. $urun->categories[0]->title .' kategorisinde bulunan ' . $urun->title .' ürünlerine indirimlerle sahip  olabilirsiniz' : $urun->spot }}",
      "brand": "{{ $urun->info->brand->title }}",
      "offers": {
        "@type": "Offer",
        "url": "{{ route('productDetail',$urun->slug)  }}",
        "priceCurrency": "TRY",
        "price": "{{ $urun->price  }}",
        "availability": "https://schema.org/InStock",
        "itemCondition": "https://schema.org/NewCondition"
      }
    }


    </script>
@endsection
@section('title','Urunler')

@section('content')
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('homeView') }}"><i class="icon-home"></i></a></li>
                <li class="breadcrumb-item">
                    @foreach($urun->categories as $kategori)
                        <a href="{{route('categoryDetail',$kategori->slug)}}">{{$kategori->title}}</a>{{ $loop->last == 1 ? '' : ',' }}
                    @endforeach
                </li>
                <li class="breadcrumb-item">{{ $urun->title }}</li>
            </ol>
        </div><!-- End .container -->
    </nav>
    <div class="container">
        @include('site.layouts.partials.messages')
        <div class="row">
            <div class="col-lg-9">
                <div class="product-single-container product-single-default">
                    <div class="row">
                        <div class="col-lg-7 col-md-6 product-single-gallery">
                            <div class="product-slider-container product-item">
                                <div class="product-single-carousel owl-carousel owl-theme">
                                    <div class="product-item">
                                        <img class="product-single-image" src="{{ config('constants.image_paths.product_image_folder_path').$urun->image }}"
                                             data-zoom-image="{{ config('constants.image_paths.product_image_folder_path').$urun->image }}" width="470" height="470"/>
                                    </div>
                                    @foreach($urun->images as $image)
                                        <div class="product-item">
                                            <img class="product-single-image" src="{{ config('constants.image_paths.product_gallery_folder_path').$image->image }}"
                                                 data-zoom-image="{{ config('constants.image_paths.product_gallery_folder_path').$image->image }}"/>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- End .product-single-carousel -->
                                <span class="prod-full-screen">
                                            <i class="icon-plus"></i>
                                        </span>
                            </div>
                            <div class="prod-thumbnail row owl-dots" id='carousel-custom-dots'>
                                <div class="col-3 owl-dot">
                                    <img src="{{ config('constants.image_paths.product270x250_folder_path').$urun->image }}"/>
                                </div>
                                @foreach($urun->images as $image)
                                    <div class="col-3 owl-dot">
                                        <img src="{{ config('constants.image_paths.product_gallery_folder_path').$image->image }}" style="height: 102px;width: 110px"/>
                                    </div>
                                @endforeach
                            </div>
                        </div><!-- End .col-lg-7 -->

                        <div class="col-lg-5 col-md-6">
                            <input type="hidden" id="productDefaultPrice" value="{{ $urun->discount_price !== null ? $urun->discount_price : $urun->price }}">
                            <input type="hidden" id="productDefaultQty" value="{{ $urun->qty }}">
                            <div class="product-single-details">
                                <h1 class="product-title">{{$urun->title}}</h1>

                                <div class="ratings-container">
                                    <span class="help-block text-sm small">Stok :<span class="qty">{{$urun->qty}}</span> </span>|
                                    <a href="#" class="rating-link">{{ $urun->comments_count }} yorum</a> | <span> {{ $urun->info->brand->title }}</span>
                                </div><!-- End .product-container -->

                                <div class="price-box">
                                    @if($discount)
                                        <span class="old-price">{{$urun->price}}₺</span>
                                        <span class="product-price"><span class="price">{{$discount}}</span> ₺</span>
                                    @else
                                        <span class="product-price"><span class="price">{{$urun->price}}</span> ₺</span>
                                    @endif

                                </div><!-- End .price-box -->

                                <div class="product-desc">
                                    <p>{{$urun->info->spot }}</p>
                                </div>

                                <div class="product-filters-container" id="productDetailsContainer">
                                    @foreach($urun->detail as $index => $detail)
                                        <tr>
                                            <input type="hidden" name="attributeTitle{{$index}}" value="{{ $detail->attribute->title }}">{{ $detail->attribute->title }}
                                            <td><select name="subAttributeTitle{{$index}}" class="form-control productVariantAttribute" id="subAttributeTitle{{$index}}"
                                                        onchange="productVariantAttributeOnChange({{$urun->id}});">
                                                    <option value="">--Özellik Seçiniz--</option>
                                                    @foreach($detail->subDetails as $subDetail)
                                                        <option data-value="{{ $subDetail->parentSubAttribute->id }}"
                                                                value="{{  $subDetail->parentSubAttribute->id }}|{{  $subDetail->parentSubAttribute->title }}|{{  $subDetail->parentDetail->attribute->title }}">{{ $subDetail->parentSubAttribute->title }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </div><!-- End .product-filters-container -->

                                <div class="product-action product-all-icons">
                                    <div class="product-single-qty">
                                        <input class="horizontal-quantity form-control" name="qty" id="qty" type="text" dt-max="{{$urun->qty}}">
                                    </div><!-- End .product-single-qty -->

                                    <a href="javascript:void(0);" onclick="return addItemToBasket({{$urun->id}})" class="paction add-cart" title="Sepete ekle">
                                        <span>Sepete ekle</span>
                                    </a>
                                    <a href="javascript:void(0);" class="paction add-wishlist" title="Favorileme Ekle " onclick="return addToFavorites({{$urun->id}})">
                                        <span>Favorilere Ekle</span>
                                    </a>
                                </div><!-- End .product-action -->

                                <div class="product-single-share">
                                    <label>Share:</label>
                                    <!-- www.addthis.com share plugin-->
                                    <div class="addthis_inline_share_toolbox"></div>
                                </div><!-- End .product single-share -->
                            </div><!-- End .product-single-details -->
                        </div><!-- End .col-lg-5 -->
                    </div><!-- End .row -->
                </div><!-- End .product-single-container -->

                @include('site.urun.partials.urun_aciklama')
            </div><!-- End .col-lg-9 -->

            <div class="sidebar-overlay"></div>
            <div class="sidebar-toggle"><i class="icon-sliders"></i></div>
            <aside class="sidebar-product col-lg-3 padding-left-lg mobile-sidebar">
                <div class="sidebar-wrapper">
                    @include('site.urun.partials.bestSellersSidebar3items')
                    <div class="widget widget-info">
                        <ul>
                            <li>
                                <i class="icon-shipping"></i>
                                <h4>Hızlı<br>Teslimat</h4>
                            </li>
                            <li>
                                <i class="fa fa-users"></i>
                                <h4>Yüksek<br>Müşteri Memnuniyeti</h4>
                            </li>
                            <li>
                                <i class="icon-online-support"></i>
                                <h4>7/24 <br> Destek</h4>
                            </li>
                        </ul>
                    </div><!-- End .widget -->

                </div>
            </aside><!-- End .col-md-3 -->
        </div><!-- End .row -->
    </div><!-- End .container -->

    <div class="featured-section">
        @include('site.urun.partials.bottomFeaturedProducts')
    </div>
@endsection
@section('footer')
    <script src="{{ asset('js/ecommerce.js') }}"></script>
    <!-- www.addthis.com share plugin -->
    <script src="https://s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5b927288a03dbde6"></script>
@endsection
