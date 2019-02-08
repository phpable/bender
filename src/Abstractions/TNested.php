<?php
namespace Able\Bender\Abstractions;

use \Able\Helpers\Src;

trait TNested {

	/**
	 * @param string $line
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public final function parseNested(string $line) {
		if (preg_match('/^([A-Za-z0-9_-]+):\s*$/', $line, $Parsed)) {

			if (class_exists($class = sprintf('%s\\Interpreters\\%s',
			 	Src::lns(AStreamable::class, 2), Src::tcm($Parsed[1])))) {

				return (new $class($this->stream()))->execute();
			}
		}
	}
}
