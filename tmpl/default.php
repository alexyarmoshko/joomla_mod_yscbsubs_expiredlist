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
/** @var \stdClass $module */

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

// Build flat data array for CSV export
$csvRows = [];

foreach ($list as $year => $users) {
    foreach ($users as $user) {
        $csvRows[] = [
            'name'        => $user->displayName,
            'email'       => $user->email,
            'mobile'      => $user->cb_mobile ?? '',
            'expiry_year' => (int) $year,
            'expiry_date' => $user->expiry_date,
        ];
    }
}

$moduleId = (int) $module->id;
$perPage  = (int) $params->get('results_per_page', 50);

?>
<div class="mod-yscbsubs-expiredlist<?php echo htmlspecialchars($moduleclass_sfx, ENT_QUOTES, 'UTF-8'); ?>"
     id="mod-yscbsubs-wrap-<?php echo $moduleId; ?>"
     data-perpage="<?php echo $perPage; ?>">
    <p>
        <button type="button" class="btn btn-secondary btn-sm" id="mod-yscbsubs-csv-<?php echo $moduleId; ?>">
            <?php echo Text::_('MOD_YSCBSUBS_EXPIREDLIST_EXPORT_CSV'); ?>
        </button>
    </p>
    <script type="application/json" id="mod-yscbsubs-data-<?php echo $moduleId; ?>">
    <?php echo json_encode($csvRows, JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR); ?>
    </script>
    <script>
    document.getElementById("mod-yscbsubs-csv-<?php echo $moduleId; ?>").addEventListener("click", function () {
        var rows = JSON.parse(document.getElementById("mod-yscbsubs-data-<?php echo $moduleId; ?>").textContent);
        var csv = "Name,Email,Mobile,Year,Expired\r\n";
        for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            csv += '"' + r.name.replace(/"/g, '""') + '",'
                 + '"' + r.email.replace(/"/g, '""') + '",'
                 + '"' + r.mobile.replace(/"/g, '""') + '",'
                 + r.expiry_year + ','
                 + r.expiry_date + "\r\n";
        }
        var blob = new Blob([csv], {type: "text/csv;charset=utf-8;"});
        var url = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = url;
        a.download = "expired_subscriptions.csv";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
    </script>
    <?php foreach ($list as $year => $users) : ?>
        <table class="table table-striped table-hover">
            <caption style="caption-side:top"><?php echo (int) $year; ?> (<?php echo count($users); ?>)</caption>
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
    <nav aria-label="Pagination" id="mod-yscbsubs-pager-<?php echo $moduleId; ?>" class="mt-2"></nav>
    <script>
    (function () {
        var wrap = document.getElementById("mod-yscbsubs-wrap-<?php echo $moduleId; ?>");
        var perPage = parseInt(wrap.getAttribute("data-perpage"), 10);
        if (!perPage) return;

        var tables = wrap.querySelectorAll("table");
        var allRows = [];
        for (var t = 0; t < tables.length; t++) {
            var tbody = tables[t].querySelector("tbody");
            if (!tbody) continue;
            var rows = tbody.querySelectorAll("tr");
            for (var r = 0; r < rows.length; r++) {
                allRows.push(rows[r]);
            }
        }

        var totalPages = Math.ceil(allRows.length / perPage);
        if (totalPages <= 1) return;

        var pager = document.getElementById("mod-yscbsubs-pager-<?php echo $moduleId; ?>");

        function showPage(page) {
            var start = (page - 1) * perPage;
            var end = start + perPage;
            for (var i = 0; i < allRows.length; i++) {
                allRows[i].style.display = (i >= start && i < end) ? "" : "none";
            }
            for (var t = 0; t < tables.length; t++) {
                var tbody = tables[t].querySelector("tbody");
                if (!tbody) continue;
                var visible = false;
                var trs = tbody.querySelectorAll("tr");
                for (var r = 0; r < trs.length; r++) {
                    if (trs[r].style.display !== "none") { visible = true; break; }
                }
                tables[t].style.display = visible ? "" : "none";
            }
            renderPager(page, totalPages);
        }

        function renderPager(current, total) {
            var pages = [];
            if (total <= 7) {
                for (var i = 1; i <= total; i++) pages.push(i);
            } else {
                pages.push(1);
                if (current > 3) pages.push("...");
                for (var i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
                    pages.push(i);
                }
                if (current < total - 2) pages.push("...");
                pages.push(total);
            }

            var html = '<ul class="pagination pagination-sm">';
            html += '<li class="page-item' + (current === 1 ? ' disabled' : '') + '">'
                  + '<a class="page-link" href="#" data-page="' + (current - 1) + '">&laquo;</a></li>';
            for (var i = 0; i < pages.length; i++) {
                var p = pages[i];
                if (p === "...") {
                    html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                } else {
                    html += '<li class="page-item' + (p === current ? ' active' : '') + '">'
                          + '<a class="page-link" href="#" data-page="' + p + '">' + p + '</a></li>';
                }
            }
            html += '<li class="page-item' + (current === total ? ' disabled' : '') + '">'
                  + '<a class="page-link" href="#" data-page="' + (current + 1) + '">&raquo;</a></li>';
            html += '</ul>';
            pager.innerHTML = html;

            var links = pager.querySelectorAll("a.page-link");
            for (var i = 0; i < links.length; i++) {
                links[i].addEventListener("click", function (e) {
                    e.preventDefault();
                    var pg = parseInt(this.getAttribute("data-page"), 10);
                    if (pg >= 1 && pg <= total) showPage(pg);
                });
            }
        }

        showPage(1);
    })();
    </script>
</div>
