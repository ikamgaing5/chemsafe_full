<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FDS extends Model
{
    use HasFactory;

    protected $table = 'infofds';
    public $timestamps = false;
    protected $fillable = [
        'produit_id',
        'physique',
        'sante',
        'ppt',
        'stabilite',
        'eviter',
        'incompatible',
        'reactivite',
        'stockage',
        'secours',
        'epi'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->code = Str::uuid();
        });
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

}