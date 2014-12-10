=== Get Directions ===
Contributors: Locally Digital Ltd
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZEWW5LKK5995J
Tags: MapQuest, Maps, Responsive, Directions
Requires at least: 3.4
Tested up to: 4.0.1
Stable tag: 1.23
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get Directions is a responsive map plugin powered by MapQuest. Widget and shortcodes that auto detect mobiles.
== Description ==
* Free
* Simple WordPress install
* Works on both Desktop and Mobile devices
* Responsive

The most comprehensive way for customers to get directions to your shop, office or location with ease on desktop, mobile & responsive themes 

Once you set the location, visitors to the website will be able to get directions to that location.

The plugin automatically picks up the visitor's location through the browser and mobile gps (when allowed).

Works on both desktop & mobile devices and can be used on full or responsive mobile websites. When your website is visited on mobile devices users get directions to the set location through Google Maps or the mobile device's default map app, making it easy for them to navigate to your location on the go.

Questions? Try our facebook page https://www.facebook.com/llocally
== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently asked questions ==

= how do I use the shortcode? =
to display a map on the page use the short code
[get-directions] with the appropriate parameters

A destination is mandatory and may be specified preferably as a latitude / longitude pair
e.g.    latlong='51.36887,-0.408999'
or can be based on postcode/zipcode & country

*Optional*

The route will be displayed if the browser permits providing the users location, however is you want to permanently turn off directions set
hideroute='yes'

Specify an operating radius, which draws an opaque circle based on your location
radius='12MI'      for 12 miles
radius='12KM'       for 12 Kilometres

Specify the map image height ( default = 300px)
height='400px'

Specify the map width ( default full width - 100%)
width='500px'

Specify the initial zoom level (1-16) (default 12)
zoom='10'
* note the map will auto zoom if a route is displayed so this setting is only really relevant when hideroute='yes'

Specify the type of map controls
controls='largezoom' (default) shows a large map control
controls='smallzoom' shows a small map control
controls='false' shows no map controls

Specify the name used on the map marker  when the mouse hovers (only applies if hideroute='yes')
rotitle='My House'   (default = none)

Specify the detailed used on the marker pointer  when the mouse is clicked(only applies if hideroute='yes')
rocontent='My Address'   (default = none)

*Examples*

Show directions to latitude 40.74802 longtitude -73.98512
[get-directions latlong='40.748021,-73.98512']

Show a simple map without a route, with marker with some details
[get-directions latlong='40.748021,-73.98512' hideroute='yes' rotitle='Empire State Building' roicontent='Empire State Building, 10118, NY, NY, USA]

= Can I display multiple maps (shortcodes or widgets) on one page? =
this feature is not yet available
= Can I display multiple map markers? =
Currently only 1 marker can be used on a non-route map
= how do I change the color and size of the 'get directions' button? =
the button is totally styled in CSS so you can change this as you like in your theme's style.css
= can I change the style of the map pointer? =
for the route pointers (A to B) the style is fixed, but for the single map pin you can change these. There is a filter hook that can be coded in your theme's functions.php
= My operating radius circle isn't a circle?
This happens sometimes when your zoom level is too high for your radius crcle to fite, try a different zoom level to get your circle inside the map
= Do you have other plugins? =
We have some more plugins in the pipeline at http://llocally.com

== Screenshots ==
1. On Desktop. Automatically picks up the users location through the browser once allowed.Provides Printable driving directions to the set location. Users can edit their location easily by dragging the pin in the interactive map
2. On Mobile. Automatically detects when being used on mobile devices. One press Get Directions button. Users get directions to the set location through installed map apps on the device. Easy Navigation on the go. Optional setting to hide the map totally on mobile, just having a button
3. Widget configuration for sidebars as well as shortcode

== Changelog ==

= 1.23 =
* Important, geocoding by postcode is no longer supported in the shortcode, only use lat / long. Change your shortcodes if using postcode before upgrading. Changed calls to MapQuest Open rather than MapQuest Community as MapQuest is removing this as of 31 Dec 2014, so earlier versions of this plugin may not work after 1 Jan 2015
= 1.22 =
* Remove credit link to llocally, cause no one likes those
= 1.21 =
* minor fix to remove warning message
= 1.2 =
* removed the need for new users to get an API key from mapquest, you can still use your own Mapquest API key if you like add
define( 'MAPQUEST_API_KEY', 'your long api key here' ); to your wp-config.php file
= 1.1 =
* fixed bug where browser asking for location when no route required
* added feature to optionally show or hide map on mobile devices - leaving just the button
* added new feature to display a radius area to the short code
= 1.002 =
* allow setting spage =
= 1.001b =
* readme changes 
= 1.001a =
* added images to readme
= 1.0 =
*  added readme.txt 

== Upgrade notice ==





