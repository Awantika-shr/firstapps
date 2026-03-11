<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DBController extends Controller
{
    function database(){
        $users= DB::select('select * from users');
        return view('users',['users'=>$users]);
    }
}
