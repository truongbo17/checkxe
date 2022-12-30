<?php

namespace Bo\BackupManager\app\Console\Commands;

use Illuminate\Console\Command;
use Bo\Base\Console\Commands\Traits\PrettyCommandOutput;

class Install extends Command
{
    use PrettyCommandOutput;

    protected $progressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:backup:install
                                {--timeout=300} : How many seconds to allow each process to run.
                                {--debug} : Show process output or not. Useful for debugging.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Backup Manager interface.';

    /**
     * Execute the console command.
     *
     * @return mixed Command-line output
     */
    public function handle()
    {
        $this->progressBar = $this->output->createProgressBar(3);
        $this->progressBar->minSecondsBetweenRedraws(0);
        $this->progressBar->maxSecondsBetweenRedraws(120);
        $this->progressBar->setRedrawFrequency(1);
        $this->progressBar->start();

        $this->line(' Publishing backup manager provider');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Bo\BackupManager\BackupManagerServiceProvider',
            '--tag' => 'backup-config',
        ]);

        $this->line(' Publishing backup manager provider');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Bo\BackupManager\BackupManagerServiceProvider',
            '--tag' => 'lang',
        ]);

        $this->progressBar->finish();
        $this->info(' Bo\BackupManager installed.');
    }
}
