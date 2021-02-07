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
<div id="proofreader_container" class="proofreader_container" style="display:none;"><?php echo $displayData['form']; ?></div>
<script>
	jQuery(document).ready(function ($) {
		$('#proofreader_container').proofreader({
				'handlerType'       : '<?php echo $displayData['options']['handler']; ?>',
				<?php if (isset($displayData['options']['load_form_url'])): ?>
				'loadFormUrl'       : '<?php echo $displayData['options']['load_form_url']; ?>',
				<?php endif; ?>
				<?php if ($displayData['options']['highlight']): ?>
				'highlightTypos'    : true,
				<?php endif; ?>
				'selectionMaxLength': <?php echo $displayData['options']['selection_limit']; ?>
			},
			{
				'reportTypo'           : '<?php echo JText::_('COM_PROOFREADER_BUTTON_REPORT_TYPO', true); ?>',
				'thankYou'             : '<?php echo JText::_('COM_PROOFREADER_MESSAGE_THANK_YOU', true); ?>',
				'browserIsNotSupported': '<?php echo JText::_('COM_PROOFREADER_ERROR_BROWSER_IS_NOT_SUPPORTED', true); ?>',
				'selectionIsTooLarge'  : '<?php echo JText::_('COM_PROOFREADER_ERROR_TOO_LARGE_TEXT_BLOCK', true); ?>'
			});
	})
</script>
