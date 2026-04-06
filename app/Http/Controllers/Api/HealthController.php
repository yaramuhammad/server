<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function check()
    {
        $services = [];
        $healthy = true;

        try {
            DB::connection()->getPdo();
            $services['database'] = 'ok';
        } catch (\Throwable) {
            $services['database'] = 'error';
            $healthy = false;
        }

        try {
            Cache::store()->put('health_check', true, 10);
            Cache::store()->forget('health_check');
            $services['cache'] = 'ok';
        } catch (\Throwable) {
            $services['cache'] = 'error';
            $healthy = false;
        }

        return response()->json([
            'status' => $healthy ? 'ok' : 'degraded',
            'services' => $services,
        ], $healthy ? 200 : 503);
    }
}
