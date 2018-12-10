<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;
use \Able\Bender\Abstractions\TTarget;

use \Able\Bender\Utilities\Registry;

use \Able\IO\ReadingBuffer;
use \Able\IO\ReadingStream;

use \ScssPhp\ScssPhp\Compiler;
use \Exception;

use \Generator;

class Compile
	extends AInterpriter {

	use TTarget;

	/**
	 * @var Compiler
	 */
	private Compiler $Compiler;

	public final function __construct(ReadingStream $Stream, Registry $Registry) {
		parent::__construct($Stream, $Registry);
		$this->Compiler = new Compiler();
	}

	/**
	 * @param string $line
	 * @throws Exception
	 */
	public function interpretate(string $line): void {
		foreach ($this->parseTarget($line) as $Target) {
			$this->output()->write(call_user_func(function() use ($Target) {
				yield $this->Compiler->compile($Target->getContent());
			}));
		}
	}

	public function finalize(): void {
		// TODO: Implement finalize() method.
	}
}
