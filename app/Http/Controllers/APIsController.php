<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class APIsController extends Controller
{
    function getUser(){
        return "user get function";
    } 
}
