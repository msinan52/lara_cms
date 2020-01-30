<?php

// admin constants
$data = [
    'title' => 'CMS Yönetim',
    'short_title' => 'CM',
    'creator' => 'NeAjans',
    'creator_link' => 'http://google.com',
    'version' => 'v1.0.0',
    // modules can be active or passive
    'settings_module' => true,
    'banner_module' => true,
    'users_module' => true,
    'content_management_module' => true,
    'sss_module' => true,
    'order_module' => true,
    'reference_module' => true,
    'gallery_module' => true,
    // if product module equal to false all product relations modules can not used
    'product_module' => true,
    'product_comment_module' => true,
    'product_attribute_module' => true,
    'product_category_module' => true,
    'product_brands_module' => true,
    'product_companies_module' => true,
    'campaign_module' => true,
    'coupon_module' => true,
    'show_logs' => true,
    'use_album_gallery' => true,
    // some product features
    'product_feature' => true,
    'product_variant' => true,
    'product_attribute' => true, // product detail
    'product_gallery' => true,
    'product_auto_code' => false,
    'product_qty' => true,


    // index page configs
    'home_page_show_products' => true,
    'home_page_show_orders' => true,
    'home_page_show_order_widgets' => true,
    // admin account
    'username' => 'm4urat@gmail.com',
    'password' => '141277kk'


];
$data['menus'] = [
    0 => [
        'title' => 'Modüller',
        'users' => ['fa fa-user', 'Kullanicilar', 'admin.users', $data['users_module']],
        'roles' => ['fa fa-users', 'Rol Yönetimi', 'admin.roles', $data['users_module']],
        'banner' => ['fa fa-image', 'Banner', 'admin.banners', $data['banner_module']],
        'category' => ['fa fa-files-o', 'Kategoriler', 'admin.categories', $data['product_category_module']],
        'products' => ['fa fa-list', 'Ürünler', 'admin.products', $data['product_module'],
            [
                ['fa fa-circle-o', 'Ürün Listesi', 'admin.products', $data['product_module']],
                ['fa fa-circle-o', 'Ürün Özellikleri', 'admin.product.attribute.list', $data['product_attribute_module']],
                ['fa fa-circle-o', 'Ürün Yorumları', 'admin.product.comment.list', $data['product_comment_module']],
            ]
        ],
        'orders' => ['fa fa-shopping-bag', 'Siparişler', 'admin.orders', $data['order_module']],
        'references' => ['fa fa-list-alt', 'Referanslar', 'admin.reference', $data['reference_module']],
        'content_management' => ['fa fa-align-center', 'İçerik Yönetim', 'admin.content', $data['content_management_module']],
        'gallery' => ['fa fa-camera', 'Galeri', 'admin.gallery', $data['gallery_module']],
        'error_orders' => ['fa fa-exclamation', 'Hatalı Siparişler', 'admin.orders.iyzico_logs', $data['order_module']],
        'coupons' => ['fa fa-tags', 'Kuponlar', 'admin.coupons', $data['coupon_module']],
        'campaign' => ['fa fa-percent', 'Kampanyalar', 'admin.campaigns', $data['campaign_module']],
        'logs' => ['fa fa-exclamation', 'Hata Yönetimi', 'admin.logs', $data['show_logs']],
    ], 1 => [
        'title' => 'Genel',
        'settings' => ['fa fa-key', 'Ayarlar', 'admin.configs', $data['settings_module']],
        'product_brands' => ['fa fa-medium', 'Ürün Markaları', 'admin.product.brands.list', $data['product_brands_module']],
        'product_companies' => ['fa fa-building', 'Ürün Firmaları', 'admin.product.company.list', $data['product_companies_module']],
        'sss' => ['fa fa-info', 'Sık Sorulan Sorular', 'admin.sss', $data['sss_module']],
    ],

];
return $data;
