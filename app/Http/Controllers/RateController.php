<?php

namespace App\Http\Controllers;

class RateController extends Controller
{
    public function __construct(
        public \App\Settings\GeneralSettings $settings
    ) { }

    public function info(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->settings->rate);
    }

    public function history(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->settings->rate_time_series);
    }
}
