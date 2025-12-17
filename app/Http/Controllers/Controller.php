<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    function megadirutSendMessage($nomor_wa, $message)
    {
        $url_megacan = env('MEGADIRUT');
        $data = [
            'chatId' => $nomor_wa . '@c.us',
            "contentType" => "string",
            "content" => $message
        ];
        return Http::withBody(json_encode($data), 'application/json')
            ->post($url_megacan . '/client/sendMessage/megacan')->object();
    }
    function megacanSendMessage($nomor_wa, $message)
    {
        $url_megacan = env('MEGACAN');
        $data = [
            'chatId' => $nomor_wa . '@c.us',
            "contentType" => "string",
            "content" => $message
        ];
        return Http::withBody(json_encode($data), 'application/json')
            ->post($url_megacan . '/client/sendMessage/megacan')->object();
    }
}
