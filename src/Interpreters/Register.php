<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Utilities\Registry;
use \Able\Bender\Abstractions\AInterpriter;

use \Able\IO\Directory;
use \Able\IO\Path;
use \Able\IO\ReadingStream;

use \Able\Helpers\Str;
use \Able\Helpers\Arr;

use \Exception;

class Register
	extends AInterpriter {

	/**
	 * @var string[]
	 */
	private array $Points = [];

	/**
	 * @param string $line
	 * @return void
	 *
	 * @throws Exception
	 */
	protected final function interpretate(string $line): void {
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

		$this->registry()->register(Str::join('.', $this->Points), new Path($Parsed[1]));
	}
}

