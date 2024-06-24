<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\QrUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class QRController extends Controller
{

    public function generate(Request $request)
    {
        $uniqueId = Str::uuid();
        $image = QrCode::size(300)
            ->format('png')
            ->errorCorrection('M')
            ->generate(
                config('app.url') . '/api/qr/check/' . $uniqueId,
            );
        $outputFile = '/img/qr-code/img-' . time() . '.png';
        $qr = Storage::disk('public')->put($outputFile, $image);

        QrUser::create([
            'user_id' => $request->user()->id,
            'unique_id' => $uniqueId,
            'path' => '/storage' . $outputFile
        ]);

        return self::response($qr, 'QR-код успешно создан');
    }

    public function check(Request $request, $id)
    {
        if (!isset(auth('sanctum')->user()->id)) {
            Log::create([
                'text' => 'Попытка отсканировать QR-код неавторизованным пользователем',
                'type' => Log::WARNING,
                'user_agent' => $request->userAgent() ?? null,
                'ip' => $request->ip() ?? null,
                'user_id' => null
            ]);
            if ($request->ajax()) {
                Artisan::call('door:open', ['--option' => 0]);

                return self::error('Нет данных по QR-коду', 400);
            } else {
                $error = 'ОШИБКА ПРИ СКАНИРОВАНИИ';
                Artisan::call('door:open', ['--option' => 0]);

                return view('qr.error', compact('error'));
            }
        }
        $qr = QrUser::where('unique_id', $id)->first();
        if (!$qr) {
            Log::create([
                'text' => 'Попытка отсканировать несуществующий QR-код',
                'type' => Log::WARNING,
                'user_agent' => $request->userAgent() ?? null,
                'ip' => $request->ip() ?? null,
                'user_id' => auth('sanctum')->user()->id ?? null
            ]);
            Artisan::call('door:open', ['--option' => 0]);

            return self::error('Нет данных по QR-коду', 400);
        }
        if ($qr->user_id != auth('sanctum')->user()->id) {
            Log::create([
                'text' => 'Попытка отсканировать QR-код под пользователем ' . auth('sanctum')->user()->email,
                'type' => Log::WARNING,
                'user_agent' => $request->userAgent() ?? null,
                'ip' => $request->ip() ?? null,
                'user_id' => auth('sanctum')->user()->id ?? null
            ]);
            Artisan::call('door:open', ['--option' => 0]);

            return self::error('В доступе отказано', 403);
        }

        if (!$qr->active) {
            Artisan::call('door:open', ['--option' => 0]);
            return self::error('QR-код уже был отсканирован', 400);
        }


// TODO - добавить при необходимости - Storage::disk('public')->delete($qr->path);
        $qr->update(['active' => 0]);
        Artisan::call('door:open', ['--option' => 1]);

        Log::create([
            'text' => 'Получен доступ к двери',
            'type' => Log::SUCCESS,
            'user_agent' => $request->userAgent() ?? null,
            'ip' => $request->ip() ?? null,
            'user_id' => auth('sanctum')->user()->id ?? null
        ]);

        return self::response([
            'success' => true
        ], 'Дверь успешно открыта');
    }

    public function showActualSession(Request $request)
    {
        $qr = QrUser::where('active', 1)->orderByDesc('created_at')->first();

        if ($request->ajax()) {
            return self::response($qr, $qr ? 'Успешно получен QR-код' : 'Зайдите в приложение и QR-код появится' );
        }

        return view('qr', compact('qr'));
    }
}
