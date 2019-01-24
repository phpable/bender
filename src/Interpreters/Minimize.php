<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;
use \Able\Bender\Abstractions\AStreamable;

use \Able\IO\File;
use \Able\IO\Reader;
use \Able\IO\Directory;
use \Able\IO\ReadingStream;

use \MatthiasMullie\Minify\JS;
use \MatthiasMullie\Minify\CSS;

use \Generator;
use \Exception;

class Minimize
	extends AStreamable {

	/**
	 * @param File $File
	 * @return Reader
	 *
	 * @throws Exception
	 */
	public final function process(File $File): Reader {
		switch (self::contentType()) {
			case AInterpriter::CT_JS:
				$File->rewrite((new JS($File))->minify());
				break;
			case AInterpriter::CT_CSS:
				$File->rewrite((new CSS($File))->minify());
				break;
		}

		return $File->toReader();
	}
}
