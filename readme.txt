=== Before deleting the posts ===
Contributors: Asumaru, moo2000
Donate link: http://asumaru.com/business/wp-plugins/b4delposts/
Tags: post, page, delete, export, backup
Requires at least: 4.0
Tested up to: 4.0
Stable tag: 0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

 This plugin backs up before deleting posts.

 When you click "Delete Permanently" WordPress will create an XML file to save to your server computer.

 The XML file is stored in "wp-content/deleted_posts" on your wordpress.
 The XML file is stored in the site-id if your wordpress is multi sites.

 The XML file is stored with a format same as Export on Wordpress.
 If you want to restore the post, you can use Import on Wordpress.

 Supporting post-types are "post", "page" and "custom post".
 But "attachment" is unsupported.

 --- Attention ---

 We warn users of version 0.1.x.
 Version 0.1.x has an error at the time of the installation.
 After having deleted version 0.1 or 0.1.1, please install you again.

== Installation ==

1. Download before-deleting-the-posts.zip 
2. Upload the unpacked folders and files into "wp-content/plugins/". 
3. Activate the plugin from the Admin interface; "Plugins". 

 --- Attention ---

 We warn users of version 0.1.x.
 Version 0.1.x has an error at the time of the installation.
 After having deleted version 0.1 or 0.1.1, please install you again.

== Frequently Asked Questions ==

= Why do we need this? =
Because administrators or editors are not you only.
The Wordpress has many administrators and editors if it is wordpress of the multi-site.
Every administrators and editors can delete posts and pages.
Other administrators or editors may delete post or the page before you know it.
After it was deleted, you cannot restore it.
Everybody wants to delete it.

== Screenshots ==

1. Trigger "Delete Permanently".

2. Backed up deleted posts.

3. Backup as Export XML.

== Changelog ==

= 0.1 (2014.12.01) =
  first version.

= 0.1.1 (2014.12.02) =
  Bug Fix.

= 0.2 (2014.12.03) =
  We changed plugin-file-name from "asm-B4DelPosts" to "before-deleting-the-posts".
  Because there was an installation error "The plugin does not have a valid header.".

== Upgrade Notice ==

= 0.2 (2014.12.03) =
 We warn users of version 0.1.x.
 Version 0.1.x has an error at the time of the installation.
 After having deleted version 0.1 or 0.1.1, please install you again.
  
== Arbitrary section ==

 --- Attention ---

 We warn users of version 0.1.x.
 Version 0.1.x has an error at the time of the installation.
 After having deleted version 0.1 or 0.1.1, please install you again.
