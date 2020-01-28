@extends('admin.layouts.master')
@section('title','sipariş detay')

@section('content')
    <div class="box box-default">
        <div class="box-body with-border">
            <div class="row">
                <div class="col-md-10">
                    <a href="{{ route('admin.home_page') }}"> <i class="fa fa-home"></i> Anasayfa</a>
                    › <a href="{{ route('admin.orders') }}"> Siparişler</a>
                    › {{ $order->full_name }}
                </div>
                <div class="col-md-2 text-right mr-3">
                    <a class="btn btn-info btn-sm" href="{{ route('admin.order.invoice',$order->id) }}"><i class="fa fa-file"></i> &nbsp;Fatura Görüntüle</a>
                    <a type="submit" onclick="document.getElementById('form').submit()" class="btn btn-success btn-sm">Kaydet</a>
                </div>
            </div>
        </div>
    </div>
    <form role="form" method="post" action="{{ route('admin.order.save',$order->id != null ? $order->id : 0) }}" id="form">
        <div class="row">
            <!-- left column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sipariş Bilgileri</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->

                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Sipariş Kodu</label>
                                <input type="text" class="form-control"
                                       value="SP-{{ $order->id }}" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Sipariş Tutarı</label>
                                <input type="text" class="form-control"
                                       value="{{ $order->order_total_price }}" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Sipariş Tarihi</label>
                                <input type="text" class="form-control"
                                       value="{{ $order->created_at }}" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Durum</label>
                                <select name="status" class="form-control">
                                    <option value="">---Durum Seçiniz --</option>
                                    @foreach($filter_types as $type)
                                        <option value="{{ $type[0] }}" {{ $type[0] == $order->status ? 'selected' : '' }}>{{ $type[1]  }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Adres</label>
                                <textarea class="form-control" rows="5" disabled>{{ $order->adres }}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Fatura Adresi</label>
                                <textarea class="form-control" rows="5" disabled>{{ $order->fatura_adres }}</textarea>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->

                </div>

            </div>
            <!--/.col (left) -->

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sepet Bilgileri</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="exampleInputEmail1">Sepet Tutarı</label>
                                <br> {{ $order->order_total_price }} ₺
                            </div>
                            <div class="form-group col-md-2">
                                <label for="exampleInputEmail1">Toplam Ürün Adet</label>
                                <br> {{ $order->basket->get_basket_item_count() }}
                            </div>
                            <div class="form-group col-md-2">
                                <label for="exampleInputEmail1">Oluşturulma Tarihi</label>
                                <br> {{ $order->created_at }}
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Telefon</label>
                                <br> {{ $order->phone }}
                            </div>
                        </div>

                    </div>
                    <div class="box box-footer">
                        <div class="col-xs-6 pull-right">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <th style="width:50%">Durum:</th>
                                        <td>{{ $order->statusLabel() }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width:50%">Alt Toplam:</th>
                                        <td>{{ $order->order_price }} ₺</td>
                                    </tr>
                                    <tr>
                                        <th style="width:50%">Kargo:</th>
                                        <td class="text-green">+ {{ $order->cargo_price }} ₺</td>
                                    </tr>
                                    @if($basket_coupon)
                                        <tr>
                                            <th style="width:50%">Kupon - {{ $basket_coupon->code }}:</th>
                                            <td class="text-red">- {{ $basket_coupon->discount_price }} ₺</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Toplam:</th>
                                        <td>{{ $order->order_total_price }} ₺</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Siparişteki Ürünler - SP-{{ $order->id }}</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th>ID</th>
                                <th>Ürün</th>
                                <th>Ürün Resmi</th>
                                <th>Adet</th>
                                <th>Fiyat</th>
                                <th>Ürün Durum</th>
                            </tr>

                            @foreach($order->basket->basket_items as $cart_item)
                                <tr>
                                    <td>{{ $cart_item->id }}</td>
                                    <td><a target="_blank" href="{{ route('admin.product.edit',$cart_item->product->id) }}">{{ $cart_item->product->title }}</a></td>
                                    <td><img src="{{ config('constants.image_paths.product_image_folder_path').''. $cart_item->product->image }}" width="50" height="50"></td>
                                    <td>{{ $cart_item->qty  }}</td>
                                    <td>{{ $cart_item->price }} ₺</td>
                                    <td>
                                        <select name="orderItem{{ $cart_item->id }}" class="form-control">
                                            <option value="">---Ürün Durum Seçiniz --</option>
                                            @foreach($item_filter_types as $itemType)
                                                <option value="{{ $itemType[0] }}" {{ $itemType[0] == $cart_item->status ? 'selected' : '' }}>{{ $itemType[1]  }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Kullanıcı Bilgileri</h3>
                        <div class="box-tools">
                            <a href="{{ route('admin.user.edit',$order->basket->user->id )}}"><i class="fa fa-edit"></i></a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Ad/Soyad</label>
                                <br>{{ $order->full_name }}
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Email</label>
                                <br>{{ $order->basket->user->email }}
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Kayıt Tarihi</label>
                                <br>{{ $order->created_at }}
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Telefon</label>
                                <br>{{ $order->phone }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">İyzico Bilgileri</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Sepet / ConversationId</label>
                                <br>{{ $order->iyzico->transaction_id }}
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Price</label>
                                <br>{{ $order->iyzico->price }} ₺
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Paid Price</label>
                                <br>{{ $order->iyzico->paidPrice }} ₺
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Taksit sayısı</label>
                                <br>{{ $order->iyzico->installment }}
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Payment ID</label>
                                <br>{{ $order->iyzico->paymentId }}
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Durum</label>
                                <br>{{ $order->iyzico->status }}
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">3D Ödeme Alındı ?</label>
                                <br> <i class="fa fa-{{ $order->is_payment == 1 ? 'check text-green':'times text-red text-bold' }}">{{ $order->is_payment == 1 ? 'Ödeme Alındı':' DİKKAT ÖDEME ALINMADI' }}</i>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="exampleInputEmail1">İyzico Json</label>
                                <textarea disabled class="form-control" name="" id="" cols="30" rows="50">{{ $order->iyzico->iyzicoJson }}</textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </form>
@endsection
