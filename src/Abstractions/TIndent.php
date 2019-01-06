<?php
namespace Able\Bender\Abstractions;

use Able\Bender\Structures\SIndent;

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
}
