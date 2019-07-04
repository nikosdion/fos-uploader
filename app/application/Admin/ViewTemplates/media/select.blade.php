<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */

/** @var  \Admin\View\Media\Html $this */
?>

@section('imageSelect')
    <div class="akeeba-panel--information">
        <header class="akeeba-block-header">
            <h3>
                @lang('ADMIN_MEDIA_SELECT')
            </h3>
        </header>
        <div class="akeeba-grid">
            @foreach ($this->files as $file)
                <a class="akeeba-btn--ghost media-image" onclick="window.parent.useMediaFile('{{{ $file }}}')">
                    <div class="media-image-thumb">
                        <img src="@route('index.php?view=media&task=thumb&format=raw&file=' . urlencode($file))"  />
                    </div>
                    <div class="media-image-filename">
                        {{{ $file }}}
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection