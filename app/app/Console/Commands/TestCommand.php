<?php

namespace App\Console\Commands;

use App\Models\User;
use Bo\Car\Models\Car;
use Bo\Medias\Models\Medias;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Bo\Notifications\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Storage;

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
        dd(Car::first()->public_url);

        $media = Medias::first();
        $media_data = json_decode($media->target_data, true);
        dump($media_data);
        $file = Storage::disk('s3.' . $media_data['bucket'])->temporaryUrl($media_data['file_name'], Carbon::now()->addDay());
        dd($file);
    }
}
