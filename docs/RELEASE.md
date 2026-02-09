# Release Notes

## v1.1.0 (2026-02-09)

### New Features

- **CSV export**: "Download CSV" button exports all expired subscriptions (Name, Email, Mobile, Year, Expired) as a CSV file via the browser. Uses client-side Blob API — no server-side endpoint required.
- **Client-side pagination**: New "Results Per Page" configuration field (default 50, set to 0 to show all). Inline JS paginates rows across year-grouped tables, hides empty year groups, and renders Bootstrap 5 pagination controls with ellipsis for large page counts.
- **Year group counts**: Each year table caption now displays the number of users in that group, e.g. "2025 (14)".

### Bug Fixes

- **Query correctness**: Replaced raw double-quoted `IN ("X")` with `$db->quote('X')` in the helper's status filter. The original was unsafe under MariaDB/MySQL `ANSI_QUOTES` SQL mode.
- **User deduplication**: Users with multiple expired subscriptions for the same plan now appear only once, showing their most recent expiry date.
- **Update XML scope**: Broadened the `targetplatform` regex from `((4\.4)|(5\.(0|1|2|3|4|5|6|7|8|9)))` to `(4\.[4-9]|5\.)`, covering Joomla 4.5-4.9 and all 5.x minor versions.
- **Packaging**: Added `LICENSE` to `PACKAGE_FILES` in the Makefile so it is included in the distribution ZIP.

### Accessibility

- Replaced `<h4>` year headings with `<caption>` elements inside each `<table>` so screen readers programmatically associate the year label with its table.

### Configuration

New fields in module settings:

| Field | Fieldset | Default | Description |
|-------|----------|---------|-------------|
| Results Per Page | Basic | 50 | Number of rows per page. Set to 0 to disable pagination. |

### Compatibility

- Joomla 4.4+ / 5.x
- PHP 8.1+
- CB Paid Subscriptions (cbsubs)

### Upgrade Notes

This is a drop-in upgrade from v1.0.0. Install the ZIP via Extensions > Manage > Install, or let the Joomla update system pick it up automatically. Existing module instances will default to 50 results per page; adjust in module settings if needed.

After installing, run `make dist` to rebuild the distribution ZIP and update the sha256 hash in `mod_yscbsubs_expiredlist.update.xml`.

---

## v1.0.0 (2026-01-21)

### Initial Release

- Joomla 4.4+/5.x site module displaying expired CB Paid Subscriptions users grouped by expiration year.
- Configurable subscription plan filter (single-select from published `usersubscription` plans).
- Displays Name ("Lastname, Firstname" from Community Builder, with Joomla username fallback), Email, Mobile, and Expired date.
- Alert messages when no plan is selected or no expired subscriptions are found.
- Module class suffix and layout override support.
- Auto-update via Joomla update server (`mod_yscbsubs_expiredlist.update.xml`).
- Makefile for packaging and sha256 hash generation.
