<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListViewController extends Controller
{
    // Save combined listview.json
    public function save(Request $request)
    {
        $data = $request->data;

        Storage::put(
            'listview.json',
            json_encode($data, JSON_PRETTY_PRINT)
        );

        return response()->json([
            "status"=>"success"
        ]);
    }

    public function get()
    {
        if(Storage::exists('listview.json')){
            $data = Storage::get('listview.json');
            return response()->json(json_decode($data));
        }
        return response()->json([]);
    }

    public function getFields()
    {
        if(Storage::exists('fields.json')){
            $data = Storage::get('fields.json');
            return response()->json(json_decode($data));
        }
        return response()->json([]);
    }

    public function saveFields(Request $request)
    {
        $data = $request->data;
        file_put_contents(base_path('fields.json'), json_encode($data, JSON_PRETTY_PRINT));

        return response()->json([
            "status"=>"fields updated"
        ]);
    }
}