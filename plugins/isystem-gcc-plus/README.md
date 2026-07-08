# iSystem GCC Plus

Admin and editor hardening for **Astra + Spectra** stacks.

| | |
|--|--|
| **Version** | 2.4.0 |
| **License** | MIT |
| **Requires** | WordPress 6.3+, PHP 7.4+ |
| **Settings** | None — activates automatically |

## Features

- Removes **jQuery Migrate** on the front end only
- Strips `async` / `defer` from critical Gutenberg / React packages in wp-admin
- Dequeues heavy admin extras (Spectra Zip AI sidebar, Object Cache Pro charts, WP Mail SMTP charts, Query Monitor in admin, Cloudflare beacon)
- Trims noisy dashboard widgets
- Quietly ignores Spectra duplicate `registerStore('spectra')` races
- Sets ReactModal app element to avoid console warnings
- Fills missing Astra pagination typography option keys

**Not included:** Jetpack Photon / Site Accelerator controls (removed — use dedicated Jetpack settings if needed). This package is **not** the legacy `retired-site-optimiser` zip.

## Install

1. Copy `isystem-gcc-plus/` to `wp-content/plugins/isystem-gcc-plus/`
2. Activate **iSystem GCC Plus (Astra + Spectra Hardening)**

To customize which admin assets are removed, edit `$matchers_js` / `$matchers_css` in `isystem-gcc-plus.php`.

## Test after install

See [TEST_AFTER_INSTALL.md](../../TEST_AFTER_INSTALL.md) — **isystem-gcc-plus** section.

## Contact

diodac.electronics@gmail.com · https://isystem.app
