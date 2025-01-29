<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebHookController extends Controller
{
    public function cardpanda(Request $request)
    {
        $data = $request->all();
        $this->logData($data);
        return response()->json(['status' => 'success']);
    }

    private function logData($data)
    {
        $logDir = storage_path('logs/cardpanda');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/cardpanda_' . date('Ymd_His') . '.log';
        $log = PHP_EOL . date('Y-m-d H:i:s') . ' ' . json_encode($data) . PHP_EOL;
        file_put_contents($logFile, $log);
    }
}

