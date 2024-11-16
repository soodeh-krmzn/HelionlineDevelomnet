<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        $req = new ModelsRequest;
        return view('request.index', compact('req'));
    }
}
