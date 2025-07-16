<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CssController extends Controller
{
    public function getListCss($page = null)
    {
        switch ($page) {
            case "profile":
                return [
                    // Put path css below here
                    'library/izitoast/dist/css/iziToast.min.css',
                ];
            case "verifikasi-nomor":
                return [
                    // Put path css below here
                    'library/izitoast/dist/css/iziToast.min.css',
                ];
            case "verifikasi-email":
                return [
                    // Put path css below here
                    'library/izitoast/dist/css/iziToast.min.css',
                ];

            default:
                return [
                    // Put path css below here
                    'css/custom.css'
                ];
        }
    }
}
