<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransferProductController extends Controller
{
    public function index()
    {
        return view('transfer_product.index')->with(['route' => route('transfer_product.list')]);
    }

    public function create(Request $request)
    {
        return view('transfer_product.form');
    }

    public function update(Request $request, $id)
    {

    }

    public function delete($id)
    {

    }
}
