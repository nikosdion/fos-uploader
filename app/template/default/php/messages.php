<?php
/**
 * @package    fos-uploader
 * @copyright  Copyright (c)2018-2019 Akeeba Ltd & Fos Photography
 * @license    proprietary
 *
 * Developed by Akeeba Ltd <https://www.akeeba.com>.
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
<div id="message-<?php echo $type ?>" class="top-message block--<?php echo $class ?>">
    <div class="message-body">
        <?php foreach($messages as $message):?>
            <p><?php echo $message ?></p>
        <?php endforeach; ?>
    </div>

</div>
<?php
	endif;
endforeach;
$this->getContainer()->application->clearMessageQueue();
