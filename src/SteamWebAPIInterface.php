<?php namespace RentAServer\SteamWebAPI;

class SteamWebAPIInterface {
	private $name;
	private $api;

	private $methods;

	public function __construct($api, $name, $methods) {
		$this->api = $api;
		$this->name = $name;
		$this->methods = $methods;
	}

	public function getMethods() {
		return $this->methods;
	}

	public function __call($name, $arguments) {
		if (!empty($this->methods->{$name})) {
			$api = $this->methods->{$name};

			$args = array();

			$parameters = $api->parameters;

			// Remove the parameter for our key
			$paramCount = count($parameters);
			for ($i = 0; $i < $paramCount; $i++) {
				if ($parameters[$i]->name == 'key') {
					array_splice($parameters, $i, 1);
					break;
				}
			}

			$paramCount = count($parameters);
			$argCount = count($arguments);

			if (!empty($arguments)) {
				if (is_array($arguments[0])) {
					$arguments = $arguments[0];
					// Verify arguments exist
					for ($i = 0; $i < $paramCount; $i++) {
						$arg = $parameters[$i];
						if (!isset($arguments[$arg->name])) {
							return;
						}
						$args[$arg->name] = $arguments[$arg->name];
					}
				} else {
					// Verify arguments and order
					for ($i = 0; $i < $paramCount; $i++) {
						$arg = $parameters[$i];
						if ($argCount <= $i + 1 && !empty($arg->optional)) {
							continue;
						}
						$args[$arg->name] = $arguments[$i];
					}
				}
			}

			$args['key'] = $this->api->key;

			return $this->api->request($this->name, $name, $api->version, $args, $api->httpmethod);
		}
	}
}