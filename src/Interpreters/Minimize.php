<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;

use \Generator;

class Minimize
	extends AInterpriter {

	public function interpretate(string $line): void {
		echo sprintf("mm->%s\n", $line);
		// TODO: Implement interpretate() method.
	}

	public function finalize(): void {
		// TODO: Implement finalize() method.
	}

}
