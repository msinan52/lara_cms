<?php

namespace App\Http\Middleware;

use Closure;

class CheckModuleActive
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $moduleConfigName = $this->_getModuleConfigName($request);
        $url = $request->url();
        if (is_null($moduleConfigName))
            return $next($request);
        elseif (config('admin.' . $moduleConfigName) == true || route('admin.home_page') == $url)
            return $next($request);
        return redirect(route('admin.home_page'))->withErrors('Bu modül aktif değil');
    }

    private function _getModuleConfigName($request)
    {
        $url = $request->url();

        switch ($url) {
            case $url == route('admin.banners');
                $moduleConfigName = 'banner_module';
                break;
            case $url == route('admin.sss');
                $moduleConfigName = 'sss_module';
                break;
            case $url == route('admin.products');
                $moduleConfigName = 'product_module';
                break;
            case $url == route('admin.product.comments.list');
                $moduleConfigName = 'product_comment_module';
                break;
            case $url == route('admin.product.brands.list');
                $moduleConfigName = 'product_brands_module';
                break;
            case $url == route('admin.categories');
                $moduleConfigName = 'product_category_module';
                break;
            default;
                $moduleConfigName = null;

        }
        return $moduleConfigName;
    }
}
