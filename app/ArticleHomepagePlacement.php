<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleHomepagePlacement extends Model
{
    const NONE = '--NINGUNO--';
    const SLIDER = 'Slider Principal';
    const FEATURED = 'Destacadas';
    const SECTION_2 = 'Secci  n 2';
    const SECTION_3 = 'Secci  n 3';
    const SECTION_4 = 'Secci  n 4';
    const SECTION_5 = 'Secci  n 5';

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}