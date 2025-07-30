<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
// Import command Anda di sini jika belum otomatis
// use App\Console\Commands\SendScheduledWhatsApp;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * (Biasanya command di app/Console/Commands otomatis terdaftar)
     * Jika tidak, Anda bisa menambahkannya di sini:
     * @var array
     */
    // protected $commands = [
    //     SendScheduledWhatsApp::class,
    // ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Contoh: Menjalankan command setiap menit
        $schedule->command('whatsapp:send-scheduled-message')
                 ->everyTenSeconds()
                 // ->dailyAt('08:00') // Contoh: Setiap hari jam 8 pagi
                 // ->cron('* * * * *') // Anda juga bisa menggunakan ekspresi cron kustom
                 ->withoutOverlapping() // Mencegah command berjalan jika instance sebelumnya masih berjalan
                 ->onSuccess(function () {
                    \Illuminate\Support\Facades\Log::channel('scheduler')->info('Task whatsapp:send-scheduled-message ran successfully.');
                 })
                 ->onFailure(function () {
                    \Illuminate\Support\Facades\Log::channel('scheduler')->error('Task whatsapp:send-scheduled-message failed to run.');
                 });

        // Anda bisa menambahkan jadwal lain di sini
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
