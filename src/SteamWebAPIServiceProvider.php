<?php namespace RentAServer\SteamWebAPI;

use Illuminate\Support\ServiceProvider;
use RentAServer\Console\Commands\GenerateSteamDocCommand;
use RentAServer\Console\Commands\SteamAPIUpdate;

class SteamWebAPIServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->app->alias('steamapi::webapi', 'RentAServer\SteamWebAPI\SteamWebAPI');

		$this->commands([ 'command.steamapi.update', 'command.steamapi.docs' ]);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->singleton('steamapi::webapi', function ($app) {
			return new SteamWebAPI(env('STEAMAPI_KEY', ''), storage_path('app/steamapi'));
		});

		$this->app->singleton('command.steamapi.update', function($app) {
			return new SteamAPIUpdate($app['steamapi::webapi']);
		});

		$this->app->singleton('command.steamapi.docs', function($app) {
			return new GenerateSteamDocCommand($app['steamapi::webapi']);
		});
	}

	/**
	 * Get the services provided.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'command.steamapi.update',
			'command.steamapi.docs'
		];
	}
}
