=== DB Cleaner Pro ===
Contributors: isystemdevelopment, diodacelectronics
Donate link: https://isystem.app/
Tags: database, cleanup, maintenance, transients, comments, performance
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Safe weekly database maintenance: expired transients, auto-drafts, spam/trash comments, and orphaned meta — without touching SEO URLs or taxonomies.

== Description ==

**DB Cleaner Pro** schedules careful WordPress database housekeeping:

* Expired transients
* Auto-drafts
* Spam and trash comments (+ orphaned comment meta)
* Orphaned post/user meta where safe

It does **not** rewrite permalinks, rename taxonomies, or delete published content.

Admin: **Tools → DB Cleaner Pro**.

== Installation ==

1. Upload `db-cleaner-pro` to `/wp-content/plugins/`
2. Activate the plugin
3. Open **Tools → DB Cleaner Pro** (optional manual run)
4. Weekly cron is scheduled on activation

== Frequently Asked Questions ==

= Will this break my SEO? =

No. The plugin avoids URL and taxonomy changes. It only removes expired / trash / orphan maintenance data.

= Can I run cleanup manually? =

Yes, from **Tools → DB Cleaner Pro**.

== Changelog ==

= 2.2.0 =
* Safer cron hook naming and logging

== Development ==

https://github.com/iSystemDevelopment/wordpress_extensions
