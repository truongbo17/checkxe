<?php

namespace Bo\PluginManager\App\Services;

interface PluginInterface
{
    public function active();

    public function remove();

    public function deactivate();
}
