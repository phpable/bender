<?php
namespace Able\Bender\Abstractions;

trait TInterpretatable {

	/**
	 * @param string $line
	 * @return mixed
	 */
	abstract protected function parseInterpretatable(string $line);
}
