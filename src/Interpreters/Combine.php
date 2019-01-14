<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;
use \Able\Bender\Abstractions\TTarget;

use \Able\Bender\Utilities\Registry;

use \Able\IO\File;

use \Exception;
use \Generator;

class Combine
	extends AInterpriter {

	use TTarget;

	/**
	 * @param string $line
	 * @throws Exception
	 */
	public function interpretate(string $line): void {
		foreach ($this->parseTarget($line) as $Target) {
			$this->storage()->toWriter()->consume($Target->toReader());
		}
	}
}
