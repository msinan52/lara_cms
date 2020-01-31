<?php

namespace App\Providers;

use App\Kullanici;
use App\Listeners\LoggingListener;
use App\Models\Auth\Role;
use App\Models\Ayar;
use App\Models\Kategori;
use App\Models\Urun;
use App\Models\UrunYorum;
use App\Observers\UrunObserver;
use App\Repositories\Concrete\AnotherOrm\MSOrderRepository;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Concrete\Eloquent\DbOrderRepository;
use App\Repositories\Concrete\Eloquent\ElKategoriDal;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\KategoriInterface;
use http\Client\Curl\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['site.*'], function ($view) {
            $site = Ayar::getCache();
            $cacheCategories = Kategori::getCache();
            $view->with(compact('site', 'cacheCategories'));
        });
        View::composer(['admin.*'], function ($view) {
            $unreadCommentsCount = UrunYorum::where(['is_read' => 0])->count();
            $lastUnreadComments = UrunYorum::where(['is_read' => 0])->get();
            $menus = $this->_getAdminMenus();

            $view->with(compact('lastUnreadComments', 'unreadCommentsCount', 'menus'));
        });
        Urun::observe(UrunObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LoggingListener::class);


        $this->app->singleton(ElBaseRepository::class, function ($app, $parameters) {
            return new ElBaseRepository($parameters['model']);
        });
    }

    private function _getAdminMenus()
    {
        $menus = config('admin.menus');
        unset($menus[0]['users']);
        $roleId = auth()->guard('admin')->user()->role_id;
        $role = Role::where('id', $roleId)->first();
        if ($role) {
            $userPermissions = $role->permissions;
            if ($userPermissions) {
                $userPermissions = $role->permissions->pluck('name');
                foreach ($menus as $index => $header) {
                    foreach ($header as $k => $head) {
                        if ($k != 'title') {
                            if (!$userPermissions->contains($head['permission'])) {
                                unset($menus[$index][$k]);
                            }
                        }
                    }
                }
            }
            return $menus;
        }
    }
}
