=== Plugin Name ===
Contributors: irvingswiftj, Electric Studio
Tags: clients, users, roles
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 0.5

A plugin that creates groups for users and allows you to change the content (with use of shortcodes) to change content depending on which user is logged in.

== Description ==

Add users by using the standard User management in wordpress. Just set them to the role of client.

Use shortcode [escl_logged_in]content[/escl_logged_in] to make content available only to logged in users.
With the [escl_logged_in] shortcode, you can specify only for a certain user or certain groups by using the attributes 'user' and 'group'.
i.e. To show content only to user 'joebloggs':
- [escl_logged_in user="joebloggs"]content[/escl_logged_in]

And to show content only to groups 'group1' and 'group2':
- [escl_logged_in group="group1|group2"]content[/escl_logged_in]

N.B. When using multiple groups, the groups must be separated by a '|'.

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

== Upgrade Notice ==

= 0.5 =
This is the 1st Beta.

