=== iSystem GCC Plus ===
Contributors: isystemdevelopment, diodacelectronics
Donate link: https://isystem.app/
Tags: astra, spectra, gutenberg, performance, admin, hardening
Requires at least: 6.3
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Admin/editor hardening for Astra + Spectra: protect critical Gutenberg scripts, trim heavy admin assets, remove front-end jQuery Migrate.

== Description ==

**iSystem GCC Plus** is a lightweight hardening layer for sites using **Astra** and **Spectra**:

* Protect critical Gutenberg packages from async/defer load-order bugs
* Trim heavy admin widgets/assets where safe
* Remove front-end jQuery Migrate (admin left alone)
* Quiet duplicate-store console noise from Spectra

No Jetpack controls. No remote “phone home”. Works as a drop-in must-use style enhancer via normal plugin activation.

== Installation ==

1. Upload `isystem-gcc-plus` to `/wp-content/plugins/`
2. Activate **iSystem GCC Plus**
3. No settings page required for default hardening

== Frequently Asked Questions ==

= Do I need Astra and Spectra? =

The plugin is aimed at Astra + Spectra sites. On other themes it still removes front-end jQuery Migrate safely.

== Changelog ==

= 2.4.0 =
* Hardening pass for Gutenberg script order and Spectra warnings

== Development ==

https://github.com/iSystemDevelopment/wordpress_extensions
