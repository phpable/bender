<?php
namespace Able\Bender\Abstractions;

use \Able\Bender\Structures\SOption;

use \Able\Helpers\Arr;

use \Exception;

trait TOption {

	/**
	 * @var array
	 */
	private array $Options = [];

	/**
	 * @return array
	 */
	protected final function options(): array {
		return $this->Options;
	}

	/**
	 * @param string $line
	 * @return bool
	 *
	 * @throws Exception
	 */
	protected final function parseOption(string $line): bool {
		if (preg_match('/^-\s*([A-Za-z0-9_-]+)\s+(@[A-Za-z0-9_-]+\s*=>.*)$/', $line, $Parsed)) {
			$this->Options = Arr::improve($this->Options, strtolower($Parsed[1]), (new SOption())->parse($Parsed[2]));
			return true;
		}

		if (preg_match('/^-\s*([A-Za-z0-9_-]+)\s+(.*)$/', $line, $Parsed)) {
			$this->Options[strtolower($Parsed[1])] = $Parsed[2];
			return true;
		}

		if (preg_match('/^-\s*([A-Za-z0-9_-]+)\s*$/', $line, $Parsed)) {
			$this->Options[strtolower($Parsed[1])] = true;
			return true;
		}

		return false;
	}
}
