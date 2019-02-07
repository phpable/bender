<?php
namespace Able\Bender\Abstractions;

use \Able\Bender\Structures\SIndent;
use \Able\Reglib\Regex;

use \Exception;

trait TIndent {

	/**
	 * @var SIndent|null
	 */
	private ?SIndent $Indent = null;

	/**
	 * @return SIndent
	 */
	protected final function indent(): SIndent {
		if (is_null($this->Indent)) {
			$this->Indent = new SIndent();
		}

		return $this->Indent;
	}

	/**
	 * @param string $line
	 * @return bool
	 *
	 * @throws Exception
	 */
	protected final function analizeIndention(string &$line): bool {

		return $this->indent()
			->parse(Regex::create('/^\s+/')->retrieve($line))->level < 1;
	}
}
