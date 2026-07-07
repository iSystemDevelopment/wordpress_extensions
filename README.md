# WordPress — iSystem plugins & themes

MIT-licensed WordPress extensions maintained by **iSystem Development** / DIODAC ELECTRONICS.

Repository: https://github.com/iSystemDevelopment/wordpress_extensions

**Hub:** [isystem.app — WordPress Plugins & Themes](https://isystem.app/#music)

---

## Layout

```
plugins/
  db-cleaner-pro/          Safe scheduled DB cleanup (transients, orphans, spam)
  isystem-gcc-plus/        Astra/Spectra hardening — editor scripts, jQuery Migrate
  OptiByte_WP/             Imagik WebP/AVIF + AI styles (v5 WordPress plugin)
themes/
  isystem-spectra-companion/   Child theme for Spectra One
docs/
  README.md                Install paths, standards, release checklist
```

This repo is **not** a full WordPress core install — copy folders into an existing site's `wp-content/`.

---

## Quick install

### Plugins

```bash
cp -R plugins/db-cleaner-pro   /var/www/yoursite/wp-content/plugins/
cp -R plugins/isystem-gcc-plus /var/www/yoursite/wp-content/plugins/
```

Activate under **Plugins** in wp-admin.

### Theme

1. Install parent theme [Spectra One](https://wordpress.org/themes/spectra-one/) from WordPress.org.
2. Copy `themes/isystem-spectra-companion/` to `wp-content/themes/`.
3. Activate **iSystem Companion for Spectra** under **Appearance → Themes**.

---

## Packages

| Slug | Type | Purpose |
|------|------|---------|
| `db-cleaner-pro` | Plugin | Weekly transient/orphan cleanup + manual run |
| `isystem-gcc-plus` | Plugin | Spectra/Gutenberg script hardening, admin trim |
| `isystem-spectra-companion` | Child theme | Spectra One companion — admin fixes, block pattern |

See per-folder `README.md` where present.

---

## Standards

- **License:** MIT ([LICENSE](LICENSE)) unless a package ships its own `LICENSE` file.
- **Prefix** public functions with the plugin slug (`db_cleaner_pro_*`).
- **No secrets** in repo — use wp-config / env on server.
- **Deploy:** SFTP/rsync plugin folder only; do not commit `vendor/`, uploads, or SQL dumps.

Full org web standards: [isystem-deploy/docs](https://github.com/iSystemDevelopment/isystem-deploy/tree/main/docs) (layout, PWA, Cloudflare cache).

---

## Development

```bash
git clone https://github.com/iSystemDevelopment/wordpress_extensions.git
# Symlink into local WordPress wp-content for testing:
# ln -s $(pwd)/plugins/db-cleaner-pro /path/to/wp-content/plugins/
```

Run PHP syntax check:

```bash
php -l plugins/db-cleaner-pro/db-cleaner-pro.php
php -l plugins/isystem-gcc-plus/isystem-gcc-plus.php
```

---

## Contact

diodac.electronics@gmail.com · https://isystem.app
