<?php namespace RentAServer\SteamWebAPI\Facades;

use Illuminate\Support\Facades\Facade;

class SteamWebAPI extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'steamapi::webapi';
	}

}