<?php

namespace App\Http\Controllers\Admin;

use App\Banner;
use App\BannerPlacement;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Response;
use Session;
use Validator;

class BannersController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()
            ->paginate(60);

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $placements = $this->parseForSelect(BannerPlacement::all());

        return view('admin.banners.create', compact('placements'));
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
     * @param Requests\BannerRequest $request
     * @return Response
     */
    public function store(Requests\BannerRequest $request)
    {
        $banner = new Banner($request->all());

        Auth::user()->banners()->save($banner);

        $this->updateImages($banner);

        return redirect(route('admin.banners.index'));
    }

    /**
     * Looks in the SESSION for data of any image that was uploaded in the previous step.
     *
     * @param Banner $banner
     */
    private function updateImages(Banner $banner)
    {
        $images = Session::get(__CLASS__);

        if (empty($images)) {
            return;
        }

        foreach ($images as $key => $imageData) {
            $imageData = $this->refreshUploadedFileData($banner, $key, $imageData);

            if (!empty($imageData)) {
                $banner->file_path = $imageData['file_path'];
                $banner->file_name = $imageData['file_name'];
                $banner->file_url_pattern = $imageData['file_url_pattern'];
                $banner->save();
            }
        }

        Session::forget(__CLASS__);
    }

    /**
     * Fix any temporary paths and urls to the final ones.
     *
     * @param Banner $banner
     * @param $key
     * @param $imageData
     * @return array
     */
    private function refreshUploadedFileData(Banner $banner, $key, $imageData)
    {
        if (!file_exists($imageData['file_path'])) {
            return;
        }

        $newFileName = str_replace($imageData['file_basename'], $banner->id . '_' . $key, $imageData['file_name']);
        $newFilePath = Banner::storagePath($newFileName);
        $newFileUrlPattern = Banner::urlPath($newFileName);

        rename($imageData['file_path'], $newFilePath);

        return [
            'file_path' => $newFilePath,
            'file_name' => $newFileName,
            'file_url_pattern' => $newFileUrlPattern
        ];
    }

    /**
     * Shows a page to EDIT an existing banner
     *
     * @param string $id
     * @return Response
     */
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        $placements = $this->parseForSelect(BannerPlacement::all());

        return view('admin.banners.edit', compact('banner', 'placements'));
    }

    /**
     * @param $id
     * @param Requests\BannerRequest $request
     * @return RedirectResponse|Redirector
     */
    public function update($id, Requests\BannerRequest $request)
    {
        $banner = Banner::findOrFail($id);

        $banner->update($request->all());

        $this->updateImages($banner);

        return redirect(route('admin.banners.index'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        $banner->delete();

        return redirect(route('admin.banners.index'));
    }

    /**
     * Handles a Upload-Image ajax POST to add images to an Banner.
     */
    public function uploadImage(Requests\ImageRequest $request)
    {
        Session::forget(__CLASS__);

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

                $fileUrlPattern = Banner::urlPath($fileName);
                $filePath = Banner::storagePath($fileName);

                $requestFile->move(Banner::storagePath(), $fileName);

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

        $fileUrl = Banner::getUrlFromPattern($fileUrlPattern, 'original');

        return Response::json(compact('fileName', 'filePath', 'fileUrl', 'fileUrlPattern', 'success', 'errors'));
    }
}
