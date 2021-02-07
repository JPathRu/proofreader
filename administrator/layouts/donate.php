<?php
/**
 * Proofreader
 *
 * @package     Proofreader
 * @author      Sergey M. Litvinov (smart@joomlatune.com)
 * @copyright   Copyright (C) 2013-2015 by Sergey M. Litvinov. All rights reserved.
 * @copyright   Copyright (C) 2005-2007 by Alexandr Balashov. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div style="margin: 0 auto; padding: 10px 0">
	<?php if (is_dir(JPATH_SITE . '/language/ru-RU') || is_dir(JPATH_ADMINISTRATOR . '/language/ru-RU')): ?>
		<?php require_once(dirname(__FILE__) . '/donate.yandex.php'); ?>
	<?php else: ?>
		<?php require_once(dirname(__FILE__) . '/donate.paypal.php'); ?>
	<?php endif; ?>
</div>