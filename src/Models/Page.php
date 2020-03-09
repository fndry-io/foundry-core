<?php

namespace Foundry\Core\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Page
 *
 * @property string $name
 * @property string $url
 * @property string $layout_id
 * @property string $content_layout_id
 * @property string $status
 * @property Carbon $published_at
 * @property array $seo
 * @property string $short_id
 * @property string $resource_type
 * @property int $resource_id
 *
 * @package Foundry\Core\Models
 */
class Page extends Model {

    use SoftDeletes;

    protected $table = 'foundry_builder_pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'name',
        'seo',
        'short_id',
        'status',
        'content_layout_id',
        'layout_id',
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


    public function setSeoAttribute($value)
    {
        $this->attributes['seo'] = $value? json_encode($value): $value;
    }

    public function getSeoAttribute($value)
    {
        return $this->jsonDecode($value);
    }

    private function jsonDecode($value)
    {
        if($value)
            return json_decode($value, true);

        return $value;
    }
}
