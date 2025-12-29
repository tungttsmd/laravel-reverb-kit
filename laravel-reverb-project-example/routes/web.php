<?php

use Illuminate\Support\Facades\Route;
use App\Events\MessageSent;

Route::prefix('api')->group(function () {
    Route::get('reverb-test', function () {
        $data = [
            'meta' => [
                'code' => 200,
                'message' => 'Success',
            ],
            'data' => [
                "api" => '/api/reverb-test',
            ],
        ];

        # Đưa array/object... về string json
        $message = json_encode($data);

        # Gọi Event MessageSent cho queue:worker xử lí
        event(new MessageSent($message));

        return response()->json("queued");
    })->name('reverb-test');
});

Route::get('dashboard', function () {
    return view('dashboard/index');
})->name('dashboard');
