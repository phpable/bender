<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;

use Able\IO\Directory;
use Able\IO\ReadingStream;
use \Generator;

class Minimize
	extends AInterpriter {

	public final function __construct(ReadingStream $Stream, Directory $Point) {
		parent::__construct($Stream, $Point);
	}

	public function finalize(): void {
		/** do nothing  */
 	}
}
