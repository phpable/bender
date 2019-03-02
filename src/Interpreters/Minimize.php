<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\TNested;
use \Able\Bender\Abstractions\AExecutable;

use \Able\IO\File;
use \Able\IO\Reader;
use \Able\IO\StringSource;
use \Able\IO\ReadingBuffer;
use \Able\IO\WritingBuffer;

use \MatthiasMullie\Minify\JS;
use \MatthiasMullie\Minify\CSS;

use \Able\Helpers\Arr;

use \Generator;
use \Exception;

class Minimize
	extends AExecutable {

	use TNested;

	/**
	 * @param Generator $Stream
	 * @return Generator
	 *
	 * @throws Exception
	 */
	protected final function process(Generator $Stream): Generator {
		switch (self::contentType()) {
			case AExecutable::CT_JS:

				return (new StringSource(
					(new JS(
						(new WritingBuffer($Stream))->getContent())
					)->minify()
				))->toReadingBuffer()->read();

				break;
			case AExecutable::CT_CSS:
				return (new StringSource(
					(new CSS(
						(new WritingBuffer($Stream))->getContent())
					)->minify()
				))->toReadingBuffer()->read();

				break;
		}

		throw new Exception(sprintf('Undefine content type: %s', self::contentType()));
	}
//
//	/**
//	 * @param string $type
//	 * @param string $fragment
//	 * @param string $content
//	 * @return string
//	 */
//	private final function rebase(string $type, string $fragment, string $content): string {
//		preg_match('/{[^{]+([A-Za-z0-9_-]+)\s*:\s*[^:]+url\((?:\'[^\']+\'|"[^"]+")\)/', $content, $Matches);
//
//		_dumpe(__LINE__, $Matches);
//	}
}
