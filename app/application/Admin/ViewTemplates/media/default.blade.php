<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */
?>
@include('media/upload')
@include('media/select')

@unless(empty($this->files))
    @yield('imageSelect')
@else
    <div class="akeeba-panel--information">
        <header class="akeeba-block-header">
            <h3>
                @lang('ADMIN_MEDIA_NOFILES')
            </h3>
        </header>
        <p>
            @lang('ADMIN_MEDIA_NOFILES_TEXT')
        </p>
    </div>
@endif

@yield('uploadbox')
