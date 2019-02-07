<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AExecutable;
use \Able\Bender\Abstractions\AStreamable;

use \Able\IO\File;
use \Able\IO\Reader;
use \Able\IO\Directory;
use \Able\IO\ReadingStream;

use \MatthiasMullie\Minify\JS;
use \MatthiasMullie\Minify\CSS;

use \Able\Helpers\Arr;

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
			case AExecutable::CT_JS:
				$File->rewrite((new JS($File))->minify());
				break;
			case AExecutable::CT_CSS:
				$File->rewrite((new CSS($File))->minify());

//				_dumpe($this->options());

				foreach (Arr::get($this->options(), 'rebase', []) as $Option) {
					$File->rewrite($this->rebase($Option->name, $Option->value, $File->getContent()));
				}

				break;
		}

		return $File->toReader();
	}

	/**
	 * @param string $type
	 * @param string $fragment
	 * @param string $content
	 * @return string
	 */
	private final function rebase(string $type, string $fragment, string $content): string {
		preg_match('/{[^{]+([A-Za-z0-9_-]+)\s*:\s*[^:]+url\((?:\'[^\']+\'|"[^"]+")\)/', $content, $Matches);

		_dumpe(__LINE__, $Matches);
	}
}
