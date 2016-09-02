<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Article
 * @property ArticleImage[] $images
 */
class Article extends Model
{
    protected $fillable = [
        'title',
        'intro',
        'body',
        'user_id',
        'section_id',
        'homepage_placement_id',
        'status'
    ];

    /**
     * @return BelongsTo
     */
    public function section()
    {
        return $this->belongsTo(ArticleSection::class);
    }

    /**
     * @return BelongsTo
     */
    public function homepagePlacement()
    {
        return $this->belongsTo(ArticleHomepagePlacement::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(ArticleTag::class)->withTimestamps();
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

    /**
     * Tells if the current Article has one or more images.
     *
     * @return array
     */
    public function hasImages()
    {
        return ($this->images()->count() > 0);
    }

//    public function setCreatedAt($date)
//    {
//        $this->attributes['created_at'] = Carbon::createFromFormat('Y-m-d', $date);
//    }

    /**
     * A Article has many Images.
     *
     * @return HasMany|ArticleImage[]
     */
    public function images()
    {
        return $this->hasMany(ArticleImage::class);
    }
}
