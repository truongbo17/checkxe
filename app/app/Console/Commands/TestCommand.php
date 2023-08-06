<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Bo\Notifications\Notifications\DatabaseNotification;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $data = "Ford
Honda
Hyundai
Toyota
Isuzu
KIA
Mercedes Benz
BMW
Mini Cooper
Audi
Lamborghini
Volvo
Jaguar
Maserati
Aston Martin
Bentley
Vinfast
Mitsubishi
Chevrolet
Lexus
Mazda
Nissan
Subaru
Ssangyong
Land Rover
Peugeot
Volkswagen
Porsche
Ferrari";

        $data = explode("\n", $data);
        dd($data);
    }
}
