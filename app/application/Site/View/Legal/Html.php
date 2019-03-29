<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

namespace Site\View\Legal;

use Awf\Text\Text;

class Html extends \Awf\Mvc\View
{
	public $lang = 'en-GB';

	public $page = 'notfound';

	public $content = '';

	public function onBeforePrivacy(): bool
	{
		$this->page    = 'privacy';
		$this->lang    = Text::detectLanguage();
		$this->content = Text::_('SITE_LEGAL_NOTFOUND');
		$file          = $this->findFile();

		if (is_null($file))
		{
			return true;
		}

		$this->content = $this->loadHtml($file);

		return true;
	}

	public function onBeforeTos(): bool
	{
		$this->page    = 'tos';
		$this->lang    = Text::detectLanguage();
		$this->content = Text::_('SITE_LEGAL_NOTFOUND');
		$file          = $this->findFile();

		if (is_null($file))
		{
			return true;
		}

		$this->content = $this->loadHtml($file);

		return true;
	}

	public function findFile(): ?string
	{
		$folder = APATH_APPROOT . '/Site/Legal/';
		$files  = [
			$folder . $this->page . '-' . $this->lang . '.html',
			$folder . $this->page . '-en-GB.html',
			$folder . $this->page . '.html',
		];

		foreach ($files as $file)
		{
			if (file_exists($file))
			{
				return $file;
			}
		}

		return null;
	}

	public function loadHtml($file): string
	{
		$content = file_get_contents($file);
		$regex   = '/<body.*>(.*)<\/body/isU';
		preg_match($regex, $content, $matches);

		return $matches[1];
	}
}