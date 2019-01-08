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
/** @var  \Admin\View\Users\Html $this */

/** @var \Admin\Model\Users $model */
$model = $this->getModel();
?>

<form action="@route('index.php?view=users')" method="post" name="adminForm" id="adminForm"
      role="form" class="akeeba-form">

    <table class="akeeba-table--striped" id="adminList">
        <thead>
        <tr>
            <th width="20px">&nbsp;</th>
            <th width="50px">
				{{ \Awf\Html\Grid::sort('ADMIN_USERS_FIELD_ID', 'id', $this->lists->order_Dir, $this->lists->order, 'browse') }}
            </th>
            <th>
				{{ \Awf\Html\Grid::sort('ADMIN_USERS_FIELD_USERNAME', 'username', $this->lists->order_Dir, $this->lists->order, 'browse') }}
            </th>
            <th>
				{{ \Awf\Html\Grid::sort('ADMIN_USERS_FIELD_NAME', 'name', $this->lists->order_Dir, $this->lists->order, 'browse') }}
            </th>
            <th>
				{{ \Awf\Html\Grid::sort('ADMIN_USERS_FIELD_EMAIL', 'email', $this->lists->order_Dir, $this->lists->order, 'browse') }}
            </th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <input type="text" name="username" value="{{{ $model->getState('username', '') }}}"
                       placeholder="@lang('ADMIN_USERS_FIELD_USERNAME')"
                       onchange="this.form.submit();">
            </td>
            <td>
                <input type="text" name="name" value="{{{ $model->getState('name', '') }}}"
                       placeholder="@lang('ADMIN_USERS_FIELD_NAME')"
                       onchange="this.form.submit();">
            </td>
            <td>
                <input type="text" name="email" value="{{{ $model->getState('email', '') }}}"
                       placeholder="@lang('ADMIN_USERS_FIELD_EMAIL')"
                       onchange="this.form.submit();">
            </td>
            <td></td>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="20" class="center">{{ $this->pagination->getListFooter() }}</td>
        </tr>
        </tfoot>
        <tbody>
		@if(empty($this->items))
        <tr>
            <td colspan="20" class="center">
				@lang('AWF_PAGINATION_LBL_NO_RESULTS')
            </td>
        </tr>
		@else
		<?php
		$i = 0;
		/** @var \Admin\Model\Users $user */
		?>
        @foreach($this->items as $user)
        <tr>
            <td>
				{{ \Awf\Html\Grid::id($i++, $user->id) }}
            </td>
            <td>
                <a href="@route('index.php?view=users&task=edit&id=' . $user->id)">
					<?php echo (int) $user->id ?>
                </a>
            </td>
            <td>
                <a href="@route('index.php?view=users&task=edit&id=' . $user->id)">
					{{{ $user->username }}}
                </a>
            </td>
            <td>
                <a href="@route('index.php?view=users&task=edit&id=' . $user->id)">
					{{{ $user->getFieldValue('name') }}}
                </a>
            </td>
            <td>
                <a href="@route('index.php?view=users&task=edit&id=' . $user->id)">
					{{{ $user->email }}}
                </a>
            </td>
        </tr>
		@endforeach
		@endif
        </tbody>
    </table>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="boxchecked" id="boxchecked" value="0">
        <input type="hidden" name="task" id="task" value="browse">
        <input type="hidden" name="filter_order" id="filter_order" value="{{{ $this->lists->order }}}">
        <input type="hidden" name="filter_order_Dir" id="filter_order_Dir"
               value="{{{ $this->lists->order_Dir }}}">
        <input type="hidden" name="token" value="@token()">
    </div>
</form>

<script type="application/javascript">
	akeeba.System.orderTable = function () {
		table     = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order     = table.options[table.selectedIndex].value;
		if (order !== '{{{ $this->lists->order }}}')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}

		akeeba.System.tableOrdering(order, dirn, '');
	}
</script>
