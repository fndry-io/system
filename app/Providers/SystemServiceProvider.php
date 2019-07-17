<?php

namespace Foundry\System\Providers;

use Foundry\Core\Requests\FormRequestHandler;
use Foundry\Core\Support\ServiceProvider;
use Foundry\System\Entities\Role;
use Foundry\System\Entities\User;
use Foundry\System\Repositories\RoleRepository;
use Foundry\System\Repositories\UserRepository;
use Foundry\System\Services\RoleService;
use Foundry\System\Services\UserService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;


class SystemServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
	    $this->app->register(RouteServiceProvider::class);
	    $this->app->register(EventServiceProvider::class);
	    $this->app->register(AuthServiceProvider::class);
	    //$this->app->register(BroadcastServiceProvider::class);
	    $this->registerRepositories();
	    $this->registerServices();
    }

    public function registerRepositories()
    {
	    $this->app->bind(UserRepository::class, function($app) {
		    return new UserRepository(
			    $app['em'],
			    $app['em']->getClassMetaData(User::class)
		    );
	    });
	    $this->app->bind(RoleRepository::class, function($app) {
		    return new RoleRepository(
			    $app['em'],
			    $app['em']->getClassMetaData(Role::class)
		    );
	    });
    }

	public function registerServices()
	{
		$this->app->bind(UserService::class, function($app) {
			return new UserService(
				resolve(UserRepository::class)
			);
		});
		$this->app->bind(RoleService::class, function($app) {
			return new RoleService(
				resolve(RoleRepository::class)
			);
		});
		$this->app->singleton( 'Foundry\Core\Contracts\FormRequestHandler', function () {
			return new FormRequestHandler();
		} );
	}

	/**
	 * Boot the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerTranslations();
		$this->registerConfig();
		$this->registerViews();
		$this->registerFactories();
		$this->registerCommands();
		$this->loadMigrationsFrom(base_path('foundry/system/database/migrations'));
		$this->mergeDoctrinePaths(base_path('foundry/system/config/mappings'));
		$this->registerGates();

		Validator::extend('username', function ($attribute, $value, $parameters, $validator) {
			return preg_match('/^[A-Za-z0-9_]+$/', $value);
		});
	}


	/**
	 * Register config.
	 *
	 * @return void
	 */
	protected function registerConfig()
	{
//		$this->publishes([
//			base_path('foundry/system/config/config.php') => config_path('foundry_system.php'),
//		], 'config');

	}

	/**
	 * Register views.
	 *
	 * @return void
	 */
	public function registerViews()
	{
		$viewPath = resource_path('views/foundry/system');

		$sourcePath = base_path('foundry/system/resources/views');

		$this->publishes([
			$sourcePath => $viewPath
		],'views');

		$this->loadViewsFrom(array_merge(array_map(function ($path) {
			return $path . '/foundry/system';
		}, Config::get('view.paths')), [$sourcePath]), 'foundry_system');
	}

	/**
	 * Register translations.
	 *
	 * @return void
	 */
	public function registerTranslations()
	{
		$langPath = resource_path('lang/plugins/foundry_system');

		if (is_dir($langPath)) {
			$this->loadTranslationsFrom($langPath, 'foundry_system');
		} else {
			$this->loadTranslationsFrom(base_path('foundry/system/resources/lang'), 'foundry_system');
		}
		$this->publishes([
			base_path('foundry/system/resources/lang') => $langPath,
		]);
	}

	/**
	 * Register an additional directory of factories.
	 *
	 * @return void
	 */
	public function registerFactories()
	{

	}

	/**
	 * Registers the commands for this service provider
	 *
	 * @return void
	 */
	public function registerCommands()
	{
		$this->commands([
//			UsersRegisterCommand::class,
//			ThemeLinkCommand::class,
//			SymLinkCommand::class
		]);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [];
	}

	public function registerGates()
	{
		Gate::before(function (User $user, $ability) {
			/**
			 * @var User $user
			 */
			if ($user->isSuperAdmin()) {
				return true;
			}
		});
	}

}
