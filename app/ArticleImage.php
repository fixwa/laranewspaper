<?php
/**
 * User: Pablo
 * Date: 5/14/2016
 * Time: 8:56 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Intervention\Image\Constraint;
use Image;

/**
 * Class ArticleImage.
 *
 * @property $title
 * @property $file_name
 * @property $file_path
 * @property $file_url_pattern
 */
class ArticleImage extends Model
{
    const URL_PATTERN = '/articles/%s/%s';

    protected $fillable = [
        'title',
        'file_name',
        'file_path',
        'file_url_pattern'
    ];

    /**
     * @param $fileName
     * @return string
     */
    public static function urlPath($fileName)
    {
        $args = [
            '%s', // leave this as is in the URL pattern.
            $fileName
        ];

        return vsprintf(url(self::URL_PATTERN), $args);
    }

    /**
     * An Image is linked to one Article.
     *
     * @return BelongsTo
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * @return string
     */
    public function getFileUrlAttribute()
    {
        return sprintf($this->file_url_pattern, 'original');
    }

    public function getUrl($sub = 'original')
    {
        return self::getUrlFromPattern($this->file_url_pattern, $sub);
    }

    public static function getUrlFromPattern($pattern, $sub)
    {
        return sprintf($pattern, $sub);
    }

    public function createThumbs()
    {
        $config = [
            '360x360' => [
                'width' => 360,
                'height' => 360,
                'quality' => null,
            ],
            '160x150' => [
                'width' => 160,
                'height' => 150,
                'quality' => null,
            ],
            'w750' => [
                'width' => 750,
                'height' => null,
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
                'quality' => 40,
            ],
            'h420' => [
                'width' => 680,
                'height' => 420,
                'quality' => 90,
            ],
        ];

        foreach ($config as $subFolder => $thumbConfig) {
            $saveTo = ArticleImage::storagePath($this->file_name, $subFolder);

            $image = Image::make($this->file_path);
            $image->fit($thumbConfig['width'], $thumbConfig['height'], function (Constraint $constraint) {
                $constraint->upsize();
            }, 'top');
            $image->save($saveTo, $thumbConfig['quality']);
        }
    }

    /**
     * @param string $file
     * @param string $sub
     * @return string
     */
    public static function storagePath($file = '', $sub = 'original')
    {
        $path = public_path() . '/articles/' . $sub;

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path . ($file ? DIRECTORY_SEPARATOR . $file : $file);
    }
}
