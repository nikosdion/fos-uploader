<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c) 2018-2019 Akeeba Ltd
 * @license    GNU Affero GPL v3 or later
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 *
 */
?>
@section('uploadbox')
    <div class="akeeba-panel--teal">
        <header class="akeeba-block-header">
            <h3>
                @lang('ADMIN_MEDIA_UPLOAD')
            </h3>
        </header>
        <form action="@route('index.php?view=media&task=upload')" method="POST" enctype="multipart/form-data"
              class="akeeba-form--inline">
            <div class="akeeba-form-group">
                <label for="username">File</label>
                <input type="file" name="file" accept=",image/png,image/jpg,image/gif,image/bmp">
            </div>
            <div class="akeeba-form-group--actions">
                <button type="submit" class="akeeba-btn">Upload</button>
            </div>
            <input name="@token" value="1" type="hidden">
        </form>
    </div>
@endsection
