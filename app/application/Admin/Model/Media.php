<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */


namespace Admin\Model;


use Awf\Mvc\Model;
use Awf\Text\Text;
use Awf\Utils\Path;
use RuntimeException;

class Media extends Model
{
	/**
	 * Path where media files are stored
	 */
	public const mediaPath = APATH_BASE . '/media/images/events';

	/**
	 * Allowed image extensions
	 */
	public const allowedExtensions = ['png', 'jpg', 'jpeg', 'bmp', 'gif'];

	/**
	 * Allowed MIME types for upload
	 */
	public const allowedMime = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp'];

	/**
	 * Returns a list of image files in the self::mediaPath folder
	 *
	 * @return  array
	 */
	public function getImages(): array
	{
		$ret = [];
		$di  = new \DirectoryIterator(self::mediaPath);

		foreach ($di as $file)
		{
			if (!$di->isFile())
			{
				continue;
			}

			if (!in_array(strtolower($di->getExtension()), self::allowedExtensions))
			{
				continue;
			}

			$ret[] = $file->getFilename();
		}

		return $ret;
	}

	/**
	 * Returns the MIME type of the given file
	 *
	 * @param   string $file The full path to the file
	 *
	 * @return  string|null
	 */
	private function getMime(string $file): ?string
	{
		$mime = null;

		try
		{
			$imagesize = getimagesize($file);
			$mime      = isset($imagesize['mime']) ? $imagesize['mime'] : null;

			if (is_null($mime) && function_exists('mime_content_type'))
			{
				$mime = mime_content_type($file);
			}

			if (is_null($mime) && function_exists('finfo_open'))
			{
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$mime  = finfo_file($finfo, $file);
				finfo_close($finfo);
			}
		}
		catch (\Exception $e)
		{
			return null;
		}

		// We have a mime here
		return $mime;
	}

	/**
	 * Makes file name safe to use
	 *
	 * @param   string $file The name of the file [not full path]
	 *
	 * @return  string  The sanitised string
	 */
	private function makeFilenameSafe(string $file): string
	{
		// Remove any trailing dots, as those aren't ever valid file names.
		$file = rtrim($file, '.');

		$regex = ['#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#'];

		return trim(preg_replace($regex, '', $file));
	}

	/**
	 * Check if the PHP file upload structure is a file allowed to be uploaded
	 *
	 * @param   array $file
	 *
	 * @return  bool
	 *
	 * @throws  RuntimeException  If the file is not allowed (also tells why)
	 */
	private function canUpload(array $file): bool
	{
		if (empty($file['name']))
		{
			throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_UPLOAD_INPUT'));
		}

		if (str_replace(' ', '', $file['name']) !== $file['name'] || $file['name'] !== $this->makeFilenameSafe($file['name']))
		{
			throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_WARNFILENAME'));
		}

		$filetypes = explode('.', $file['name']);

		if (count($filetypes) < 2)
		{
			// There seems to be no extension
			throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_WARNFILETYPE'));
		}

		array_shift($filetypes);

		// Media file names should never have executable extensions buried in them.
		$executable = [
			'php', 'js', 'exe', 'phtml', 'java', 'perl', 'py', 'asp', 'dll', 'go', 'ade', 'adp', 'bat', 'chm', 'cmd',
			'com', 'cpl', 'hta', 'ins', 'isp',
			'jse', 'lib', 'mde', 'msc', 'msp', 'mst', 'pif', 'scr', 'sct', 'shb', 'sys', 'vb', 'vbe', 'vbs', 'vxd',
			'wsc', 'wsf', 'wsh',
		];

		$check = array_intersect($filetypes, $executable);

		if (!empty($check))
		{
			throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_WARNFILETYPE'));
		}

		$filetype  = array_pop($filetypes);
		$allowable = array_map('trim', self::allowedExtensions);

		if ($filetype == '' || $filetype == false || (!in_array($filetype, $allowable)))
		{
			throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_WARNFILETYPE'));
		}

		// If tmp_name is empty, then the file was bigger than the PHP limit
		if (!empty($file['tmp_name']))
		{
			// Get the mime type this is an image file
			$mime = $this->getMime($file['tmp_name']);

			// Did we get anything useful?
			if (!is_null($mime))
			{
				if (!in_array($mime, self::allowedMime))
				{
					throw new RuntimeException(Text::sprintf('AWF_MEDIA_ERROR_WARNINVALID_MIMETYPE', $mime));
				}
			}
			// We can't detect the mime type so it looks like an invalid image
			else
			{
				throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_WARNINVALID_IMG'));
			}
		}
		else
		{
			throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_WARNFILETOOLARGE'));
		}

		$xss_check = file_get_contents($file['tmp_name'], false, null, -1, 256);

		$html_tags = [
			'abbr', 'acronym', 'address', 'applet', 'area', 'audioscope', 'base', 'basefont', 'bdo', 'bgsound', 'big',
			'blackface', 'blink',
			'blockquote', 'body', 'bq', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col', 'colgroup',
			'comment', 'custom', 'dd', 'del',
			'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'fn', 'font', 'form', 'frame', 'frameset', 'h1',
			'h2', 'h3', 'h4', 'h5', 'h6',
			'head', 'hr', 'html', 'iframe', 'ilayer', 'img', 'input', 'ins', 'isindex', 'keygen', 'kbd', 'label',
			'layer', 'legend', 'li', 'limittext',
			'link', 'listing', 'map', 'marquee', 'menu', 'meta', 'multicol', 'nobr', 'noembed', 'noframes', 'noscript',
			'nosmartquotes', 'object',
			'ol', 'optgroup', 'option', 'param', 'plaintext', 'pre', 'rt', 'ruby', 's', 'samp', 'script', 'select',
			'server', 'shadow', 'sidebar',
			'small', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea',
			'tfoot', 'th', 'thead', 'title',
			'tr', 'tt', 'ul', 'var', 'wbr', 'xml', 'xmp', '!DOCTYPE', '!--',
		];

		foreach ($html_tags as $tag)
		{
			// A tag is '<tagname ', so we need to add < and a space or '<tagname>'
			if (stripos($xss_check, '<' . $tag . ' ') !== false || stripos($xss_check, '<' . $tag . '>') !== false)
			{
				throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_WARNIEXSS'));
			}
		}

		return true;
	}

