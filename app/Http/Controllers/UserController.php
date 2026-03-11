<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class UserController extends Controller
{
    //
    function getUser(){
        return view('user');
    }
    function aboutUser(){
        return "Hello this is Awantika";
    }
    function getUserName($name){
        //echo "hello this is ".$name;
        return view('getuser',['name'=>$name]);
    }
    function adminLogin(){
        $user="Annu";
        $users=['Annu','Aaru','peter'];
        return view('admin.login',["user"=>$user,"users"=>$users]);
    }
    function userHome(){
        return view('home');
    }
}

