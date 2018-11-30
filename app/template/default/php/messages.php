<?php
/**
 * @package    poc-upload-to-s3
 * @copyright  Copyright (c)2018-2018 Akeeba Ltd
 * @license    proprietary
 */

use Awf\Document\Document;

/** @var Document $this */

foreach (array(
	'error'		=> 'failure',
	'warning'	=> 'warning',
	'success'	=> 'success',
	'info'		=> 'info',
	) as $type => $class):
	$messages = $this->getContainer()->application->getMessageQueueFor($type);

	if (!empty($messages)):
		$class = "alert-$class";
?>
<div id="akeeba-message-<?php echo $type ?>" class="akeeba-message akeeba-block--<?php echo $class ?> small">
<?php foreach($messages as $message):?>
	<p><?php echo $message ?></p>
<?php endforeach; ?>
</div>
<?php
	endif;
endforeach;
$this->getContainer()->application->clearMessageQueue();
