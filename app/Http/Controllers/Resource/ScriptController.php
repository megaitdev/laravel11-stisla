<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScriptController extends Controller
{
    public function getListScript($page = null)
    {
        switch ($page) {
            case "profile":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",     // Library alert
                    "js/profile/{$page}.js",
                ];
            case "verifikasi-nomor":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",     // Library alert
                    "library/sweetalert/dist/sweetalert.min.js",     // Library sweetalert
                    "library/moment/min/moment.min.js",     // Library moment
                    "js/profile/{$page}.js",
                ];
            case "verifikasi-email":
                return [
                    // Put path script below here
                    "library/izitoast/dist/js/iziToast.min.js",     // Library alert
                    "library/sweetalert/dist/sweetalert.min.js",     // Library sweetalert
                    "library/moment/min/moment.min.js",     // Library moment
                    "js/profile/{$page}.js",
                ];

            default:
                return [
                    // Put path script below here
                    "js/custom.js",
                ];
        }
    }
}
