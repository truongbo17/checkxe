<?php

namespace Bo\Generators\Console\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'bo:test
        {--reset : reset crawl}
    ';

    public function handle()
    {
        dd(exist_plugin('blogs1'));
    }
}
