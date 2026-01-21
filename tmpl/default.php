<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_yscbsubs_expiredlist
 *
 * @copyright   (C) 2026 Yak Shaver https://www.kayakshaver.com
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var array $list */
/** @var Joomla\Registry\Registry $params */

$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$planId          = (int) $params->get('subs_plan_id', 0);

// Show message if no plan selected
if ($planId === 0) {
    echo '<div class="alert alert-warning">' . Text::_('MOD_YSCBSUBS_EXPIREDLIST_NO_PLAN') . '</div>';
    return;
}

// Show message if no results
if (empty($list)) {
    echo '<div class="alert alert-info">' . Text::_('MOD_YSCBSUBS_EXPIREDLIST_NO_RESULTS') . '</div>';
    return;
}

?>
<div class="mod-yscbsubs-expiredlist<?php echo htmlspecialchars($moduleclass_sfx, ENT_QUOTES, 'UTF-8'); ?>">
    <?php foreach ($list as $year => $users) : ?>
        <h4><?php echo (int) $year; ?></h4>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('MOD_YSCBSUBS_EXPIREDLIST_NAME'); ?></th>
                    <th scope="col"><?php echo Text::_('MOD_YSCBSUBS_EXPIREDLIST_EMAIL'); ?></th>
                    <th scope="col"><?php echo Text::_('MOD_YSCBSUBS_EXPIREDLIST_MOBILE'); ?></th>
                    <th scope="col"><?php echo Text::_('MOD_YSCBSUBS_EXPIREDLIST_EXPIRY'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($user->displayName, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($user->cb_mobile ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo HTMLHelper::_('date', $user->expiry_date, Text::_('DATE_FORMAT_LC4')); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>
