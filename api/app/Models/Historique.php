<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Historique extends Model
{
    use HasFactory;

    protected $table = 'historique';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'produit_id',
        'atelier_id',
        'usine_id',
        'type',
        'action',
        'created_at',
        'created_by'
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
}