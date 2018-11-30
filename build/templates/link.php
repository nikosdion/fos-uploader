<?php
$hardlink_files = [];

$symlink_files = [
	// ================================================================================
	// FEF
	// ================================================================================
	// FEF - CSS
	'../fef/sa-css/style.min.css'   => 'app/media/css/fef.min.css',

	// FEF - JavaScript
	'../fef/js/menu.min.js'         => 'app/media/js/menu.min.js',
	'../fef/js/tabs.min.js'         => 'app/media/js/tabs.min.js',
	'../fef/js/dropdown.min.js'     => 'app/media/js/dropdown.min.js',

	// FEF - Akeeba Logo
	'../fef/images/akeeba-logo.svg' => 'app/media/images/akeeba-logo.svg',
];

$symlink_folders = [
	// FEF Fonts
	'../fef/fonts/akeeba'     => 'app/media/fonts/akeeba',
	'../fef/fonts/Ionicon'    => 'app/media/fonts/Ionicon',
	'../fef/fonts/Montserrat' => 'app/media/fonts/Montserrat',
	'../fef/fonts/Open_Sans'  => 'app/media/fonts/Open_Sans',
];
