# Test after install

Run this checklist after copying a plugin or theme into `wp-content` and activating on staging (or production). Repeat after every version bump.

---

## All packages (smoke)

| Step | Verify | Pass |
|------|--------|------|
| 1 | `php -l` on changed PHP files | No syntax errors |
| 2 | Activate in wp-admin | No fatal error on activation |
| 3 | Front-end home page | Loads without PHP notices (debug log clean) |
| 4 | wp-admin dashboard | No new JS console errors on core screens |

---

## db-cleaner-pro (2.2.0+)

| Step | Verify | Pass |
|------|--------|------|
| 1 | **Tools → DB Cleaner** visible | Menu requires `manage_options` |
| 2 | Manual run | Log line in `wp-content/db-cleaner-pro.log` |
| 3 | Cron hook | `db_cleaner_pro_weekly` scheduled (not legacy hook only) |
| 4 | Transients | Expired transients removed; site still loads fast |

---

## isystem-gcc-plus (2.4.0+)

| Step | Verify | Pass |
|------|--------|------|
| 1 | Spectra One + Astra active | Plugin does not fatal |
| 2 | Block editor | Spectra blocks load; no script 404s |
| 3 | Front-end | No duplicate or broken GCC assets |

---

## OptiByte WP (5.0.1+)

| Step | Verify | Pass |
|------|--------|------|
| 1 | **Media → OptiByte** | Settings save (requires `manage_options`) |
| 2 | Upload test image | WebP/AVIF output under `uploads/optibyte-wp/` if enabled |
| 3 | Front-end image | `<picture>` or optimized URL serves correctly |

---

## isystem-spectra-companion (1.1.0+)

| Step | Verify | Pass |
|------|--------|------|
| 1 | Parent **Spectra One** installed | Child theme activates |
| 2 | Site front page | Companion styles/pattern render |
| 3 | Block pattern | iSystem pattern inserts without markup errors |

---

## Release gate

Do not tag or deploy until staging checklist passes. Mirror changes to `isystem-codebase/wordpress/` when publishing the public repo.
