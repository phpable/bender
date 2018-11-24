<?php
namespace Able\Bender\Structures;

use \Able\Struct\AStruct;
use \Able\Helpers\Arr;

use \Exception;

/**
 * @property int level
 * @property int size
 * @property bool changed
 * @property bool increased
 * @property bool decreased
 * @property string text
 */
class SIndent extends AStruct {

	/**
	 * @var array
	 */
	protected static array $Prototype = [
		'level', 'text', 'changed', 'increased', 'decreased',
	];

	/**
	 * @var bool
	 */
	protected const defaultChangedValue = false;

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
	public final function setChangedProperty(bool $value): bool {
		return $value;
	}

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
			throw new Exception('Incorrect indentation characters!');
		}

		$this->changed = false;

		$this->decreased = false;
		$this->increased = false;

		if (strlen($indent) > Arr::last($this->Stack)) {
			array_push($this->Stack, strlen($indent));

			$this->changed = true;
			$this->increased = true;
			$this->level++;
		} else {

			while (strlen($indent) < Arr::last($this->Stack)) {
				array_pop($this->Stack);

				$this->changed = true;
				$this->decreased = true;
				$this->level--;
			}
		}
 	}
}
