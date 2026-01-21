# CLAUDE.md - Project Guidelines

## Project Overview

This is a Joomla module (`mod_yscbsubs_expiredlist`). This module lists all users (last, first names, email and mobile phone number) who has their subscription for a particular plan lapsed.
User list ordered by year (DESC).

Follow Joomla and PHP best practices. [Joomla documentation](https://manual.joomla.org/docs/) for version 5.x and 6.x is to be followed.

## Tech Stack

- **CMS**: Joomla 5.4
- **Language**: PHP 8.3
- **Database**: MariaDB via Joomla Database API

## Coding Standards

### PHP

- Follow [Joomla Coding Standards](https://developer.joomla.org/coding-standards.html)
- Use PSR-12 as the base style guide
- Use strict types: `declare(strict_types=1);`
- Use type hints for parameters and return types
- Use `camelCase` for variables and methods
- Use `PascalCase` for class names
- Prefix module classes with `Mod` (e.g., `ModYscbsubsExpiredlistHelper`)

### File Structure (Joomla 4/5 Module)

```
mod_modulename/
├── mod_modulename.xml       # Manifest file
├── tmpl/
│   └── default.php          # Default template
├── src/
│   ├── Dispatcher/
│   │   └── Dispatcher.php   # Module dispatcher
│   └── Helper/
│       └── ModulenameHelper.php
├── language/
│   └── en-GB/
│       └── mod_modulename.ini
└── services/
    └── provider.php         # Service provider, module entry point
```

## Joomla Best Practices

### Database Queries

- Always use Joomla's Database API (`$db = Factory::getContainer()->get(DatabaseInterface::class);`)
- Use prepared statements with `bind()` for user input
- Use query builder methods (`$query->select()`, `$query->from()`, etc.)

### Security

- Escape output: `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` or `$this->escape()`
- Never trust user input - validate and sanitize all inputs
- Use `JPATH_BASE` constants, never hardcode paths
- Check access with `$user->authorise()`

### Translation

- Use language strings: `Text::_('MOD_MODULENAME_KEY')`
- Define strings in language INI files
- Use `Text::sprintf()` for strings with placeholders

### Output

- Keep logic out of template files (`tmpl/`)
- Use module helpers for data processing
- Escape all output in templates

## Common Commands

```bash
# Check PHP syntax
php -l mod_modulename.php

# Run PHP CodeSniffer with Joomla standards
phpcs --standard=Joomla src/
```

## Important Notes

- Test on both Joomla 5.x and 6.x if supporting multiple versions
- Use namespaces following Joomla conventions: `Joomla\Module\Modulename\Site\*`
- Register services in `services/provider.php` for Joomla 4+
