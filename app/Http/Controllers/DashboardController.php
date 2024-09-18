<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data = [
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'scripts' => ['js/custom.js'],
            'csses' => ['css/custom.css']
        ];
        return view('dashboard', $data);
    }
}
