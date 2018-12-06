<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;

use \Able\IO\Path;
use \Able\IO\ReadingStream;

use \Able\Helpers\Str;
use \Able\Helpers\Arr;

use \Exception;

class Register
	extends AInterpriter {

	/**
	 * @var array
	 */
	private array $Paths = [];

	/**
	 * @var string[]
	 */
	private array $points = [];

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
			$this->points =  Arr::push(Arr::cut($this->points, abs($this->indent()->interval) + 1), $Parsed[2]);
		}

		if ($this->indent()->increased) {
			$this->points = Arr::push($this->points, $Parsed[2]);
		}

		if (!$this->indent()->changed) {
			$this->points = Arr::push(Arr::cut($this->points), $Parsed[2]);
		}

		$this->Paths[Str::join('.', $this->points)] = $Parsed[1];
	}

	/**
	 * @param string $name
	 * @return Path|null
	 *
	 * @throws Exception
	 */
	public final function search(string $name): ?Path {
			return isset($this->Paths[$name]) ? new Path($this->Paths[$name]) : null;
	}

	/**
	 * @throws Exception
	 */
	protected final function finalize(): void {
		foreach ($this->Paths as $name => $path) {
			if (preg_match('/^(.*)\.[A-Za-z0-9_-]+$/', $name, $Matches)) {
				$this->Paths[$name] = $this->Paths[$Matches[1]] . $this->Paths[$name];
			}
		}
	}
}

