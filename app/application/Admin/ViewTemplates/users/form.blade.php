<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

use Awf\Text\Text;

/** @var \Admin\View\Users\Html $this */

/** @var \Admin\Model\Users $model */
$model = $this->getModel();

$permissions = array(
	'create'    => false,
	'configure' => false,
	'system'    => false,
);

if ($model->id)
{
	$user        = $this->container->userManager->getUser($model->id);
	$permissions = array(
		'create'    => $user->getPrivilege('fos.create', false),
		'configure' => $user->getPrivilege('fos.configure', false),
		'system'    => $user->getPrivilege('fos.system', false),
	);
}

?>
<form name="adminForm" id="adminForm" action="@route('index.php?view=users')" method="post"
      role="form" class="akeeba-form--horizontal">

    <div>
        <div class="akeeba-form-group">
            <label for="username">
                @lang('ADMIN_USERS_FIELD_USERNAME')
            </label>
            <input type="text" name="username" maxlength="255" size="50"
                   value="{{{ $model->username }}}"
                   class="form-control" required />
        </div>

        <div class="akeeba-form-group">
            <label for="password">
                @lang('ADMIN_USERS_FIELD_PASSWORD')
            </label>
            <input type="password" name="password" maxlength="255" size="50"
                   value=""
                   class="form-control" />
        </div>

        <div class="akeeba-form-group">
            <label for="repeatpassword">
                @lang('ADMIN_USERS_FIELD_PASSWORDREPEAT')
            </label>
            <input type="password" name="repeatpassword" maxlength="255" size="50"
                   value=""
                   class="form-control" />
        </div>

        <div class="akeeba-form-group">
            <label for="email">
                @lang('ADMIN_USERS_FIELD_EMAIL') *
            </label>
            <input type="email" name="email" maxlength="255" size="50"
                   value="{{{ $model->email }}}"
                   class="form-control" required />
        </div>

        <div class="akeeba-form-group">
            <label for="name">
                @lang('ADMIN_USERS_FIELD_NAME')
            </label>
            <input type="text" name="name" maxlength="255" size="50"
                   value="{{{ $model->name }}}"
                   class="form-control" />
        </div>

        <div class="akeeba-form-group">
            <label for="name">
                @lang('ADMIN_USERS_FIELDSET_PERMISSIONS')
            </label>
            <div class="akeeba-form-group--checkbox">
                <label>
                    <input type="checkbox"
                           name="permissions[create]" <?php echo $permissions['create'] ? 'checked' : ''?>>
					<?php echo Text::_('ADMIN_USERS_FIELD_PERMISSIONS_CREATE') ?>
                </label>
            </div>
        </div>

        <div class="akeeba-form-group--checkbox--pull-right">
            <label>
                <input type="checkbox"
                       name="permissions[configure]" <?php echo $permissions['configure'] ? 'checked' : ''?>>
				<?php echo Text::_('ADMIN_USERS_FIELD_PERMISSIONS_CONFIGURE') ?>
            </label>
        </div>

        <div class="akeeba-form-group--checkbox--pull-right">
            <label>
                <input type="checkbox"
                       name="permissions[system]" <?php echo $permissions['system'] ? 'checked' : ''?>>
				<?php echo Text::_('ADMIN_USERS_FIELD_PERMISSIONS_SYSTEM') ?>
            </label>
        </div>
    </div>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="id" value="{{{ $model->id }}}" />
        <input type="hidden" name="@token()" value="1">
    </div>
</form>
