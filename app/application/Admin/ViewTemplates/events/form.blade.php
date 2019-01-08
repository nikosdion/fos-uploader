<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

use Awf\Text\Text;

/** @var \Admin\View\Events\Html $this */
/** @var \Admin\Model\Events $model */
$model = $this->getModel();
?>
<form name="adminForm" id="adminForm" action="@route('index.php?view=Events')" method="post"
      role="form" class="akeeba-form--horizontal">

    <div>
        <div class="akeeba-form-group">
            <label for="title">
                @lang('ADMIN_EVENTS_FIELD_TITLE')
            </label>
            <input type="text" name="title" maxlength="255" size="50"
                   value="{{{ $model->title }}}"
                   class="form-control" required />
            <p class="akeeba-help-text">
                @lang('ADMIN_EVENTS_FIELD_TITLE_HELP')
            </p>
        </div>

        <div class="akeeba-form-group">
            <label for="shortcode">
                @lang('ADMIN_EVENTS_FIELD_SHORTCODE')
            </label>
            <input type="text" name="shortcode" maxlength="255" size="50"
                   value="{{{ $model->shortcode }}}"
                   class="form-control" required />
            <p class="akeeba-help-text">
                @lang('ADMIN_EVENTS_FIELD_SHORTCODE_HELP')
            </p>
        </div>

        <div class="akeeba-form-group">
            <label for="folder">
                @lang('ADMIN_EVENTS_FIELD_FOLDER')
            </label>
            <input type="text" name="folder" maxlength="255" size="50"
                   value="{{{ $model->folder }}}"
                   class="form-control" required />
            <p class="akeeba-help-text">
                @lang('ADMIN_EVENTS_FIELD_FOLDER_HELP')
            </p>
        </div>

        <div class="akeeba-form-group">
            <label for="published">
                @lang('ADMIN_EVENTS_FIELD_PUBLISHED')
            </label>
            <div class="akeeba-toggle">
                {{ \Admin\Helper\FEFSelect::booleanlist('enabled', ['forToggle' => true], $model->enabled) }}
            </div>
            <p class="akeeba-help-text">
                @lang('ADMIN_EVENTS_FIELD_PUBLISHED_HELP')
            </p>
        </div>

        <div class="akeeba-form-group">
            <label for="image">
                @lang('ADMIN_EVENTS_FIELD_IMAGE')
            </label>
            <input type="text" name="image" maxlength="255" size="50"
                   value="{{{ $model->image }}}"
                   class="form-control" required />
            <p class="akeeba-help-text">
                @lang('ADMIN_EVENTS_FIELD_IMAGE_HELP')
            </p>
        </div>

        <div class="akeeba-form-group">
            <label for="notes">
                @lang('ADMIN_EVENTS_FIELD_NOTES')
            </label>
            <textarea rows="20" cols="70" name="notes">{{{ $model->notes }}}</textarea>
            <p class="akeeba-help-text">
                @lang('ADMIN_EVENTS_FIELD_NOTES_HELP')
            </p>
        </div>

        <div class="akeeba-hidden-fields-container">
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="id" value="{{{ $model->id }}}" />
            <input type="hidden" name="@token()" value="1">
        </div>
</form>
