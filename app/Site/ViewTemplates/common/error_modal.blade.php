<?php
/**
 * @package        solo
 * @copyright Copyright (c)2014-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;

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
