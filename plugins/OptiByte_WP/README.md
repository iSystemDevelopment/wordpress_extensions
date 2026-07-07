# OptiByte WP — WordPress plugin

MIT · **iSystem Development** · [optibyte.isystem.app](https://optibyte.isystem.app/)

WordPress media optimizer evolved from **OptiByte PRO v4** (legacy cron era). v5 is a native WP plugin:

| Layer | Role |
|-------|------|
| **Imagik** | PHP Imagick / ImageMagick CLI — WebP + AVIF encode, local style presets |
| **AI client** | Optional `api.isystem.app` OptiByte routes for full AI styles (when token set) |
| **Queue + cron** | JSON queue under `wp-content/uploads/optibyte-wp/`, hourly WP-Cron |

---

## Install

1. Copy `OptiByte_WP/` to `wp-content/plugins/`
2. Activate **OptiByte WP** in wp-admin
3. **Media → OptiByte** — configure quality, formats, default style
4. Drop source images in the staging folder or enable **Auto-queue uploads**

**Server:** PHP 8+ with `imagick` extension *or* `magick`/`convert` on PATH.

---

## AI styles (match OptiByte Studio)

| Style | Local (Imagik preset) | Cloud (API) |
|-------|----------------------|-------------|
| `none` | Pass-through encode | — |
| `enhance` | normalize + sharpen | Vertex/Gemini when wired |
| `cartoon` | quantize + edge | API |
| `artistic` | oil paint | API |
| `vintage` | sepia | API |
| `abstract` | swirl + modulate | API |

Without API token, AI-named styles still run **local Imagik presets** (good for dev). Set **Service token** for production AI parity with [optibyte.isystem.app](https://optibyte.isystem.app/).

---

## Layout

```
optibyte-wp.php              Bootstrap
includes/
  class-optibyte-imagik.php  Imagik engine (ImageMagick)
  class-optibyte-ai-client.php
  class-optibyte-optimizer.php
  class-optibyte-scanner.php
  ...
admin/views/dashboard.php
legacy/v4-archive/               Archived [retired-host] cron bundle
```

---

## Legacy v4

Pre-WP cron scripts live in `legacy/v4-archive/` for reference only. Do not deploy `/var/www/shared/` paths on new hosts.

---

## Contact

diodac.electronics@gmail.com · https://isystem.app
