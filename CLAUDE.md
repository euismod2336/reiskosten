# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Reiskosten is a PHP CLI script that generates monthly travel expense (reiskosten) declaration spreadsheets in XLSX format. It calculates travel costs for weekdays in the previous month, excluding specified dates.

## Running the Script

```bash
# Using Docker (recommended)
docker compose up -d
docker compose exec phpfpm php index.php

# Direct PHP execution (requires PHP 8.2+ with calendar extension)
php index.php
```

Output files are saved to the `export/` directory with naming format: `{year}-{month}-{FILENAME}-{PERSON_NAME}.xlsx`

## Configuration

Copy `.env.example` to `.env` and configure:

- `FILENAME` - Base name for exported files
- `PERSON_NAME` - Name shown in the spreadsheet
- `START` / `END` - Travel route locations
- `DISTANCE` - Round-trip kilometers
- `COSTS` - Cost per kilometer (e.g., 0.23)
- `EXCLUDES` - Comma-separated dates to exclude (format: YYYY-MM-DD)

## Dependencies

Uses PhpSpreadsheet via Composer. Vendor directory is committed; use `php composer.phar install` if needed.
