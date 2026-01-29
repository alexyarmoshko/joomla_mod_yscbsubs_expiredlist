# Members With Expired Subscription (mod_yscbsubs_expiredlist)

A Joomla 5.x site module that lists Community Builder users whose CB Paid Subscriptions have lapsed for a selected plan. Results are grouped by expiration year (newest first) and show name, email, mobile, and expiry date.

## Features

- Required plan filter populated from published CB Paid Subscriptions plans
- Includes subscriptions with status `X` (expired) and status `A` where `expiry_date < NOW()`
- Groups results by expiration year (descending)
- Displays alerts for "no plan selected" and "no results"
- Uses Joomla date formatting (`DATE_FORMAT_LC4`) and output escaping

## Requirements

- Joomla 5.4+
- Community Builder + CB Paid Subscriptions
- Database tables: `#__cbsubs_plans`, `#__cbsubs_subscriptions`, `#__comprofiler`, `#__users`

## Installation

1. Download the ZIP from Releases or use the packaged file in `installation/`.
2. Install via Extensions -> Manage -> Install.
3. (Optional) Configure an update server using the URL in `mod_yscbsubs_expiredlist.xml`.

## Configuration

- Subscription Plan (required): populated from published CB Paid Subscriptions plans.
- Module Class Suffix (optional): standard Joomla module option.

## Output

- Table grouped by expiration year (newest first).
- Columns: Name, Email, Mobile, Expired.
- Name uses "Lastname, Firstname" when available, otherwise Joomla `users.name`.

## Development / Packaging

Build a distribution ZIP and refresh the update XML hash:

```
make dist
```

Requires `zip` and `shasum` in your shell environment.

## License

GPL-2.0-or-later
