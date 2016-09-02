<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\ArticleHomepagePlacement;
use App\ArticleImage;
use App\ArticleSection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Image;
use Intervention\Image\Constraint;
use Response;
use Session;
use Validator;

class ArticlesSectionsController extends Controller
{
    public function index()
    {
        $sections = ArticleSection::all();

        return view('admin.articles-sections.index', compact('sections'));
    }
}
