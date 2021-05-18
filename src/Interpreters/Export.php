<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AExecutable;
use \Able\Bender\Abstractions\TInterpretatable;

use \Able\IO\Path;
use \Able\IO\Directory;
use \Able\IO\ReadingStream;

use \Able\Helpers\Str;
use \Able\Helpers\Arr;

use \Exception;

class Export
	extends AExecutable {

	use TInterpretatable;

	/**
	 * @var string[]
	 */
	private array $Points = [];

	/**
	 * @param string $line
	 * @return bool
	 *
	 * @throws Exception
	 */
	protected final function parseInterpretatable(string $line): bool {
		if (!preg_match('/^(.*)\s+as\s+([A-Za-z0-9_-]+)$/', $line, $Parsed)) {
			throw new \Exception(sprintf('Invalid format: %s!', $line));
		}

		if ($this->indent()->decreased) {
			$this->Points =  Arr::push(Arr::cut($this->Points, abs($this->indent()->interval) + 1), $Parsed[2]);
		}

		if ($this->indent()->increased) {
			$this->Points = Arr::push($this->Points, $Parsed[2]);
		}

		if (!$this->indent()->changed) {
			$this->Points = Arr::push(Arr::cut($this->Points), $Parsed[2]);
		}

		$this->registry()
			->register(Str::join('.', $this->Points), new Path($Parsed[1]));

		return true;
	}
}

