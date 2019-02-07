<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AExecutable;

use \Able\Bender\Abstractions\AStreamable;
use \Able\Bender\Abstractions\TTargetable;

use \Able\Bender\Utilities\Registry;

use \Able\IO\File;

use \Exception;
use \Generator;

class Combine
	extends AStreamable {

	use TTargetable;

	/**
	 * @param string $line
	 * @throws Exception
	 */
	public function interpretate(string $line): void {
		foreach ($this->targets($line) as $Target) {
			$this->storage()->toWriter()->consume($Target->toReader());
		}
	}
}
