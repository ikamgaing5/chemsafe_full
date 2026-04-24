<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produit extends Model
{
    use HasFactory;
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $table = 'produit';
    public $timestamps = false;
    protected $fillable = [
        'code',
        'nomprod',
        'type_emballage',
        'poids',
        'nature',
        'utilisation',
        'fabricant',
        'photo',
        'fds',
        'risque'
    ];
    protected $casts = [
        'has_photo' => 'boolean',
    ];


    public function atelier()
    {
        return $this->belongsToMany(Atelier::class, 'contenir', 'produit_id', 'atelier_id');
    }
    public function danger()
    {
        return $this->belongsToMany(Danger::class, 'possede', 'produit_id', 'danger_id');
    }

    public function infofds()
    {
        return $this->hasOne(FDS::class, 'produit_id', 'id');
    }

    public function getAteliersListAttribute()
    {
        return $this->atelier()->pluck('nomatelier')->join(', ');
    }

    public function getNbAteliersAttribute()
    {
        return $this->atelier()->count();
    }
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->code = Str::uuid();
        });
    }

}
