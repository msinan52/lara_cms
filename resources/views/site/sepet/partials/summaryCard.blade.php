<div class="order-summary">
    <h3>Sepet</h3>
    <h4>
        <a data-toggle="collapse" href="#order-cart-section" class="collapsed" role="button" aria-expanded="false"
           aria-controls="order-cart-section">Sepette {{ Cart::count() }} ürün var</a>
    </h4>
    <div class="collapse" id="order-cart-section">
        <table class="table table-mini-cart">
            <tbody>
            @foreach(Cart::content() as $item)
                <tr>
                    <td class="product-col">
                        <figure class="product-image-container">
                            <a href="{{route('productDetail',$item->options->slug)}}" class="product-image">
                                <img src="{{config('constants.image_paths.product270x250_folder_path').$item->options->image}}" alt="{{$item->name}}">
                            </a>
                        </figure>
                        <div>
                            <h2 class="product-title">
                                <a href="{{route('productDetail',$item->options->slug)}}">{{$item->name}}</a>
                            </h2>

                            <span class="product-qty">Adet: {{$item->qty}}</span>
                        </div>
                    </td>
                    <td class="price-col">{{$item->total()}} ₺</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
