<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AStreamable;
use \Able\Bender\Abstractions\TTargetable;

use \Able\Bender\Compilers\SassCompiler;

use \Able\Helpers\Arr;

use \Exception;
use \Generator;

final class Compile
	extends AStreamable {

	use TTargetable;

	/**
	 * @var array
	 */
	private static array $Bindings = [];

	/**
	 * @param string $extension
	 * @param string $compiler
	 *
	 * @throws Exception
	 */
	public static function bind(string $extension, string $compiler): void {
		if (!preg_match('/^[A-Za-z0-9_-]{1,5}$/', $extension)) {
			throw new Exception(sprintf('Invalid extendions: %s!', $extension));
		}

		static::$Bindings[strtolower($extension)] = $compiler;
	}

	/**
	 * @param string $line
	 * @throws Exception
	 */
	public function interpretate(string $line): void {
		foreach ($this->targets($line) as $Target) {

			if (is_null($compiler = Arr::get(static::$Bindings, $Target->getExtension()))) {
				throw new Exception(sprintf('Unsupported target type: %s!', $Target->toString()));
			}

			$this->consume((new $compiler)->compile($Target));
		}
	}
}

Compile::bind('scss', SassCompiler::class);
