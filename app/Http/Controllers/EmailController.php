<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerification;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    function sendEmail()
    {
        $code = mt_rand(100000, 999999);
        Mail::to('abimanugraha@gmail.com')->send(new EmailVerification('tuanputri', $code));
        return "Email sent";
    }
}
