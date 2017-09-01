=== MailPlus Forms ===
Contributors: mailplus
Donate link: http://www.mailplus.nl
Tags: forms, mailplus
Requires at least: 3.3
Tested up to: 4.7
Stable tag: trunk
License: Modified BSD License

With the MailPlus Forms Plugin you can easily integrate web forms or surveys created in MailPlus (www.mailplus.nl) without technical knowledge.

== Description ==

With the MailPlus Forms Plugin web masters can easily integrate web forms or online surveys created in [MailPlus](http://www.mailplus.nl) on pages and posts without any technical knowledge. MailPlus is an online marketing platform which contains a user-friendly form editor with a lot of features. For example, matrix questions, conditional questions, skip logic/branching, multi-paging, extensive features for validating answers from respondents, great e-mail confirmation possibilities and much more. 
 
== Installation ==

1. Install the plugin.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter the Consumer Key and Consumer Secret through the 'MailPlus Forms' option in the 'Settings' menu in Wordpress.
4. Add a form in a post or a page through the "Add a MailPlus form" button in the HTML editor (TinyMCE)

== Changelog ==

= 1.0.6 =
* Fix warnings while submitting a form

= 1.0.5 =
* Fix for supporting HTTPS completely

= 1.0.4 =
* Support external URL's as last form page. This has been an option in MailPlus for quite a while and was not supported until now.
* Support for prefilling forms inside Wordpress. When linking to a page with a form add "?encId={encId}" to the URL in MailPlus.

= 1.0.3 =
* Unset $_POST['formEncId'] after handling the post to prevent the handling of posted values more than once when multiple forms are used in one page

= 1.0.2 =
* Changed \_\_DIR\_\_ to dirname(\_\_FILE\_\_) so the plugin works on PHP < 5.3.1

= 1.0.1 =
* Plugin directory is "mailplus-forms", not "mailplusforms"

= 1.0 =
* Initial version released

== Upgrade Notice ==

