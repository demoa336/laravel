<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name', 'image', 'description', 'price'
    ];

    protected $appends = ['file'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function getImageAttribute($value)
    {
        return url('/').'/storage/'.$value;
    }

    public function getFileAttribute()
    {
        return $this->attributes['image'];
    }
}
