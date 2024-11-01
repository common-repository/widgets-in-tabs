=== Widgets In Tabs ===
Contributors: ahspw
Tags: tabs, tabbed, widget, tabbed widget, theme, sidebar, widget area
Requires at least: 3.9.0
Tested up to: 4.5
Stable tag: 2.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show all the widgets you love, forget about clutter.

== Description ==

Simple and elegant. WIT allows you to display all the widgets you love, without cluttering your site.


= How it works =

1. WIT adds a speical sidebar and a special widget. Let's call them WIT sidebar, and WIT widget.
2. The WIT sidebar is where you put all the widgets you love.
3. The WIT widget is what turns them into one tabbed, light and beautiful widget.

WIT sidebar is special, because it will not be displayed anywhere on your site. It's exclusively for WIT widget.
WIT widget is special, because you should never ever add it to WIT sidebar! Too much of special is just, well, too much!

= Features =

* **Adaptive Style**: Integrates seamlessly with professional themes. Choose the theme you love, and watch WIT dressing the same.
* **Unlimited Tabs**: Extra tabs are hidden behind a beatiful scrollbar. You can also display all tabs if you want.
* **Animations**: Animated, customizable, and sleek transition between widgets.
* **Customizable** Transitions: Be creative, and make your own transition recipe!
* **Automatic Transition**: Make the transition automatic with customizable interval.
* **User Friendly**: WIT is smart enough to never interrupt your visitors while they interact with it.
* **Shortcode**: Display WIT wherever you want, using the WIT shortcode. See FAQ for details.
* **Translation Ready**: RTL support and translation ready. It already has Arabic!
* **Multiple instances with different widgets**. You can add as many extra WIT areas as you want.

== Frequently Asked Questions ==

= How to use the WIT shortcode? =

Simply, type `[wit]` wherever you want WIT widget to appear inside your post or page. This will add a WIT instance with default options.

You can also use the WIT button for more options. Here are some examples:

* `[wit interval='0']`
* `[wit interval='3']`
* `[wit tab_style='scroll']`
* `[wit tab_style='show_all']`
* `[wit interval='3' tab_style='show_all']`

The interval attribute only accepts integer values that are equal to or larger than zero.
The tab_style attribute only accepts 'show_all' or 'scroll'.
If an inavlid value is provided, WIT will revert back to the default one.

**Important**

If you are using multiple WIT instances with the same area, you MUST specify a unique id for each instance to avoid invalid HTML on your blog, although WIT will attempt to avoid that anyways.
Example:

* `[wit area='my-special-wit' id='wit1']`
* `[wit area='my-speical-wit' id='wit2']`

= How to add extra WIT areas? =

Go to Dashboard -> Appearance -> WIT.

== Credits ==

WIT uses the following plugins:

