<?php
namespace Able\Bender\Abstractions;

use \Generator;
use \Exception;

trait TTargetable  {

	/**
	 * @param string $line
	 * @return Generator
	 *
	 * @throws Exception
	 */
	protected final function targets(string $line): Generator {
		if (!preg_match('/^&([A-Za-z0-9_.-]+){([^}]+)}$/',
			$line, $Matches)) {

				throw new Exception(sprintf('Invalid target: %s', $Matches[1]));
		}

		if (is_null($Path = $this->registry()->search($Matches[1]))
			|| !$Path->isReadable()) {

				_dumpe($Path);

				throw new Exception(sprintf('Invalid target allias: %s', $Matches[1]));
		}

		foreach(preg_split('/\s*,\s*/', $Matches[2], -1, PREG_SPLIT_NO_EMPTY) as $name) {
			$Target = $Path->toPath()->append($name);

			if (!$Target->isReadable()) {
				throw new Exception(sprintf('Invalid target destination: %s!', $Target->toString()));
			}

			yield $Target->toFile();
		}
	}
}
