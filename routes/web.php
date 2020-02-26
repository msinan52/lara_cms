<?php

//social auth
Route::get('/redirect/{service}', 'SocialAuthController@redirect');
Route::get('/callback/{service}', 'SocialAuthController@callback');


Route::get('test', 'TestController@index');
Route::get('iletisim', 'IletisimController@index')->name('iletisim');
Route::post('sss', 'SSSController@list')->name('sss');
Route::post('referanslar', 'ReferenceController@list')->name('referanslar');
Route::post('{slug}', 'IcerikYonetimController@detail')->name('content.detail');
Route::post('referanslar/{slug}', 'ReferenceController@detail')->name('referanslar.detail');
Route::post('galeri', 'GaleriController@detail')->name('gallery.list');
Route::get('haberler','BlogController@list')->name('blog.list');
Route::get('haberler/{slug}','BlogController@detail')->name('blog.detail');
Route::post('galeri/{slug}', 'GaleriController@detail')->name('gallery.detail');
Route::post('iletisim', 'IletisimController@sendMail')->name('iletisim.sendMail');
Route::post('createBulten', 'EBultenController@createEBulten')->name('ebulten.create');

Route::get('/', 'AnasayfaController@index')->name('homeView');
Route::get('/sitemap.xml', 'AnasayfaController@sitemap');
Route::get('/ara', 'AramaController@ara')->name('searchView');
Route::get('/searchPageFilter', 'AramaController@searchPageFilterWithAjax');
Route::get('/headerSearchBarOnChangeWithAjax', 'AramaController@headerSearchBarOnChangeWithAjax');


//------------Ajax Routes --------------------
Route::get('getProductVariantPriceAndQtyWithAjax', 'UrunController@getProductVariantPriceAndQtyWithAjax')->name('getProductVariantPriceAndQtyWithAjax');
Route::get('productFilterWithAjax', 'KategoriController@productFilterWithAjax')->name('productFilterWithAjax');


//------------- Basket Routes --------------------

Route::group(['prefix' => 'sepet'], function () {
    Route::get('', 'SepetController@index')->name('basketView');
    Route::post('/ekle', 'SepetController@itemAddToBasket')->name('basket.add');
    Route::delete('/sil/{rowId}', 'SepetController@removeItemFromBasket')->name('basket.remove');
    Route::delete('/tumunu-kaldir', 'SepetController@removeAllItems')->name('basket.removeAllItems');
    Route::patch('/guncelle/{rowId}', 'SepetController@updateBasket')->name('basket.updateBasket');

    // Ajax
    Route::post('/addToBasket', 'SepetController@itemAddToBasketWithAjax')->name('basket.add.ajax');
    Route::post('/removeBasketItem', 'SepetController@removeItemFromBasketWithAjax')->name('basket.remove.ajax');
    Route::post('/multiple-update', 'SepetController@updateMultipleBasketItem');
});

//------------ Odeme Controller -------------------
Route::group(['prefix' => 'odeme/', 'middleware' => 'auth'], function () {
    Route::get('adres', 'AdresController@address')->name('odeme.adres');
    Route::get('review', 'OdemeController@index')->name('odemeView');
    Route::post('review', 'OdemeController@payment');
    Route::get('taksit-getir', 'OdemeController@getIyzicoInstallmentCount')->name('odgetIyzicoInstallmentCount');
    Route::post('setDefaultAddress/{id}', 'AdresController@setDefaultAddress')->name('odeme.address.setDefault');

    Route::get('threeDSecurityRequest', 'OdemeController@threeDSecurityRequest')->name('odeme.threeDSecurityRequest');
    Route::post('threeDSecurityResponse', 'OdemeController@threeDSecurityResponse')->name('odeme.threeDSecurityResponse');
});


