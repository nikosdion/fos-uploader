<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-${YEAR} Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
 */

// Used for type hinting
/** @var  \Admin\View\Events\Html $this */

/** @var \Admin\Model\Events $model */
$model = $this->getModel();
?>

<form action="@route('index.php?view=events')" method="post" name="adminForm" id="adminForm"
      role="form" class="akeeba-form">

    <table class="akeeba-table--striped" id="adminList">
        <thead>
        <tr>
            <th width="20px">&nbsp;</th>
            <th width="50px">
				{{ \Awf\Html\Grid::sort('ADMIN_EVENTS_FIELD_ID', 'event_id', $this->lists->order_Dir, $this->lists->order, 'browse') }}
            </th>
            <th>
				{{ \Awf\Html\Grid::sort('ADMIN_EVENTS_FIELD_TITLE', 'title', $this->lists->order_Dir, $this->lists->order, 'browse') }}
            </th>
            <th>
				{{ \Awf\Html\Grid::sort('ADMIN_EVENTS_FIELD_SHORTCODE', 'shortcode', $this->lists->order_Dir, $this->lists->order, 'browse') }}
            </th>
            <th>
				{{ \Awf\Html\Grid::sort('ADMIN_EVENTS_FIELD_PUBLISHED', 'published', $this->lists->order_Dir, $this->lists->order, 'browse') }}
            </th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>
                <input type="text" name="title" value="{{{ $model->getState('title', '') }}}"
                       placeholder="@lang('ADMIN_EVENTS_FIELD_TITLE')"
                       onchange="this.form.submit();">
            </td>
            <td>
                <input type="text" name="shortcode" value="{{{ $model->getState('shortcode', '') }}}"
                       placeholder="@lang('ADMIN_EVENTS_FIELD_SHORTCODE')"
                       onchange="this.form.submit();">
            </td>
            <td>
                {{ \Admin\Helper\FEFSelect::published('published', [
                	'onclick' => 'document.forms.adminForm.submit()',
                	'placeholder' => 'ADMIN_EVENTS_FIELD_PUBLISHED'
                ], $model->getState('published', '')) }}
            </td>
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
		/** @var \Admin\Model\Events $event */
		?>
        @foreach($this->items as $event)
        <tr>
            <td>
				{{ \Awf\Html\Grid::id($i++, $event->event_id) }}
            </td>
            <td>
                <a href="@route('index.php?view=Events&task=edit&id=' . $event->event_id)">
					<?php echo (int) $event->event_id ?>
                </a>
            </td>
            <td>
                <a href="@route('index.php?view=Events&task=edit&id=' . $event->event_id)">
					{{{ $event->title }}}
                </a>
            </td>
            <td>
                <a href="@route('index.php?view=Events&task=edit&id=' . $event->event_id)">
					{{{ $event->shortcode }}}
                </a>
            </td>
            <td>
                @if ($event->published)
                    <a href="@route('index.php?view=Events&task=unpublish&id=' . $event->event_id)">
                        <span class="akeeba-label--green">
                            <span class="akion-checkmark-circled"></span>
                        </span>
                    </a>
                @else
                    <a href="@route('index.php?view=Events&task=publish&id=' . $event->event_id)">
                        <span class="akeeba-label--red">
                            <span class="akion-android-remove-circle"></span>
                        </span>
                    </a>
                @endif
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
