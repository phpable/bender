<?php
namespace Able\Bender\Structures;

use \Able\Struct\AStruct;
use \Able\Helpers\Arr;

use \Exception;

/**
 * @property int level
 * @property int size
 * @property bool increased
 * @property bool decreased
 * @property string text
 */
class SIndent extends AStruct {

	/**
	 * @var array
	 */
	protected static array $Prototype = [
		'level', 'text', 'increased', 'decreased',
	];

	/**
	 * @var bool
	 */
	protected const defaultIncreasedValue = false;

	/**
	 * @var bool
	 */
	protected const defaultDecreasedValue = false;

	/**
	 * @var bool
	 */
	protected const defaultLevelValue = 0;

	/**
	 * @param bool $value
	 * @return bool
	 */
	public final function setIncreasedProperty(bool $value): bool {
		return $value;
	}

	/**
	 * @param bool $value
	 * @return bool
	 */
	public final function setDecreasedProperty(bool $value): bool {
		return $value;
	}

	/**
	 * @var int[]
	 */
	private array $Stack = [];

	/**
	 * @param string $indent
	 * @throws Exception
	 */
	public final function analize(string $indent): void {
		if (preg_match('/[^\t]+/', $indent)) {
			_dumpe($indent);
			throw new Exception('Incorrect indentation characters!');
		}

		$this->decreased = false;
		$this->increased = false;

		if (strlen($indent) > Arr::last($this->Stack)) {
			array_push($this->Stack, strlen($indent));

			$this->increased = true;
			$this->level++;
		} else {
			if (strlen($indent) < Arr::last($this->Stack)) {
				array_pop($this->Stack);

				if (strlen($indent) <= Arr::last($this->Stack)) {
					$this->decreased = true;
					$this->level--;

				} else {
					array_push($this->Stack, strlen($indent));
				}
			}
		}
 	}

	/*
	public final function debuge() {
		echo sprintf("DEBUG: increased: %s, decreased: %s, level: %s\n",
			$this->increased ? 'true' : 'false',
			$this->decreased ? 'true' : 'false',
			$this->level
		);
 	}
	*/
}
