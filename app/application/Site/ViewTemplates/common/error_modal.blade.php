<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

/* Error modal */
?>
<div id="errorDialog" tabindex="-1" role="dialog" aria-labelledby="errorDialogLabel" aria-hidden="true"
     style="display:none;">
    <div class="akeeba-renderer-fef">
        <h4 id="errorDialogLabel">
	        @lang('SITE_ERRORDIALOG_HEADER')
        </h4>

        <p>
	        @lang('SITE_ERRORDIALOG_LBL')
        </p>
        <pre id="errorDialogPre"></pre>
    </div>
</div>