//---------- User Routes ----------------------
Route::group(['prefix' => 'kullanici'], function () {
    Route::get('/giris', 'KullaniciController@loginForm')->name('kullaniciLoginView');
    Route::post('/giris', 'KullaniciController@login');
    Route::post('/cikis', 'KullaniciController@logout')->name('kullaniciLogoutView');
    Route::get('/kayit', 'KullaniciController@registerForm')->name('kullaniciRegisterView');
    Route::post('/kayit', 'KullaniciController@register');
    Route::get('/aktiflestir/{activation_code}', 'KullaniciController@activateUser')->name('kullaniciAktiflestir');

    Route::group(['middleware' => 'auth'], function () {
        Route::get('siparisler', 'SiparisController@index')->name('siparisView');
        Route::get('siparisler/{siparisId}', 'SiparisController@siparisDetay')->name('siparisDetayView');
        Route::get('adresler', 'AccountController@addressView')->name('kullanici.address');
        Route::get('adres/{id}', 'AccountController@addressDetail')->name('kullanici.address.edit');
        Route::get('adres/{id}/{redirectUrl}', 'AccountController@addressDetail')->name('kullanici.address.editWithRedirect');
        Route::post('adres', 'AccountController@addressSave')->name('kullanici.address.save');
        Route::post('setDefaultAddress/{id}', 'AccountController@setDefaultAddress')->name('kullanici.address.setDefault');
        Route::post('setDefaultInvoiceAddress/{id}', 'AccountController@setDefaultInvoiceAddress')->name('kullanici.address.setDefaultInvoiceAddress');
        Route::post('setDefaultInvoiceAddress/{id}/{redirectRouteName}', 'AccountController@setDefaultInvoiceAddress')->name('kullanici.address.setDefaultInvoiceAddressWithRedirect');
        Route::get('profil', 'AccountController@userDetail')->name('kullanici.user.detail');
        Route::post('profil', 'AccountController@userDetailSave')->name('kullanici.user.detail');
        Route::get('hesabim', 'AccountController@dashboard')->name('kullanici.user.dashboard');
        Route::get('favorilerim', 'FavoriController@list')->name('kullanici.favorites.list');
        Route::get('hata-kodlari', 'AccountController@userLogErrors')->name('kullanici.user.userLogErrors');
    });
});
Route::group(['prefix' => 'cityTownService'], function () {
    Route::get('/getTownsByCityId/{cityId}', 'CityTownController@getTownsByCityId')->name('cityTownService.getTownsByCityId');
});

//Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');

// Coupon code
Route::post('kupon/uygula', 'KuponController@applyCoupon')->name('coupon.apply');

// Favorites Route
Route::post('favoriler/ekle', 'FavoriController@addToFavorites');
Route::get('favoriler/listele', 'FavoriController@listAnonimUserFavorites')->name('favoriler.anonimList');


// campaigns Route
Route::get('kampanyalar', 'KampanyaController@list')->name('campaigns.list');
Route::get('kampanyalar/{slug}', 'KampanyaController@detail')->name('campaigns.detail');
Route::get('kampanyalar/{slug}/{category}', 'KampanyaController@detail')->name('campaigns.detail');
Route::get('campaignsFilterWithAjax', 'KampanyaController@campaignsFilterWithAjax')->name('campaigns.filterWithAjax');

//=================Ajax Routes ==============
Route::get('getActiveBrands', 'UrunController@getActiveProductBrandsJson');
//---------- Car Brand Routes
Route::group(['prefix' => 'brands'], function () {
    Route::get('getAllActiveCarBrands', 'AracMarkaController@getAllActiveCarBrands');
    Route::get('getModelsByBrandId/{id}', 'AracMarkaController@getModelsByMarkaId');
});
Route::get('lang/{locale}','AnasayfaController@setLanguage')->name('home.setLocale');
// ------------Product Routes ----------------
Route::get('{urunSlug}', 'UrunController@detail')->name('productDetail');
Route::get('urun/quickView/{slug}', 'UrunController@quickView')->name('product.quickView');
Route::get('kategori/{categorySlug}', 'KategoriController@index')->name('categoryDetail');
Route::post('urun/yorum-ekle', 'UrunController@addNewComment')->name('product.comments.add')->middleware('auth');





