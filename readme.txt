=== Better Google Analytics ===
Contributors: digitalpoint
Tags: analytics, google analytics, statistics, tracking, code, dashboard, analytics dashboard, google analytics dashboard, reports, charts, api, stats, realtime, youtube, outbrain, taboola, adsense, google, digitalpoint
Donate link: https://marketplace.digitalpoint.com/better-analytics.3354/item#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin
Requires at least: 3.8
Tested up to: 4.2.2
Stable tag: 1.0.2
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Track everything with Google Analytics (clicked links, emails opened, YouTube videos being watched, etc.). Includes real time Analytics dashboard.

== Description ==
The Better Analytics plugin allows you to easily add Google Analytics code to your website and gives you the power to track virtually everything.  Better Analytics includes heat maps, reports, charts, events and site issue tracking in your WordPress admin area without the need to log into your Google Analytics account.

<strong>Better Google Analytics Basic Tracking Features (each can be enabled/disabled):</strong>

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
* Force Traffic Over SSL
* RSS/Email Link Source Tracking
* Advertising Ad Clicks
* Page Not Found (404)
* AJAX Requests

<strong>Better Google Analytics Dimension Tracking:</strong>

* Categories
* Author
* Tags
* User

<strong>Better Google Analytics Social Button Engagement Tracking:</strong>

* Facebook
* Twitter
* Google+
* Pinterest
* LinkedIn

<strong>Better Google Analytics Reporting Features (can be viewed site-wide or for individual page/URL):</strong>

* Dashboard Charts (real time or historical)
* Weekly Heat Maps
* Historical Area Percent Charts
* Events
* Issue Monitoring

<strong>Better Google Analytics Advanced Features:</strong>

* Suppress Tracking By User Role
* View Reports By User Role
* Adjust Location Of Analytics Code
* Campaign Tracking By Anchor or Parameters
* Adjustable Sample Rate
* Insert Your Own Custom JavaScript
* Debugging Mode

<strong>Better Google Analytics Widgets Included:</strong>

* Popular Posts
* Statistics based on selectable metric

> <strong>Better Analytics Pro</strong><br>
> If you would like additional advanced functions, we offer a Pro version.
>
> * Additional ad networks for ad click tracking
> * More options for site issue monitoring
> * More heat map metrics
> * More charting dimensions
> * eCommerce tracking (coming soon)
> * Option for server-side tracking of users (or bots)
> * Faster API calls (uses a custom system for parallel requests)
> * Priority support
>
> [Pro license available here](https://marketplace.digitalpoint.com/better-analytics-pro.3355/item#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin)

== Installation ==
1. Upload `better-analytics` folder to the `/wp-content/plugins/` directory.
1. Activate the Better Analytics plugin through the 'Plugins' menu in the WordPress admin area.
1. Add your Web Property ID under the 'Settings -> Better Analytics' area of the WordPress admin.
1. Optional (but probably a good idea so you don't double report your traffic) - disable any other Analytics code you have enabled.

== Frequently Asked Questions ==
= Can Better Analytics Be Used With Legacy Google Analytics Code? =
No, the Better Analytics plugin is for Google Universal Analytics.  You can upgrade any old non-Universal property to support both legacy and Universal under your [Google Analytics Property Settings](https://www.google.com/analytics/web/?#management/Settings/).

= Can You Add [insert feature here] To Better Analytics? =
If it's possible and it makes sense, then yes.  The best way to put in a feature request for Better Analytics would be to create a thread in the [support forum over here](https://forums.digitalpoint.com/forums/better-analytics.31/#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin).

= I speak a language that isn't supported by Better Analytics, can I help translate it? =
Yes.  Unfortunately we don't speak every language in the world, so if you would like to help with translating, please contact us in the [support forum over here](https://forums.digitalpoint.com/forums/better-analytics.31/#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin).

== CDN ==

The JavaScript used by Better Analytics should be able to be cached properly by content delivery networks (it has been tested with CloudFlare).  This means if your site uses CloudFlare, the JavaScript code used by Google Analytics will be cached in their data centers and delivered to end users via the closest data center.

== Thanks ==

Thank you to all the individuals who have contributed translations for Better Analytics:

* Indonesian: [Arick](http://www.developingwp.com/#utm_source=readme&utm_medium=wordpress&utm_campaign=plugin)

== Screenshots ==

1. Google Analytics dashboard in realtime mode.
2. Google Analytics dashboard showing page views by normalized categories for the last month.
3. Google Analytics dashboard showing organic search traffic by country for the last 3 months.
4. Google Analytics dashboard showing sessions by date for the last 3 months.
5. One of ~1,000 metric/segment combinations for Google Analytics weekly heat maps (showing all sessions for the last 4 weeks).
6. Stacked area percent charts of your Google Analytics data allow you to see historical changes (browser usage for the last 10 years shows the rise of Chrome and the fall of Internet Explorer).
7. Better Analytics event report shows things like external links being clicked, YouTube video engagement, comments being created, etc.  You are able to correlate that data against any other metrics from your Google Analytics account.  For example maybe you wanted to see what countries users are in that watch YouTube videos.
8. Better Analytics issue monitoring report alerts to you client-side issues with your site.  Things like invalid pages being accessed (404), JavaScript errors, images not loading, embedded YouTube videos that the author removed, etc.
9. An automated system that is able to check your Google Analytics account and helps you configure your Google Analytics web property settings properly is included.
10. Better Analytics includes an optional front-end widget that shows popular pages/posts being viewed right now.
11. Better Analytics General settings.
12. Google Analytics custom dimension tracking allows you to track categories, authors, tags and registered users.
13. Social button engagement allows you to track things like Likes/Unlikes/Tweets/Shares right within your Google Analytics account.
14. Track clicks on the ads on your site.
15. Issue monitoring settings allow you to keep on top of client-side issues with your site.
16. Advanced settings allow you to fine tune how the system works with Google Analytics.

== Changelog ==
= 1.0.2 =
* Bug: Fixed cosmetic error message when creating a new site in a multi-site setup with debugging on
* Feature: Better Google Analytics heat maps, charts, event tracking and issue monitoring can be viewed on a per page basis via new Page Analytics option on admin bar
* Feature: Better Google Analytics Stats Widget

= 1.0.1 =
* Translation: Indonesian

= 1.0.0 =
* Initial release of Better Google Analytics