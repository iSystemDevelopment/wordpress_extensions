=== OptiByte WP ===
Contributors: isystemdevelopment, diodacelectronics
Donate link: https://isystem.app/
Tags: images, webp, avif, media, optimization, imagick, seo, performance
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 5.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

On-host WebP/AVIF media optimisation with Imagick, queue + WP-Cron, and optional iSystem cloud AI styles.

== Description ==

**OptiByte WP** optimises WordPress media on your server using Imagick / ImageMagick:

* Encode uploads to **WebP** and **AVIF**
* Optional local style presets (enhance, vintage, etc.)
* Queue + WP-Cron processing under `uploads/optibyte-wp/`
* Optional cloud AI styles via [iSystem](https://isystem.app/) User Bay token (`obwp_…`) — Creative / Business plan bonus

Imagick local encode is free forever. Cloud features are optional and never required for basic optimisation.

== Installation ==

1. Upload the `optibyte-wp` folder to `/wp-content/plugins/`
2. Activate **OptiByte WP** through the Plugins menu
3. Open **Media → OptiByte** to set quality, formats, and defaults
4. (Optional) Paste your iSystem cloud token for AI styles

== Frequently Asked Questions ==

= Does this require a paid subscription? =

No. Local Imagick optimisation works without an account. Cloud AI styles need an iSystem Creative or Business plan token.

= Which PHP extensions are required? =

Imagick **or** the ImageMagick CLI (`convert` / `magick`) available to PHP.

= Where is the queue stored? =

Under `wp-content/uploads/optibyte-wp/` as JSON jobs processed by WP-Cron.

== Screenshots ==

1. Media → OptiByte settings and queue status

== Changelog ==

= 5.1.0 =
* Cloud connect UI for iSystem service tokens
* Creative / Business plan bonus packaging

= 5.0.1 =
* Native WP plugin release (Imagick + optional API)

== Upgrade Notice ==

= 5.1.0 =
Adds optional cloud token connect for AI styles.

== Development ==

Source and issue tracker: https://github.com/iSystemDevelopment/wordpress_extensions
Product site: https://optibyte.isystem.app/
