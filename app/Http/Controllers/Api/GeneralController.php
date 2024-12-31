<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class GeneralController extends Controller
{

    function encrypt($string, $key = '', $secret = '', $method = 'AES-256-CBC') {
        $key = env('PUB_KEY');
        $secret = env('PRV_KEY');
        // hash
        $key = hash('sha256', $key);
        // create iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $secret), 0, 16);
        // encrypt
        $output = openssl_encrypt($string, $method, $key, 0, $iv);
        // encode
        return base64_encode($output);
    }

    function decrypt($string, $key = '', $secret = '', $method = 'AES-256-CBC') {
        $key = env('PUB_KEY');
        $secret = env('PRV_KEY');
        // hash
        $key = hash('sha256', $key);
        // create iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $secret), 0, 16);
        // decode
        $string = base64_decode($string);
        // decrypt
        return openssl_decrypt($string, $method, $key, 0, $iv);
    }

}