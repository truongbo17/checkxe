<?php

namespace Bo\PluginManager;

use Bo\PluginManager\App\Services\Plugin;
use Bo\PluginManager\App\Services\PluginInterface;
use Composer\Autoload\ClassLoader;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Psr\SimpleCache\InvalidArgumentException;

class PluginManagerProvider extends ServiceProvider
{
    /**
     * path route
     * @var string $routeFilePath
     * */
    private string $routeFilePath = '/routes/pluginmanager.php';

    /**
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/pluginmanager.php',
            'bo.pluginmanager'
        );

        $this->loadTranslationsFrom(realpath(__DIR__ . '/resources/lang'), 'pluginmanager');

        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'pluginmanager');

        $this->setupRoutes($this->app->router);

        $this->loadHelpers();

        \SideBarDashBoard::registerItem('plugin')
            ->setLabel(trans('pluginmanager::pluginmanager.name'))
            ->setPosition(2)
            ->setRoute(bo_url(config('bo.pluginmanager.route')))
            ->setIcon('nav-icon las la-braille')
            ->setGroup('setting_group')
            ->render();

        $this->registerPluginActivated();
    }

    /**
     * Register plugin activated with loader
     *
     * @return void
     *
     * @throws FileNotFoundException|InvalidArgumentException
     * @throws Exception
     */
    private function registerPluginActivated(): void
    {
        $plugin_construct = new Plugin(new File());
        $activated_plugins = $plugin_construct->getAllPluginActivated();
        if (count($activated_plugins) > 0) {
            $loader = new ClassLoader();
            $providers = [];
            $namespaces = [];

            if (cache()->has('plugin_namespaces') && cache()->has('plugin_providers')) {
                $providers = cache('plugin_providers');
                if (!is_array($providers) || empty($providers) || count($providers) != count($activated_plugins)) {
                    $providers = [];
                }

                $namespaces = cache('plugin_namespaces');

                if (!is_array($namespaces) || empty($namespaces) || count($namespaces) != count($activated_plugins)) {
                    $namespaces = [];
                }
            }

            if (empty($namespaces) || empty($providers)) {
                foreach ($activated_plugins as $plugin) {
                    if (empty($plugin)) {
                        continue;
                    }

                    $content = $plugin_construct->getPlugin($plugin);
                    if (!empty($content)) {
                        if (Arr::has($content, 'namespace') && !class_exists($content['provider'])) {
                            $namespaces[$plugin] = $content['namespace'];
                        }

                        $providers[] = $content['provider'];
                    }
                }

                if (count($providers) == count($activated_plugins) && count($namespaces) == count($activated_plugins)) {
                    cache()->forever('plugin_namespaces', $namespaces);
                    cache()->forever('plugin_providers', $providers);
                }
            }

            foreach ($namespaces as $key => $namespace) {
                $loader->setPsr4($namespace, plugin_path($key . '/src'));
            }

            $loader->register();

            foreach ($providers as $provider) {
                if (!class_exists($provider)) {
                    continue;
                }

                $this->app->register($provider);
            }
        }
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in package
        $routeFilePathInUse = __DIR__ . $this->routeFilePath;

        $this->loadRoutesFrom($routeFilePathInUse);
    }

    private function loadHelpers()
    {
        require_once __DIR__ . '/helpers/plugins.php';
    }

    public function register()
    {
        $this->app->bind(PluginInterface::class, Plugin::class);
    }
}
