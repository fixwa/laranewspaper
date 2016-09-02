<?php
/**
 * Created by PhpStorm.
 * User: Pablo
 * Date: 5/29/2016
 * Time: 12:50 PM
 */

namespace App\Http\ViewComposers;

use App\Banner;
use App\BannerPlacement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class HeaderComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $headerBanner = Banner::whereHas('placement', function (Builder $query) {
            $query->where('name', '=', BannerPlacement::PLACE_HEADER);
        })
            ->where('status', '=', true)
            ->first();

        $view->with(compact('headerBanner'));
    }
}