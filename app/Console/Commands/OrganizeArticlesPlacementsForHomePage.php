<?php

namespace App\Console\Commands;

use App\Article;
use App\ArticleHomepagePlacement;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrganizeArticlesPlacementsForHomePage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:organize-homepage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Organizes all the existing Articles to display the Homepage correctly.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->printCountByPlacement();

        /**
         * @var Article[] $articles
         */
        $articles = Article::where('homepage_placement_id', '>', 0)
            ->orderBy('homepage_placement_id', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        $placements = ArticleHomepagePlacement::where('id', '>', 0)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($placements as $placement) {
            foreach ($articles as $article) {
                echo '.';
                if (is_null($article->homepagePlacement)) {
                    \Log::error('Article has no HomePagePlacement.', ['id' => $article->id]);
                    continue;
                }

                if ($article->homepagePlacement->id === $placement->id) {
                    $count = Article::where('homepage_placement_id', '=', $placement->id)->count();

                    if ($count > $placement->max_articles || ($placement->requires_image && !$article->hasImages())) {

                        try {
                            $nextPlacement = ArticleHomepagePlacement::findOrFail($placement->id + 1);
                        } catch (ModelNotFoundException $e) {
                            //$this->error('Reached highest placement. Moving to 0');
                            $nextPlacement = ArticleHomepagePlacement::findOrFail(0);
                        }

                        $article->homepagePlacement()->associate($nextPlacement);
                        $article->save();
                    }
                }
            }
        }

        $this->info('Done!');

        $this->printCountByPlacement();
//
//
//        $noPlacement = ArticleHomepagePlacement::findOrFail(0);
//        $inSliderPlacement = ArticleHomepagePlacement::findOrFail(1);
//        $featuredPlacement = ArticleHomepagePlacement::findOrFail(2);
//        $section2Placement = ArticleHomepagePlacement::findOrFail(3);
//        $section3Placement = ArticleHomepagePlacement::findOrFail(4);
//        $section4Placement = ArticleHomepagePlacement::findOrFail(5);
//        $section5Placement = ArticleHomepagePlacement::findOrFail(6);
//
//        $arrangedByPlacement = [];
//        foreach ($articles as $article) {
//            if (is_null($article->homepagePlacement)) {
//                $article->homepagePlacement()->associate($noPlacement);
//                $article->save();
//            }
//            $arrangedByPlacement[$article->homepagePlacement->name][] = $article;
//        }
//
//        foreach ($arrangedByPlacement as $placement => $articles) {
//            foreach ($articles as $article) {
//                if (($article->homepagePlacement->name === $inSliderPlacement->name)) {
//                    if (count($arrangedByPlacement[$inSliderPlacement->name]) > $inSliderPlacement->max_articles) {
//                        $article->homepagePlacement()->associate($featuredPlacement);
//                        $article->save();
//                        continue;
//                    } else {
//                        continue;
//                    }
//                }
//
//                if (($article->homepagePlacement->name === $featuredPlacement->name)) {
//                    if (count($arrangedByPlacement[$featuredPlacement->name]) > $featuredPlacement->max_articles) {
//                        $article->homepagePlacement()->associate($section2Placement);
//                        $article->save();
//                        continue;
//                    } else {
//                        continue;
//                    }
//                }
//
//                if (($article->homepagePlacement->name === $section2Placement->name)) {
//                    if (count($arrangedByPlacement[$section2Placement->name]) > $section2Placement->max_articles) {
//                        $article->homepagePlacement()->associate($section3Placement);
//                        $article->save();
//                        continue;
//                    } else {
//                        continue;
//                    }
//                }
//
//                if (($article->homepagePlacement->name === $section3Placement->name)) {
//                    if (count($arrangedByPlacement[$section3Placement->name]) > $section3Placement->max_articles) {
//                        $article->homepagePlacement()->associate($section4Placement);
//                        $article->save();
//                        continue;
//                    } else {
//                        continue;
//                    }
//                }
//
//                if (($article->homepagePlacement->name === $section4Placement->name)) {
//                    if (count($arrangedByPlacement[$section4Placement->name]) > $section4Placement->max_articles) {
//                        $article->homepagePlacement()->associate($section4Placement);
//                        $article->save();
//                        continue;
//                    } else {
//                        continue;
//                    }
//                }
//
//                if (($article->homepagePlacement->name === $section5Placement->name)) {
//                    if (count($arrangedByPlacement[$section5Placement->name]) > $section5Placement->max_articles) {
//                        $article->homepagePlacement()->associate($noPlacement);
//                        $article->save();
//                        continue;
//                    } else {
//                        continue;
//                    }
//                }
//            }
//        }
    }

    private function printCountByPlacement()
    {
        for ($i = 0; $i < 7; $i++) {
            $this->line(
                'Number of Articles in Placement [' . $i . ']: ' .
                Article::where('homepage_placement_id', '=', $i)->count()
            );
        }
    }

    private function updatePlacement()
    {

    }
}
