<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AStreamable;
use \Able\Bender\Abstractions\TTarget;

use \Able\Bender\Utilities\Registry;

use \Able\IO\File;

use \Exception;
use \Generator;

class Combine
	extends AStreamable {

	use TTarget;

	/**
	 * @param string $line
	 * @throws Exception
	 */
	public function interpretate(string $line): void {
		_dumpe($this->registry()->toArray());

		foreach ($this->parseTarget($line) as $Target) {
			$this->storage()->toWriter()->consume($Target->toReader());
		}
	}
}
