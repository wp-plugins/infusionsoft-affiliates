=== Plugin Name ===
Contributors: jeremyshapiro
Tags: affiliates, affiliate program, infusion, infusionsoft, tracking, jeremy shapiro, webinar, webinars, personalization, caching
Requires at least: 2.0.2
Tested up to: 4.3
Stable tag: 0.3

This plugin allows you to load an Infusionsoft Affiliate's information into your wordpress pages using the [affiliate] shortcode.

== Description ==

After you configure your Infusionsoft Affiliate, you'll be able to sync up your affiliates to your wordpress plog and add in affiliate specific merge fields.

Where might you use this?

* Personalized sales pages for your affiliates to use in promoting your product
* Personalized opt-in pages that appear to be written by your affiliate
* Custom webinar sign up forms, one for each affiliate

Use the `[affiliate]` shortcode with a specific field; for example, to get the affiliate's name, use `[affiliate field="AffName"]`.

You can also specify a `default` value like `[affiliate field="AffName" default="your friend"]`.

For fields that are dates, you can specify a [PHP friendly date format](http://php.net/manual/en/function.date.php), for example `[affiliate field="_WebinarDate" format="l, f Js" /]`.

Additionally, for date fields, you can move a date forward or backwards in time with the `dateshift` option like [affiliate field="_WebinarDate" default="3 days before our webinar" format="l, F jS" dateshift=" -3 days" /]

`dateshift` should start with a `+` or a `-` followed by a number and then a unit, for example `- 1 day`, `+90 minutes`, or `+1 year`.

All your custom affiliate fields get pulled down, too, just don't forget to put the underscore in front, i.e. _YourCustomField.

== Installation ==

To install the plugin:
1. Download the plugin
2. Upload to your wordpress blog
3. Activate and Configure

= Configuration =

To configure your installation, click Settings and then select enter your Infusionsoft Application name and API Key.

== Changelog ==

7/6: Version 0.2 Added
7/6: Version 0.3 Added dateshift option for dates so you can move make relative dates 
7/6: Version 0.3 Fixed bug so you can now access settings right from the plugin listing
