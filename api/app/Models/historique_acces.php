<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class historique_acces extends Model
{
    use HasFactory;

    protected $table = 'historique_acces';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'created_at',
        'action',
        'time'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->code = Str::uuid();
        });
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}