# iSystem Companion for Spectra

**Author:** iSystem Development
**Version:** 1.0.0
**Requires Parent Theme:** [Spectra One](https://wordpress.org/themes/spectra-one/)

## Description

This is a companion child theme for **Spectra One**. It enhances the admin experience by fixing common plugin conflicts and adds a fallback block pattern to assist in theme development. It is designed to be lightweight and work seamlessly in modern hosting environments (including NGINX, Redis, and Cloudflare setups).

### Core Features

* **Admin Conflict Fixes:** Prevents known JavaScript errors on post edit screens by selectively dequeuing scripts from WPForms and Jetpack.
* **Custom Block Pattern:** Includes a simple, pre-styled "Sidebar Fallback" block pattern under a new "iSystem Patterns" category.

## Installation

1.  First, ensure the parent theme, **Spectra One**, is installed from the WordPress theme directory.
2.  Upload the `isystem-spectra-companion` theme folder to your `/wp-content/themes/` directory.
3.  Navigate to **Appearance > Themes** in your WordPress dashboard and activate the **iSystem Companion for Spectra** theme.