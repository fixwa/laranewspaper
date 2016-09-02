<?php

namespace App\Console\Commands;

use App\Article;
use App\ArticleHomepagePlacement;
use App\ArticleImage;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateArticlesThumbs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:thumbs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create thumb images for all Articles.';

    /**
     * Create a new command instance.
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
        /**
         * @var Article[] $articles
         */
        $articles = Article::all();

        foreach ($articles as $article) {
            if ($article->hasImages()) {
                $images = $article->images;

                foreach ($images as $image) {
                    $image->createThumbs();
                    echo '.';
                }
            }
        }
    }
}
