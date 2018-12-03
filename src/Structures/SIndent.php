<?php
namespace Able\Bender\Structures;

use \Able\Struct\AStruct;
use \Able\Helpers\Arr;

use \Exception;

/**
 * @property int level
 * @property int interval
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
		'level', 'text', 'interval', 'changed', 'increased', 'decreased',
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
	 * @var bool
	 */
	protected const defaultIntervalValue = 0;

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
		if ($this->increased !== $value) {
			$this->changed = true;
		}

		return $value;
	}

	/**
	 * @param bool $value
	 * @return bool
	 */
	public final function setDecreasedProperty(bool $value): bool {
		if ($this->decreased !== $value) {
			$this->changed = true;
		}

		return $value;
	}

	/**
	 * @param int $value
	 * @return int
	 */
	public final function setLevelProperty(int $value): int {
		$this->interval += ($value - $this->level);

		if ($this->level > $value) {
			$this->decreased = true;
		}

		if ($this->level < $value) {
			$this->increased = true;
		}

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

		$this->flush('interval',
			'decreased', 'increased', 'changed');

		if (strlen($indent) > Arr::last($this->Stack)) {
			array_push($this->Stack, strlen($indent));

			$this->level++;
		} else {
			while (strlen($indent) < Arr::last($this->Stack)) {
				array_pop($this->Stack);

				$this->level--;
			}
		}
 	}
}
