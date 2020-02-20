<?php

namespace Foundry\Core\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class SitePage
 *
 * @property string $name
 * @property string $path
 * @property string $layout
 * @property string $width
 * @property string $height
 * @property array $styles
 * @property array $classes
 * @property array $meta
 * @property array $children
 * @property string $uuid
 * @property string $resource_type
 * @property int $resource_id
 * @property int $site_id
 *
 * @package Foundry\Core\Models
 */
class SitePage extends Model {

    use SoftDeletes;

    protected $table = 'site_pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'uuid',
        'path',
        'meta',
        'layout',
        'width',
        'height',
        'styles',
        'classes',
        'children',
        'site_id',
        'resource_type',
        'resource_id'
    ];

    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $guarded = [

    ];

    public function setStylesAttribute($value)
    {
        return $this->attributes['styles'] = json_encode($value);
    }

    public function getStylesAttribute($value)
    {
        return $this->jsonEncode($value);
    }

    public function setMetaAttribute($value)
    {
        return $this->attributes['meta'] = json_encode($value);
    }

    public function getMetaAttribute($value)
    {
        return $this->jsonEncode($value);
    }

    public function setChildrenAttribute($value)
    {
        $this->attributes['children'] = json_encode($value);
    }

    public function getChildrenAttribute($value)
    {
        return $this->jsonEncode($value);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    private function jsonEncode($value)
    {
        if($value)
            return json_decode($value, true);

        return $value;
    }
}
