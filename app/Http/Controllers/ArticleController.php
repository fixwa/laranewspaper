<?php

namespace App\Http\Controllers;

use App\Article;
use App\Http\Requests;
use Illuminate\Http\Response;


class ArticleController extends Controller
{
    public function show($id)
    {
        $article = Article::findOrFail($id);
        $article->views += 1;
        $article->save();

        return view('frontend.single-article', compact('article'));
    }
}