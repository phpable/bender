<?php
namespace Able\Bender\Abstractions;

use \Able\Helpers\Src;

trait TNested {

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

				$this->storage()->toWriter()
					->consume($this->process((new $class($this->stream(), $this->Output))->execute()->storage()));

				return true;
			}
		}

		return false;
	}
}
