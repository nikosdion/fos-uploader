<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

/** @var \Site\View\Thankyou\Html $this */
?>
<div class="akeeba-panel--teal">
    <header class="akeeba-block-header">
        <h1>@lang('SITE_THANKYOU_HEADER')</h1>
    </header>
    <p>
        Total files {{{ $this->totalFiles }}}, total size {{{ $this->formatSize($this->totalSize) }}}
    </p>
</div>
