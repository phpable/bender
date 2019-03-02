<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\TNested;
use \Able\Bender\Abstractions\AExecutable;
use \Able\Bender\Abstractions\TTargetable;
use \Able\Bender\Abstractions\TInterpretatable;

use \Able\Bender\Compilers\SassCompiler;

use \Able\Helpers\Arr;

use \Exception;
use \Generator;

final class Compile
	extends AExecutable {

	use TNested;
	use TTargetable;
	use TInterpretatable;

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
	 * @return bool
	 *
	 * @throws Exception
	 */
	protected final function parseInterpretatable(string $line): bool {
		foreach ($this->targets($line) as $Target) {

			if (is_null($compiler = Arr::get(static::$Bindings, $Target->getExtension()))) {
				throw new Exception(sprintf('Unsupported target type: %s!', $Target->toString()));
			}

			array_push($this->Stack, (new $compiler)->compile($Target)->read());
		}

		return true;
	}
}

Compile::bind('scss', SassCompiler::class);
