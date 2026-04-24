<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Atelier;
use App\Models\Produit;
use App\Models\User;
use App\Models\Usine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => "Nom d'utilisateur ou mot de passe incorrect"
            ], 401);
        }

        $user = Auth::user();
        // $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            // 'token' => $token,
            'message' => 'Connexion réussie'
        ]);
    }

    // public function logout(){
    //     Auth::logout();
    //     Session::flush();
    //     return response()->json([
    //         'message' => 'Déconnexion réussie'
    //     ]);
    // }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Déconnecté avec succès'
        ]);
    }


    public function user(Request $request)
    {
        $user = User::select('users.id', 'users.name', 'users.role', 'role.description', 'usine.code')
            ->join('usine', 'users.usine_id', '=', 'usine.id')
            ->join('role', 'users.role', '=', 'role.name')
            
            ->findOrFail($request->user()->id);
        return response()->json([
            'user' => $user,
            'info' => 'ok'
        ]);
    }

    public function profile(Request $request)
    {
        $user = User::select('users.id', 'users.name', 'users.role', 'role.description')
            ->join('role', 'users.role', '=', 'role.name')->findOrFail($request->user()->id);
        return response()->json([
            'user' => $user,
            'message' => 'Information du profil',
            'info' => 'ok'
        ]);
    }

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

    public function dashboard()
    {
        $user = Auth::user();
        $infoUser = User::count();
        $infoUsine = Usine::where('active', 'true')->count();
        $infoAtelier = Atelier::where('active', 'true')->count();
        $infoProduit = Produit::all()->count();

        if (Auth::user()->role != 'global') {
            $infoAtelier = Atelier::where('active', 'true')->where('usine_id', Auth::user()->usine_id)->count();
            $infoUser = User::where('usine_id', Auth::user()->usine_id)->count();
        }
        $produitSansFDS = Produit::where('fds', '')->count();
        return response()->json([
            'user' => $user,
            'message' => 'Tableau de bord',
            'infoUser' => $infoUser,
            'infoUsine' => $infoUsine,
            'infoAtelier' => $infoAtelier,
            'infoProduit' => $infoProduit,
            'produitSansFDS' => $produitSansFDS
        ]);
    }

    public function essaie(Request $request)
    {
        return response()->json([
            'message' => $request->user(),
            'info' => 'ok'
        ]);
    }
}
