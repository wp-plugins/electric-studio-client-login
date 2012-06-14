=== Plugin Name ===
Contributors: irvingswiftj, Matthew Burrows, Electric Studio
Tags: clients, users, roles, groups
Requires at least: 3.1
Tested up to: 3.4
Stable tag: 0.8.1

A plugin that creates groups for users and allows you to change the content (with use of shortcodes) to change content depending on which user is logged in.

== Description ==

Add users by using the standard User management in wordpress. Just set them to the role of client.

Use shortcode [escl_logged_in]content[/escl_logged_in] to make content available only to logged in users.
With the [escl_logged_in] shortcode, you can specify only for a certain user or certain groups by using the attributes 'user' and 'group'.
i.e. To show content only to user 'joebloggs':
* [escl_logged_in user="joebloggs"]content[/escl_logged_in]

And to show content only to groups 'group1' and 'group2' (When using multiple groups, the groups must be separated by a '|'):
* [escl_logged_in group="group1|group2"]content[/escl_logged_in]

N.B. This is a beta version. The more feedback we get, the quicker we can make it into a stable version.


== Installation ==

1. Upload the .zip file using the Builting Wordpress plugin Uploader
1. Wordpress will install the plugin for you. 

== Frequently Asked Questions ==

= Can the user attribute handle multiple users =

No. If you want to handle multiple users, create a new group instead.

= This plugin have stop working after changing my theme =

On activation of this plug, a template file is copy to your theme folder. Therefore, if you change theme, you must reinstall this plugin

== Screenshots ==

1. The Configuration Page
2. Restrict pages using our metabox on the posts/pages edit view.

== Changelog ==

= 0.5 =
* 1st Beta Version

= 0.7 =
* 2nd Beta Version
* Add custom fields to users
* Better Wordpress Admin intergration
* Fixed Redirecting Bug
* Nicer Styling (IMO!)

= 0.7.1 =
* Improved Redirecting

= 0.7.5 =
* Can now deal with usernames with spaces
* New option to redirect to homepage on logout

= 0.7.6 =
* Wordpress 3.3 bug fixed

= 0.7.7 =
* Activation and deactivation bug fix

= 0.8 =
* Deletes tables on uninstall
* Client Login User Management Removed, instead using Wordpress' default user management
* Markup on Login Form improved
* Group Management improved and slightley redesigned
* Groups now show on the user page
* Fix for redirecting to login on a page that contains a link to a locked post/page
* Groups, Users, Function and Options are now all coded in classes (keeps plugin consistant with out other plugins)
* Client setup page slightley redesigned
* Restrictions on hierachical post types now affect their child posts

= 0.8.1 =
* Minor Bug Fixes
* Changed method in which files are copied during activation (you shouldn't see headers already sent error when activatinga anymore)
* Clients Area page showing blank has be resolved
* There is now a default group in which all clients are automatically a member. 

== Upgrade Notice ==

= 0.5 =
This is the 1st Beta.

= 0.7 =
* Bug Fix
* New Admin Interface
* Added Features

= 0.7.1 =
* Bug Fix

= 0.7.5 =
* Can now deal with usernames with spaces
* New option to redirect to homepage on logout

= 0.7.6 =
* Wordpress 3.3 bug fixed

= 0.7.7 =
* Activation and deactivation bug fix

= 0.8 =
* Deletes tables on uninstall
* Bug fixes
* Admin interface redesign
* Restrictions on hierachical post types now affect their child posts

= 0.8.1 =
* Minor Bug Fixes