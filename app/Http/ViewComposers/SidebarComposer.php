<?php
/**
 * Created by PhpStorm.
 * User: Pablo
 * Date: 5/29/2016
 * Time: 12:50 PM
 */

namespace App\Http\ViewComposers;

use App\Article;
use App\Banner;
use App\BannerPlacement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $sidebarBanners = $this->getBanners();
        $popularNews = $this->getPopularNews();
        $latestNews = $this->getLatestNews();

        $view->with(compact('sidebarBanners', 'popularNews', 'latestNews'));
    }

    private function getBanners()
    {
        $banners = Banner::whereHas('placement', function (Builder $query) {
            $query->whereIn('name', BannerPlacement::getSidebarPlacements());
        })
            ->where('status', '=', true)
            ->get();

        $sidebarBanners = [];

        foreach ($banners as $banner) {
            $sidebarBanners[$banner->placement->name] = $banner;
        }

        return $sidebarBanners;
    }

    private function getPopularNews()
    {
        $articles = Article::where('status', '=', true)
            ->where('views', '>', 0)
            ->take(4)
            ->orderBy('views', 'desc')
            ->get();
        return $articles;
    }

    private function getLatestNews()
    {
        $articles = Article::where('status', '=', true)
            ->take(3)
            ->orderBy('updated_at', 'desc')
            ->get();
        return $articles;
    }
}