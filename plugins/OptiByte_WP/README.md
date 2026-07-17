# OptiByte WP — WordPress plugin

MIT · **iSystem Development** · [optibyte.isystem.app](https://optibyte.isystem.app/)

| | |
|--|--|
| **Version** | 5.1.0 |
| **License** | MIT (repo root) |
| **Requires** | WordPress 6.0+, PHP 8.0+, Imagick **or** ImageMagick CLI |

WordPress media optimizer — native WP plugin with on-host Imagick encode and optional iSystem cloud API. **v5 is the supported product.**

**Packaging:** Imagick local encode is **free forever**. Cloud AI styles are a **Creative / Business plan bonus** — download the zip and mint an `obwp_…` token from [isystem.app](https://isystem.app/) User Bay (no separate WP subscription).

| Layer | Role |
|-------|------|
| **Imagik** | PHP Imagick / ImageMagick CLI — WebP + AVIF encode, local style presets |
| **AI client** | Optional `api.isystem.app` OptiByte routes (Bearer `obwp_…` from User Bay) |
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
includes/          Imagick, AI client, queue, scanner, admin
admin/views/
```

---

## Test after install

See [TEST_AFTER_INSTALL.md](../../TEST_AFTER_INSTALL.md) — **OptiByte WP** section.

---

## Contact

diodac.electronics@gmail.com · https://isystem.app
