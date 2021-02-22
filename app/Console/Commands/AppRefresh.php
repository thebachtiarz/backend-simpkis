<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class AppRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the app for clean all cache etc.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $artisan = ['php artisan down', 'view:clear', 'config:clear', 'cache:clear', 'dumpautoload', 'php artisan up'];

            Log::channel('maintenance')->info('Maintenance server daily, started...');

            foreach ($artisan as $key => $command) {
                if (preg_match('/:/', $command)) {
                    Artisan::queue($command);
                } else {
                    (new Process(explode(' ', $command)))->run();

                    if (!preg_match('/up/', $command)) sleep(15);
                }
            }

            Log::channel('maintenance')->info('Maintenance server daily, success');
        } catch (\Throwable $th) {
            Log::channel('maintenance')->info('Maintenance server daily, failed : ' . $th->getMessage());
        }
    }
}
