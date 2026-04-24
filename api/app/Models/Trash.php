<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Trash extends Model
{
    use HasFactory;

    protected $table = 'trash';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'produit_id',
        'atelier_id',
        'usine_id',
        'created_at',
        'created_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userr()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function atelier()
    {
        return $this->belongsTo(Atelier::class, 'atelier_id');
    }

    public function usine()
    {
        return $this->belongsTo(Usine::class, 'usine_id');
    }

    public function getDaysRemainingAttribute()
    {
        $hoursPassed = now()->diffInRealHours($this->created_at);
        return floor(30 - $hoursPassed / 24);
    }

    /**
     * Définir quels enregistrements doivent être supprimés automatiquement.
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(30));
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->code = Str::uuid();
        });
    }

}
// This model represents the Trash entity, which is used to store deleted products
// along with their associated user, atelier, and usine information.
// It includes relationships to the User, Produit, Atelier, and Usine models,