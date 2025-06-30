<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminListController extends Controller
{
    public function listView()
    {
        return view('admin.list');
    }
}
