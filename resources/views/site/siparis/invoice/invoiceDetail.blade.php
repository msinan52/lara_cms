<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{$site->title}} Fatura Bilgileri - {{ $order->id }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="/admin_files/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/admin_files/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/admin_files/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/admin_files/dist/css/AdminLTE.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
{{--<body onload="window.print();">--}}
<body>
<div class="wrapper">
    <!-- Main content -->
    <section class="invoice">
        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">
                    <i class="fa fa-globe"></i> {{ $owner->full_name }}
                    <small class="pull-right">Tarih:  {{ date('d/m/Y') }}</small>
                </h2>
            </div>
            <!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                Satıcı
                <address>
                    <strong>{{ $owner->full_name }}</strong><br>
                    {!!  $owner->company_address  !!}<br>
                    Telefon: {{$owner->phone}}<br>
                    Email:{{$owner->email}}
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                Alıcı
                <address>
                    <strong>{{ title_case($order->full_name) }}</strong><br>
                    {{ $order->fatura_adres }}<br>
                    Telefon: {{ $order->phone }}<br>
                    Email: {{ $order->basket->user->email }}
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>Fatura #{{$order->id}}</b><br>
                <br>
                <b>Sipariş Id:</b> {{$order->id}}<br>
                <b>İşlem Tarihi:</b>{{$order->created_at}}<br>
                <b>Taksit :</b>&nbsp;{{$order->iyzico->installment}}
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Özellikler</th>
                        <th>Fiyat</th>
                        <th>Adet</th>
                        <th>Toplam</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->basket->basket_items as $item)
                        <tr>
                            <td>{{ $item->product->title }}</td>
                            <td>{{$item->attributes_text}}</td>
                            <td>{{$item->price}} ₺</td>
                            <td>{{$item->qty}}</td>
                            <td>{{ $item->qty * $item->price }} ₺</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <div class="row">
            <!-- /.col -->
            <div class="col-xs-6 pull-right">
                <p class="lead">Ödenecek Tutar {{ date('d/m/Y') }}</p>

                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <th style="width:50%">Ara Toplam:</th>
                            <td>{{ $order->order_price }} ₺</td>
                        </tr>
                        @if($basket_coupon)
                            <tr>
                                <th style="width:50%">Kupon - {{ $basket_coupon->code }}:</th>
                                <td class="text-red">- {{ $basket_coupon->discount_price }} ₺</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Kargo:</th>
                            <td>{{$order->cargo_price}} ₺</td>
                        </tr>
                        <tr>
                            <th>Toplam:</th>
                            <td>{{$order->order_total_price}} ₺</td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>

