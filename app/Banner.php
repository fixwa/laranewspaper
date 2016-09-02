<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Banner
 * @property ArticleImage[] $images
 */
class Banner extends Model
{
    const URL_PATTERN = '/banners/%s/%s';

    protected $fillable = [
        'title',
        'url_to',
        'placement_id',
        'clicks_limit',
        'impressions_limit',
        'status'
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

    public static function getUrlFromPattern($pattern, $sub)
    {
        return sprintf($pattern, $sub);
    }

    /**
     * @param string $file
     * @param string $sub
     * @return string
     */
    public static function storagePath($file = '', $sub = 'original')
    {
        $path = public_path() . '/banners/' . $sub;

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path . ($file ? DIRECTORY_SEPARATOR . $file : $file);
    }

    /**
     * @return BelongsTo
     */
    public function placement()
    {
        return $this->belongsTo(BannerPlacement::class);
    }

    /**
     * An Article is owned by a user.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function url()
    {
        return sprintf($this->file_url_pattern, 'original');
    }
}
