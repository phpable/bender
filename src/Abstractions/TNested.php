<?php
namespace Able\Bender\Abstractions;

use \Able\Bender\Abstractions\AExecutable;
use \Able\Helpers\Src;

use \Generator;

trait TNested {

//	/**
//	 * @var AExecutable[]
//	 */
//	protected array $Stack = [];

	/**
	 * @param string $line
	 * @return bool
	 *
	 * @throws Exception
	 */
	public final function parseNested(string $line): bool {
		if (preg_match('/^([A-Za-z0-9_-]+):\s*$/', $line, $Parsed)) {

			if (class_exists($class = sprintf('%s\\Interpreters\\%s',
			 	Src::lns(AStreamable::class, 2), Src::tcm($Parsed[1])))) {

				array_push($this->Stack, $this->process((new $class($this->stream()))->execute()));
				return true;
			}
		}

		return false;
	}

	/**
	 * @param Generator $Stream
	 * @return Generator
	 */
	protected function process(Generator $Stream): Generator {
		yield from $Stream;
	}
}
