<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;
use \Able\IO\ReadingStream;

use \Exception;

class Register
	extends AInterpriter {

	/**
	 * @param string $line
	 */
	public function interpretate(string $line): void {
		echo sprintf("r==>%s:%s\n", $this->Indent->level, $line);
	}
}
