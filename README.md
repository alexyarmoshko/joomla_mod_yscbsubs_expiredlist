# Members With Expired Subscription (mod_yscbsubs_expiredlist)

A Joomla 4.4+/5.x site module that lists Community Builder users whose CB Paid Subscriptions have lapsed for a selected plan. Results are grouped by expiration year (newest first) and show name, email, mobile, and expiry date.

## Features

- Required plan filter populated from published CB Paid Subscriptions plans
- Includes subscriptions with status `X` (expired) and status `A` where `expiry_date < NOW()`
- Groups results by expiration year (descending)
- Displays alerts for "no plan selected" and "no results"
- Uses Joomla date formatting (`DATE_FORMAT_LC4`) and output escaping
- Automatic updates via Joomla's update server mechanism

## Requirements

- Joomla 4.4+ or 5.x
- PHP 8.1+
- Community Builder + CB Paid Subscriptions
- Database tables: `#__cbsubs_plans`, `#__cbsubs_subscriptions`, `#__comprofiler`, `#__users`

## File Structure

```text
mod_yscbsubs_expiredlist.xml        Module manifest and configuration fields
mod_yscbsubs_expiredlist.update.xml Update server metadata (version, hash, download URL)
services/
└── provider.php                    DI service provider registration
src/
├── Dispatcher/
│   └── Dispatcher.php              Prepares layout data via the helper
└── Helper/
    └── ExpiredlistHelper.php       Database query and result grouping
tmpl/
└── default.php                     Frontend template (tables and alerts)
language/
└── en-GB/
    ├── mod_yscbsubs_expiredlist.ini      UI language strings
    └── mod_yscbsubs_expiredlist.sys.ini  Installer language strings
Makefile                            Packaging automation
installation/                       Built distribution ZIP
docs/
├── execution_plan.md               Implementation plan and architecture
└── execution_changelog.md          Documentation change history
```

## Installation

1. Download the ZIP from Releases or use the packaged file in `installation/`.
2. Install via Extensions -> Manage -> Install.
3. The module registers an update server automatically; Joomla will check for new versions in Extensions -> Update.

## Configuration

### Basic

- **Subscription Plan** (required): Select a CB Paid Subscriptions plan. The dropdown lists published plans by name and rounded rate.

### Advanced

- **Alternative Layout**: Select an alternative template override if one exists in your Joomla template's `html/mod_yscbsubs_expiredlist/` directory.
- **Module Class Suffix**: A CSS class suffix appended to the module's wrapper `<div>` for custom styling.

## Output

- Table grouped by expiration year (newest first).
- Columns: Name, Email, Mobile, Expired.
- Name uses "Lastname, Firstname" when available, otherwise the Joomla `users.name` field.
- If no plan is selected, displays an alert prompting the administrator to configure the module.
- If no expired subscriptions match, displays an informational "no results" alert.

## Development / Packaging

Build a distribution ZIP and refresh the update XML hash:

```sh
make dist
```

Requires `zip` and `shasum` in your shell environment.

Clean up the built ZIP:

```sh
make clean
```

## License

GPL-2.0-or-later
