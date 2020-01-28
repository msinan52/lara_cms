<header class="main-header">
    <!-- Logo -->
    <a href="{{ route('admin.home_page') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">{{ config('admin.short_title') }}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">{{ config('admin.title') }}</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                @if(config('admin.product_comment_module'))
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success">{{ $unreadCommentsCount }}</span>
                    </a>

                        <ul class="dropdown-menu">
                            <li class="header">Okunmamış {{ $unreadCommentsCount }} yeni yorum var</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    @foreach($lastUnreadComments as $uc)
                                        <li>
                                            <a href="{{ route('admin.product.comments.edit',$uc->id) }}">
                                                <i class="fa fa-comment text-aqua"></i> {{ $uc->message }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                            <li class="footer"><a href="{{ route('admin.product.comments.list') }}">Tümünü Göster</a></li>
                        </ul>

                </li>
                @endif

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="/admin_files/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{ Auth::guard('admin')->getUser()->getFullName() }}</span>

                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="/admin_files/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">

                            <p>
                                {{ Auth::guard('admin')->getUser()->getFullName() }}
                                <small>Kayıt : {{ Auth::guard('admin')->getUser()->created_at }}</small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <li class="user-body">
                            <div class="row">
                                <div class="col-xs-4 text-center">
                                    <a target="_blank" href="{{ route('homeView') }}">Siteyi Görüntüle</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="{{ route('admin.clearCache') }}">Önbellek Temizle</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="#">Friends</a>
                                </div>
                            </div>
                            <!-- /.row -->
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('admin.user.edit',Auth::guard('admin')->getUser()->id) }}"
                                   class="btn btn-default btn-flat">Profil</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ route('admin.logout') }}" class="btn btn-default btn-flat">Çıkış yap</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
            </ul>
        </div>
    </nav>
</header>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/admin_files/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Auth::guard('admin')->getUser()->getFullName() }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">Modüller</li>

            <li>
                <a href="{{ route('admin.users') }}">
                    <i class="fa fa-user"></i>
                    <span>Kullanıcılar</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.banners') }}">
                    <i class="fa fa-image"></i>
                    <span>Banner</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories') }}">
                    <i class="fa fa-files-o"></i>
                    <span>Kategoriler</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li class="treeview">
                <a href="{{ route('admin.products') }}">
                    <i class="fa fa-list"></i>
                    <span>Ürünler</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('admin.products') }}"><i class="fa fa-circle-o"></i> Ürün Listesi</a></li>

                    <li><a href="{{ route('admin.product.attribute.list') }}"><i class="fa fa-circle-o"></i> Ürün
                            Özellikler</a></li>
                    <li><a href="{{ route('admin.product.comments.list') }}"><i class="fa fa-circle-o"></i> Ürün Yorumları</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('admin.orders') }}">
                    <i class="fa fa-shopping-bag"></i>
                    <span>Siparişler</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.iyzico_logs') }}">
                    <i class="fa fa-shopping-bag"></i> <i class="fa fa-exclamation"></i>
                    <span>Hatalı Siparişler</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.coupons') }}">
                    <i class="fa fa-tags"></i>
                    <span>Kuponlar</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.campaigns') }}">
                    <i class="fa fa-percent"></i>
                    <span>Kampanyalar</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.logs') }}">
                    <i class="fa fa-exclamation"></i>
                    <span>Hata Yönetimi</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li class="header">Genel</li>
            <li>
                <a href="{{ route('admin.configs') }}">
                    <i class="fa fa-key"></i>
                    <span>Ayarlar</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.product.brands.list') }}">
                    <i class="fa fa-medium"></i>
                    <span>Ürün Markaları</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.product.company.list') }}">
                    <i class="fa fa-building"></i>
                    <span>Tedarikçi Firmalar</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.sss') }}">
                    <i class="fa fa-info"></i>
                    <span>Sık Sorulan Sorular</span>
                    <span class="pull-right-container">
            </span>
                </a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
