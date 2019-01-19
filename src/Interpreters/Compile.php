<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AStreamable;
use \Able\Bender\Abstractions\TTarget;

use \Able\Bender\Utilities\Registry;

use \Able\IO\Directory;
use \Able\IO\ReadingStream;

use \ScssPhp\ScssPhp\Compiler;

use \Exception;
use \Generator;

class Compile
	extends AStreamable {

	use TTarget;

	/**
	 * @param string $line
	 * @throws Exception
	 */
	public function interpretate(string $line): void {
		foreach ($this->parseTarget($line) as $Target) {

			$Compiler = new Compiler();
			$Compiler->addImportPath($Target->toPath()->getParent()->toString());

			$this->storage()->append($Compiler->compile($Target->getContent()));
		}
	}
}