* [prefect-scrollbar](http://noraesae.github.io/perfect-scrollbar)
* [jQuery hashchange event](http://benalman.com/projects/jquery-hashchange-plugin/)

== Screenshots ==

1. WIT Area
2. WIT Widget
3. WIT Options
4. Extra WIT areas screen
5. WIT in 2014 default theme
6. WIT in 2013 default theme
7. WIT in 2012 default theme

== Changelog ==

= 2.2.2 =

* Bug Fix: Using multiple instances of WIT widget that display the same WIT sidebar on the same page breaks all WIT instances on that page.
* Bug Fix: PHP syntax error.

= 2.2.1 =

*16 Apr 2016*

* Bug Fix: Typo in animation name.
* Bug Fix: Tabs titles are connected in complex scripts.
* Bug Fix: WIT does not work if shortcode is used alone without any WIT widget.
* Enhancement: Change some strings.
* Enhancement: jQueryUI animation scripts are loaded despite disabling animations.
* Known Issue: Using multiple instances of WIT widget that display the same WIT sidebar on the same page breaks all WIT instances on that page.

= 2.2.0 =

*18 Jan 2015*

* Bug fix:      If URL contains WIT hash, the selected effect should now fire once instead of twice.
* Bug fix:      If WIT has only one widget, do not rotate.
* Enhancement:  WIT should now be compatible with dynamic widgets
* Change:       Default effect duration is now 600ms, and default effect style is now sequential.
* NEW:          Extra feature: Hide current tab's content if its title is clicked.
* NEW:          Extra feature: Remember last shown tabs when page is reloaded

= 2.1.0 =

*06 Dec 2014*

* NEW: Link to a specific tab within WIT.
* Bug fix: Invalid HTML when multiple WITs show the same area.

= 2.0.4 =

*12 Nov 2014*

* Bug fix: Overlapping tabs on Chrome and Safari (introduced in version 2.0.2).

= 2.0.3 =

*08 Nov 2014*

* Bug fix: cannot add new area after deleting all areas.

= 2.0.2 =

*31 Oct 2014*

* NEW: Add classical effects (without using jQuery UI Effects).
* Enhancement: Use WP built-in jQuery UI rather than including it.
* Enhancement: Simplify WIT widget options form.
* Bug fix: Avoid JS conflicts with plugins that listens to click events.

= 2.0.1 =

*17 Oct 2014*

* Bug fix: WIT TinyMCE plugin should now work with any page that uses the built in editor.
* Enhancement: Better UX for the extra areas interface.

= 2.0 =

*10 Oct 2014*

* NEW: Multiple instances with different widgets.
* Several enhancements.

= 1.3 =

*09 Sep 2014*

* NEW: customize height (fixed or adaptive).

= 1.2 =

*01 Sep 2014*

* NEW: pause animation while the user is interacting with WIT, then resume.
* Enhanced transition.

= 1.1 =

*23 Aug 2014*

* NEW: customize effect duration.

= 1.0 =

*10 Aug 2014*

* NEW: lots of new transitions and customizations.
* Fix: several enhancements.
* Known issues
    * scrollbar disappears on RTL websites on non-webkit browsers

= 0.7 =

*25 Apr 2014*

* Bugfix: each WIT instance should have its own unique options.
* New: Option to show all tabs instead of a scrollbar.
* New: Shortcode to display WIT widget anywhere inside a page or a post.
* New: Editor button for WIT shortcode.
* Known issues:
    * scrollbar disappears on RTL websites on non-webkit browsers.
    * the sidebar to which WIT is added will have a long height depending on how many tabs WIT has.

= 0.5 =

*22 Apr 2014*

* Bugfix: when animation is disabled, clicking on a tab causes crazy animation.
* Dependencies upgraded.
* Code reviewed and some parts rewritten.
* WIT widget in admin area is now unique!.
* Known issues: scrollbar disappears on RTL websites on non-webkit browsers.

= 0.1 = 

*14 Apr 2014*

Initial release.

== Upgrade Notice ==

= 2.2.2 =
Bug fixes.

= 2.2.1 =
Bug fixes and performance improvements. Read the change log for an important known bug that is not yet fixed.

= 2.2.0 =
New features, enhancements and bug fixes.

= 2.1.0 =
New feature and a bug fix.

= 2.0.4 =
Bug fix.

= 2.0.3 =
Bug fix.

= 2.0.2 =
New effects, bug fixes and enhancements.

= 2.0.1 =
This release contains a bug fix and an enhancement, and requires at least WP 3.9.0.

= 2.0 =
NEW: Multiple instances with different widgets plus several other enhancements.

= 1.3 =
NEW: WIT now can have a fixed or adaptive height.

= 1.2 =
NEW: pause animation while the user is interacting with WIT, then resume. Enhanced transition.

= 1.1 =
NEW: customize effect duration.

= 1.0 =
NEW transitions and customizations.

= 0.7 =
This version fixes a bug and introduces new features. See Changelog for details.

= 0.5 =

This version fixes a bug, upgrades dependencies, and improves performance. See Changelog for details.