<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;
use \Able\Bender\Abstractions\TTarget;

use \Able\Bender\Utilities\Registry;

use \Able\IO\Directory;
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

	/**
	 * @param ReadingStream $Stream
	 * @param Directory $Point
	 * @throws Exception
	 */
	public final function __construct(ReadingStream $Stream, Directory $Point) {
		parent::__construct($Stream, $Point);
		$this->Compiler = new Compiler();
	}

	/**
	 * @param string $line
	 * @throws Exception
	 */
	public function interpretate(string $line): void {
		foreach ($this->parseTarget($line) as $Target) {
			$this->input()->write(call_user_func(function() use ($Target) {

				yield $this->Compiler
					->compile($Target->getContent());
			}));
		}
	}

	public function finalize(): void {
		// TODO: Implement finalize() method.
	}
}
