<?php

use App\Http\Controllers\SendMessageController;
use App\Services\ServicesApi;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//
// Schedule::call(new SendMessage)->everySecond();

Schedule::call(SendMessageController::class)->everyFiveMinutes(); // Ini akan memanggil metode __invoke()
// Schedule::call(function() {

//     // DB::table('account')->insert(
//     //     [
//     //         'phone'=>"03939399339",
//     //         'status_read'=>1,
//     //         'status_call'=>2,
//     //         'status_type'=>3,
//     //         'status_available'=>4
//     //     ]
//     //     );
// })->everySecond();
