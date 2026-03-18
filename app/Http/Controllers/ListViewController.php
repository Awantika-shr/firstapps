<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListViewController extends Controller
{

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

/* EDIT VIEW SAVE */

public function saveEditView(Request $req)
{

Storage::put(
'editview.json',
json_encode($req->data, JSON_PRETTY_PRINT)
);

return response()->json([
"status"=>"saved"
]);

}

/* EDIT VIEW LOAD */

public function getEditView()
{

if(Storage::exists('editview.json')){

return response()->json(
json_decode(Storage::get('editview.json'), true)
);

}

return response()->json([]);

}

/* FIELDS SAVE */

public function saveFields(Request $req)
{

Storage::put(
'fields.json',
json_encode($req->data, JSON_PRETTY_PRINT)
);

return response()->json([
"status"=>"saved"
]);

}

/* FIELDS LOAD */

public function getFields()
{

if(Storage::exists('fields.json')){

return response()->json(
json_decode(Storage::get('fields.json'), true)
);

}

return response()->json([]);

}

} 