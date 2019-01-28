<?php
namespace Able\Bender\Structures;

use \Able\Struct\AStruct;
use \Able\Helpers\Arr;

use \Exception;

/**
 * @property string name
 * @property string value
 */
class SOption extends AStruct {

	/**
	 * @var array
	 */
	protected static array $Prototype = [
		'name', 'value',
	];

	/**
	 * @param string $value
	 * @return string
	 */
	public final function setNameProperty(string $value): string {
		return $value;
	}

	/**
	 * @param string $value
	 * @return string
	 */
	public final function setValueProperty(string $value): string {
		return $value;
	}

	/**
	 * @param string $line
	 * @return SOption
	 * @throws Exception
	 */
	public final function parse(string $line): SOption {
		if (!preg_match('/(^@[A-Za-z0-9_-]+)\s*=>\s*(.*)$/', $line, $Parsed)) {
			throw new Exception('Incorrect indentation characters!');
		}

		$this->name = $Parsed[1];
		$this->value = $Parsed[2];

		return $this;
 	}
}
