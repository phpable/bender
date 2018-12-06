<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;

use \Generator;

class Copy
	extends AInterpriter {

	public function interpretate(string $line): void {
		echo sprintf("cp->%s\n", $line);
		// TODO: Implement interpretate() method.
	}

	public function finalize(): void {
		// TODO: Implement finalize() method.
	}

}
