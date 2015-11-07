<?php namespace RentAServer\SteamWebAPI;

use IllegalArgumentException;
use stdClass;
use GuzzleHttp\Client;

/**
 * @inheritdoc \RentAServer\SteamWebAPI\Gen\SteamWebAPIDefinition
 * @package RentAServer\SteamWebAPI
 */
class SteamWebAPI {

	private $cachePath;

	public $key;

	public $interfaces = [];

	public function __construct($key, $cachePath, $scheme = 'http') {
		$this->key = $key;

		$this->cachePath = $cachePath;

		$this->client = new Client([
			'base_uri' => $scheme . '://api.steampowered.com/'
		]);
	}

	public function __get($property) {
		if (array_key_exists($property, $this->interfaces)) {
			return $this->interfaces[$property];
		}

		$this->interfaces[$property] = $this->LoadAPI($property);

		return $this->interfaces[$property];
	}

	public function GetAPIList() {
		$dir = opendir($this->cachePath);

		$out = [];

		while ($file = readdir($dir)) {
			if ($file[0] == '.') {
				continue;
			}

			$out[] = substr($file, 0, strpos($file, '.'));
		}

		closedir($dir);

		return $out;
	}

	public function LoadAPI($name) {
		$file = $this->cachePath . '/' . $name . '.json';

		if (file_exists($file)) {
			$interface = json_decode(file_get_contents($file));

			return $this->interfaces[$name] = new SteamWebAPIInterface($this, $interface->name, $interface->methods);
		}

		throw new IllegalArgumentException('Invalid interface.');
	}

	public function LoadSupportedAPIList() {
		if (!is_dir($this->cachePath)) {
			mkdir($this->cachePath, 0777, true);
		}

		$json = $this->request('ISteamWebAPIUtil', 'GetSupportedAPIList', 1, $this->make_request());

		// Map it into a format we can easily use
		foreach ($json->apilist->interfaces as $obj) {
			$api = new stdClass;

			$api->name = $obj->name;
			$api->methods = new stdClass;
			foreach ($obj->methods as $method) {
				$api->methods->{$method->name} = $method;
			}

			file_put_contents($this->cachePath . '/' . $obj->name . '.json', json_encode($api));
		}
	}

	private function make_request(array $info = []) {
		return array_merge($info, [ 'key' => $this->key ]);
	}

	public function request($api, $method, $version, array $info, $http_method = 'GET') {
		$version = 'v' . str_pad("$version", 4, "0", STR_PAD_LEFT);

		$url = sprintf("%s/%s/%s/", $api, $method, $version);

		$options = [];

		if ($method == 'POST') {
			$options['form_params'] = $info;
		} else {
			$options['query'] = $info;
		}

		$res = $this->client->request($http_method, $url, $options);

		$json = json_decode($res->getBody());

		if (!$json) {
			throw new \Exception('Invalid response from Steam API');
		}

		return $json;
	}
}