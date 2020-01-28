<div class="container bg-white padding-left-lg pt-5 p-md-5">
    <div class="bg-content">
        <div class="alert alert-danger" role="alert" style="display: none">
            <span id="alertMessage"></span>
        </div>
        <form action="#" id="adresDetailForm" method="post" onsubmit="return false">
            @csrf
            <input type="hidden" value="{{$address->id}}" id="addressId">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group required-field">
                                <label for="acc-name">Adres İsmi</label>
                                <input type="text" class="form-control" name="title" id="title" required="" placeholder="Örnek : Evim" value="{{$address->title}}">
                            </div><!-- End .form-group -->
                        </div><!-- End .col-md-4 -->
                        <div class="col-md-4">
                            <div class="form-group required-field">
                                <label for="acc-name">Adınız</label>
                                <input type="text" class="form-control" name="name" id="name" required="" value="{{$address->name}}">
                            </div><!-- End .form-group -->
                        </div><!-- End .col-md-4 -->

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="acc-mname">Soyadınız</label>
                                <input type="text" class="form-control" name="surname" id="surname" value="{{$address->surname}}">
                            </div><!-- End .form-group -->
                        </div><!-- End .col-md-4 -->

                        <div class="col-md-3">
                            <label>Telefon </label>
                            <div class="form-control-tooltip">
                                <input type="tel" class="form-control" required="" id="phone" value="{{$address->phone}}" maxlength="10" placeholder="5xx.....">
                                <span class="input-tooltip" data-toggle="tooltip" title="" data-placement="right" data-original-title="For delivery questions."><i
                                        class="icon-question-circle"></i></span>
                            </div><!-- End .form-control-tooltip -->
                        </div><!-- End .form-group -->
                        <div class="col-md-3">
                            <label>İl</label>
                            <div class="select-custom">

                                <select class="form-control" name="city" id="city" onchange="citySelectOnChange(this)" required>
                                    <option value="">Seçiniz</option>
                                    @if(!is_null($address->id))
                                        @foreach($cities as $city)
                                            <option value="{{$city->id}}" {{ $city->id == $address->city ? 'selected' :''}}>{{$city->title}}</option>
                                        @endforeach
                                    @else
                                        @foreach($cities as $city)
                                            <option value="{{$city->id}}">{{$city->title}}</option>
                                        @endforeach
                                    @endif
                                </select>

                            </div><!-- End .select-custom -->
                        </div><!-- End .form-group -->
                        <div class="col-md-3">
                            <label>ilçe</label>
                            <div class="select-custom">
                                <select class="form-control" name="town" id="town" onchange="townSelectOnChange(this)" required>
                                    <option value="">Seçiniz</option>
                                    @foreach($towns as $town)
                                        <option value="{{$town->id}}" {{ $town->id == $address->town ? 'selected' :'' }}>{{$town->title}}</option>
                                    @endforeach
                                </select>
                            </div><!-- End .select-custom -->
                        </div><!-- End .form-group -->
                        <div class="col-md-3">
                            <label>Adres Tipi</label>
                            <div class="select-custom">
                                <select class="form-control" name="type" required id="type">
                                    <option
                                        value="{{\App\Models\KullaniciAdres::TYPE_DELIVERY}}" {{ request()->get('type',1) == \App\Models\KullaniciAdres::TYPE_DELIVERY ? 'selected' : '' }}>
                                        Teslimat Adresi
                                    </option>
                                    <option
                                        value="{{\App\Models\KullaniciAdres::TYPE_INVOICE}}" {{ request()->get('type',1) == \App\Models\KullaniciAdres::TYPE_INVOICE ? 'selected' : '' }}>
                                        Fatura Adresi
                                    </option>
                                </select>
                            </div><!-- End .select-custom -->
                        </div><!-- End .form-group -->
                        <div class="col-md-9">
                            <label>Adres </label>
                            <textarea type="text" class="form-control" required="" id="adres">{{$address->adres}}</textarea>
                        </div>

                    </div><!-- End .row -->
                    <div class="form-row">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary" onclick="return saveAdresDetail('{{$redirectUrl.'?type='.request()->get('type',1)}}')">Kaydet</button>
                        </div>
                    </div>
                </div><!-- End .col-sm-11 -->
            </div>
        </form>
    </div>
</div>
<script src="/site/assets/js/jquery.min.js"></script>
<script src="/js/userAdresDetailPage.js"></script>

