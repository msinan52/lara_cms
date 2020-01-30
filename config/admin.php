<?php

// admin constants
$data = [


    'title' => 'CMS Yönetim',
    'short_title' => 'CM',
    // modules can be active or passive
    'settings_module' => true,
    'banner_module' => true,
    'users_module' => true,
    'sss_module' => true,
    'order_module' => true,
    // if product module equal to false all product relations modules can not used
    'product_module' => true,
    'product_comment_module' => true,
    'product_attribute_module' => true,
    'product_category_module' => true,
    'product_brands_module' => false,
    'product_companies_module' => true,
    'campaign_module' => false,
    'coupon_module' => false,
    'company_module' => false,
    'show_logs' => true,


    // index page configs
    'home_page_show_products' => true,
    'home_page_show_orders' => true,
    'home_page_show_order_widgets' => true,


];
$data['menus'] = [
    0 => [
        'title' => 'Modüller',
        'users' => ['fa fa-user', 'Kullanicilar', '/admin/user', $data['users_module']],
        'banner' => ['fa fa-image', 'Banner', '/admin/banner', $data['banner_module']],
        'category' => ['fa fa-files-o', 'Kategoriler', '/admin/category', $data['product_category_module']],
        'products' => ['fa fa-list', 'Ürünler', '/admin/product', $data['product_module'],
            [
                ['fa fa-circle-o', 'Ürün Listesi', 'admin/product', $data['product_module']],
                ['fa fa-circle-o', 'Ürün Özellikleri', 'admin/product/attributes', $data['product_attribute_module']],
                ['fa fa-circle-o', 'Ürün Yorumları', 'admin/product/attributes', $data['product_comment_module']],
            ]
        ],
        'orders' => ['fa fa-shopping-bag', 'Siparişler', '/admin/order', $data['order_module']],
        'error_orders' => ['fa fa-exclamation', 'Hatalı Siparişler', '/admin/order/iyzico-fails', $data['order_module']],
        'coupons' => ['fa fa-tags', 'Kuponlar', '/admin/coupon', $data['coupon_module']],
        'campaign' => ['fa fa-percent', 'Kampanyalar', '/admin/campaigns', $data['campaign_module']],
        'logs' => ['fa fa-exclamation', 'Hata Yönetimi', '/admin/logs', $data['show_logs']],
    ], 1 => [
        'title' => 'Genel',
        'settings' => ['fa fa-key', 'Ayarlar', '/admin/configs', $data['settings_module']],
        'product_brands' => ['fa fa-medium', 'Ürüm Markaları', '/admin/product/brands', $data['product_brands_module']],
        'product_companies' => ['fa fa-building', 'Ürün Firmaları', '/admin/product/company', $data['product_companies_module']],
        'sss' => ['fa fa-info', 'Sık Sorulan Sorular', '/admin/sss', $data['sss_module']],
    ],

];
return $data;
