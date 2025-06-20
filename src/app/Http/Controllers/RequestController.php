<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function storeRequest(Request $request)
    {
        $user = Auth::user();
    }
}
