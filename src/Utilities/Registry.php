<?php
namespace Able\Bender\Utilities;

use \Able\IO\Path;
use \Able\IO\Directory;

use \Able\Prototypes\ICountable;
use \Able\Prototypes\IArrayable;

use \Exception;

class Registry
	implements ICountable, IArrayable {

	/**
	 * @var Path[]
	 */
	private array $Data = [];

	/**
	 * @return int
	 */
	public final function count(): int {
		return count($this->Data);
	}

	/**
	 * @param array $Data
	 */
	public final function __construct(array $Data = []) {
		$this->Data = $Data;
	}

	/**
	 * @param string $name
	 * @param Path $Path
	 */
	public final function register(string $name, Path $Path): void {
		$this->Data[$name] = $Path;
	}

	/**
	 * @param string $name
	 * @return Path|null
	 *
	 * @throws Exception
	 */
	public final function search(string $name): ?Path {
		return isset($this->Data[$name]) ? $this->Data[$name] : null;
	}

	/**
	 * @return array
	 */
	public final function toArray(): array {
		return $this->Data;
	}
}
