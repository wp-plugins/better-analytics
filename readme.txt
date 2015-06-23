=== Better Google Analytics ===
Contributors: digitalpoint
Tags: analytics, google analytics, universal analytics, statistics, tracking, code, dashboard, analytics dashboard, google analytics dashboard, google analytics plugin, google analytics widget, reports, charts, multisite, api, stats, web stats, visits, javascript, pageviews, marketing, widget, realtime, real time, youtube, outbrain, taboola, adsense, google, digitalpoint
Donate link: https://marketplace.digitalpoint.com/better-analytics.3354/item#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin
Requires at least: 3.8
Tested up to: 4.2.2
Stable tag: 1.0.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track everything with Google Analytics (clicked links, emails opened, YouTube videos being watched, etc.). Includes real time Analytics dashboard.

== Description ==
The Better Google Analytics plugin allows you to easily add Google Analytics code to your website and gives you the power to track virtually everything.  Better Analytics includes heat maps, reports, charts, events and site issue tracking in your WordPress admin area without the need to log into your Google Analytics account.

Better Google Analytics utilizes all the latest and greatest features of Google Analytics (Universal analytics, user-ID session unification, event tracking, campaign tracking, custom dimensions, server-side tracking, social engagement tracking, remarketing, etc.)

If you link your Google Analytics account, the Better Google Analytics plugin is able to make extensive use of the Google Analytics API to give you a plethora of reporting options (both historical and realtime).  Google Analytics API calls are cached to make them as fast as possible.

Better Google Analytics uses lightweight (and client-side cacheable) JavaScript to give your users the fastest possible experience on your website.  Fully compatible with multisite network setups.

= Better Google Analytics Basic Tracking Features (each can be enabled/disabled): =

* Link Attribution
* User-ID/Session Unification
* User Engagement
* Comment Creation
* User Registration
* YouTube Video Engagement
* Emails Sent/Opened
* External Link Clicks
* File Downloads
* Anonymize IPs
* Demographic & Interest
* Force Google Analytics Traffic Over SSL
* RSS/Email Link Source Tracking
* Advertising Ad Clicks
* Page Not Found (404)
* AJAX Requests

 = Better Google Analytics Dimension Tracking: =

* Categories
* Author
* Tags
* Publication Year
* User Role
* User

= Better Google Analytics Social Button Engagement Tracking: =

* Facebook
* Twitter
* Google+
* Pinterest
* LinkedIn

= Better Google Analytics Reporting Features (can be viewed site-wide or for individual page/URL): =

* Dashboard Charts (real time or historical)
* Weekly Heat Maps
* Historical Area Percent Charts
* Events
* Issue Monitoring

= Better Google Analytics Advanced Features: =

* Suppress Google Analytics Tracking By User Role
* View Analytics Reports By User Role
* Adjust Location Of Google Analytics Code
* Google Analytics Campaign Tracking By Anchor or Parameters
* Adjustable Analytics Sample Rate
* Insert Your Own Custom Google Analytics JavaScript
* Debugging Mode

= Better Google Analytics Widgets Included: =

* Popular Posts
* Statistics based on selectable Analytics metric

