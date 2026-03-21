<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListViewController extends Controller
{

/* ---------------- LIST VIEW SAVE ---------------- */

public function save(Request $request)
{
    $data = $request->data ?? [];

    Storage::put(
        'listview.json',
        json_encode($data, JSON_PRETTY_PRINT)
    );

    return response()->json([
        "status" => "success"
    ]);
}

/* ---------------- LIST VIEW LOAD ---------------- */

public function get()
{
    if (Storage::exists('listview.json')) {
        $data = Storage::get('listview.json');
        return response()->json(json_decode($data, true));
    }

    return response()->json([]);
}

/* ---------------- EDIT VIEW SAVE ---------------- */

public function saveEditView(Request $req)
{
    $data = $req->data ?? [];

    Storage::put(
        'editview.json',
        json_encode($data, JSON_PRETTY_PRINT)
    );

    return response()->json([
        "status" => "saved"
    ]);
}

/* ---------------- EDIT VIEW LOAD ---------------- */

public function getEditView()
{
    if (Storage::exists('editview.json')) {
        return response()->json(
            json_decode(Storage::get('editview.json'), true)
        );
    }

    return response()->json([]);
}

/* ---------------- FIELDS SAVE ---------------- */
/* ⚠️ Use this ONLY when creating/updating master manually */

public function saveFields(Request $req)
{
    $data = $req->data ?? [];

    Storage::put(
        'fields.json',
        json_encode($data, JSON_PRETTY_PRINT)
    );

    return response()->json([
        "status" => "saved"
    ]);
}

/* ---------------- FIELDS LOAD (MASTER) ---------------- */

public function getFields()
{
    if (Storage::exists('fields.json')) {
        return response()->json(
            json_decode(Storage::get('fields.json'), true)
        );
    }

    return response()->json([]);
}

}