	/**
	 * Calculate the dimensions of a resized image given the target bigger dimension
	 *
	 * @param   integer $width  Image width
	 * @param   integer $height Image height
	 * @param   integer $target Target size
	 *
	 * @return  array  The new width and height
	 */
	public function getResizedDimensions(int $width, int $height, int $target): array
	{
		/*
		 * Takes the larger size of the width and height and applies the
		 * formula accordingly. This is so this script will work
		 * dynamically with any size image
		 */
		if ($width > $height)
		{
			$percentage = ($target / $width);
		}
		else
		{
			$percentage = ($target / $height);
		}

		// Gets the new value and applies the percentage, then rounds the value
		$width  = round($width * $percentage);
		$height = round($height * $percentage);

		return [$width, $height];
	}

	/**
	 * Get the resized image data. It returns a raw PNG stream which can be output to a browser.
	 *
	 * @param   string  $imageFile
	 * @param   int     $targetDimension
	 *
	 * @return  string|null
	 */
	public function getResizedImageData(string $imageFile, int $targetDimension): ?string
	{
		// Try to get an image resource
		$imageResource = null;

		try
		{
			$mime = $this->getMime($imageFile);

			switch (strtolower($mime))
			{
				case 'image/jpeg':
					$imageResource = imagecreatefromjpeg($imageFile);
					break;

				case 'image/gif':
					$imageResource = imagecreatefromgif($imageFile);
					break;

				case 'image/png':
					$imageResource = imagecreatefrompng($imageFile);
					break;

				case 'image/bmp':
					$imageResource = imagecreatefrombmp($imageFile);
					break;
			}
		}
		catch (RuntimeException $e)
		{
			return null;
		}

		if (!is_resource($imageResource))
		{
			return null;
		}

		// Get the resized image dimensions
		$meta = getimagesize($imageFile);

		if (!is_array($meta) || (count($meta) < 7))
		{
			return null;
		}

		$width  = $meta[0];
		$height = $meta[1];
		list($newWidth, $newHeight) = $this->getResizedDimensions($width, $height, $targetDimension);

		// Get and return the resized image
		$newImage = imagescale($imageResource, $newWidth, $newHeight, IMG_BICUBIC);
		imagedestroy($imageResource);

		@ob_start();
		imagepng($newImage);
		imagedestroy($newImage);
		return @ob_get_clean();
	}

	/**
	 * Handle a file upload
	 *
	 * @param   array $file
	 */
	public function handleUpload(array $file)
	{
		$file['name']     = $this->makeFilenameSafe($file['name']);
		$file['name']     = str_replace(' ', '-', $file['name']);
		$file['filepath'] = Path::clean(implode(DIRECTORY_SEPARATOR, [self::mediaPath, $file['name']]));

		if ($file['error'] == 1)
		{
			// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
			throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_WARNFILETOOLARGE'));
		}

		if (!isset($file['name']))
		{
			throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_WARNFILENAME'));
		}

		$this->canUpload($file);

		$src  = $file['tmp_name'];
		$dest = $file['filepath'];

		if (!is_uploaded_file($src))
		{
			throw new RuntimeException(Text::_('AWF_MEDIA_ERROR_UPLOAD_INPUT'));
		}

		$this->container->fileSystem->copy($src, $dest);
	}
}