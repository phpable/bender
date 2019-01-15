<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AStreamable;

use \Able\IO\File;
use \Able\IO\Reader;
use \Able\IO\Directory;
use \Able\IO\ReadingStream;

use \Generator;

class Minimize
	extends AStreamable {

	public final function process(File $File): Reader {
		return parent::process($File);
	}
}
