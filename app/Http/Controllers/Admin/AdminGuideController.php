<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminGuideController extends Controller
{
    public function index()
    {
        return view('admin.guide');
    }
}
