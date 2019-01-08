<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

use Awf\Text\Text;

// Used for type hinting
/** @var  \Admin\View\login\Html  $this */

$router = $this->container->router;

?>
<form class="akeeba-form--stretch" role="form" action="@route('index.php?view=login&task=login')"
      method="POST" id="loginForm">

    <div class="akeeba-panel--info">
        <header class="akeeba-block-header">
            <h2>
		        @lang('ADMIN_LOGIN_PLEASELOGIN')
            </h2>
        </header>

        <div class="akeeba-form-group">
            <input type="text" id="username" name="username" class="form-control" placeholder="@lang('ADMIN_LOGIN_LBL_USERNAME')" required autofocus value="{{{ $this->username }}}">
        </div>
        <div class="akeeba-form-group">
            <input type="password" id="password" name="password" class="form-control" placeholder="@lang('ADMIN_LOGIN_LBL_PASSWORD')" required value="{{{ $this->password }}}">
        </div>

        <div class="akeeba-form-group--actions">
            <button class="akeeba-btn--primary--block" style="width: 100%" type="submit" id="btnLoginSubmit">
                <span class="akion-log-in"></span>
			    @lang('ADMIN_LOGIN_LBL_LOGIN')
            </button>
        </div>

        <div class="akeeba-hidden-fields-container">
            <input type="hidden" name="token" value="@token()">
        </div>
    </div>

</form>