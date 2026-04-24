<?php

namespace App\Http\Controllers;

use App\Models\Atelier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GrapheController extends Controller
{

    public function getDangerStatsByUsine($idusine)
    {
        // Récupérer les dangers et le nombre de produits pour l'usine
        $dangerStats = DB::table('danger')
            ->join('possede', 'danger.id', '=', 'possede.danger_id')
            ->join('produit', 'possede.produit_id', '=', 'produit.id')
            ->join('contenir', 'produit.id', '=', 'contenir.produit_id')
            ->join('atelier', 'contenir.atelier_id', '=', 'atelier.id')
            ->where('atelier.usine_id', $idusine)
            ->select('danger.id', 'danger.nomdanger', DB::raw('COUNT(DISTINCT produit.id) as count'))
            ->groupBy('danger.id', 'danger.nomdanger')
            ->orderBy('count', 'DESC')
            ->get();

        // Récupérer les produits par danger pour cette usine
        $productsByDanger = DB::table('danger')
            ->join('possede', 'danger.id', '=', 'possede.danger_id')
            ->join('produit', 'possede.produit_id', '=', 'produit.id')
            ->join('contenir', 'produit.id', '=', 'contenir.produit_id')
            ->join('atelier', 'contenir.atelier_id', '=', 'atelier.id')
            ->where('atelier.usine_id', $idusine)
            ->select('danger.id', 'danger.nomdanger', 'produit.id as produit_id', 'produit.nomprod')
            ->orderBy('danger.nomdanger')
            ->orderBy('produit.nomprod')
            ->get()
            ->groupBy('id')
            ->map(function ($group) {
                return [
                    'id' => $group->first()->id,
                    'nomdanger' => $group->first()->nomdanger,
                    'products' => array_unique($group->pluck('nomprod')->toArray())
                ];
            });

        // Ajouter la liste des produits à chaque danger dans les statistiques
        $dangerStats->transform(function ($stat) use ($productsByDanger) {
            $stat->products = isset($productsByDanger[$stat->id])
                ? $productsByDanger[$stat->id]['products']
                : [];
            return $stat;
        });

        return response()->json($dangerStats);

    }

    public function getDangerStatsAllAteliers()
    {
        $dangerStats = DB::table('danger')
            ->join('possede', 'danger.id', '=', 'possede.danger_id')
            ->join('produit', 'possede.produit_id', '=', 'produit.id')
            ->join('contenir', 'produit.id', '=', 'contenir.produit_id')
            ->select('danger.id', 'danger.nomdanger', DB::raw('COUNT(DISTINCT produit.id) as count'))
            ->groupBy('danger.id', 'danger.nomdanger')
            ->orderBy('count', 'DESC')
            ->get();
        // Récupérer les produits par danger (pour tous ateliers)
        $productsByDanger = DB::table('danger')
            ->join('possede', 'danger.id', '=', 'possede.danger_id')
            ->join('produit', 'possede.produit_id', '=', 'produit.id')
            ->join('contenir', 'produit.id', '=', 'contenir.produit_id')
            ->select('danger.id', 'danger.nomdanger', 'produit.id as produit_id', 'produit.nomprod')
            ->orderBy('danger.nomdanger')
            ->orderBy('produit.nomprod')
            ->get()
            ->groupBy('id')
            ->map(function ($group) {
                return [
                    'id' => $group->first()->id,
                    // 'encrypted_id' => ($group->first()->code),
                    'nomdanger' => $group->first()->nomdanger,
                    'products' => array_unique($group->pluck('nomprod')->toArray())
                ];
            });

        // Ajouter la liste des produits à chaque danger dans les statistiques
        $dangerStats->transform(function ($stat) use ($productsByDanger) {
            $stat->products = isset($productsByDanger[$stat->id])
                ? $productsByDanger[$stat->id]['products']
                : [];
            // dd($productsByDanger);
            // $stat->encrypted_id = isset($productsByDanger[$stat->id])
            //     ? $productsByDanger[$stat->id]['encrypted_id']
            //     : null;
            return $stat;
        });

        // dd($dangerStats);
        return response()->json($dangerStats);
    }

    public function getAteliersWithDetailsAll()
    {
        $results = DB::table('atelier as a')
            ->leftJoin('contenir as c', 'a.id', '=', 'c.atelier_id')
            ->leftJoin('produit as p', 'c.produit_id', '=', 'p.id')
            ->join('usine as u', 'a.usine_id', '=', 'u.id')
            ->select(
                'a.id as idatelier',
                'a.nomatelier as nom_atelier',
                'u.id as idusine',
                'u.nomusine',
                DB::raw('COUNT(DISTINCT p.id) as total_produits'),
                DB::raw("SUM(CASE WHEN p.id IS NOT NULL AND p.fds IS NOT NULL AND p.fds != '' THEN 1 ELSE 0 END) as produits_avec_fds"),
                DB::raw("SUM(CASE WHEN p.id IS NOT NULL AND (p.fds IS NULL OR p.fds = '') THEN 1 ELSE 0 END) as produits_sans_fds"),
                DB::raw("(
                    SELECT COUNT(DISTINCT c2.produit_id)
                    FROM atelier a2
                    LEFT JOIN contenir c2 ON a2.id = c2.atelier_id
                    WHERE a2.usine_id = a.usine_id
                ) as total_usine")
            )
            ->where('a.active', 'true')
            ->groupBy('a.id', 'a.nomatelier', 'u.id', 'u.nomusine')
            ->orderBy('u.nomusine')
            ->orderBy('a.nomatelier')
            ->get();

        return response()->json($results);
    }

    public static function getAteliers($idusine)
    {
        $results = Atelier::where('usine_id', $idusine)
            ->where('active', 'true')
            ->withCount([
                'produits as total_produits' => function ($query) {
                    $query->select(DB::raw('COUNT(DISTINCT produit.id)'));
                },
                'produits as produits_avec_fds' => function ($query) {
                    $query->whereNotNull('fds')
                        ->where('fds', '!=', '');
                },
                'produits as produits_sans_fds' => function ($query) {
                    $query->where(function ($q) {
                        $q->whereNull('fds')
                            ->orWhere('fds', '');
                    });
                }
            ])
            ->orderBy('nomatelier')
            ->get(['id', 'nomatelier as nom_atelier']);


        return response()->json($results);
    }

    public function getDangersData(int $atelierId): JsonResponse
    {
        try {
            Atelier::findOrFail($atelierId);

            $dangerStats = DB::table('danger')
                ->join('possede', 'danger.id', '=', 'possede.danger_id')
                ->join('produit', 'possede.produit_id', '=', 'produit.id')
                ->join('contenir', 'produit.id', '=', 'contenir.produit_id')
                ->where('contenir.atelier_id', $atelierId)
                ->select(
                    'danger.id',
                    'danger.nomdanger',
                    DB::raw('COUNT(DISTINCT produit.id) as count')
                )
                ->groupBy('danger.id', 'danger.nomdanger')
                ->get();

            $productsByDanger = DB::table('danger')
                ->join('possede', 'danger.id', '=', 'possede.danger_id')
                ->join('produit', 'possede.produit_id', '=', 'produit.id')
                ->join('contenir', 'produit.id', '=', 'contenir.produit_id')
                ->where('contenir.atelier_id', $atelierId)
                ->select('danger.id', 'danger.nomdanger', 'produit.nomprod')
                ->orderBy('danger.nomdanger')
                ->get()
                ->groupBy('id')
                ->map(fn($group) => [
                    'id' => $group->first()->id,
                    'nomdanger' => $group->first()->nomdanger,
                    'products' => array_values(array_unique(
                        $group->pluck('nomprod')->toArray()
                    )),
                ]);

            $result = $dangerStats->map(fn($stat) => [
                'id' => $stat->id,
                'nomdanger' => $stat->nomdanger,
                'count' => $stat->count,
                'products' => $productsByDanger[$stat->id]['products'] ?? [],
            ]);

            return response()->json(['data' => $result]);

        } catch (\Exception $e) {
            Log::error('getDangersData failed', [
                'error' => $e->getMessage(),
                'atelier_id' => $atelierId,
            ]);
            return response()->json(['message' => 'Une erreur est survenue.'], 500);
        }
    }

}
