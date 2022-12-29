<?php

namespace Bo\Base\Console\Commands;

use Illuminate\Console\Command;
use Bo\Base\Services\AddSidebarService;

class AddSidebarContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:dashboard:add-sidebar-content
                                {code : HTML/PHP code that shows the sidebar item. Use either single quotes or double quotes. Never both. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add HTML/PHP code to the Dashbroad BoCMS sidebar_content file';

    /**
     * Service AddSideBar Admin
     *
     * @var AddSidebarService $addSidebarContent
     * */
    protected AddSidebarService $addSidebarContent;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AddSidebarService $addSidebarContent)
    {
        parent::__construct();
        $this->addSidebarContent = $addSidebarContent;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $code = $this->argument('code');
        $this->info($this->addSidebarContent->add($code));

        return self::SUCCESS;
    }

}
