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
	 * @var Directory
	 */
	protected Directory $Directory;

	/**
	 * @return Path
	 */
	public final function toPath(): Path {
		return $this->Directory->toPath();
	}

	/**
	 * Registry constructor.
	 * @param Directory|null $Directory
	 * @throws Exception
	 */
	public final function __construct(?Directory $Directory = null) {
		$this->Directory = !is_null($Directory) ? $Directory : (new Path(getcwd()))->toDirectory();
	}

	/**
	 * @param string $name
	 * @param Path $Path
	 *
	 * @throws Exception
	 */
	public final function register(string $name, Path $Path): void {
		if (preg_match('/^(.*)\.[A-Za-z0-9_-]+$/', $name, $Matches)) {
			if (is_null($Prefix = $this->search($Matches[1]))) {
				throw new \Exception(sprintf('Invalid refix: %s!', $Matches[1]));
			}

			$Path->prepend($Prefix);
		} else {
			$Path->prepend($this->Directory);
		}

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
