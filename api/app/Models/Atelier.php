<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Atelier extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $table = 'atelier';
    protected $fillable = [
        'usine_id',
        'nomatelier',
        'active'
    ];

    public $timestamps = false;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->code = Str::uuid();
        });
    }
    
    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'contenir', 'atelier_id', 'produit_id');
    }
    public function usine()
    {
        return $this->belongsTo(Usine::class, 'usine_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'atelier_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 'true');
    }

    public function produitSansFds()
    {
        return $this->belongsToMany(Produit::class, 'contenir', 'atelier_id', 'produit_id')
            ->where(function ($query) {
                $query->whereNull('fds')->orWhere('fds', '');
            });
    }

    public function historique()
    {
        return $this->hasMany(Historique::class, 'atelier_id');
    }


}