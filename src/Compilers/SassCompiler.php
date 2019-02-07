<?php
namespace Able\Bender\Compilers;

use \Able\Bender\Abstractions\ICompiler;

use \Able\IO\Abstractions\IPatchable;
use \Able\IO\Abstractions\IReader;

use \Able\IO\ReadingBuffer;
use \Able\IO\StringSource;

use \ScssPhp\ScssPhp\Compiler;

use \Exception;

class SassCompiler
	implements ICompiler {

	/**
	 * @param IPatchable $Target
	 * @return IReader
	 *
	 * @throws Exception
	 */
	public final function compile(IPatchable $Target): IReader {
		$Compiler = new Compiler();
		$Compiler->addImportPath($Target->toPath()->getParent()->toString());

		/**
		 * The compiler's result must always be a kind of reader,
		 * so wrapping like this is the only solution for external compilers for now.
		 */
		return new ReadingBuffer(new StringSource($Compiler->compile($Target->getContent())));
	}
}
