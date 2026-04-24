<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role';
    protected $timestamp = false;
    protected $fillable = [
        'name',
        'description'
    ];

    // You can add any additional methods or relationships here if needed
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
