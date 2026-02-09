# Execution Changelog

Changes to the execution plan and project documentation, extracted from the execution plan's living-document sections.

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
