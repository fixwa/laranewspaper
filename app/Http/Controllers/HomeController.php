<?php

namespace App\Http\Controllers;

use App\Article;
use App\Banner;
use App\BannerPlacement;
use App\Http\Requests;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $articles = $this->getArticles();
        $banners = $this->getBanners();

        return view('frontend.home', compact('articles', 'banners'));
    }

    /**
     * @return Article[]
     */
    private function getArticles()
    {
        $articles = Article::where('status', '=', true)
            ->where('homepage_placement_id', '>', 0)
            ->take(200)
            ->orderBy('updated_at', 'desc')
            ->get();

        $organizedArticles = [];

        foreach ($articles as $article) {
            $organizedArticles[$article->homepagePlacement->name][] = $article;
        }

        return $organizedArticles;
    }

    /**
     * @return Banner[]
     */
    private function getBanners()
    {
        $banners = Banner::whereHas('placement', function (Builder $query) {
            $query->whereIn('name', BannerPlacement::getHomePlacements());
        })
            ->where('status', '=', true)
            ->get();

        $organizedBanners = [];

        foreach ($banners as $banner) {
            $organizedBanners[$banner->placement->name] = $banner;
        }

        return $organizedBanners;
    }
}
