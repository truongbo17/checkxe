<?php

namespace Bo\Ecommerce;

use Bo\PluginManager\App\Services\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        \Schema::disableForeignKeyConstraints();
        \Schema::dropIfExists('ecommerces');
    }
}
