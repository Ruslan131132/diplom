<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ManageDoorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'door:open';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $option = $this->option('option');

        if ($option) {
            exec('gpio mode 9 out && gpio write 9 1 && gpio write 11 0', $output, $return_var);
        } else {
            exec('gpio mode 11 out && gpio write 11 1 && gpio write 9 0', $output, $return_var);
        }
    }
}
