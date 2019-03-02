<?php
namespace Able\Bender\Abstractions;

trait TInterpretatable {

	/**
	 * @param string $line
	 * @return bool
	 */
	abstract protected function parseInterpretatable(string $line): bool;
}
