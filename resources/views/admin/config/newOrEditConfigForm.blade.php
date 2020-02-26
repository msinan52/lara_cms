@extends('admin.layouts.master')
@section('title','Ayar detay')

@section('content')
    <div class="box box-default">
        <div class="box-body with-border">
            <div class="row">
                <div class="col-md-10">
                    <a href="{{ route('admin.home_page') }}"> <i class="fa fa-home"></i> Anasayfa</a>
                    › <a href="{{ route('admin.configs') }}"> Ayarlar</a>
                    › {{ $config->title }}
                </div>
                <div class="col-md-2 text-right mr-3">
                    <a type="submit" onclick="document.getElementById('form').submit()" class="btn btn-success btn-sm">Kaydet</a>
                </div>
            </div>
        </div>
    </div>
    <form role="form" method="post" action="{{ route('admin.config.save',$config->id != null ? $config->id : 0) }}" id="form" enctype="multipart/form-data">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Genel Ayarlar</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->

                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Başlık</label>
                                <input type="text" class="form-control" name="title" placeholder="Site başlık" value="{{ old('title', $config->title) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Domain</label>
                                <input type="text" class="form-control" name="domain" placeholder="Domain ex:http://google.com"
                                       value="{{ old('domain', $config->domain) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Kelimeler</label>
                                <input type="text" class="form-control" name="keywords" value="{{ old('keywords', $config->keywords) }}">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="image">Logo</label><br>
                                <input type="file" class="form-control" name="logo">
                                @if($config->logo)
                                    <span class="help-block"><a
                                            href="/{{ config('constants.image_paths.config_image_folder_path').''.$config->logo }}">{{ $config->logo }}</a></span>
                                @endif
                            </div>
                            <div class="form-group col-md-5">
                                <label for="image">Açıklama</label><br>
                                <textarea class="form-control" name="desc" rows="5">{{ old('desc',$config->desc) }}</textarea>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="image">Footer Logo</label><br>
                                <input type="file" class="form-control" name="footer_logo">
                                @if($config->footer_logo)
                                    <span class="help-block"><a
                                            href="/{{ config('constants.image_paths.config_image_folder_path').''.$config->footer_logo }}">{{ $config->footer_logo }}</a></span>
                                @endif
                            </div>
                            <div class="form-group col-md-1">
                                <label for="image">İcon</label><br>
                                <input type="file" class="form-control" name="icon">
                                @if($config->icon)
                                    <span class="help-block"><a
                                            href="/{{ config('constants.image_paths.config_image_folder_path').''.$config->icon }}">{{ $config->icon }}</a></span>
                                @endif
                            </div>
                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Aktif Mi ?</label><br>
                                <input type="checkbox" class="minimal" name="active" {{ $config->active == 1 ? 'checked': '' }}>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Kargo Fiyatı</label><br>
                                <input type="number" class="minimal form-control" name="cargo_price" value="{{ old('cargo_price',$config->cargo_price) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="image">Footer Text</label><br>
                                <textarea class="form-control" name="footer_text">{{ old('footer_text',$config->footer_text) }}</textarea>
                            </div>
                            <button type="submit" class="hidden">Kaydet</button>
                        </div>
                    </div>

                </div>
                <!-- /.box -->

            </div>
            <!--/.col (left) -->

        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sosyal Medya</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Facebook Adresi</label>
                                <input type="text" class="form-control" name="facebook" placeholder="Facebook" value="{{ old('facebook', $config->facebook) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">İnstagram Adresi</label>
                                <input type="text" class="form-control" name="instagram" value="{{ old('instagram', $config->instagram) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Twitter Adresi</label>
                                <input type="text" class="form-control" name="twitter" value="{{ old('twitter', $config->twitter) }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Youtube Adresi</label>
                                <input type="text" class="form-control" name="youtube" value="{{ old('youtube', $config->youtube) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">İletişim Bilgileri</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <div class="box-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail1">Telefon</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone', $config->phone) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail1">Email</label>
                                <input type="text" class="form-control" name="mail" value="{{ old('mail', $config->mail) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail1">Adres</label>
                                <input type="text" class="form-control" name="adres" value="{{ old('adres', $config->adres) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </form>
@endsection
