# OptiByte WP — WordPress plugin

MIT · **iSystem Development** · [optibyte.isystem.app](https://optibyte.isystem.app/)

| | |
|--|--|
| **Version** | 5.0.1 |
| **License** | MIT (repo root) |
| **Requires** | WordPress 6.0+, PHP 8.0+, Imagick **or** ImageMagick CLI |

WordPress media optimizer evolved from **OptiByte PRO v4** (prior hosted cron era). **v5 is the supported product** — a native WP plugin. The legacy cron tree is archived under `legacy/v4-archive/` for reference only and must **not** be deployed on new hosts.

| Layer | Role |
|-------|------|
| **Imagik** | PHP Imagick / ImageMagick CLI — WebP + AVIF encode, local style presets |
| **AI client** | Optional `api.isystem.app` OptiByte routes (when service token set) |
| **Queue + cron** | JSON queue under `wp-content/uploads/optibyte-wp/`, hourly WP-Cron |

Admin: **Media → OptiByte** (capability: `manage_options`).

---

## Install

1. Copy `OptiByte_WP/` to `wp-content/plugins/` (folder name may stay `OptiByte_WP`)
2. Activate **OptiByte WP**
3. Open **Media → OptiByte** — set quality, formats, default style
4. Drop source images in the staging folder or enable **Auto-queue uploads**

---

## AI styles (match OptiByte Studio)

| Style | Local (Imagik preset) | Cloud (API) |
|-------|----------------------|-------------|
| `none` | Pass-through encode | — |
| `enhance` | normalize + sharpen | when token set |
| `cartoon` | quantize + edge | API |
| `artistic` | oil paint | API |
| `vintage` | sepia | API |
| `abstract` | swirl + modulate | API |

Without an API token, named styles still run **local Imagik presets**.

---

## Layout

```
optibyte-wp.php
includes/          Imagik, AI client, queue, scanner, admin
admin/views/
legacy/v4-archive/     Archived [retired] deployment — do not use on new sites
```

---

## Test after install

See [TEST_AFTER_INSTALL.md](../../TEST_AFTER_INSTALL.md) — **OptiByte WP** section.

---

## Contact

diodac.electronics@gmail.com · https://isystem.app