> <strong>Better Analytics Pro</strong><br>
> If you would like additional advanced functions for Google Analytics, we offer a Pro version.
>
> * Additional ad networks for ad click tracking
> * More options for site issue monitoring
> * More heat map metrics
> * More charting dimensions
> * eCommerce tracking (coming soon)
> * Option for server-side tracking of users (or bots)
> * Faster Google Analytics API calls (uses a custom system for parallel requests)
> * Priority support
>
> [Pro license available here](https://marketplace.digitalpoint.com/better-analytics-pro.3355/item#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin)

== Installation ==
1. Upload `better-analytics` folder to the `/wp-content/plugins/` directory.
1. Activate the Better Google Analytics plugin through the 'Plugins' menu in the WordPress admin area.
1. Add your Google Analytics Web Property ID under the 'Settings -> Better Analytics' area of the WordPress admin.
1. Optional (but probably a good idea so you don't double report your traffic inside Google Analytics) - disable any other Google Analytics code you have enabled.

== Frequently Asked Questions ==
= What are the requirements of the Better Analytics plugin? =
You need a WordPress site (of course) running WordPress 3.8 or higher and a Google Analytics account (which is [free over here](http://www.google.com/analytics/) if you don't already have a Google Analytics account).

= Can Better Analytics be used with legacy Google Analytics code? =
No, the Better Analytics plugin is for Google Universal Analytics.  You can upgrade any old non-Universal Google Analytics property to support both legacy and Universal under your [Google Analytics Property Settings](https://www.google.com/analytics/web/?#management/Settings/).

= Can you add [insert feature here] to Better Google Analytics? =
If it's possible and it makes sense, then yes.  The best way to put in a feature request for Better Google Analytics would be to create a thread in the [support forum over here](https://forums.digitalpoint.com/forums/better-analytics.31/#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin).

= I speak a language that isn't supported by Better Analytics, can I help translate it? =
Yes.  Unfortunately we don't speak every language in the world, so if you would like to help with translating the Better Google Analytics plugin, please contact us in the [support forum over here](https://forums.digitalpoint.com/forums/better-analytics.31/#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin).

= Does Better Google Analytics Support A WordPress Multisite Network? =
Yes, you can install the Better Google Analytics plugin for a single site in the network or for all sites in the network.  Additionally, you can optionally link a single Google Analytics account for all sites in the network (or you can link unique Google Analytics accounts for each site in the network... either way, it's up to you).

= Do you have access to our Google Analytics data? =
In no way, shape, or form do we have access to your Google Analytics data.

= How can I ensure you don't really have access to my Google Analytics data? =
The way OAuth2 works with your Google Analytics account, it wouldn't be possible for us to access your Google Analytics data even if we wanted to (which we don't).  If you utilize the default Google Analytics API project credentials, the system will ask you for permission to access your data.  Google will then issue a one-time use code that is exchanged for OAuth2 credentials that are used when making Google Analytics API calls.  <strong>The code is one-time use</strong> (meaning if someone intercepted it and redeemed it for credentials, you wouldn't be able to yourself).  After your site redeems the code, it's no longer valid.  The resulting credentials are stored inside your installation and are never sent anywhere.  That being said, if you are still worried about the security of your Google Analytics data, you are able to utilize your own Google Analytics API project credentials (it's just a little more work for you to set up that Google API project - the only API type that you need to enable under that project is the Google Analytics API).

== CDN ==

The JavaScript used by Better Google Analytics should be able to be cached properly by content delivery networks (it has been tested with CloudFlare).  This means if your site uses CloudFlare, the JavaScript code used by Google Analytics will be cached in their data centers and delivered to end users via the closest data center (long story short is that it will make for a faster user experience).

== Thanks ==

Thank you to all the individuals who have contributed translations for Better Google Analytics:

* Indonesian: [Arick](http://www.developingwp.com/#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin)

== Screenshots ==

1. Google Analytics dashboard in real time mode.
2. Google Analytics dashboard showing page views by normalized categories for the last month.
3. Google Analytics dashboard showing organic search traffic by country for the last 3 months.
4. Google Analytics dashboard showing sessions by date for the last 3 months.
5. One of ~1,000 metric/segment combinations for Google Analytics weekly heat maps (showing all sessions for the last 4 weeks).
6. Stacked area percent charts of your Google Analytics data allow you to see historical changes (browser usage for the last 10 years shows the rise of Chrome and the fall of Internet Explorer).
7. Better Google Analytics event report shows things like external links being clicked, YouTube video engagement, comments being created, etc.  You are able to correlate that data against any other metrics from your Google Analytics account.  For example maybe you wanted to see what countries users are in that watch YouTube videos.
8. Better Google Analytics issue monitoring report alerts to you client-side issues with your site.  Things like invalid pages being accessed (404), JavaScript errors, images not loading, embedded YouTube videos that the author removed, etc.
9. An automated system that is able to check your Google Analytics account and helps you configure your Google Analytics web property settings properly is included.
10. Better Google Analytics includes an optional front-end widget that shows popular pages/posts being viewed right now (data comes from Google Analytics Real Time API).
11. Better Google Analytics includes an optional front-end widget that allows you to display your Google Analytics stats based on any metric you wish.
12. Better Google Analytics General settings allows you to enable/disable all sorts of tracking features in your Google Analytics account.
13. Google Google Analytics custom dimension tracking allows you to track categories, authors, tags, publication year, user roles and registered users.
14. Social button engagement allows you to track things like Likes/Unlikes/Tweets/Shares right within your Google Analytics account.
15. Track clicks on the ads on your site within your Google Analytics account.
16. Issue monitoring settings allow you to utilize your Google Analytics account to keep on top of client-side issues with your site.
17. Advanced settings allow you to fine tune how the system works with Google Analytics.

== Changelog ==
= 1.0.4 =
* Bug: Fixed cosmetic formatting issue on settings page when on very thin screens (responsive mobile)
* Feature: Added new Google Analytics Custom Dimension tracking option (Publication Year)
* Feature: Added new Google Analytics Custom Dimension tracking option (User Role)
* Feature: Added ability to optionally have a single linked Google Analytics account for all sites in a multisite network setup
* Feature: Added ability to specify a custom Google Analytics API project ID for a multisite network (similar to how you can already for a single site)
* Added "Verify Domain" link to plugin page when Pro version is installed on an unknown domain

= 1.0.3 =
* Enhancement: Custom Dimensions settings generates a drop-down list of custom dimensions defined within Google Analytics account (if you have a Google Analytics account linked via API)
* Translation: Added a few missed phrases to WordPress translation system
* Removed some unnecessary debugging code

= 1.0.2 =
* Bug: Fixed cosmetic error message when creating a new site in a multi-site setup with debugging on
* Feature: Better Google Analytics heat maps, charts, event tracking and issue monitoring can be viewed on a per page basis via new Page Analytics option on admin bar
* Feature: Better Google Analytics Stats Widget

= 1.0.1 =
* Translation: Indonesian

= 1.0.0 =
* Initial release of Better Google Analytics