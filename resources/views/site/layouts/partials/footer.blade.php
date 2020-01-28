<footer class="footer">
    <div class="footer-middle">
        <div class="container">
            <div class="footer-ribbon">
                İletişimde Kal
            </div><!-- End .footer-ribbon -->
            <div class="row">
                <div class="col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">Iletisim</h4>
                        <ul class="contact-info">
                            <li>
                                <span class="contact-info-label">Adres:</span>{{$site->adres}}
                            </li>
                            <li>
                                <span class="contact-info-label">İletişim:</span><a href="tel:">{{$site->phone}}</a>
                            </li>
                            <li>
                                <span class="contact-info-label">Email:</span> <a href="mailto:mail@example.com">{{$site->email}}</a>
                            </li>
                            <li>
                                <span class="contact-info-label">Çalışma Gün/Saatleri:</span>
                                Pazartesi - Pazar / 9:00 - 22:00
                            </li>
                        </ul>
                        <div class="social-icons">
                            <a href="{{ $site->facebook }}" class="social-icon" target="_blank"><i class="icon-facebook"></i></a>
                            <a href="{{ $site->twitter }}" class="social-icon" target="_blank"><i class="icon-twitter"></i></a>
                            <a href="{{ $site->instagram}}" class="social-icon" target="_blank"><i class="icon-instagram"></i></a>
                        </div><!-- End .social-icons -->
                    </div><!-- End .widget -->
                </div><!-- End .col-lg-3 -->

                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="widget">
                                <h4 class="widget-title">Hesabım</h4>

                                <div class="row">
                                    <div class="col-sm-6 col-md-5">
                                        <ul class="links">
                                            <li><a href="{{route('kullaniciLoginView')}}">Giriş</a></li>
                                            <li><a href="{{route('siparisView')}}">Siparislerim</a></li>
                                            <li><a href="{{route('kullanici.address')}}">Adreslerim</a></li>
                                            <li><a href="{{ route('basketView') }}">Sepet</a></li>
                                            <li><a href="{{ auth()->check() ? route('kullanici.favorites.list') : route('favoriler.anonimList') }}">Favorilerim</a></li>
                                            <li><a href="{{route('kullanici.user.detail')}}">Hesabım</a></li>
                                        </ul>
                                    </div><!-- End .col-sm-6 -->
                                </div><!-- End .row -->
                            </div><!-- End .widget -->
                        </div><!-- End .col-md-5 -->

                        <div class="col-md-5">
                            <div class="widget">
                                <h4 class="widget-title">Site</h4>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <ul class="links">
                                            <li><a href="{{ route('sss') }}">Sık Sorulan Sorular</a></li>
                                            <li><a href="{{ route('iletisim') }}">İletişim</a></li>
                                            <li><a href="{{ route('siparisView') }}">Sipariş Takip</a></li>
                                            <li><a href="{{ route('campaigns.list') }}">Kampanyalar</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-6">
                                        <ul class="links">
                                            <li><a href="{{ route('kullaniciRegisterView') }}">Üye Ol</a></li>
                                            <li><a href="{{ route('homeView') }}/sitemap.xml">Site Haritası</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget">
                                <h4 class="widget-title">Kategoriler</h4>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <ul class="links">
                                            @foreach($cacheCategories as $category)
                                                <li><a href="{{ route('categoryDetail',$category->slug) }}" title="{{ $category->title }}">{{ $category->title }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End .row -->
                </div><!-- End .col-lg-9 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .footer-middle -->

    <div class="container">
        <div class="footer-bottom">
            <p class="footer-copyright">{{$site->title}}. &copy; {{date('Y')}}. Tüm hakları saklıdır</p>

            <img src="/site/assets/images/payments.png" alt="payment methods" class="footer-payments">
        </div><!-- End .footer-bottom -->
    </div><!-- End .container -->
</footer><!-- End .footer -->
