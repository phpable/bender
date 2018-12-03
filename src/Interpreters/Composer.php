<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;
use \Able\IO\ReadingStream;

use \Exception;

class Composer
	extends AInterpriter {

	/**
	 * @param string $line
	 */
	protected function interpretate(string $line): void {
		echo sprintf("с==>%s:%s\n", $this->Indent->level, $line);
	}

	protected final function finalize(): void {
		// TODO: Implement finalize() method.
	}
}
