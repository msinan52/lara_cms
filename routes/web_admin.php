<?php

/*
|--------------------------------------------------------------------------
| Web Admin Routes
|--------------------------------------------------------------------------
|
*/

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::redirect('', '/admin/giris/');
    Route::match(['get', 'post'], 'giris', 'KullaniciController@login')->name('admin.login');
    Route::get('/clear_cache','AnasayfaController@cacheClear')->name('admin.clearCache');
    Route::group(['middleware' => 'admin'], function () {
        Route::get('home', 'AnasayfaController@index')->name('admin.home_page');
        Route::get('cikis', 'KullaniciController@logout')->name('admin.logout');

        //----- Admin/User/..
        Route::group(['prefix' => 'user/'], function () {
            Route::get('/', 'KullaniciController@listUsers')->name('admin.users');
            Route::get('new', 'KullaniciController@newOrEditUser')->name('admin.user.new');
            Route::get('edit/{user_id}', 'KullaniciController@newOrEditUser')->name('admin.user.edit');
            Route::post('save/{user_id}', 'KullaniciController@saveUser')->name('admin.user.save');
            Route::get('delete/{user_id}', 'KullaniciController@deleteUser')->name('admin.user.delete');
        });

        //----- Admin/category/..
        Route::group(['prefix' => 'category/'], function () {
            Route::get('/', 'KategoriController@listCategories')->name('admin.categories');
            Route::get('new', 'KategoriController@newOrEditCategory')->name('admin.category.new');
            Route::get('edit/{user_id}', 'KategoriController@newOrEditCategory')->name('admin.category.edit');
            Route::post('save/{user_id}', 'KategoriController@saveCategory')->name('admin.category.save');
            Route::get('delete/{user_id}', 'KategoriController@deleteCategory')->name('admin.category.delete');
        });

        //----- Admin/Products/..
        Route::group(['prefix' => 'product/'], function () {
            Route::get('/', 'UrunController@listProducts')->name('admin.products');
            Route::get('new', 'UrunController@newOrEditProduct')->name('admin.product.new');
            Route::get('edit/{product_id}', 'UrunController@newOrEditProduct')->name('admin.product.edit');
            Route::post('save/{product_id}', 'UrunController@saveProduct')->name('admin.product.save');
            Route::get('delete/{product_id}', 'UrunController@deleteProduct')->name('admin.product.delete');

            Route::get('getAllProductsForSearch', 'UrunController@getAllProductsForSearchAjax');
            Route::get('getSubAttributesByAttributeId/{id}', 'UrunController@getSubAttributesByAttributeId')->name('getSubAttributesByAttributeId');
            Route::get('getAllProductAttributes', 'UrunController@getAllProductAttributes')->name('getAllProductAttributes');
            Route::get('deleteProductDetailById/{id}', 'UrunController@deleteProductDetailById')->name('deleteProductDetailById');
            Route::get('getProductDetailWithSubAttributes/{product_id}', 'UrunController@getProductDetailWithSubAttributes')->name('getProductDetailWithSubAttributes');
            Route::get('deleteProductVariant/{variant_id}', 'UrunController@deleteProductVariant')->name('deleteProductVariant');
            Route::get('deleteProductImage/{id}', 'UrunController@deleteProductImage')->name('deleteProductImage');

            Route::group(['prefix' => 'attributes/'], function () {
                Route::get('/', 'UrunOzellikController@list')->name('admin.product.attribute.list');
                Route::get('new', 'UrunOzellikController@detail')->name('admin.product.attribute.new');
                Route::get('edit/{id}', 'UrunOzellikController@detail')->name('admin.product.attribute.edit');
                Route::post('save/{id}', 'UrunOzellikController@save')->name('admin.product.attribute.save');
                Route::get('delete/{id}', 'UrunOzellikController@delete')->name('admin.product.attribute.delete');

                Route::post('deleteSubAttribute/{id}', 'UrunOzellikController@deleteSubAttribute')->name('admin.product.attribute.subAttribute.delete');
            });

            Route::group(['prefix' => 'comments/'], function () {
                Route::get('/', 'UrunYorumController@list')->name('admin.product.comments.list');
                Route::get('new', 'UrunYorumController@detail')->name('admin.product.comments.new');
                Route::get('edit/{id}', 'UrunYorumController@detail')->name('admin.product.comments.edit');
                Route::post('save/{id}', 'UrunYorumController@save')->name('admin.product.comments.save');
                Route::get('delete/{id}', 'UrunYorumController@delete')->name('admin.product.comments.delete');
            });
            Route::group(['prefix' => 'brands/'], function () {
                Route::get('/', 'UrunMarkaController@list')->name('admin.product.brands.list');
                Route::get('new', 'UrunMarkaController@detail')->name('admin.product.brands.new');
                Route::get('edit/{id}', 'UrunMarkaController@detail')->name('admin.product.brands.edit');
                Route::post('save/{id}', 'UrunMarkaController@save')->name('admin.product.brands.save');
                Route::get('delete/{id}', 'UrunMarkaController@delete')->name('admin.product.brands.delete');
            });
            Route::group(['prefix' => 'company/'], function () {
                Route::get('/', 'UrunFirmaController@list')->name('admin.product.company.list');
                Route::get('new', 'UrunFirmaController@detail')->name('admin.product.company.new');
                Route::get('edit/{id}', 'UrunFirmaController@detail')->name('admin.product.company.edit');
                Route::post('save/{id}', 'UrunFirmaController@save')->name('admin.product.company.save');
                Route::get('delete/{id}', 'UrunFirmaController@delete')->name('admin.product.company.delete');
            });

        });

        //----- Admin/Orders/..
        Route::group(['prefix' => 'order/'], function () {
            Route::get('/', 'SiparisController@list')->name('admin.orders');
            Route::get('/iyzico-fails', 'SiparisController@iyzicoErrorOrderList')->name('admin.orders.iyzico_logs');
            Route::get('/iyzico-fails/{id}', 'SiparisController@iyzicoErrorOrderDetail')->name('admin.orders.iyzico_logs_detail');
            Route::get('edit/{id}', 'SiparisController@newOrEditOrder')->name('admin.order.edit');
            Route::post('save/{id}', 'SiparisController@saveOrder')->name('admin.order.save');
            Route::get('delete/{id}', 'SiparisController@deleteOrder')->name('admin.order.delete');

            Route::get('edit/{id}/invoice', 'SiparisController@invoiceDetail')->name('admin.order.invoice');
        });

        //----- Admin/Banners/..
        Route::group(['prefix' => 'banner/'], function () {
            Route::get('/', 'BannerController@list')->name('admin.banners');
            Route::get('new', 'BannerController@newOrEditForm')->name('admin.banners.new');
            Route::get('edit/{id}', 'BannerController@newOrEditForm')->name('admin.banners.edit');
            Route::post('save/{id}', 'BannerController@save')->name('admin.banners.save');
            Route::get('delete/{id}', 'BannerController@delete')->name('admin.banners.delete');
        });

        //----- Admin/Configs/..
        Route::group(['prefix' => 'configs/'], function () {
            Route::get('/', 'AyarlarController@list')->name('admin.configs');
            Route::get('new', 'AyarlarController@newOrEditForm')->name('admin.config.new');
            Route::get('edit/{id}', 'AyarlarController@newOrEditForm')->name('admin.config.edit');
            Route::post('save/{id}', 'AyarlarController@save')->name('admin.config.save');
            Route::get('delete/{id}', 'AyarlarController@delete')->name('admin.config.delete');
        });

        //----- Admin/Logs/..
        Route::group(['prefix' => 'logs/'], function () {
            Route::get('/', 'LogController@list')->name('admin.logs');
            Route::get('show/{id}', 'LogController@show')->name('admin.log.show');
            Route::get('delete/{id}', 'LogController@delete')->name('admin.log.delete');
            Route::get('deleteAll', 'LogController@deleteAll')->name('admin.log.delete_all');
        });

        //----- Admin/Coupons/..
        Route::group(['prefix' => 'coupon/'], function () {
            Route::get('/', 'KuponController@list')->name('admin.coupons');
            Route::get('new', 'KuponController@newOrEditForm')->name('admin.coupons.new');
            Route::get('edit/{id}', 'KuponController@newOrEditForm')->name('admin.coupons.edit');
            Route::post('save/{id}', 'KuponController@save')->name('admin.coupons.save');
            Route::get('delete/{id}', 'KuponController@delete')->name('admin.coupons.delete');
        });

        //----- Admin/Campaigns/..
        Route::group(['prefix' => 'campaigns/'], function () {
            Route::get('/', 'KampanyaController@list')->name('admin.campaigns');
            Route::get('new', 'KampanyaController@newOrEditForm')->name('admin.campaigns.new');
            Route::get('edit/{id}', 'KampanyaController@newOrEditForm')->name('admin.campaigns.edit');
            Route::post('save/{id}', 'KampanyaController@save')->name('admin.campaigns.save');
            Route::get('delete/{id}', 'KampanyaController@delete')->name('admin.campaigns.delete');
        });

        //----- Admin/Coupons/..
        Route::group(['prefix' => 'sss/'], function () {
            Route::get('/', 'SSSController@list')->name('admin.sss');
            Route::get('new', 'SSSController@newOrEditForm')->name('admin.sss.new');
            Route::get('edit/{id}', 'SSSController@newOrEditForm')->name('admin.sss.edit');
            Route::post('save/{id}', 'SSSController@save')->name('admin.sss.save');
            Route::get('delete/{id}', 'SSSController@delete')->name('admin.sss.delete');
        });
    });


});


Route::get('/home', 'AnasayfaController@index')->name('home');
