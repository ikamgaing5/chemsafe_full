<?php

namespace App\Http\Controllers;

use App\Models\Danger;
use Illuminate\Http\Request;

class DangerController extends Controller
{
    public function all(){
        return response()->json(Danger::all());
    }
}
