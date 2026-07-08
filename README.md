# WordPress — iSystem plugins & themes

MIT-licensed WordPress extensions maintained by **iSystem Development** / DIODAC ELECTRONICS.

Repository: https://github.com/iSystemDevelopment/wordpress_extensions

**Hub:** [isystem.app](https://isystem.app/)

---

## Layout

```
plugins/
  db-cleaner-pro/          Safe scheduled DB cleanup (2.2.0)
  isystem-gcc-plus/        Astra/Spectra admin+editor hardening (2.4.0)
  OptiByte_WP/             Imagik WebP/AVIF + AI styles (5.0.1); legacy archive in legacy/v4-archive/
themes/
  isystem-spectra-companion/   Child theme for Spectra One (1.1.0)
docs/
  README.md                Install paths, standards, release checklist
```

This repo is **not** a full WordPress core install — copy folders into an existing site's `wp-content/`.

**[retired]:** Only OptiByte keeps an archived `legacy/v4-archive/` tree for historical reference. Other packages have no legacy-host dependency and must not ship old `retired-site-optimiser` zips.

---

## Quick install

### Plugins

```bash
cp -R plugins/db-cleaner-pro   /var/www/yoursite/wp-content/plugins/
cp -R plugins/isystem-gcc-plus /var/www/yoursite/wp-content/plugins/
cp -R plugins/OptiByte_WP      /var/www/yoursite/wp-content/plugins/
```

Activate under **Plugins** in wp-admin.

### Theme

1. Install parent theme [Spectra One](https://wordpress.org/themes/spectra-one/) from WordPress.org.
2. Copy `themes/isystem-spectra-companion/` to `wp-content/themes/`.
3. Activate **iSystem Companion for Spectra** under **Appearance → Themes**.

---

## Packages

| Slug | Type | Version | Purpose |
|------|------|---------|---------|
| `db-cleaner-pro` | Plugin | 2.2.0 | Weekly expired-transient / orphan cleanup + manual run |
| `isystem-gcc-plus` | Plugin | 2.4.0 | Spectra/Gutenberg script hardening, admin trim |
| `OptiByte_WP` | Plugin | 5.0.1 | Media WebP/AVIF optimizer (Imagik + optional AI) |
| `isystem-spectra-companion` | Child theme | 1.1.0 | Spectra One companion — admin fixes, block pattern |

See per-folder `README.md`.

---

## Standards

- **License:** MIT ([LICENSE](LICENSE)) — theme folder includes its own `LICENSE` copy.
- **Headers:** bump `Version:` in plugin/theme file headers on every release.
- **Prefix** public functions with the plugin/theme slug.
- **No secrets** in repo — API tokens live in wp-admin / wp-config / env on server.
- **Deploy:** rsync/SFTP one folder at a time; do not commit `vendor/`, uploads, or SQL dumps.

---

## Development

```bash
git clone https://github.com/iSystemDevelopment/wordpress_extensions.git
# Symlink into local WordPress wp-content for testing:
# ln -s $(pwd)/plugins/db-cleaner-pro /path/to/wp-content/plugins/
```

```bash
php -l plugins/db-cleaner-pro/db-cleaner-pro.php
php -l plugins/isystem-gcc-plus/isystem-gcc-plus.php
php -l plugins/OptiByte_WP/optibyte-wp.php
php -l themes/isystem-spectra-companion/functions.php
```

Monorepo mirror: `isystem-codebase/wordpress/` (keep in sync with this public repo).

---

## Test after install

Run the staging checklist in [TEST_AFTER_INSTALL.md](TEST_AFTER_INSTALL.md) after copying packages into `wp-content` and on every version bump.

---

## Contact

diodac.electronics@gmail.com · https://isystem.app
