# WordPress repo — install & release SSOT

## wp-content paths

| Repo path | Install to |
|-----------|------------|
| `plugins/db-cleaner-pro/` | `wp-content/plugins/db-cleaner-pro/` |
| `plugins/isystem-gcc-plus/` | `wp-content/plugins/isystem-gcc-plus/` |
| `plugins/OptiByte_WP/` | `wp-content/plugins/OptiByte_WP/` |
| `themes/isystem-spectra-companion/` | `wp-content/themes/isystem-spectra-companion/` |

## Release checklist

- [ ] Bump `Version:` (and matching `*_VERSION` constant) in plugin/theme header
- [ ] Update package `README.md` version table
- [ ] `php -l` on changed PHP files
- [ ] Complete [TEST_AFTER_INSTALL.md](../TEST_AFTER_INSTALL.md) on staging (PHP 7.4+ / WP 6.3+; OptiByte needs PHP 8.0+)
- [ ] Tag release `slug-x.y.z`
- [ ] Deploy via rsync/scp — single folder only
- [ ] Mirror into monorepo `isystem-codebase/wordpress/` when shipping public changes

## db-cleaner-pro notes

- Version **2.2.0**
- Log: `wp-content/db-cleaner-pro.log`
- Cron hook: `db_cleaner_pro_weekly` (legacy `weekly_database_cleanup` cleared on activate)
- Admin: **Tools → DB Cleaner**
- Uses `delete_expired_transients()` — no bulk wipe of warm cache

## isystem-gcc-plus notes

- Version **2.4.0**
- Astra + Spectra only — **no Jetpack / [retired] zip** instructions
- See plugin file for `$matchers_js` / `$matchers_css`

## OptiByte WP notes

- Version **5.0.1**
- Admin: **Media → OptiByte** (`manage_options`)
- Staging/output under `uploads/optibyte-wp/`
- `legacy/v4-archive/` is archive-only — do not deploy if you never used [retired] OptiByte

## isystem-spectra-companion notes

- Version **1.1.0**
- **Requires** parent theme Spectra One (`Template: spectra-one`)
- MIT `LICENSE` in theme folder

## .gitignore rationale

Ignored: OS junk, IDE, local wp-config copies, vendor, node_modules, zip artifacts, SQL dumps, uploaded media.

## Related

- Product: `isystem.app`, `optibyte.isystem.app`
- Portfolio: `diodac.org`
- Public repo: https://github.com/iSystemDevelopment/wordpress_extensions
- Monorepo folder: `isystem-codebase/wordpress/`
