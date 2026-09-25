<?php

namespace App\Http\Middleware;

use App\Services\FrontService;
use App\Services\HomeLayout;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 * Membagikan data bersama (menu, sidebar, footer, popup, settings) ke seluruh
 * view publik — padanan Display::view() pada aplikasi lama.
 */
class ShareFrontData
{
    public function __construct(private FrontService $front)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        [$menuLinks, $menuLen] = $this->front->menuTop();

        $categories = $this->front->categories();
        $tree = [];
        foreach ($categories as $c) {
            if (($c['category_show'] ?? 'no') !== 'yes') {
                continue;
            }
            $subs = [];
            foreach ($this->front->subCategories((int) $c['category_id']) as $sc) {
                if (($sc['sub_category_show'] ?? 'no') !== 'yes') {
                    continue;
                }
                $subs[] = ['name' => $sc['sub_category_name'], 'uri' => $sc['sub_category_uri']];
            }
            $tree[] = ['name' => $c['category_name'], 'uri' => $c['category_uri'], 'subs' => $subs];
        }

        $pop = $this->front->popup();
        $popStatus = $pop['ads_status'] ?? 'off';
        $tipe = $pop['ads_type'] ?? '2';
        $popupView = $tipe === '0' ? 'pop_upload' : ($tipe === '1' ? 'pop_embed' : 'pop_html');

        View::share([
            'frontMenuLinks' => $menuLinks,
            'frontMenuLen' => $menuLen,
            'frontClassTop' => $menuLen > 98 ? 'top-gap-85' : 'top-gap-40',
            'frontSide' => $tree,
            'frontCategoriesTree' => $tree,
            'frontFooterInfo' => $this->front->footerInfo(),
            'frontFooterSosial' => $this->front->footerSosial(),
            'frontSys' => $this->front->sysSettings(),
            'frontPopup' => $pop ?? ['ads_status' => 'off', 'ads_type' => '2', 'ads_url' => '', 'ads_file_type' => '2', 'ads_content' => ''],
            'frontPopStatus' => $popStatus,
            'frontPopupView' => $popupView,
            'frontService' => $this->front,
            'frontHomeLayout' => HomeLayout::get(),
        ]);

        return $next($request);
    }
}
