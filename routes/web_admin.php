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
    Route::get('/clear_cache', 'AnasayfaController@cacheClear')->name('admin.clearCache');
    Route::group(['middleware' => ['admin', 'admin.module', 'role']], function () {
        Route::get('home', 'AnasayfaController@index')->name('admin.home_page');
        Route::get('contacts', 'AnasayfaController@contacts')->name('admin.contacts');
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

        //----- Admin/References/..
        Route::group(['prefix' => 'references/'], function () {
            Route::get('/', 'ReferansController@list')->name('admin.reference');
            Route::get('new', 'ReferansController@newOrEditForm')->name('admin.reference.new');
            Route::get('edit/{id}', 'ReferansController@newOrEditForm')->name('admin.reference.edit');
            Route::post('save/{id}', 'ReferansController@save')->name('admin.reference.save');
            Route::get('delete/{id}', 'ReferansController@delete')->name('admin.reference.delete');
        });
        //----- Admin/PhotoGallery/..
        Route::group(['prefix' => 'photo-gallery/'], function () {
            Route::get('/', 'FotoGalleryController@list')->name('admin.gallery');
            Route::get('new', 'FotoGalleryController@newOrEditForm')->name('admin.gallery.new');
            Route::get('edit/{id}', 'FotoGalleryController@newOrEditForm')->name('admin.gallery.edit');
            Route::post('save/{id}', 'FotoGalleryController@save')->name('admin.gallery.save');
            Route::get('delete/{id}', 'FotoGalleryController@delete')->name('admin.gallery.delete');
            Route::get('deleteGalleryImage/{id}', 'FotoGalleryController@deleteGalleryImage')->name('admin.gallery.image.delete');
        });
        //----- Admin/Content/..
        Route::group(['prefix' => 'content/'], function () {
            Route::get('/', 'IcerikYonetimController@list')->name('admin.content');
            Route::get('new', 'IcerikYonetimController@newOrEditForm')->name('admin.content.new');
            Route::get('edit/{id}', 'IcerikYonetimController@newOrEditForm')->name('admin.content.edit');
            Route::post('save/{id}', 'IcerikYonetimController@save')->name('admin.content.save');
            Route::get('delete/{id}', 'IcerikYonetimController@delete')->name('admin.content.delete');
        });
        //----- Admin/Roles/..
        Route::group(['prefix' => 'roles/'], function () {
            Route::get('/', 'RoleController@list')->name('admin.roles');
            Route::get('new', 'RoleController@newOrEditForm')->name('admin.role.new');
            Route::get('edit/{id}', 'RoleController@newOrEditForm')->name('admin.role.edit');
            Route::post('save/{id}', 'RoleController@save')->name('admin.role.save');
            Route::get('delete/{id}', 'RoleController@delete')->name('admin.role.delete');
        });
        //----- Admin/Blog/..
        Route::group(['prefix' => 'blog'], function () {
            Route::get('/', 'BlogController@list')->name('admin.blog');
            Route::get('new', 'BlogController@newOrEditForm')->name('admin.blog.new');
            Route::get('edit/{id}', 'BlogController@newOrEditForm')->name('admin.blog.edit');
            Route::post('save/{id}', 'BlogController@save')->name('admin.blog.save');
            Route::get('delete/{id}', 'BlogController@delete')->name('admin.blog.delete');
        });
        //----- Admin/OurTeam/..
        Route::group(['prefix' => 'our-team/'], function () {
            Route::get('/', 'OurTeamController@list')->name('admin.our_team');
            Route::get('new', 'OurTeamController@newOrEditForm')->name('admin.our_team.new');
            Route::get('edit/{id}', 'OurTeamController@newOrEditForm')->name('admin.our_team.edit');
            Route::post('save/{id}', 'OurTeamController@save')->name('admin.our_team.save');
            Route::get('delete/{id}', 'OurTeamController@delete')->name('admin.our_team.delete');
        });
        //----- Admin/EBulten/..
        Route::group(['prefix' => 'ebulten/'], function () {
            Route::get('/', 'EBultenController@list')->name('admin.ebulten');
            Route::get('delete/{id}', 'EBultenController@delete')->name('admin.ebulten.delete');
        });
    });


});


Route::get('/home', 'AnasayfaController@index')->name('home');
