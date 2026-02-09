# Execution Changelog

Changes to the execution plan and project documentation, extracted from the execution plan's living-document sections.

## 2026-02-09 - Client-side pagination

- **Results per page**: Added a `results_per_page` number field (default 50, 0 = show all) to the `basic` fieldset in `mod_yscbsubs_expiredlist.xml`. The template reads this param and passes it via a `data-perpage` attribute on the wrapper div.
- **Pagination JS**: Added inline JavaScript to `tmpl/default.php` that collects all `<tr>` elements across year-grouped tables into a flat list, shows/hides rows based on the current page, hides year tables with no visible rows, and renders Bootstrap 5 pagination controls with ellipsis for large page counts. Element IDs use `$module->id` for multi-instance safety.
- **CSV export unaffected**: The CSV export continues to use the full JSON data block, so all rows are included in the download regardless of the current page.
- **Language strings**: Added `MOD_YSCBSUBS_EXPIREDLIST_FIELD_PERPAGE_LABEL` and `MOD_YSCBSUBS_EXPIREDLIST_FIELD_PERPAGE_DESC` to `language/en-GB/mod_yscbsubs_expiredlist.ini`.

## 2026-02-09 - Usability features

- **CSV export**: Added a "Download CSV" button to `tmpl/default.php`. The template embeds the list data as a JSON `<script type="application/json">` block; a small inline JS reads it, builds a CSV string (headers: Name, Email, Mobile, Year, Expired), and triggers a browser download via the Blob API. Element IDs use `$module->id` so multiple module instances on the same page do not conflict. New language key `MOD_YSCBSUBS_EXPIREDLIST_EXPORT_CSV` added to `language/en-GB/mod_yscbsubs_expiredlist.ini`.
- **Year group count**: Each table `<caption>` now shows "YYYY (N)" where N is the number of users in that year group, giving administrators a quick summary.

## 2026-02-09 - Code fixes

- **Query correctness**: Replaced raw double-quoted `IN ("X")` with `$db->quote('X')` in `src/Helper/ExpiredlistHelper.php`. The original was unsafe under MariaDB/MySQL `ANSI_QUOTES` SQL mode where double-quoted strings are treated as identifiers.
- **User deduplication**: Added seen-user tracking in the helper's grouping loop so each user appears only once, with their most recent expiry date (the query already orders by `expiry_date DESC`).
- **Update XML scope**: Broadened the `targetplatform` regex in `mod_yscbsubs_expiredlist.update.xml` from `((4\.4)|(5\.(0|1|2|3|4|5|6|7|8|9)))` to `(4\.[4-9]|5\.)`, covering Joomla 4.5-4.9 and all 5.x minors.
- **Accessibility**: Replaced `<h4>` year headings with `<caption>` elements inside each `<table>` in `tmpl/default.php` so screen readers programmatically associate the year label with the table.
- **Makefile**: Added `LICENSE` to `PACKAGE_FILES` so it is included in the distribution ZIP.

## 2026-02-09 - Documentation review and alignment

- Extracted this changelog from `docs/execution_plan.md` into a standalone document.
- Updated the execution plan to fix inconsistencies with the implementation: corrected the Joomla compatibility range to 4.4+/5.x (matching the update XML `targetplatform`), fixed the Milestone 1 file tree (`provider.php/` trailing slash), added the `advanced` fieldset fields (`layout`, `moduleclass_sfx`) to the plan, and added missing imports (`Container`, `ServiceProviderInterface`) to the Interfaces section.
- Updated `README.md` to reflect the actual implementation: corrected the Joomla compatibility range, added a file structure overview, documented all configuration options including the advanced fieldset, and added an auto-update / update server section.

## 2026-01-29 - Plan and README alignment with repository

- Updated the execution plan and README to reflect the repository root structure, the actual SQL filter logic, the plan selector label formatting, and the alert behavior in the template.
- Decision: Align documentation with current repository layout and implementation details. The earlier plan referenced a different root path and outdated status logic.

## 2026-01-20 - Initial implementation completed

- Milestones 1-7 completed: module file structure, manifest, service provider, dispatcher, helper with database queries, template, language files, and integration testing.
- Decisions made:
  - Use Joomla 5.x module architecture with `services/provider.php`, `src/Dispatcher/Dispatcher.php`, and `src/Helper/ExpiredlistHelper.php` (matches Joomla core module conventions and enables DI for helpers).
  - Define lapsed subscriptions as status `X` or status `A` with `expiry_date < NOW()` (captures both explicit expirations and active records past their expiry date).
  - Extract expiration year from `expiry_date` and group results by year descending (provides readable grouping for renewal outreach).
  - Require a single-select plan filter using `name` plus rounded `rate` from published `usersubscription` plans (keeps output focused; matches manifest SQL field configuration).
  - Display names as "Lastname, Firstname" when available, falling back to `#__users.name` (Community Builder provides structured names; Joomla's user name is a safe fallback).
- Surprises:
  - The helper treats lapsed subscriptions as status `X`, or status `A` with `expiry_date < NOW()`, not status `C`. Evidence: `src/Helper/ExpiredlistHelper.php` status clause.
  - The plan selector shows plan `name` with rounded `rate`, not the plan alias. Evidence: `mod_yscbsubs_expiredlist.xml` SQL field query.
