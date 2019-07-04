<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

/** @var \Site\View\Thankyou\Html $this */
?>

<header  style="background-image:url('/media/images/events/{{{ $this->event->image }}}')">
    <div class="fos-image">
        <h1>
            @lang('SITE_THANKYOU_HEADER')
        </h1>
        <div class="triangle"><img src="/media/images/logo.svg" alt="Fos Photography" width="50"/></div>
    </div>
</header>


<section class="main">
    <h2>
        @lang('SITE_THANKYOU_SUCCESS1') <strong>{{{ $this->totalFiles }}}</strong> @lang('SITE_THANKYOU_SUCCESS2') {{{ $this->event->title }}}!
    </h2>
    <a class="button" href="/upload">@lang('SITE_THANKYOU_MOREBUTTON')</a>

    <h4>@lang('SITE_THANKYOU_WHATNOW_TITLE')</h4>
    <p>@lang('SITE_THANKYOU_WHATNOW_TEXT')</p>
    <p><a class="button" style="display:block; font-size:1.1em; text-align: center;" href="https://www.fosproductions.gr/" target="_blank">@lang('SITE_THANKYOU_LEARNMORE')</a></p>
</section>
