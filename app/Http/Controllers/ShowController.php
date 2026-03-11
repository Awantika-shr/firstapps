<?php

namespace App\Http\Controllers;
use App\Http\Middleware\AgeCheck;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    function show(){
        return 'List of students ';
    }
    function add(){
        return 'add students ';
    }
    function delete(){
        return 'delete students ';
    }
    
}
