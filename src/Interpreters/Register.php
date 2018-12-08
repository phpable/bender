<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Utilities\Registry;
use \Able\Bender\Abstractions\AInterpriter;

use Able\IO\Directory;
use \Able\IO\Path;
use \Able\IO\ReadingStream;

use \Able\Helpers\Str;
use \Able\Helpers\Arr;

use \Exception;

class Register
	extends AInterpriter {

	/**
	 * @var Directory
	 */
	private Directory $Point;

	/**
	 * @param ReadingStream $Stream
	 * @param Directory $Point
	 */
	public final function __construct(ReadingStream $Stream, Directory $Point) {
		parent::__construct($Stream);
		$this->Point = $Point;
	}

	/**
	 * @var array
	 */
	private array $Paths = [];

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

		$this->Paths[Str::join('.', $this->Points)] = $Parsed[1];
	}

	/**
	 * @return Registry
	 * @throws Exception
	 */
	public final function toRegistry(): Registry {
		return new Registry(array_map(function ($_){
			return($this->Point->toPath()->append($_));
		}, $this->Paths));
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

