# DB Cleaner Pro

Safe WordPress database maintenance — expired transients, auto-drafts, spam/trash comments, and orphaned meta. Does **not** rewrite SEO URLs or taxonomy.

| | |
|--|--|
| **Version** | 2.2.0 |
| **License** | MIT (repo root `LICENSE`) |
| **Requires** | WordPress 6.0+, PHP 7.4+ |
| **Cron** | `db_cleaner_pro_weekly` (weekly) |
| **Log** | `wp-content/db-cleaner-pro.log` |
| **Admin** | **Tools → DB Cleaner** |

## Install

1. Copy `db-cleaner-pro/` to `wp-content/plugins/db-cleaner-pro/`
2. Activate under **Plugins**
3. Optional: run once from **Tools → DB Cleaner**

## What it cleans

- Expired transients (via `delete_expired_transients()` when available)
- `auto-draft` posts
- Comments in `spam` / `trash`
- Orphaned `commentmeta` / `postmeta`
- `OPTIMIZE TABLE` on core tables only

## What it does **not** do

- Does not delete warm/valid transients
- Does not purge large `autoload=off` options (risk of data loss)
- Does not touch term / SEO rewrite tables beyond optional optimize

## Contact

diodac.electronics@gmail.com · https://isystem.app
