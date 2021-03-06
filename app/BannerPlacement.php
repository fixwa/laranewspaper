<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class BannerPlacement extends Model
{
    const PLACE_NONE = '--NINGUNO--';
    const PLACE_HEADER = 'Encabezado';
    const PLACE_HOME_SECTION_2 = 'Home - Secci  n 2';
    const PLACE_HOME_SECTION_3 = 'Home - Secci  n 3';
    const PLACE_HOME_SECTION_4 = 'Home - Secci  n 4 (Vertical)';
    const PLACE_SIDEBAR_TOP = 'Sidebar - Top';
    const PLACE_SIDEBAR_BOTTOM = 'Sidebar - Bottom';

    public static function getHomePlacements()
    {
        return [
            self::PLACE_HEADER,
            self::PLACE_HOME_SECTION_2,
            self::PLACE_HOME_SECTION_3,
            self::PLACE_HOME_SECTION_4,
        ];
    }

    public static function getSidebarPlacements()
    {
        return [
            self::PLACE_SIDEBAR_TOP,
            self::PLACE_SIDEBAR_BOTTOM,
        ];
    }

    public function banners()
    {
        return $this->hasMany(Banner::class);
    }
}