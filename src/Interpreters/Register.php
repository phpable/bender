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
	 * @throws Exception
	 */
	protected final function interpretate(string $line): void {
		if (!preg_match('/^(.*)\s+as\s+([A-Za-z0-9_-]+)$/', $line, $Parsed)) {
			throw new \Exception(sprintf('Invalid format: %s!', $line));
		}

		if ($this->Indent->decreased) {
			array_pop($this->points);
		}

		if ($this->Indent->increased) {
			array_push($this->points, $Parsed[2]);
		}

		if (!$this->Indent->increased && !$this->Indent->decreased) {
			$this->points = Arr::push(Arr::cut($this->points, 1), $Parsed[2]);
		}

		$this->Paths[Str::join('.', $this->points)] = $Parsed[1];
	}

	/**
	 * @param string $name
	 * @return Path|null
	 */
	public final function search(string $name): ?Path {
			return isset($this->Paths[$name]) ? new Path($this->Paths[$name]) : null;
	}

	/**
	 * @return void
	 */
	protected final function finalize(): void {
		foreach ($this->Paths as $name => $path) {
			if (preg_match('/^(.*)\.[A-Za-z0-9_-]+$/', $name, $Matches)) {
				$this->Paths[$name] = $this->Paths[$Matches[1]] . $this->Paths[$name];
			}
		}


		_dumpe($this->search('nm.tw'), $this->Paths);
	}
}

