# WordPress.org submission — iSystem plugins

**Goal:** publish `db-cleaner-pro`, `isystem-gcc-plus`, and `optibyte-wp` on [wordpress.org/plugins](https://wordpress.org/plugins/).

## Plugin slugs (directory folder names)

| Product | Current folder (monorepo) | Suggested .org slug / zip root |
|---------|---------------------------|--------------------------------|
| DB Cleaner Pro | `db-cleaner-pro` | `db-cleaner-pro` |
| iSystem GCC Plus | `isystem-gcc-plus` | `isystem-gcc-plus` |
| OptiByte WP | `OptiByte_WP` | `optibyte-wp` (rename folder in the review zip) |

WordPress.org requires **lowercase slug folders**. When packaging OptiByte, zip as:

```text
optibyte-wp/
  optibyte-wp.php          ← rename from optibyte-wp.php (keep Text Domain)
  readme.txt
  includes/
  admin/
  assets/
```

Update `Plugin Name` / main file bootstrap paths if you rename the main PHP file; Text Domain may stay `optibyte-wp`.

## Pre-review checklist (Plugin Handbook)

- [x] `readme.txt` present (WP.org format) in each plugin
- [ ] **License header** `License: GPLv2 or later` (MIT alone is GPL-compatible; .org prefers explicit GPL header)
- [ ] No PHP short tags; `defined( 'ABSPATH' ) || exit;`
- [ ] Sanitize input (`sanitize_*`) / escape output (`esc_*`)
- [ ] Nonces + `current_user_can()` on admin actions
- [ ] No obfuscated / encoded PHP
- [ ] No bundling of unrelated premium upsells that block core features
- [ ] No calling home without disclosure (OptiByte cloud API is optional and documented)
- [ ] Assets for directory listing (upload after approval):
  - `assets/icon-128x128.png`
  - `assets/icon-256x256.png`
  - `assets/banner-772x250.png`
  - `assets/banner-1544x500.png` (optional retina)
  - `assets/screenshot-1.png` …

## How to submit

1. Create / login: https://wordpress.org/plugins/developers/add/
2. For each plugin, upload a zip of **only that plugin folder** (slug root)
3. Wait for the first human review (can take days–weeks)
4. After approval, push updates via SVN (`svn.wordpress.org`) — GitHub stays the SSOT; mirror releases

## Local package script

```powershell
cd C:\Users\dioda\Documents\GitHub\isystem-codebase\wordpress
powershell -ExecutionPolicy Bypass -File .\scripts\package-wporg.ps1
```

Zips land in `wordpress/dist-wporg/`.

## Legal note

Keep MIT in `LICENSE` for GitHub if desired; for .org use **GPLv2 or later** in `readme.txt` + plugin header (dual-licensing is fine: MIT + GPL).
