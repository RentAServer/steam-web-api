<?php namespace RentAServer\SteamWebAPI\Commands;

use Illuminate\Console\Command;
use RentAServer\SteamWebAPI\SteamWebAPI;

class SteamAPIUpdate extends Command {

	protected $name = 'steamapi:update';

	private $api;

	public function __construct(SteamWebAPI $api) {
		parent::__construct();
		$this->api = $api;
	}

	public function fire() {
		$this->info('Loading steam api list...');
		$this->api->LoadSupportedAPIList();
		$this->info('Done.');
	}
}