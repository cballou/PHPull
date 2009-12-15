=== PHPull ===
Contributors: jqueryin
Donate link: http://www.jqueryin.com/
Tags: php, source, code, sourcecode, developer, developers, syntax, function, method, lookup
Requires at least: 2.5.1
Tested up to: 2.7+
Stable tag: 1.0

Easily reference a PHP function and have it displayed in a styled tooltip on hover.

== Description ==

PHPull is a Wordpress plugin for developers who wish to easily and accurately reference php functions directly from
the PHP.net website.  The plugin is very easy to use and the tooltip display is easily customizable by updating
an existing stylesheet theme or creating your own.

Credit goes out to S.C. Chen for his Simple HTML DOM class that was used in this plugin. For more information on
this class, please visit http://sourceforge.net/projects/simplehtmldom/.

Credit also goes out to Michael Leigeber for his amazingly lightweight tooltip javascript code.  For more
information on this class, please visit http://www.leigeber.com/2008/06/javascript-tooltip/. Michael also
has a wonderful blog.

*Related Links:*

* <a href="http://www.jqueryin.com/phpull/" title="PHPull WordPress Plugin">Official Plugin Homepage</a>
* <a href="http://wordpress.org/tags/phpull">PHPull Support Forum</a>

*Change Log*

* 1.0	- Support for Firefox 2/3, IE 6/7, Safari 3/4, class support

== Installation ==

Installing PHPull is as easy as following a few simple steps:

1.	Download the PHPull plugin from the WordPress Plugin Repository.
2.	Extract the .zip file to the `/wp-content/plugins/` directory, preserving the PHPull directory
3.	Browse to the Plugins page from your WordPress admin
4.	Activate PHPull from the list of inactive plugins on the plugins menu page.
5.	Browse to `Settings > PHPull` if you would like to change your default theme.
6.  Refer to the official plugin page for further documentation

== Frequently Asked Questions ==

= What is the format of the PHPull tag? =

PHPull is utilized by simply adding the following BBCode tag into your posts, wrapping the function name as follows:
`[phpull]ksort[/phpull]`

PHPull is also capable of handling class functions, i.e.:

`[phpull class="domdocument"]getelementbytagname[/phpull]`

= Does PHPull currently support any other languages? =

The short answer is no.  If you would like to contribute to this project, please contact me and I would be more
than willing to integrate multiple languages (**only those supported by php.net mirrors**).

== Screenshots ==

1. This screen shot is an example of the default themed PHPull tooltip. (screenshot-1.jpg in basedir of plugin)
2.	This screen shot is an example of the preloader utilized in the default PHPull theme.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin
