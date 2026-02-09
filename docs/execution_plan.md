# Members With Expired Subscription (mod_yscbsubs_expiredlist)

This ExecPlan is a living document. The sections `Progress`, `Surprises & Discoveries`, `Decision Log`, and `Outcomes & Retrospective` must be kept up to date as work proceeds.

This document must be maintained in accordance with `.agent/PLANS.md`.

## Purpose / Big Picture

This Joomla 4.4+/5.x module displays a list of Community Builder users whose CB Paid Subscriptions for a selected plan have lapsed. A subscription is considered lapsed when its status is `X` (expired) or when it is `A` (active) but the `expiry_date` is in the past. Administrators can place the module on any site position; it renders a table grouped by expiration year (newest first) with name, email, mobile, and expiry date. If no plan is selected or no results match, the module displays a clear alert message instead of an empty table.

## Progress

- [x] (2026-01-20) Milestone 1: Create module file structure and manifest.
- [x] (2026-01-20) Milestone 2: Implement service provider and dependency injection.
- [x] (2026-01-20) Milestone 3: Implement Dispatcher class.
- [x] (2026-01-20) Milestone 4: Implement Helper class with database queries.
- [x] (2026-01-20) Milestone 5: Create template files.
- [x] (2026-01-20) Milestone 6: Add language files.
- [x] (2026-01-20) Milestone 7: Integration testing and validation.
- [x] (2026-01-29) Updated plan and README to reflect the current repository layout and implementation details.
- [x] (2026-02-09) Documentation review: fixed inconsistencies, extracted changelog to `docs/execution_changelog.md`, updated README.

## Surprises & Discoveries

- Observation: The helper treats lapsed subscriptions as status `X`, or status `A` with `expiry_date < NOW()`, not status `C`.
  Evidence: `src/Helper/ExpiredlistHelper.php` status clause.
- Observation: The plan selector shows plan `name` with rounded `rate`, not the plan alias.
  Evidence: `mod_yscbsubs_expiredlist.xml` SQL field query.

## Decision Log

- Decision: Use the Joomla 4.4+/5.x module architecture with `services/provider.php`, `src/Dispatcher/Dispatcher.php`, and `src/Helper/ExpiredlistHelper.php`.
  Rationale: Matches Joomla core module conventions and enables DI for helpers.
  Date/Author: 2026-01-20

- Decision: Define lapsed subscriptions as status `X` or status `A` with `expiry_date < NOW()`.
  Rationale: The helper currently filters this way to capture both explicit expirations and active records that are past their expiry date.
  Date/Author: 2026-01-20

- Decision: Extract expiration year from `expiry_date` and group results by year (descending).
  Rationale: This provides a simple, readable grouping for renewal outreach.
  Date/Author: 2026-01-20

- Decision: Require a single-select plan filter using `name` plus rounded `rate` from published `usersubscription` plans.
  Rationale: Keeps output focused and matches the current manifest SQL field configuration.
  Date/Author: 2026-01-20

- Decision: Display names as "Lastname, Firstname" when available, falling back to `#__users.name`.
  Rationale: Community Builder provides structured names; Joomla's user name is a safe fallback.
  Date/Author: 2026-01-20

- Decision: Align documentation with current repository layout and implementation details.
  Rationale: The earlier plan referenced a different root path and outdated status logic.
  Date/Author: 2026-01-29

- Decision: Correct Joomla compatibility from "5.x" to "4.4+/5.x" and document the `advanced` fieldset fields (`layout`, `moduleclass_sfx`).
  Rationale: The update XML `targetplatform` regex matches Joomla 4.4 and 5.0-5.9. The manifest defines an `advanced` fieldset with layout override and module class suffix that was not reflected in the plan or README.
  Date/Author: 2026-02-09

- Decision: Extract changelog into a separate `docs/execution_changelog.md` document.
  Rationale: Keeps the execution plan focused on architecture and implementation while preserving the history of changes in a dedicated file.
  Date/Author: 2026-02-09

## Outcomes & Retrospective

### Completed: 2026-01-20

The module implementation is complete and follows Joomla 4.4+/5.x conventions with DI, helper-based data retrieval, and a template that escapes output. The manifest and language files fully support configuration, and the update server file is included for distribution.

### Documentation update: 2026-01-29

The plan and README now reflect the repository root structure, the actual SQL filter logic, the plan selector label formatting, and the alert behavior in the template.

### Documentation review: 2026-02-09

Corrected Joomla compatibility range to 4.4+/5.x (matching the update XML `targetplatform`). Fixed the Milestone 1 file tree. Added the `advanced` fieldset fields to the plan. Added missing DI container imports to the Interfaces section. Extracted the changelog to `docs/execution_changelog.md`. Updated `README.md` with file structure, all configuration options, and auto-update information.

## Context and Orientation

This repository is the module root. The key files are `mod_yscbsubs_expiredlist.xml` (manifest and config fields), `services/provider.php` (DI registration), `src/Dispatcher/Dispatcher.php` (layout data preparation), `src/Helper/ExpiredlistHelper.php` (database query and grouping), `tmpl/default.php` (frontend rendering), `language/en-GB/mod_yscbsubs_expiredlist.ini` and `language/en-GB/mod_yscbsubs_expiredlist.sys.ini` (translations), `mod_yscbsubs_expiredlist.update.xml` (update server metadata), `Makefile` (packaging), and `installation/` (built ZIP).

The module namespace is `Joomla\Module\YSCBSubsExpiredList\Site`. The dispatcher obtains module params, calls the helper, and passes the grouped data to the template. The template renders an alert if no plan is selected or no results are found, otherwise it renders a table for each expiration year.

### Database Tables

1. `#__cbsubs_plans` (subscription plans): `id`, `name`, `alias`, `rate`, `item_type`, `published`, `ordering`.
2. `#__cbsubs_subscriptions` (subscriptions): `id`, `user_id`, `plan_id`, `status`, `expiry_date`, `subscription_date`.
3. `#__comprofiler` (CB profiles): `id`, `firstname`, `lastname`, `cb_mobile`.
4. `#__users` (Joomla users): `id`, `name`, `email`.

## Joomla Module Architecture (Joomla 4.4+/5.x)

Modern Joomla modules use dependency injection via a service provider and prepare data through a dispatcher:

1. `services/provider.php` registers the dispatcher and helper with the DI container.
2. `src/Dispatcher/Dispatcher.php` retrieves module params and invokes the helper.
3. `src/Helper/ExpiredlistHelper.php` performs the query and groups results.
4. `tmpl/default.php` renders alerts and the grouped table.
5. `mod_yscbsubs_expiredlist.xml` defines the module metadata, files, and configuration fields.

## Plan of Work

The module is already implemented in this repository; this plan documents the current structure and behavior so a novice can reproduce it or modify it safely. The implementation flow is: define the manifest and configuration field, register the module services, retrieve data in the helper, render in the template with proper escaping, and provide translations and packaging metadata. The milestones below describe the concrete files and logic that should exist after each step.

### Milestone 1: Create Module File Structure and Manifest

The module root contains:

    mod_yscbsubs_expiredlist.xml
    mod_yscbsubs_expiredlist.update.xml
    Makefile
    LICENSE
    .gitignore
    services/
    └── provider.php
    src/
    ├── Dispatcher/
    │   └── Dispatcher.php
    └── Helper/
        └── ExpiredlistHelper.php
    tmpl/
    └── default.php
    language/
    └── en-GB/
        ├── mod_yscbsubs_expiredlist.ini
        └── mod_yscbsubs_expiredlist.sys.ini
    installation/
    docs/
    ├── execution_plan.md
    └── execution_changelog.md

The manifest file (`mod_yscbsubs_expiredlist.xml`) defines metadata, the namespace, file listings, language files, and configuration fields split across two fieldsets. The `basic` fieldset contains the `subs_plan_id` field, a required SQL single-select showing published `usersubscription` plans with a label built from `name` and a rounded `rate`:

    SELECT id AS value,
           CONCAT(name, ' (', round(rate), ')') AS text
    FROM #__cbsubs_plans
    WHERE published = 1 AND item_type = 'usersubscription'
    ORDER BY ordering ASC, alias ASC

The `advanced` fieldset provides two standard Joomla module options: `layout` (a `modulelayout` field that lets administrators select an alternative template override) and `moduleclass_sfx` (a textarea for a CSS class suffix appended to the module's wrapper element).

### Milestone 2: Implement Service Provider

In `services/provider.php`, an anonymous class implements `ServiceProviderInterface` and registers `ModuleDispatcherFactory`, `HelperFactory`, and `Module` with the DI container using the module namespace.

### Milestone 3: Implement Dispatcher Class

In `src/Dispatcher/Dispatcher.php`, the dispatcher extends `AbstractModuleDispatcher`, uses `HelperFactoryAwareTrait`, and overrides `getLayoutData()` to call the helper `ExpiredlistHelper` and assign the grouped list to the layout data.

### Milestone 4: Implement Helper Class

In `src/Helper/ExpiredlistHelper.php`, `ExpiredlistHelper` implements `DatabaseAwareInterface` and defines `getExpiredUsers(Registry $params): array`. It reads the selected plan ID, returns an empty list when no plan is selected, and performs a parameterized query that joins subscriptions to users and Community Builder profiles:

    SELECT
        u.id,
        u.name,
        u.email,
        cb.firstname,
        cb.lastname,
        cb.cb_mobile,
        YEAR(s.expiry_date) AS expiry_year,
        s.expiry_date
    FROM #__cbsubs_subscriptions AS s
    INNER JOIN #__users AS u ON s.user_id = u.id
    LEFT JOIN #__comprofiler AS cb ON s.user_id = cb.id
    WHERE s.plan_id = :planId
      AND (s.status IN ('X')
      OR (s.status = 'A' AND s.expiry_date < NOW())
      )
    ORDER BY s.expiry_date DESC, u.name ASC

The helper builds a `displayName` as "Lastname, Firstname" when available, otherwise uses `u.name`, and groups records by `expiry_year` in descending order.

### Milestone 5: Create Template Files

In `tmpl/default.php`, render an alert if no plan is selected or if the list is empty. Otherwise, loop through the grouped list by year and render a table with columns for Name, Email, Mobile, and Expired. Use `HTMLHelper::_('date', ...)` for the expiry date and `htmlspecialchars()` for all user output.

### Milestone 6: Add Language Files

`language/en-GB/mod_yscbsubs_expiredlist.ini` provides UI strings for the module name, field labels, and alert messages. `language/en-GB/mod_yscbsubs_expiredlist.sys.ini` provides the module name and XML description for the installer.

### Milestone 7: Integration Testing

Install the module in Joomla, create a module instance, select a plan, and verify the frontend output. Confirm that the alert messages appear when no plan is selected or when no results are found.

## Concrete Steps

Working directory: `C:\Users\alex\repos\joomla_mod_yscbsubs_expiredlist`

### Step 1: Verify PHP syntax

    php -l services/provider.php
    php -l src/Dispatcher/Dispatcher.php
    php -l src/Helper/ExpiredlistHelper.php
    php -l tmpl/default.php

### Step 2: Package a release (optional)

    make dist

### Step 3: Install and validate in Joomla

1. Navigate to Extensions -> Manage -> Install.
2. Install the ZIP from `installation/`.
3. Create a module instance, select a plan, and assign a position.
4. View the frontend page and confirm the table or alert appears.

## Validation and Acceptance

The module is accepted when:

1. The module installs and appears in Extensions -> Modules.
2. The Subscription Plan field is required and lists published `usersubscription` plans.
3. If no plan is selected, the module shows `MOD_YSCBSUBS_EXPIREDLIST_NO_PLAN`.
4. If no rows match, the module shows `MOD_YSCBSUBS_EXPIREDLIST_NO_RESULTS`.
5. When data exists, results are grouped by `expiry_year` (descending).
6. Each row shows Name, Email, Mobile, and Expired date formatted with `DATE_FORMAT_LC4`.
7. All output is escaped to prevent XSS.

## Idempotence and Recovery

The module is read-only against the database and has no migrations. Re-running packaging commands only regenerates the ZIP and updates the hash in `mod_yscbsubs_expiredlist.update.xml`. If installation fails, check Joomla logs under `administrator/logs/` and retry the install.

## Artifacts and Notes

### XML Manifest Snippet (mod_yscbsubs_expiredlist.xml)

    <field
        name="subs_plan_id"
        type="sql"
        label="MOD_YSCBSUBS_EXPIREDLIST_FIELD_PLAN_LABEL"
        description="MOD_YSCBSUBS_EXPIREDLIST_FIELD_PLAN_DESC"
        required="true"
        query="SELECT id AS value, CONCAT(name, ' (', round(rate), ')') AS text FROM #__cbsubs_plans WHERE published = 1 AND item_type = 'usersubscription' ORDER BY ordering ASC, alias ASC"
        key_field="value"
        value_field="text"
    >
        <option value="">MOD_YSCBSUBS_EXPIREDLIST_SELECT_PLAN</option>
    </field>

### Helper Query Pattern

    $query->where($db->quoteName('s.plan_id') . ' = :planId')
        ->where(
            '('
            . $db->quoteName('s.status') . ' IN ("X")'
            . ' OR ('
            . $db->quoteName('s.status') . ' = ' . $db->quote('A')
            . ' AND ' . $db->quoteName('s.expiry_date') . ' < NOW()'
            . '))'
        )
        ->bind(':planId', $planId, ParameterType::INTEGER);

## Interfaces and Dependencies

The module uses Joomla core classes:

    use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
    use Joomla\CMS\Extension\Service\Provider\HelperFactory;
    use Joomla\CMS\Extension\Service\Provider\Module;
    use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
    use Joomla\CMS\Helper\HelperFactoryAwareInterface;
    use Joomla\CMS\Helper\HelperFactoryAwareTrait;
    use Joomla\CMS\HTML\HTMLHelper;
    use Joomla\CMS\Language\Text;
    use Joomla\Database\DatabaseAwareInterface;
    use Joomla\Database\DatabaseAwareTrait;
    use Joomla\Database\ParameterType;
    use Joomla\DI\Container;
    use Joomla\DI\ServiceProviderInterface;
    use Joomla\Registry\Registry;

For a chronological history of changes to this plan and project documentation, see `docs/execution_changelog.md`.

Plan update note: 2026-01-29 - Updated this plan to match the current repository layout and the implemented SQL, template behavior, and packaging details so the documentation reflects the actual module.

Plan update note: 2026-02-09 - Fixed Joomla compatibility range to 4.4+/5.x. Fixed file tree. Added `advanced` fieldset fields, missing DI imports. Extracted changelog. Updated README.
