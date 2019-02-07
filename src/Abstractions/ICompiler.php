<?php
namespace Able\Bender\Abstractions;

use \Able\IO\Abstractions\IReader;
use \Able\IO\Abstractions\IPatchable;


interface ICompiler {

	/**
	 * @param IPatchable $Target
	 * @return IReader
	 */
	public function compile(IPatchable $Target): IReader;
}
