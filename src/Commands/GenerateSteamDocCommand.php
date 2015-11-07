<?php namespace RentAServer\SteamWebAPI\Commands;

use Illuminate\Console\Command;
use RentAServer\SteamWebAPI\SteamWebAPI;

class GenerateSteamDocCommand extends Command {

	protected $name = 'steamapi:docs';

	private $api;

	public function __construct(SteamWebAPI $api) {
		parent::__construct();
		$this->api = $api;
	}

	public function fire() {
		$list = $this->api->GetAPIList();

		$apidoc = "<?php namespace RentAServer\\SteamWebAPI\\Gen;\n\n";
		$apidoc .= "/**\n";

		foreach ($list as $name) {
			$iface = "<?php namespace RentAServer\\SteamWebAPI\\Gen;\n\ninterface $name {\n\n";

			foreach ($this->api->{$name}->getMethods() as $method) {
				$iface .= "\t/**\n";

				$params = [];
				foreach ($method->parameters as $param) {
					if ($param->name != 'key') {
						$def = '$' . $param->name;

						if ($param->optional) {
							$def .= ' = null';
						}

						$iface .= sprintf("\t * @param %s %s %s", $this->datatype($param->type), '$' . $param->name, isset($param->description)?$param->description:'') . "\n";
						$params[] = $def;
					}
				}

				$iface .= "\t * @version {$method->version}\n";
				$iface .= "\t */\n";
				$iface .= "\tpublic function {$method->name}(" . implode(', ', $params) . ");\n\n";
			}

			$iface .= "}\n";

			$apidoc .= " * @property \RentAServer\\SteamWebAPI\\Gen\\$name $$name\n";

			file_put_contents(__DIR__ . '/../Gen/' . $name . '.php', $iface);
		}

		$apidoc .= "*/\n";

		$apidoc .= "interface SteamWebAPIDefinition {\n}\n";

		file_put_contents(__DIR__ . '/../Gen/SteamWebAPIDefinition.php', $apidoc);
	}

	private function datatype($type) {
		switch ($type) {
		case 'uint32':
			return 'int';
		}
		return 'string';
	}
}