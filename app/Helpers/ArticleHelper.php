<?php
/**
 * Created by PhpStorm.
 * User: Pablo
 * Date: 5/18/2016
 * Time: 8:14 PM
 */

namespace App\Helpers;

use Image;
use App\Article;
use App\ArticleImage;

class ArticleHelper
{
    public function getLatestImage(Article $article, $sub = 'original')
    {
        $image = $article->images->last();
        return sprintf($image->file_url_pattern, $sub);

        $config = [
            '360x360' => [
                'width' => 360,
                'height' => 360,
                'quality' => null,
            ],
            'w750' => [
                'width' => 750,
                'height' => 300,
                'quality' => 99,
            ],
            'w210' => [
                'width' => 210,
                'height' => null,
                'quality' => 80,
            ],
            'w260' => [
                'width' => 260,
                'height' => null,
                'quality' => 80,
            ],
            'w375' => [
                'width' => 375,
                'height' => null,
                'quality' => 80,
            ],
            '83x83' => [
                'width' => 83,
                'height' => 83,
                'quality' => 75,
            ],
        ];

        $image = $article->images->last();

        $saveTo = ArticleImage::storagePath($image->file_name, $sub);
        $newImage = Image::make($image->file_path);
        $newImage->fit($config[$sub]['width'], $config[$sub]['height'], function ($constraint) {
            $constraint->upsize();
        }, 'top');
//        $newImage->resize($config[$sub]['width'], $config[$sub]['height'], function ($constraint) {
//            $constraint->aspectRatio();
//            $constraint->upsize();
//        });

        // Fill up the blank spaces with transparent color
//        $newImage->resizeCanvas($config[$sub]['width'], (null !== $config[$sub]['height'] ?  $config[$sub]['height'] : $config[$sub]['width']), 'top', false, array(255, 255, 255, 0));

//
//        $background = Image::canvas($config[$sub]['width'], (null !== $config[$sub]['height'] ?  $config[$sub]['height'] : $config[$sub]['width']));
//        $newImage = Image::make($image->file_path)->resize($config[$sub]['width'], $config[$sub]['height'], function ($c) {
//            $c->aspectRatio();
//            $c->upsize();
//        });
//        $background->insert($newImage, 'center');

        $newImage->save($saveTo, $config[$sub]['quality']);
        return sprintf($image->file_url_pattern, $sub);


        $ratio = 4 / 3;

        $img = Image::make($image->file_path);
//        $img->resize($config[$sub]['width'], $config[$sub]['height'], function (Constraint $constraint) {
//                $constraint->aspectRatio();
//                $constraint->upsize();
//            })
        // ->fit($thumbConfig['width'], $thumbConfig['height'])
//        $img->fit($config[$sub]['width'], $config[$sub]['height'], false, false)
//            ->save($saveTo, $config[$sub]['quality']);

        if ((int)($img->width() / $ratio > $img->height())) {
            // Fit the img to ratio of 4:3, based on the height
            $img->fit((int)($img->height() * $ratio), $img->height());
        } else {
            // Fit the img to ratio of 4:3, based on the width
            $img->fit($img->width(), (int)($img->width() / $ratio));
        }
        $img->save($saveTo, $config[$sub]['quality']);
        return sprintf($image->file_url_pattern, $sub);
        //---replace return sprintf($article->images->last()->file_url_pattern, $sub);
    }
}