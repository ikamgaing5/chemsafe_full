<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Atelier;
use App\Models\Danger;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    public function ListProduit(Request $request, $codeAtelier)
    {
        $search = $request->query('search', '');
        $produits = Produit::
            select(
                'produit.id',
                'produit.code',
                'produit.nomprod',
                'produit.type_emballage',
                'produit.photo',
                'produit.fds',
            )
            ->join('contenir', 'produit.id', '=', 'contenir.produit_id')
            ->join('atelier', 'contenir.atelier_id', '=', 'atelier.id')
            ->where('atelier.code', $codeAtelier)
            ->where('produit.nomprod', 'like', "%$search%")
            ->orderBy('produit.nomprod', 'asc')
            ->paginate(40);

        $dangers = Danger::whereHas('produit', function ($query) use ($codeAtelier) {
            $query->whereHas('atelier', function ($q) use ($codeAtelier) {
                $q->where('code', $codeAtelier);
            });
        })->get();

        $atelier = Atelier::select('id', 'nomatelier', 'usine_id')
            ->with('usine:id,nomusine,code')
            ->where('code', $codeAtelier)
            ->first();

        return response()->json([
            'message' => "Liste des produits de l'atelier $atelier->nomatelier",
            'produits' => $produits,
            'atelier' => $atelier,
            'dangers' => $dangers
        ]);
    }

    public function getAll(Request $request){
        $search = $request->query('search', '');
        $produits = Produit::
            select(
                'produit.id',
                'produit.code',
                'produit.nomprod',
                'produit.type_emballage',
                'produit.photo',
                'produit.fds',
            )
            ->where('produit.nomprod', 'like', "%$search%")
            ->orderBy('produit.nomprod', 'asc')
            ->paginate(40);

        return response()->json([
            'message' => "Liste de tous les produits.",
            'produits' => $produits
        ]);
    }

    public function getPhoto($path)
    {
        // Sécurise le chemin pour éviter la lecture en dehors du dossier privé
        $path = str_replace('..', '', $path);

        if (!Storage::disk('private')->exists($path)) {
            return response()->json([
                'message' => "Fichier introuvanle."
            ], 404);
        }

        return response()->file(
            // storage_path("app/private/{$path}")
            Storage::disk('private')->path($path)
        );
    }

    public function getFDS($path)
    {
        // Sécurise le chemin pour éviter la lecture en dehors du dossier privé
        $path = str_replace('..', '', $path);

        if (!Storage::disk('private')->exists($path)) {
            return response()->json([
                'message' => "Fichier introuvanle."
            ], 404);
        }

        return response()->file(
            // storage_path("app/private/{$path}")
            Storage::disk('private')->path($path)
        );
    }

    public function add(){
        return response()->json(
            ['message' => "Ajout d'un produit."]
        );
    }

    public function oneProduct($code)
    {
        $produit = Produit::with('infofds', 'danger')
            ->where('code', $code)->first();
        return response()->json([
            'message' => "Informations du produit " . $produit->nomprod
            ,
            'prod' => $produit,
        ]);
    }


}
