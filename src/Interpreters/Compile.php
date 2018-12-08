<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;
use \Able\Bender\Utilities\Registry;

use \Able\IO\ReadingBuffer;
use \Able\IO\ReadingStream;

use \ScssPhp\ScssPhp\Compiler;
use \Exception;

use \Generator;

class Compile
	extends AInterpriter {

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
		if (!preg_match('/^&([A-Za-z0-9_.-]+){([^}]+)}$/', $line, $Matches)) {
			throw new Exception(sprintf('Invalid target: %s!', $line));
		}

		if (is_null($Path = $this->registry()->search($Matches[1]))
			|| !$Path->isReadable()) {

				throw new Exception(sprintf('Invalid target allias: %s', $Matches[1]));
		}

		foreach(preg_split('/\s*,\s*/', $Matches[2], -1, PREG_SPLIT_NO_EMPTY) as $name) {
			$Target = $Path->toPath()->append($name);

			if (!$Target->isReadable()) {
				throw new Exception(sprintf('Invalid target destination: %s!', $Target->toString()));
			}

			$this->output()->write(call_user_func(function() use ($Target) {
				yield $this->Compiler->compile($Target->toFile()->getContent());
			}));
		}
	}

	public function finalize(): void {
		// TODO: Implement finalize() method.
	}
}
