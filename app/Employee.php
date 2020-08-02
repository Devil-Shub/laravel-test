<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'salary',
        'image'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
