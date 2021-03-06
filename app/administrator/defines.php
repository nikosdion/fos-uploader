<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

// Uncomment to enable debug mode.
// define('AKEEBADEBUG', 1);

// Do not change these paths unless you know what you're doing
define('APATH_BASE', realpath(__DIR__ . '/..'));
define('APATH_ROOT', APATH_BASE . '/administrator');
define('APATH_SITE', APATH_BASE . '/administrator');
define('APATH_THEMES', APATH_BASE . '/template');
define('APATH_TRANSLATION', APATH_BASE . '/language');
define('APATH_TMP', APATH_BASE . '/tmp');
define('APATH_APPROOT', realpath(APATH_BASE . '/application'));