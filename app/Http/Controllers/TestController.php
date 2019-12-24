<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TestController extends Controller
{
    public function getDistance() {
        $client = new Client();
        $request = $client->get('https://api.mapbox.com/directions/v5/mapbox/driving/106.87154263%2C-6.17712096%3B106.84240311%2C-6.26275035.json?access_token=pk.eyJ1IjoiZmFpc2FsaGFtZGkiLCJhIjoiY2s0OWFuZmQ3MDNjcDNtbzdzNGJseGR5byJ9.XvAMKikKwTTKfpcHuXz3RA');
        $response = $request->getBody()->getContents();

        $jarak = json_decode($response, true);

        print("<pre>".print_r($jarak, true)."</pre>");
    }
}
