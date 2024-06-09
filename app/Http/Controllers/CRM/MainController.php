<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\QrUser;
use App\Services\CRMService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index(CRMService $crmService)
    {
        $logs = Log::query()->orderByDesc('created_at')->whereDate('created_at', Carbon::now())->get();
        $testLogs = Log::query()->orderByDesc('created_at')->whereDate('created_at', Carbon::parse('2024-06-02'))->get();
//        dd($testLogs);
//        foreach ($testLogs as $log) {
//            $newDate =  Carbon::parse($log->created_at)->addMinutes(960)->format('Y-m-d H:i:s');
//            DB::table('logs')
//                ->where('id', $log->id)
//                ->update(['created_at' => $newDate]);
//        }
        $qrGenerations = $crmService->getQrActivationsInterval();
        $qrActivations = $crmService->getQrActivationsInterval(true);

        return view('main', compact('logs', 'qrActivations', 'qrGenerations'));
    }

    public function logs(Request $request)
    {
        $logs = Log::query()
            ->when($request->created_from ?? false, function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->created_from);
            })
            ->when($request->created_to ?? false, function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->created_to);
            })
            ->when($request->text ?? false, function ($query) use ($request) {
                $query->where('text', 'like'  ,'%' . $request->text . '%');
            })
            ->orderByDesc('created_at')->get();

        return view('logs', compact('logs'));
    }


}
