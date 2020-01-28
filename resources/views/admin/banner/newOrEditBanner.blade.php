@extends('admin.layouts.master')
@section('title','Banner detay')

@section('content')
    <div class="box box-default">
        <div class="box-body with-border">
            <div class="row">
                <div class="col-md-10">
                    <a href="{{ route('admin.home_page') }}"> <i class="fa fa-home"></i> Anasayfa</a>
                    › <a href="{{ route('admin.banners') }}"> Bannerlar</a>
                    › {{ $banner->title }}
                </div>
                <div class="col-md-2 text-right mr-3">
                    <a type="submit" onclick="document.getElementById('form').submit()" class="btn btn-success btn-sm">Kaydet</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Banner Detay</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" method="post" action="{{ route('admin.banners.save',$banner->id != null ? $banner->id : 0) }}" id="form"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Başlık</label>
                                <input type="text" class="form-control" name="title" placeholder="başlık" required
                                       value="{{ old('title', $banner->title) }}">
                            </div>

                            <div class="form-group col-md-2">
                                <label for="exampleInputEmail1">Alt Başlık</label>
                                <input type="text" class="form-control" name="sub_title" placeholder="Alt Başlık"
                                       value="{{ old('slug', $banner->sub_title) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Link</label>
                                <input type="text" class="form-control" name="link" placeholder="Yönlendirelecek link"
                                       value="{{ old('link', $banner->link) }}">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="image">Fotoğraf</label><br>
                            <input type="file" class="form-control" name="image">
                            @if($banner->image)
                                <span class="help-block"><a
                                        href="{{ config('constants.image_paths.product_image_folder_path') }}{{ $banner->image }}">{{ $banner->image }}</a></span>
                            @endif
                        </div>
                        <div class="form-group col-md-2">
                            <label for="exampleInputEmail1">Aktif Mi ?</label><br>
                            <input type="checkbox" class="minimal" name="active" {{ $banner->active == 1 ? 'checked': '' }}>
                        </div>


                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>
                </form>
            </div>
            <!-- /.box -->

        </div>
        <!--/.col (left) -->

    </div>
@endsection
