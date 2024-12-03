<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChallangeController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'ChallangeController is working!']);
    }
}
