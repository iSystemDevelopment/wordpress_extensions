# Stubs — OptiByte WP

**Policy:** [docs/stub-policy.md](../../../docs/stub-policy.md)

**Last reviewed:** 2026-07-07

---

## Registry

| ID | Stub | Where | Why not wired yet | Wire plan | Risk |
|----|------|-------|-------------------|-----------|------|
| S1 | API service token auth | `includes/class-optibyte-ai-client.php` | No WP site-to-site bearer on `api.isystem.app` yet | Add `OPTIBYTE_WP_SERVICE_TOKEN` on API + store in plugin settings | Medium — credits/billing |
| S2 | AI multipart upload | `class-optibyte-ai-client.php` `build_multipart()` | Manual multipart; needs curl parity test vs multer | Switch to `curl` + `CURLFile` or WP HTTP API hook | Low |
| S3 | Attachment replace | Optimizer outputs not swapped into Media Library | Phase 2 — need `wp_update_attachment_metadata` flow | Map `attachment_id` → sidecar WebP/AVIF or srcset filter | Medium |
| S4 | Alt-text AI | — | OptiByte Studio feature not exposed in WP UI | REST route when API ships `alt` endpoint | Low |

---

## Wired (v5.0.0 scaffold)

- Imagik local WebP/AVIF encode
- Queue / log / scanner (WP uploads paths)
- WP admin dashboard (Media → OptiByte)
- Hourly WP-Cron `optibyte_wp_process_queue`
- Local Imagik presets for all style ids

---

*Update when AI token or attachment swap lands.*
