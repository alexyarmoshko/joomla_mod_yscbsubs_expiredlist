<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_yscbsubs_expiredlist
 *
 * @copyright   (C) 2026 Yak Shaver https://www.kayakshaver.com
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\YSCBSubsExpiredList\Site\Helper;

use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper for mod_yscbsubs_expiredlist
 *
 * @since  1.0.0
 */
class ExpiredlistHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Get expired subscription users grouped by year
     *
     * @param   Registry  $params  Module parameters
     *
     * @return  array  Array of users grouped by expiry year [year => [users]]
     *
     * @since   1.0.0
     */
    public function getExpiredUsers(Registry $params): array
    {
        $planId = (int) $params->get('subs_plan_id', 0);

        // Return empty if no plan or no years selected
        if ($planId === 0) {
            return [];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select([
            $db->quoteName('u.id'),
            $db->quoteName('u.name'),
            $db->quoteName('u.email'),
            $db->quoteName('cb.firstname'),
            $db->quoteName('cb.lastname'),
            $db->quoteName('cb.cb_mobile'),
            'YEAR(' . $db->quoteName('s.expiry_date') . ') AS ' . $db->quoteName('expiry_year'),
            $db->quoteName('s.expiry_date'),
        ])
            ->from($db->quoteName('#__cbsubs_subscriptions', 's'))
            ->join('INNER', $db->quoteName('#__users', 'u'), $db->quoteName('s.user_id') . ' = ' . $db->quoteName('u.id'))
            ->join('LEFT', $db->quoteName('#__comprofiler', 'cb'), $db->quoteName('s.user_id') . ' = ' . $db->quoteName('cb.id'))
            ->where($db->quoteName('s.plan_id') . ' = :planId')
            ->where(
                '('
                . $db->quoteName('s.status') . ' = ' . $db->quote('X')
                . ' OR ('
                . $db->quoteName('s.status') . ' = ' . $db->quote('A')
                . ' AND ' . $db->quoteName('s.expiry_date') . ' < NOW()'
                . '))'
            )
            ->bind(':planId', $planId, ParameterType::INTEGER)
            ->order($db->quoteName('s.expiry_date') . ' DESC')
            ->order($db->quoteName('u.name') . ' ASC');

        $db->setQuery($query);
        $users = $db->loadObjectList();

        if (empty($users)) {
            return [];
        }

        // Process users and group by year, keeping only the latest expiry per user
        $grouped = [];
        $seen    = [];

        foreach ($users as $user) {
            $uid = (int) $user->id;

            if (isset($seen[$uid])) {
                continue;
            }

            $seen[$uid] = true;

            // Build display name: prefer firstname + lastname, fall back to full name
            if (!empty($user->firstname) || !empty($user->lastname)) {
                $user->displayName = trim(($user->lastname ?? '') . ', ' . ($user->firstname ?? ''), ', ');
            } else {
                $user->displayName = $user->name;
            }

            $year = (int) $user->expiry_year;

            if (!isset($grouped[$year])) {
                $grouped[$year] = [];
            }

            $grouped[$year][] = $user;
        }

        // Sort by year descending (newest first)
        krsort($grouped);

        return $grouped;
    }
}
