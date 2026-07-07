# WordPress repo — install & release SSOT

## wp-content paths

| Repo path | Install to |
|-----------|------------|
| `plugins/db-cleaner-pro/` | `wp-content/plugins/db-cleaner-pro/` |
| `plugins/isystem-gcc-plus/` | `wp-content/plugins/isystem-gcc-plus/` |
| `themes/isystem-spectra-companion/` | `wp-content/themes/isystem-spectra-companion/` |

## Release checklist

- [ ] Bump `Version:` in plugin/theme header
- [ ] `php -l` on changed PHP files
- [ ] Test activate on staging (PHP 7.4+ / WP 6.3+)
- [ ] Tag git release `plugin-slug-x.y.z`
- [ ] Deploy via rsync/scp — single folder only

## db-cleaner-pro notes

- Log file: `wp-content/db-cleaner-pro.log`
- Cron hook: `weekly_database_cleanup`
- Admin: **Tools → DB Cleaner**

## isystem-gcc-plus notes

- Targets Spectra + Astra stack; no Jetpack tweaks
- See plugin header for script protection list

## isystem-spectra-companion notes

- **Requires** parent theme Spectra One
- Includes GPL-compatible theme `LICENSE` in theme folder

## .gitignore rationale

Ignored: OS junk, IDE, local wp-config copies, vendor, node_modules, zip artifacts, SQL dumps, uploaded media.

## Related domains

- Product marketing: `isystem.app`, `*.isystem.app`
- Personal portfolio: `diodac.org` (see isystem-deploy)
- Services (future): `isystem.cloud`
