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
        $user = User::find(1);
        $user->notify(new DatabaseNotification(
            $type = 'info', // info / success / warning / error
            $message = 'Test Notification',
            $messageLong = 'This is a longer message for the test notification '.rand(1, 99999), // optional
            $href = '/some-custom-url', // optional, e.g. backpack_url('/example')
            $hrefText = 'Go to custom URL' // optional
        ));
    }
}
