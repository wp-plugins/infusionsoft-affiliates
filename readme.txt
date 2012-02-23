=== Plugin Name ===
Contributors: jeremyshapiro
Tags: affiliates, affiliate program, infusion, infusionsoft, tracking, jeremy shapiro, webinar, webinars, personalization, caching
Requires at least: 2.0.2
Tested up to: 3.2.1
Stable tag: 1.8

This plugin allows you to load an Infusionsoft Affiliate's information into your wordpress pages using the [affiliate] shortcode.

== Description ==

After you configure your Infusionsoft Affiliate plugin, you'll be able to sync up your affiliates to your WordPress site and add in affiliate specific merge fields.

Where might you use this?

* Personalized sales pages for your affiliates to use in promoting your product
* Personalized opt-in pages that appear to be written by your affiliate
* Custom webinar sign up forms, one for each affiliate

Use the `[affiliate]` shortcode with a specific field; for example, to get the affiliate's name, use `[affiliate field="AffName"]`.

You can also specify a `default` value like `[affiliate field="AffName" default="your friend"]`.

For fields that are dates, you can specify a [PHP friendly date format](http://php.net/manual/en/function.date.php), for example `[affiliate field="_WebinarDate" format="l, f Js" /]`.

Additionally, for date fields, you can move a date forward or backwards in time with the `dateshift` option like `[affiliate field="_WebinarDate" default="3 days before our webinar" format="l, F jS" dateshift=" -3 days" /]`

When using `dateshift`, the value should start with a `+` or a `-` followed by a number and then a unit, for example `- 1 day`, `+90 minutes`, or `+1 year`.

If you are returning any HTML code, for example a tracking pixel, an image tag, rich HTML, etc... you may want to specify `htmldecode=1` to prevent wordpres from escaping your HTML.

All your custom affiliate fields get pulled down, too, just don't forget to put the underscore in front, i.e. _YourCustomField.

== Installation ==

To install the plugin, download it, upload to your wordpres blog, activate and configure.

= Configuration =

To configure your installation, click Settings and then select enter your Infusionsoft Application name and API Key.

= Custom Development =

If you need help with a custom installation or modification to better integrate into your Infusionsoft application and WordPress site, please contact Jeremy B. Shapiro directly.

== Changelog ==

= Version 1.8 =
* 2/22/2012: Added support for htmldecode to prevent WordPress from escaping HTML when you actually want HTML.

= Version 1.7 =
* 1/28/2012: Added support for registering the plugin. Made redirects temporary redirects. Pages that do not redirect now don't redirect to themselves losing query paramters.

= Version 1.5 =
* 11/22/2011: If a URL is a valid page, don't check for the affiliate in the root of the URL. This prevents affiliates from hijacking pages on your site by picking affiliate codes that match the URLs of pages on your site.

= Version 1.4 =
* 10/31/2011: Added support for affiliate lists over 1,000
* 10/31/2011: Implemented a basic form of locking to prevent simultaneous syncing

= Version 1.2 =
* 10/12/2011: Changed version number to a stable number since... it's stable :)
* 10/12/2011: If an invalid code is specified and a valid code is required, the default page will now be shown. Previously, invalid codes worked

= Version 0.6 =
* 8/2/2011: Added support for individual page overrides on what to do when an affiliate code isn't present
* 8/10/2011: Bug fix to allow for better default page processing when a query string is present

= Version 0.5 =
* 7/12/2011: Help added to the options page
* 7/12/2011: Root URL affiliate code detection mildly operational
* 7/12/2011: Options for where to look for the affiliate code have been revised so multiple options can be selected

= Version 0.4 =
* 7/11/2011: Default pages are now supported for when an affiliate code is found, but no path is specified, or when an affiliate code is simply not found.

= Version 0.3 =
* 7/6/2011: Added dateshift option for dates so you can move make relative dates 
* 7/6/2011: Fixed bug so you can now access settings right from the plugin listing
* 7/6/2011: Caching is now supported and deactivation no longer deletes options and data

= Version 0.2 =
* 7/6/2011: Plugin Added
