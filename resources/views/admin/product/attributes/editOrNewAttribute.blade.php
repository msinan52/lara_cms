@extends('admin.layouts.master')
@section('title','Özellik Detay')
@section('header')
    <meta name="csrf-token" content="{{csrf_token()}}">
@endsection

@section('content')
    <div class="box box-default">
        <div class="box-body with-border">
            <div class="row">
                <div class="col-md-10">
                    <a href="{{ route('admin.home_page') }}"> <i class="fa fa-home"></i> Anasayfa</a>
                    › <a href="{{ route('admin.product.attribute.list') }}"> Ürün Özellikleri</a>
                    › {{ $item->title }}
                </div>
                <div class="col-md-2 text-right mr-3">
                    <a type="submit" onclick="document.getElementById('form').submit()" class="btn btn-success btn-sm">Kaydet</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <form role="form" method="post" action="{{ route('admin.product.attribute.save',$item->id != null ? $item->id : 0) }}" id="form">
        {{ csrf_field() }}
        <!-- left column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Özellik Detay</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->

                    <div class="box-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Başlık</label>
                                <input type="text" class="form-control" name="title" placeholder="Kategori başlık"
                                       value="{{ old('title', $item->title) }}">
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="exampleInputEmail1">Aktif Mi ?</label><br>
                            <input type="checkbox" class="minimal" name="active" {{ old('active',$item->active) == 1 ? 'checked': '' }}>
                        </div>


                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>

                </div>
                <!-- /.box -->

            </div>
            <!--/.col (left) -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Alt Özellikler</h3>
                        <div class="pull-right box-tools">
                            <button type="button" class="btn btn-default btn-sm" title="Yeni Özellik Ekle" onclick="addNewProductAttributeItem()">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->

                    <div class="box-body" id="productSubAttributeContainer">
                        @foreach($item->subAttributes as $index=>$sub)
                            <div class="form-row productSubAttribute" data-index="{{$index}}">
                                <div class="form-group col-md-9">
                                    <label for="exampleInputEmail1">Başlık</label>
                                    <input type="hidden" name="productSubAttributeTitleHidden{{$index}}" value="{{$sub->id}}">
                                    <input type="text" class="form-control" name="productSubAttributeTitle{{$index}}"
                                           value="{{ $sub->title}}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="exampleInputEmail1">&nbsp;</label><br>
                                    <a href="javascript:void(0);" onclick="deleteProductSubAttributeFromDB({{$sub->id}},{{$index}})"><i
                                            class="fa fa-trash text-red"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>

                </div>
                <!-- /.box -->

            </div>
        </form>

    </div>
@endsection
@section('footer')
    <script src="{{ asset('admin_files/js/productAttribute.js') }}"></script>
@endsection
