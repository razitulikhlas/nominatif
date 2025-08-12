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
        $schedule->command('queue:work --stop-when-empty')
        ->everyMinute()
        ->withoutOverlapping();

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
