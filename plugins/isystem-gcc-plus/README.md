iSystem GCC Plus 
 
A custom functionality plugin, designed to apply advanced performance and stability patches tailored to the site's specific theme and famous plugin stack.

Admin performance + compatibility hardening for Astra/Spectra. Disables Jetpack "optimization" (Photon / Site Accelerator & lazy images), removes heavy admin-only assets/widgets, and mitigates Spectra duplicate store issues.

Features
Frontend Performance: Disables jquery-migrate.js on the front end to reduce unnecessary script loading for visitors.

Jetpack Slim-Down: Disables Jetpack's Asset CDN (Photon) and lazy loading features, allowing for more control with dedicated optimization tools.

Admin Performance Boost: Intelligently dequeues non-essential, resource-heavy scripts and styles from plugins like Spectra and Cloudflare within the /wp-admin/ area. This significantly speeds up backend page loads.

Clean Dashboard: Removes unnecessary and heavy widgets from the main WordPress dashboard for a faster, cleaner interface.

Editor Stability Patch: Injects a safe "monkey-patch" to prevent the "Store is already registered" JavaScript error caused by race conditions between Spectra/Jetpack and the block editor.

Astra Theme Guard: Prevents potential PHP notices in the Astra theme customizer by ensuring default typography settings are always present.

Installation
Create the Plugin: Follow the instructions above to create the retired-site-optimiser.zip file.

Upload & Activate: In your WordPress dashboard, go to Plugins -> Add New -> Upload Plugin, select your .zip file, and activate the "iSystem GCC Plus" plugin.

Usage
This plugin works automatically once activated. There are no settings to configure. The optimizations are applied via WordPress hooks and filters in the background.

To customize the scripts that are dequeued in the admin area, you can edit the $matchers_js and $matchers_css arrays within the isystem-gcc-plus.php file.