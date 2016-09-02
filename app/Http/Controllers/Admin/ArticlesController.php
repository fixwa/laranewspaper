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

class ArticlesController extends Controller
{
    public function index()
    {
        $articles = Article::latest()
            ->paginate(60);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $sections = $this->parseForSelect(ArticleSection::all());
        $homePagePlacements = $this->parseForSelect(ArticleHomepagePlacement::all());

        return view('admin.articles.create', compact('sections', 'homePagePlacements'));
    }

    /**
     * @param Collection $collection
     * @return array
     */
    private function parseForSelect($collection)
    {
        $arrayForSelect = [];
        foreach ($collection as $item) {
            $arrayForSelect[$item->id] = $item->name;
        }

        return $arrayForSelect;
    }

    /**
     * @param Requests\ArticleRequest $request
     * @return Response
     */
    public function store(Requests\ArticleRequest $request)
    {
        $article = new Article($request->all());

        Auth::user()->articles()->save($article);

        $this->updateImages($article);

        return redirect(route('admin.articles.index'));
    }

    private function updateImages(Article $article)
    {
        $images = Session::get(__CLASS__);

        if (empty($images)) {
            return;
        }

        foreach ($images as $key => $imageData) {
            $imageData = $this->refreshUploadedFileData($article, $key, $imageData);

            if (!empty($imageData)) {
                $image = new ArticleImage($imageData);
                $article->images()->save($image);
            }
        }

        Session::forget(__CLASS__);
    }

    private function refreshUploadedFileData(Article $article, $key, $imageData)
    {
        if (!file_exists($imageData['file_path'])) {
            return;
        }

        $newFileName = str_replace($imageData['file_basename'], $article->id . '_' . $key, $imageData['file_name']);
        $newFilePath = ArticleImage::storagePath($newFileName);
        $newFileUrlPattern = ArticleImage::urlPath($newFileName);

        rename($imageData['file_path'], $newFilePath);

        return [
            'file_path' => $newFilePath,
            'file_name' => $newFileName,
            'file_url_pattern' => $newFileUrlPattern
        ];
    }

    /**
     * Shows a page to EDIT an existing article
     *
     * @param string $id
     * @return Response
     */
    public function edit($id)
    {
        $article = Article::findOrFail($id);

        $sections = $this->parseForSelect(ArticleSection::all());
        $homePagePlacements = $this->parseForSelect(ArticleHomepagePlacement::all());

        return view('admin.articles.edit', compact('article', 'sections', 'homePagePlacements'));
    }

    public function update($id, Requests\ArticleRequest $request)
    {
        $article = Article::findOrFail($id);

        $article->update($request->all());

        $this->updateImages($article);

        return redirect(route('admin.articles.index'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        $article->delete();

        return redirect(route('admin.articles.index'));
    }

    /**
     * Handles a Upload-Image ajax POST to add images to an Article.
     */
    public function uploadImage(Requests\ImageRequest $request)
    {
        $requestFile = $request->file('imgfile');

        $fileBaseName = '';
        $extension = '';
        $fileUrlPattern = '';
        $filePath = '';

        $fileName = empty($requestFile->getClientOriginalName())
            ? $requestFile->getClientOriginalName()
            : $requestFile->getFilename();


        $file = ['image' => $requestFile];
        $rules = ['image' => 'required']; //mimes:jpeg,bmp,png and for max size max:10000
        $validator = Validator::make($file, $rules);

        if ($validator->fails()) {
            $success = false;
            $errors = $validator->errors();
        } else {
            if ($requestFile->isValid()) {

                $extension = $requestFile->guessExtension();
                $fileBaseName = '_temp_' . mt_rand(11111, 99999);
                $fileName = $fileBaseName . '.' . $extension;

                $fileUrlPattern = ArticleImage::urlPath($fileName);
                $filePath = ArticleImage::storagePath($fileName);

                Image::make($requestFile)
                    // prevent possible upsizing
                    ->resize(1000, null, function (Constraint $constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->orientate()
                    ->save($filePath);

                $success = true;
                $errors = [];
            } else {
                $success = false;
                $errors = ['uploaded file is not valid'];
            }
        }

        if ($success) {
            Session::push(__CLASS__, [
                'file_name' => $fileName,
                'file_url_pattern' => $fileUrlPattern,
                'file_path' => $filePath,
                'file_basename' => $fileBaseName,
                'file_extension' => $extension,
                'success' => $success,
                'errors' => $errors,
            ]);
        }

        $fileUrl = ArticleImage::getUrlFromPattern($fileUrlPattern, 'original');

        return Response::json(compact('fileName', 'filePath', 'fileUrl', 'fileUrlPattern', 'success', 'errors'));
    }
}
