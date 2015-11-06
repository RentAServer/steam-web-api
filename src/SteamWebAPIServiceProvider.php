<?php namespace RentAServer\SteamWebAPI;

use Illuminate\Support\ServiceProvider;

class SteamWebAPIServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
		//
		$this->app->alias('steamapi::webapi', 'RentAServer\Services\SteamWebAPI');
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
	}

}
