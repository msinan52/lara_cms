@extends('admin.layouts.master')
@section('title','Ürün Listesi')
@section('content')
    <div class="box box-default">
        <div class="box-body with-border">
            <div class="row">
                <div class="col-md-10">
                    <a href="{{ route('admin.home_page') }}"> <i class="fa fa-home"></i> Anasayfa</a>
                    › Ürünler
                </div>
                <div class="col-md-2 text-right mr-3">
                    <a href="{{ route('admin.product.new') }}"> <i class="fa fa-plus"></i> Yeni Ürün Ekle</a>&nbsp;
                    <a href="{{ route('admin.products') }}"><i class="fa fa-refresh"></i>&nbsp;Yenile</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box ">
                <div class="box-header">
                    <h3 class="box-title"><span class="help-block text-sm">{{ count($list) }} adet listeleniyor </span></h3>

                    <div class="box-tools">
                        <form action="{{ route('admin.products') }}" method="get" id="form">
                            <div class="row">
                                <div class="col-md-2  pull-right">
                                    <select name="category_filter" class="form-control" id="category_filter" onchange="document.getElementById('form').submit()">
                                        <option value="">--Kategori Seçiniz--</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ request('category_filter') == $cat->id ? 'selected' : '' }}>{{ $cat->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(config('admin.product_companies_module'))
                                    <div class="col-md-2  pull-right">
                                        <select name="company_filter" class="form-control" id="company_filter" onchange="document.getElementById('form').submit()">
                                            <option value="">--Mağazaya Göre Filtrele--</option>
                                            @foreach($companies as $com)
                                                <option value="{{ $com->id }}" {{ request('company_filter') == $com->id ? 'selected' : '' }}>{{ $com->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-3 input-group input-group-sm hidden-xs  pull-right">
                                    <input type="text" name="q" class="form-control pull-right" placeholder="Ürünlerde ara.." value="{{ request('q') }}">

                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-hover table-bordered">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Başlık</th>
                            <th>Kategori</th>
                            <th>Slug</th>
                            <th>Firma</th>
                            <th>Marka</th>
                            <th>Fiyat</th>
                            <th>İndirimli</th>
                            <th>Fotoğraf</th>
                            <th>Durum</th>
                            <th>#</th>
                        </tr>
                        @if(count($list) > 0)
                            @foreach($list as $l)
                                <tr>
                                    <td>{{ $l ->id }}</td>
                                    <td><a href="{{ route('admin.product.edit',$l->id) }}"> {{ $l->title }}</a></td>
                                    <td>
                                        @foreach($l->categories as $cat)
                                            @if($loop->index < 3)
                                                <a href="{{route('admin.product.edit',$cat->id)}}"> {{ $cat->title }} {{ !$loop->index <= 3  ? ',' : '' }}</a>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{ $l ->slug }}</td>
                                    <td>{{ $l ->info->company->title }}</td>
                                    <td>{{ $l ->info->brand->title }}</td>
                                    <td>{{ $l ->price }} ₺</td>
                                    <td>{{ is_null($l ->discount_price) ? '-' : $l ->discount_price. " ₺" }}</td>
                                    <td>@if($l->image)
                                            <a target="_blank" href="{{ config('constants.image_paths.product_image_folder_path').''. $l ->image }}">
                                                <img src="{{ config('constants.image_paths.product_image_folder_path').''. $l ->image }}" alt="" width="50" height="50"></a>
                                        @endif</td>
                                    <td><i class="fa fa-{{ $l-> active == false ? 'times text-red' : 'check text-green' }}"></i></td>
                                    <td><a href="{{ route('admin.product.delete',$l->id) }}" onclick="return confirm('Silmek istediğine emin misin ?')"><i
                                                class="fa fa-trash text-red"></i></a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center"><h5>Ürün Bulunamadı</h5></td>
                            </tr>
                        @endif
                        </tbody>

                    </table>
                    <div class="text-right"> {{ $list->appends(['q' => request('q'),'parent_category'=> request('parent_category')])->links() }}</div>
                </div>

                <!-- /.box-body -->
            </div>

            <!-- /.box -->
        </div>
    </div>

@endsection
