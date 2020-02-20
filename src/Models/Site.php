<?php

namespace Foundry\Core\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Site
 *
 * @property string $title
 * @property array $pages
 * @property string $uuid
 *
 * @package Foundry\Core\Models
 */
class Site extends Model {

    use SoftDeletes;

    protected $table = 'sites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'uuid'
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

    public function pages()
    {
        return $this->hasMany(SitePage::class);
    }
}
