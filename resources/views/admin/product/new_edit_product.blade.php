@extends('admin.layouts.master')
@section('title','Ürün Detay')

@section('content')
    <form role="form" method="post" action="{{ route('admin.product.save',$product->id != null ? $product->id : 0) }}" id="form" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="box box-default">
            <div class="box-body with-border">
                <div class="row">
                    <div class="col-md-10">
                        <a href="{{ route('admin.home_page') }}"> <i class="fa fa-home"></i> Anasayfa</a>
                        › <a href="{{ route('admin.products') }}"> Ürünler</a>
                        › {{ $product->title }}
                    </div>
                    <div class="col-md-2 text-right mr-3">
                        @if(!is_null($product->slug))<a target="_blank" href="{{ route('productDetail',$product->slug) }}">Sitede Görüntüle <i class="fa fa-eye"></i></a>&nbsp;&nbsp;&nbsp;
                        &nbsp;@endif
                        <a type="submit" onclick="document.getElementById('form').submit()" class="btn btn-success btn-sm">Kaydet</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ürün Bilgileri</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">Başlık</label>
                                <input type="text" class="form-control" name="title" placeholder="ürün başlık" maxlength="90"
                                       value="{{ old('title', $product->title) }}">
                            </div>


                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Slug</label>
                                <input type="text" class="form-control" id="slug" name="slug" placeholder="ürün slug" disabled
                                       value="{{ old('slug', $product->slug) }}">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Kod</label>
                                <input type="text" class="form-control" placeholder="ürün kodu otomatik oluşur" disabled
                                       value="{{ old('code', $product->info->code) }}">
                            </div>

                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Aktif Mi ?</label><br>
                                <input type="checkbox" class="minimal" name="active" {{ $product->active == 1 ? 'checked': '' }}>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="image">Fotoğraf</label><br>
                                <input type="file" class="form-control" name="image">
                                @if($product->image)
                                    <span class="help-block"><a
                                            href="{{ config('constants.image_paths.product_image_folder_path') }}{{ $product->image }}">{{ $product->image }}</a></span>
                                @endif
                            </div>
                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Satış Fiyat</label>
                                <input type="number" class="form-control" name="price" placeholder="fiyat" required
                                       value="{{ old('price', $product->price) }}">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Alış Fiyatı</label>
                                <input type="number" class="form-control" name="buying_price" placeholder="firmadan alış fiyatı"
                                       value="{{ old('buying_price', $product->info->buying_price) }}">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">İndirimli Fiyat</label>
                                <input type="number" class="form-control" name="discount_price" placeholder="İndirimli Fiyat"
                                       value="{{ old('discount_price', $product->discount_price) }}">
                            </div>
                        </div>
                        <div class="row">

                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Adet</label>
                                <input type="number" class="form-control" id="qty" name="qty" placeholder="Adet" required
                                       value="{{ old('qty', $product->qty) }}">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Marka @if($product->info->brand_id)<a target="_blank"
                                                                                                      href="{{ route('admin.product.brands.edit',$product->info->brand_id) }}"><i
                                            class="fa fa-eye text-blue"></i></a> @endif</label>
                                <select name="brand" id="brand" class="form-control">
                                    <option value="">---Marka Seçiniz --</option>
                                    @foreach($brands as $brand)
                                        <option {{ !is_null($product->info) ? ($product->info->brand_id == $brand->id ? 'selected' : '') : '' }}
                                                value="{{ $brand->id }}">{{ $brand->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="exampleInputEmail1">Tedarikçi Firma &nbsp;@if($product->info->company_id)<a target="_blank"
                                                                                                                        href="{{ route('admin.product.company.edit',$product->info->company_id) }}"><i
                                            class="fa fa-eye text-blue"></i></a> @endif </label>
                                <select name="company" id="company" class="form-control">
                                    <option value="">---Firma Seçiniz --</option>
                                    @foreach($companies as $com)
                                        <option {{ !is_null($product->info) ? ($product->info->company_id == $com->id ? 'selected' : '') : '' }}
                                                value="{{ $com->id }}">{{ $com->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail1">Kategoriler</label>
                                <select name="categories[]" id="categories" class="form-control" multiple required>
                                    <option value="">---Kategori Seçiniz --</option>
                                    @foreach($categories as $cat)

                                        <option {{ collect(old('categories',$selected_categories))->contains($cat->id) ? 'selected' : '' }}
                                                value="{{ $cat->id }}">{{ $cat->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-5">
                                <label for="exampleInputEmail1">Kısa Açıklama</label>
                                <input type="text" class="form-control" id="spot" name="spot" placeholder="Kısa açıklama" maxlength="255"
                                       value="{{ old('spot', $product->info->spot) }}">
                            </div>

                        </div>


                    </div>
                </div>
                <!-- Ürün Açıklama -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header" data-widget="collapse" data-toggle="tooltip">
                                <h3 class="box-title">Ürün Açıklama
                                    <small>Ürün hakkında açıklama</small>
                                </h3>
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip"
                                            title="Daralt">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="box-body pad">
                         <textarea class="textarea" placeholder="Place some text here" id="editor1"
                                   style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                                   name="desc">{{ $product->desc }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Ürün Detay
                                    <small>Ürün hakkında özellik bilgileri</small>
                                </h3>
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-default btn-sm" title="Yeni Özellik Ekle" onclick="addNewProductDetail()">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <div class="box-body pad" id="productDetailAttributeContainer">
                                @foreach($productDetails as $index => $detail)
                                    <!-- product detail item -->
                                        <div class="form-row row productDetailAttribute" data-index="{{ $index }}">
                                            <div class="form-group col-md-5">
                                                <label for="exampleInputEmail1">Ürün Özellik Adı</label>
                                                <select name="attribute{{$index}}" id="attributes{{$index}}" class="form-control"
                                                        onchange="getAndFillSubAttributeByAttributeID(this.value,'#subAttributes{{$index}}')">
                                                    @foreach($attributes as $attr)
                                                        <option
                                                            value="{{ $attr->id }}" {{ $attr->id == $detail['parent_attribute']  ? 'selected' : '' }}>{{ $attr->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-5">
                                                <label for="exampleInputEmail1">Alt Özellikler</label>
                                                <select name="subAttributes{{$index}}[]" id="subAttributes{{$index}}" class="form-control" multiple required>
                                                    @foreach($subAttributes as $subAttribute)
                                                        @if($subAttribute->parent_attribute == $detail['parent_attribute'])
                                                            <option
                                                                value="{{ $subAttribute->id }}" {{ collect($productSelectedSubAttributesIdsPerAttribute[$index])->contains($subAttribute->id) ? 'selected' : '' }}>{{ $subAttribute->title }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="exampleInputEmail1">&nbsp;</label><br>
                                                <a href="javascript:void(0);" onclick="deleteProductDetailFromDB({{$detail['id']}},{{$index}})"><i
                                                        class="fa fa-trash text-red"></i></a>
                                            </div>
                                        </div>
                                        <!--.//product detail item -->
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Özellikler -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Özellikler <small>Ürüne ait yeni özellikler oluşturabilirsiniz</small></h3>
                                <div class="box-tools">
                                    <button type="button" class="btn btn-default btn-sm" title="Yeni Özellik Ekle" onclick="addToNewProperty()">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="containerOzellikler">
                            @if(count($product->info->properties) > 0)
                                @foreach($product->info->properties as $i=>$properties)
                                    <!-- Attr item -->
                                        <div class="box-body itemOzellikler" id="productPropertyContainer{{$i}}">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1">Başlık</label>
                                                <input type="text" class="form-control" name="properties[{{ $i }}][key]" placeholder="Özellik Adı" value="{{$properties['key']}}">
                                            </div>
                                            <div class="form-group col-md-5">
                                                <label>Açıklama</label>
                                                <input type="text" class="form-control" name="properties[{{ $i }}][value]" placeholder="Açıklama" value="{{$properties['value'] ?? ''}}">
                                            </div>
                                            <div class="form-group col-md-1">
                                                <label>Sil</label><br>
                                                <a onclick="deleteProductProperties({{$i}})"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </div>
                                @endforeach
                            @endif
                            <!-- ./Attr item -->
                            </div>


                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Oem Kodları <small>Ürüne ait oem kodları ekleyebilirsiniz</small></h3>
                                <div class="box-tools">
                                    <button type="button" class="btn btn-default btn-sm" title="Yeni Özellik Ekle" onclick="addToNewOem()">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="containerOem">
                            @if(count($product->info->oems) > 0)
                                @foreach($product->info->oems as $i=>$oem)
                                    <!-- Attr item -->
                                        <div class="box-body itemOem" id="productOemContainer{{$i}}">
                                            <div class="form-group col-md-6">
                                                <label for="exampleInputEmail1">Oem Marka</label>
                                                <select name="oems[{{ $i }}][key]" required class="form-control">
                                                    <option value="">Marka Seçiniz</option>
                                                    @foreach($brands as $b)
                                                        <option value="{{ $b->title }}" {{ $oem['key'] == $b->title ? 'selected' : '' }}>{{ $b->title }}</option>
                                                    @endforeach
                                                </select>
                                                {{--                                                <input type="text" class="form-control" name="oems[{{ $i }}][key]" placeholder="Oem Marka" value="{{$oem['key']}}">--}}
                                            </div>
                                            <div class="form-group col-md-5">
                                                <label>Oem Kodu</label>
                                                <input type="text" class="form-control" name="oems[{{ $i }}][value]" placeholder="Oem Kodu" value="{{$oem['value'] ?? ''}}">
                                            </div>
                                            <div class="form-group col-md-1">
                                                <label>Sil</label><br>
                                                <a onclick="deleteProductOem({{$i}})"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </div>
                                @endforeach
                            @endif
                            <!-- ./Attr item -->
                            </div>


                        </div>
                    </div>
                </div>
                <!-- / özellkler -->
                <!-- ürün uyumlu araçlar -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Ürün Uyumlu Araçlar
                                    <small>Ürünün uyumlu olduğu araç listesini ekleyebilirisiniz</small>
                                </h3>
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-default btn-sm" title="Yeni Uyumlu Araç Ekle" onclick="addToNewSupportCar({{$product->id}})">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="containerUyumluAraclar">
                                @foreach($product->info->supported_cars as $indexForCar => $car)
                                    <div class="box-body itemUyumluAraclar" id="productOemContainer{{$index}}">
                                        <div class="form-group col-md-2">
                                            <label for="exampleInputEmail1">Marka</label>
                                            <select name="supported_cars[{{ $indexForCar }}][parent_marka]" required class="form-control">
                                                <option value="">Marka Seçiniz</option>
                                                @foreach($vehicles['markalar'] as $marka)
                                                    <option value="{{ $marka->id }}" {{ $car['parent_marka'] == $marka->id ? 'selected' : '' }}>{{ $marka->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="exampleInputEmail1">Model</label>
                                            <select name="supported_cars[{{ $indexForCar }}][parent_model]" required class="form-control">
                                                <option value="">Model Seçiniz</option>
                                                @foreach($vehicles['productSupportedModelList'][$indexForCar][0] as $model)
                                                    <option value="{{ $model->id }}" {{ $car['parent_model'] == $model->id ? 'selected' : '' }}>{{ $model->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="exampleInputEmail1">Kasa</label>
                                            <select name="supported_cars[{{ $indexForCar }}][parent_kasa]" required class="form-control">
                                                <option value="">Kasa Seçiniz</option>
                                                @foreach($vehicles['productSupportedModelList'][$indexForCar][1] as $kasa)
                                                    <option value="{{ $kasa->id }}" {{ $car['parent_kasa'] == $kasa->id ? 'selected' : '' }}>{{ $kasa->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-1">
                                            <label for="exampleInputEmail1">Model Yılı</label>
                                            <select name="supported_cars[{{ $indexForCar }}][parent_model_yili]" required class="form-control">
                                                <option value="">Model Yılı Seçiniz</option>
                                                @foreach($vehicles['productSupportedModelList'][$indexForCar][2] as $modelYear)
                                                    <option value="{{ $modelYear->id }}" {{ $car['parent_model_yili'] == $modelYear->id ? 'selected' : '' }}>{{ $modelYear->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="exampleInputEmail1">Motor Hacmi</label>
                                            <select name="supported_cars[{{ $indexForCar }}][parent_motor_hacmi]" required class="form-control">
                                                <option value="">Motor Hacmi Seçiniz</option>
                                                @foreach($vehicles['productSupportedModelList'][$indexForCar][3] as $motorHacmi)
                                                    <option
                                                        value="{{ $motorHacmi->id }}" {{ $car['parent_motor_hacmi'] == $motorHacmi->id ? 'selected' : '' }}>{{ $motorHacmi->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="exampleInputEmail1">Beygir Gücü</label>
                                            <select name="supported_cars[{{ $indexForCar }}][beygir_gucu]" required class="form-control">
                                                <option value="">Model Yılı Seçiniz</option>
                                                @foreach($vehicles['productSupportedModelList'][$indexForCar][4] as $beygirGucu)
                                                    <option value="{{ $beygirGucu->id }}" {{ $car['beygir_gucu'] == $beygirGucu->id ? 'selected' : '' }}>{{ $beygirGucu->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-1">
                                            <label>Sil</label><br>
                                            <a onclick="deleteProductOem({{$i}})"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ./uyumlu araçklar -->
                <!-- Ürün Variants -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Ürün Varyantları
                                    <small>Ürün özelliklerine göre fiyat belirlenebilir</small>
                                </h3>
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-default btn-sm" title="Yeni Varyant Ekle" onclick="addNewProductVariantItem({{$product->id}})">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="box-body pad" id="productVariantContainer">

                                @foreach($productVariants as $index => $variant)
                                    <!-- product variant item -->
                                        <div class="form-row row productVariantItem" data-index="{{ $index }}">
                                        <!-- variant id below hidden input name=variantIndexHidden{{$index}}-->
                                            <input type="hidden" value="{{ $variant->id }}" name="variantIndexHidden{{$index}}">
                                            <div class="form-group">
                                                @foreach($productDetails as $subIndex => $detail)

                                                    <div class="col-md-1">
                                                        <td><label for="">{{ $detail['attribute']['title'] }}</label>
                                                        <!-- variant attribute id below hidden input name=variantAttributeHidden{{$index}}-->
                                                            <input type="hidden" value="{{ $detail['attribute']['id'] }}" name="variantAttributeHidden{{$index}}-{{$subIndex}}">
                                                        </td>
                                                        <td>
                                                            <select name="variantAttributeSelect{{$index}}-{{$subIndex}}" class="form-control">
                                                                <option value="">Seçiniz</option>
                                                                @foreach($detail['sub_details'] as $subDetail)
                                                                    <option {{ collect($variant->urunVariantSubAttributes()->get()->map(function ($item) {
                                                                     return $item->sub_attr_id;
                                                                    }))->contains($subDetail['sub_attribute']) ? 'selected' : 'note' }}

                                                                            value="{{  $subDetail['parent_sub_attribute']['id'] }}">


                                                                        {{ $subDetail['parent_sub_attribute']['title'] }}</option>
                                                                @endforeach
                                                            </select>

                                                        </td>
                                                    </div>

                                                @endforeach
                                                <div class="col-md-1">
                                                    <td><label for="">Fiyat</label></td>
                                                    <td>
                                                        <input type="number" class="form-control" value="{{ $variant->price }}" name="variantPrice{{$index }}" required>
                                                        <p class="help-block">Ürünün girilen özelliklere ait fiyatı</p>
                                                    </td>
                                                </div>
                                                <div class="col-md-1">
                                                    <td><label for="">Adet</label></td>
                                                    <td><input type="number" class="form-control" value="{{$variant->qty}}" name="variantQty{{$index }}" required></td>
                                                    <p class="help-block">Seçilen özelliklere ait adet sayısı</p>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>&nbsp;</label><br>
                                                    <a href="javascript:void(0);" onclick="deleteProductVariantFromDB({{$variant->id}},{{$index}})"><i
                                                            class="fa fa-trash text-red"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <!--.//product detail item -->
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Ürün Galerisi</h3>
                                <div class="box-tools">
                                    <label for="">Dosya Ekle</label>
                                    <input type="file" name="imageGallery[]" multiple>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    @foreach($product->images as $image)
                                        <div class="col-md-2" id="productImageCartItem{{$image->id}}">
                                            <div class="card">
                                                <div class="card-body">
                                                    <a href="javascript:void(0);" onclick="deleteProductImage({{ $image->id }})" class="btn btn-danger btn-xs pull-right">X</a>
                                                    <a target="_blank" href="{{ config('constants.image_paths.product_gallery_folder_path').$image->image }}">
                                                        <img src="{{ config('constants.image_paths.product_gallery_folder_path').$image->image }}" height="170" width="170"
                                                             class="card-img-top" style="width: 100%"
                                                             alt="...">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <button type="submit" class="btn btn-success pull-right">Kaydet</button>
    </form>
@endsection
@section('footer')
    <script src="//cdn.ckeditor.com/4.9.2/standard/ckeditor.js"></script>
    <script>

        $(function () {
            $('select[id*="categories"]').select2({
                placeholder: 'kategori seçiniz'
            });
            $('select#company').select2({
                placeholder: 'firma seçiniz'
            });
            $('select#brand').select2({
                placeholder: 'marka seçiniz'
            });

            $('select[id*="attributes"]').select2({
                placeholder: 'özellik seçiniz'
            });
            $('select[id*="subAttributes"]').select2({
                placeholder: 'alt özellik seçiniz'
            });
            // $('#subAttributes').select2({
            //     placeholder: 'Alt Özellikler seçiniz'
            // });

            var options = {
                language: 'tr',
                filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
                filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
                filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
                filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
            };
            CKEDITOR.replace('editor1', options);
        })
    </script>
    <script src="{{ asset('admin_files/js/productFilter.js') }}"></script>
    <script src="{{ asset('admin_files/js/adminProductDetailVehicles.js') }}"></script>
    <script src="{{ asset('admin_files/js/.js') }}"></script>
@endsection
