<?php

namespace Bo\Contact;

use Bo\PluginManager\App\Services\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        \Schema::disableForeignKeyConstraints();
        \Schema::dropIfExists('contacts');
    }
}
