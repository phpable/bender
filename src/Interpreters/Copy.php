<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AStreamable;

use \Generator;

class Copy
	extends AStreamable {

	public function interpretate(string $line): void {
		echo sprintf("cp->%s\n", $line);
		// TODO: Implement interpretate() method.
	}

	public function finalize(): void {
		// TODO: Implement finalize() method.
	}

}
