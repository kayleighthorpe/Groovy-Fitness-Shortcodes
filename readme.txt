=== Fitbit Shortcodes by Albinofruit.com ===
Thanks: Dav, Phil, Dave and Tim for answering my questions! 
Tags: fitbit, shortcodes

Fitbit Shortcodes by Albinofruit.com

== Description ==

A plugin that uses Oauth2 authorisation code flow to grab your Fitbit statistics and you can place them anywhere on your site with the use of shortcodes.

The plugin currently only supports steps, but there will be other metrics added very soon.

Fitbit Shortcodes uses WP_CRON to grab API updates, it will not work when this is disabled. This also means stat updates are dependent on your site's traffic and activity. See FAQ for more details.

== Installation ==

1. Download and extract plugin files to a wp-content/plugin directory.
2. Activate the plugin
3. Navigate to 'Settings' > 'Groovy Fitness Shortcodes' in the WordPress dashboard.
4. Follow the steps given, hit save.
5. Hit the 'authorise with Fitbit link'
6. Place your shortcodes where you want the data to appear on your site.



== Frequently Asked Questions ==

= How do I obtain my Fitbit user ID? =

Login on fitbit.com and select your profile avatar from the top right hand side. Your profile URL will be displayed there including your unique profile ID.

= WP_CRON and making the stats update =

The plugin is dependent on WP_CRON - meaning this must be enabled, which it is by default on most WordPress sites. WP_CRONs are triggered by site activity, so they may not always update at the exact cron intervals.

= My stats have stopped displaying =

Check your Fitbit App at dev.bitbit.com - ensure your Client ID, Secret and callback URI are all set to the same values displayed there. If so, save them again and click the 'authenticate with Fitbit' link again. They should appear soon after.

= Feedback and bugs =

Please either contact me via albinofruit.com/contact if you have any questions and wish to get in touch ASAP. I will also check the WordPress forum regularly for feedback and to help with issues.

= What metrics are being added soon? =

Calories, Heartrate, floors, active minutes, water, sleep.