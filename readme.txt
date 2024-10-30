=== Current Post ===
Contributors: basitadhi
Tags: post, shortcode, currentpost, current, blog, time, author, toc
Requires at least: 3.0
Tested up to: 6.1.1
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is a shortcode to display current post information.

== Description ==
This is a shortcode to display current post information. For example, you can use this shortcode below quote, so you can screenshot and share easily, your quote and link of the post.

= Features =
* Available information to display:
  POST
  ID, post_author, post_author_link [+ 1.0.1], post_author_nick_link [+ 1.0.1], post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, permalink, permalink_link, address_bar_permalink [+ 1.0.8], address_bar_permalink_link [+ 1.0.8]
  TIME
  now [+ 1.0.2]
  BLOG
  blog_name [+ 1.0.7], blog_description [+ 1.0.7], blog_wpurl [+ 1.0.7], blog_wpurl_link [+ 1.0.7], blog_url [+ 1.0.7], blog_url_link [+ 1.0.7], blog_charset [+ 1.0.7], blog_language [+ 1.0.7], blog_atom_url [+ 1.0.7], blog_atom_url_link [+ 1.0.7], blog_rdf_url [+ 1.0.7], blog_rdf_url_link [+ 1.0.7], blog_rss_url [+ 1.0.7], blog_rss_url_link [+ 1.0.7], blog_rss2_url [+ 1.0.7], blog_rss2_url_link [+ 1.0.7], blog_comments_atom_url [+ 1.0.7], blog_comments_atom_url_link [+ 1.0.7], blog_comments_rss2_url [+ 1.0.7], blog_comments_rss2_url_link [+ 1.0.7]
  AUTHOR
  author_description [+ 1.0.7], author_display_name [+ 1.0.7], author_first_name [+ 1.0.7], author_last_name [+ 1.0.7], author_nickname [+ 1.0.7]
  VARIABLE
  variableinput [+ 1.0.31], variableoutput [+ 1.0.31]
  TOC
  toc (table of content) [+ 1.0.2, e 1.0.3: with link to TOC in the bottom of the page, e 1.0.8 smart link to TOC, e 1.0.28 facility to exclude from TOC], toc_without_back [+ 1.0.3], toc_neighbour_by_id [+ 1.0.3, e 1.0.4 with post title], toc_neighbour_by_url [+ 1.0.3, e 1.0.4 with post title]
* Add label for information to display [+ 1.0.2]
* Automatically add meta description [+ 1.0.11]
* Automatically add meta title [+ 1.0.11, - 1.0.13]
  Rule: 1st paragraph >= 50 chars? if not, 2nd paragraph >= 50 chars? if not, 3rd paragraph >= 50 chars? if not, 1st blockquote exists? if not, no meta description.
* Automatically add meta keyword for post improvement purpose [+ 1.0.13]
* Automatically add open graph and json-ld [+ 1.0.15, + 1.0.17], date published and modified in meta head [+ 1.0.16]
* JQuery Datatables and tools to convert from spreadsheet to HTML: spreadsheet_to_html [+ 1.0.18], datatable [+ 1.0.18]
* Hijri date format (Bahasa Indonesia Only) [+e 1.0.21]

== Screenshots ==
1. Screenshot and share quote easily

== Catalogue ==
[Current Post](https://pdsi.unisayogya.ac.id/how-to-use-currentpost-wordpress-plugin/ "Current Post by Basit AP")

== Testing History ==
[Testing History](https://pdsi.unisayogya.ac.id/current-post-tester/ "Testing History of Current Post")

== Example ==
[currentpost type="permalink, post_date"]
Result Example:
https://example.org 2021-01-01 00:00:00

[currentpost type="post_author_nick_link"]
Result Example:
<a href="https://example.org/author/basit/">[bst]</a>

[currentpost type="post_author_nick_link" label="This is the sample of post_author_nick_link"]
Result Example:
This is the sample of post_author_nick_link <a href="https://example.org/author/basit/">[bst]</a>

[currentpost type="toc_neighbour_by_id" label="1"]
Result Example:
* <a href="https://example.org/#head1">This is Heading</a>
* <a href="https://example.org/#head2">This is another Heading</a>
* <a href="https://example.org/2/#head3">This is Heading in page 2</a>
* <a href="https://example.org/3/#head4">This is Heading in page 3</a>
* <a href="https://example.org/3/#head5">This is another Heading in page 3</a>

== Bugs Fix ==

= 1.0.3
* Fix Missing closing UL in toc

= 1.0.5
* Fix Wrong URL of toc of neighbour link

= 1.0.6
* Remove space in the URL in the toc

= 1.0.8
* No link to anchor if URL of address bar different from permalink (smart link to TOC)

= 1.0.9
* Add indentation in TOC based on heading level
* Fix TOC if post use page break(s)

= 1.0.10
* Change the_author to get_the_author
* Fix now
* Fix style of link to TOC

= 1.0.11
* Add meta title and description

= 1.0.12
* Fix exact word between title and description from case sensitive to incase sensitive. Blog description as last option to generate meta description, not title.

= 1.0.13
* Exact word move to keyword, for post improvement purpose. 
* Remove meta title.
* Can use both gutenberg and classic

= 1.0.14
* Meta show if only URL of address bar same as permalink

= 1.0.15
* Add open graph and json-ld. Thumbnail taken from featured image, then first image (img src), then image post attachment, if exists.
* Fix extract_tags_current_post when handle self closing

= 1.0.16
* Add date published and modified in meta head

= 1.0.17
* Add open graph and json-ld for index

= 1.0.18
* Add Jquery Datatables from usual table
* Add tools to convert from spreadsheet to HTML and an shortcode of currentpost jquery datatables

= 1.0.19
* Fix tools (from echo to return)
* Fix closing ul in TOC

= 1.0.20
* Add date format 'Y-m-d-hijriah', add Hijri to Post's Date (Bahasa Indonesia Only)

= 1.0.21
* Change date format 'Y-m-d-hijriah' to 'Y-m-d-#'; and add short format 'Y-m-d-$' (Bahasa Indonesia Only)

= 1.0.22
* Override both the_time and get_the_time

= 1.0.23
* bugs fix on override get_the_time

= 1.0.24
* remove the_time (the_time call get_the_time)

= 1.0.25
* Change date format 'Y-m-d-#' to 'Y-m-d-4' and 'Y-m-d-$' to 'Y-m-d-5' (Bahasa Indonesia Only)
* Fix date and time problem

= 1.0.28
* Change, use id="exclude_toc" on header to exclude it from TOC

= 1.0.30
* use curl rather than file_get_contents 

= 1.0.32
* fix mismatch in example

= 1.0.33
* add eval ability

= 1.0.34
* bug fix, bracket

= 1.0.35
* bug fix variable mismatch

= 1.0.36
* add function call, optimize javascript

= 1.0.37
* add string call

= 1.0.38
* fix string call and <> symbol; input as output (event: onfocus)

= 1.0.39
* trim variableinput, enclose variableoutput, dot escape