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
        'users' => [
            'icon' => 'fa fa-user',
            'permission' => 'Kullanici@listUsers',
            'title' => 'Kullanicilar',
            'routeName' => 'admin.users',
            'active' => $data['users_module']
        ],
        'roles' => [
            'icon' => 'fa fa-users',
            'permission' => 'Role@list',
            'title' => 'Rol Yönetimi',
            'routeName' => 'admin.roles',
            'active' => $data['users_module']
        ],
        'banner' => [
            'icon' => 'fa fa-image',
            'permission' => 'Banner@list',
            'title' => 'Banner',
            'routeName' => 'admin.banners',
            'active' => $data['banner_module']
        ],
        'category' => [
            'icon' => 'fa fa-files-o',
            'permission' => 'Kategori@listCategories',
            'title' => 'Kategoriler',
            'routeName' => 'admin.categories',
            'active' => $data['product_category_module']
        ],
        'products' => [
            'icon' => 'fa fa-list',
            'permission' => 'Urun@listProducts',
            'title' => 'Ürünler',
            'routeName' => 'admin.products',
            'active' => $data['product_module'],
            'subs' => [
                ['icon' => 'fa fa-circle-o',
                    'permission' => 'Urun@listProducts',
                    'title' => 'Ürün Listesi',
                    'routeName' => 'admin.products',
                    'active' => $data['product_module']
                ],
                ['icon' => 'fa fa-circle-o',
                    'permission' => 'UrunOzellik@list',
                    'title' => 'Ürün Özellikleri',
                    'routeName' => 'admin.product.attribute.list',
                    'active' => $data['product_attribute_module']
                ],
                ['icon' => 'fa fa-circle-o',
                    'permission' => 'UrunYorum@list',
                    'title' => 'Ürün Yorumları',
                    'routeName' => 'admin.product.comments.list',
                    'active' => $data['product_comment_module']
                ],
            ]
        ],
        'orders' => [
            'icon' => 'fa fa-shopping-bag',
            'permission' => 'Siparis@list',
            'title' => 'Siparişler',
            'routeName' => 'admin.orders',
            'active' => $data['order_module']
        ],
        'references' => [
            'icon' => 'fa fa-list-alt',
            'permission' => 'Referans@list',
            'title' => 'Referanslar',
            'routeName' => 'admin.reference',
            'active' => $data['reference_module']
        ],
        'content_management' => [
            'icon' => 'fa fa-align-center',
            'permission' => 'IcerikYonetim@list',
            'title' => 'İçerik Yönetim',
            'routeName' => 'admin.content',
            'active' => $data['content_management_module']
        ],
        'gallery' => [
            'icon' => 'fa fa-camera',
            'permission' => 'FotoGallery@list',
            'title' => 'Galeri Yönetim',
            'routeName' => 'admin.gallery',
            'active' => $data['gallery_module']
        ],
        'error_orders' => [
            'icon' => 'fa fa-exclamation',
            'permission' => 'Siparis@iyzicoErrorOrderList',
            'title' => 'Hatalı Siparişler',
            'routeName' => 'admin.orders.iyzico_logs',
            'active' => $data['order_module']
        ],
        'coupons' => [
            'icon' => 'fa fa-tags',
            'permission' => 'Kupon@list',
            'title' => 'Kuponlar',
            'routeName' => 'admin.coupons',
            'active' => $data['coupon_module']
        ],
        'campaign' => [
            'icon' => 'fa fa-percent',
            'permission' => 'Kampanya@list',
            'title' => 'Kampanyalar',
            'routeName' => 'admin.campaigns',
            'active' => $data['campaign_module']
        ],
        'logs' => [
            'icon' => 'fa fa-exclamation',
            'permission' => 'Log@list',
            'title' => 'Hata Yönetimi',
            'routeName' => 'admin.logs',
            'active' => $data['show_logs']
        ],
    ], 1 => [
        'title' => 'Genel',
        'settings' => [
            'icon' => 'fa fa-key',
            'permission' => 'Ayarlar@list',
            'title' => 'Ayarlar',
            'routeName' => 'admin.configs',
            'active' => $data['settings_module']
        ],
        'product_brands' => [
            'icon' => 'fa fa-medium',
            'permission' => 'UrunMarka@list',
            'title' => 'Ürün Markaları',
            'routeName' => 'admin.product.brands.list',
            'active' => $data['product_brands_module']
        ],
        'product_companies' => [
            'icon' => 'fa fa-building',
            'permission' => 'UrunFirma@list',
            'title' => 'Ürün Firmaları',
            'routeName' => 'admin.product.company.list',
            'active' => $data['product_companies_module']
        ],
        'sss' => [
            'icon' => 'fa fa-info',
            'permission' => 'SSS@list',
            'title' => 'Sık Sorulan Sorular',
            'routeName' => 'admin.sss',
            'active' => $data['sss_module']
        ],
    ],

];
return $data;
