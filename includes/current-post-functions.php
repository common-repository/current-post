<?php
define('CPost_DT_NOTDATE', 0);
define('CPost_DT_DATE', 1);
define('CPost_DT_TIME', 2);
define('CPost_DT_DATETIME', 3);

$isdatatableloaded = false;
// Hook the 'admin_menu' action hook, run the function named 'current_post_Add_My_Admin_Link()'
add_action( 'admin_menu', 'current_post_Add_My_Admin_Link' );

// Add a new top level menu link
function current_post_Add_My_Admin_Link()
{
  add_menu_page(
        'Current Post - UNISA Yogyakarta', // Title of the page
        'Current Post', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'current-post/includes/current-post-adminmenu.php' // The 'slug' - file to display when clicking the link
    );
}

function get_current_post ( $atts )
{
  $result = "";
  $all_types  = array_unique ( explode ( ",", $atts['type'] ) );
  $all_labels = explode ( ",", $atts['label'] );
  if ( is_array ( $all_types ) )
  {
    if ( array_key_exists('label', $atts)  )
    {
      foreach ($all_types as $idx => $val)
      {
        $result .= process_single_current_post ( trim( $val ), array_key_exists("" . $idx, $all_labels ) ? $all_labels[$idx] : "") . " ";
      }
    }
    else
    {
      foreach ($all_types as $idx => $val)
      {
        $result .= process_single_current_post ( trim( $val ) ) . " ";
      }
    }
  }
  else
  {
    $result = process_single_current_post ( trim ( $atts['type'] ) );
  }
  return $result;
}

function process_single_current_post( $type, $label = "" )
{
  
  if (trim ( $label ) != "") $label = trim ( wp_strip_all_tags(sanitize_text_field($label)) ) . ((substr($type, 0, 8) == "variable")?"":" ");  
  switch($type)
  {
    //post
    case "ID":
    case "post_author":
    case "post_date":
    case "post_date_gmt":
    case "post_content":
    case "post_title":
    case "post_excerpt":
    case "post_status":
    case "comment_status":
    case "ping_status":
      return $label.get_post_field ( $type );
    case "permalink": 
      return $label.esc_url ( get_permalink() );
    case "permalink_link": 
      $result = $label.esc_url ( get_permalink() );
      return $label . "<a href='" .  $result  . "'>" . $result . "</a>";
    case "post_author_link":
      return $label . "<a href='" .  esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . "' title='" .  esc_attr( get_the_author() ) . "'>" . get_the_author() . "</a>";
    case "post_author_nick_link":
      return $label . "<a href='" .  esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . "' title='" .  esc_attr( get_the_author() ) . "'>[" . get_the_author_meta( 'nickname' ) . "]</a>";
    case "address_bar_permalink": 
      return $label.permalink_address_bar();
    case "address_bar_permalink_link": 
      $result = $label.permalink_address_bar();
      return $label . "<a href='" .  $result  . "'>" . $result . "</a>";
    //blog
    case "blog_name":
    case "blog_description":
    case "blog_wpurl":
    case "blog_url":
    case "blog_charset":
    case "blog_language":
    case "blog_atom_url":
    case "blog_rdf_url":
    case "blog_rss_url":
    case "blog_rss2_url":
    case "blog_comments_atom_url":
    case "blog_comments_rss2_url":
      return $label.get_bloginfo ( from_left ( $type, 5 ) );
    case "blog_wpurl_link":
    case "blog_url_link":
    case "blog_atom_url_link":
    case "blog_rdf_url_link":
    case "blog_rss_url_link":
    case "blog_rss2_url_link":
    case "blog_comments_atom_url_link":
    case "blog_comments_rss2_url_link":
      $type = from_left_right ( $type, 5, 5 );
      return $label . "<a href='" . esc_url( get_bloginfo ( $type ) ) . "' title='" . esc_attr( get_the_author() ) . "'>" . get_bloginfo ( $type ) . "</a>";
    //author
    case "author_description":
    case "author_display_name":
    case "author_first_name":
    case "author_last_name":
    case "author_nickname":
      return $label.get_the_author_meta( from_left ($type, 7) );
    //time
    case "now":
      //based on wordpress current_datetime , wp_timezone and wp_timezone_string
      $time = new DateTimeImmutable( 'now', new DateTimeZone( wp_timezone_string_() ) );
      return $label.$time->format( 'Y-m-d H:i:s' );
    //toc
    case "toc":
      return generate_toc_current_post($label);
    case "toc_without_back":
      return generate_toc_current_post($label, true);
    case "toc_neighbour_by_id":
      return generate_toc_current_post($label, false, "by_id");
    case "toc_neighbour_by_url":
      return generate_toc_current_post($label, false, "by_url");
    //variable
	   case "variableinput":
      $elem    = explode("|||", $label);
      $idinput = "i".$elem[0];
      if (count($elem) == 1)
      {
         $value        = "";
         $labelinput   = "";
         $classformula = "";
         $script       = "";
         $event        = "";
         $readonly     = "";
      }
      else
      {
         if (substr($elem[1], 0, 8) == "formula:")
         {
            $value        = "";
            $labelinput   = "<label for=\"".$idinput."\">Focus on this textbox to update content</label>";
            $formula      = substr($elem[1], 8);
            $setting      = variable_output_formula($formula);
            $classformula = "class=\"".$setting['class']."\" formula=\"".$elem[1]."\"";
            $script       = "<script>".$setting['callsetvar'].$setting['callfill']."</script>";
            $event        = "onfocus=\"setvar".$elem[0]."();fill".$elem[0]."()\"";
            $readonly     = "readonly";
         }
         else
         {
            $value        = "value=\"".$elem[1]."\"";
            $labelinput   = "";
            $classformula = "";
            $script       = "";
            $event        = "onkeyup=\"setvar".$elem[0]."();fill".$elem[0]."()\"";
            $readonly     = "";
         }
      }
      return $labelinput."<input type=\"text\" autocomplete=\"off\" id=\"".$idinput."\" ".$event." ".$classformula." ".$value." ".$readonly."/><script>var ".$elem[0].";function setvar".$elem[0]."(){".$elem[0]."=+document.getElementById('".$idinput."').value;if(isNaN(".$elem[0].")){".$elem[0]."=document.getElementById('".$idinput."').value;}}function fill".$elem[0]."(){domobj=document.getElementsByClassName('o".$elem[0]."');for(var i=0,max=domobj.length;i<max;i++){if(domobj[i].tagName.toLowerCase()=='span'){while(domobj[i].firstChild){domobj[i].removeChild(domobj[i].firstChild);}domobj[i].appendChild(document.createTextNode(eval(domobj[i].getAttribute('formula'))));}else{domobj[i].value=eval(domobj[i].getAttribute('formula'));}}}</script>".$script;
    case "variableoutput":
      $elem       = explode("|||", $label);
      $enclose    = (count($elem) == 1) ? "" : $elem[1];
      $gtlt       = ["gt;"=>"lt;", "lt;"=>"gt;"];
      $encloseend = strtr(strtr($enclose, "[]{}()/\\", "][}{)(\\/"), $gtlt);
      $formula = trim(html_entity_decode(stripslashes($elem[0])));
      $setting = variable_output_formula($formula);
      return $enclose."<span class='".$setting['class']."' formula=\"".str_replace("\.", "+'.'+", $formula)."\"></span>".$encloseend."<script>".$setting['callsetvar'].$setting['callfill']."</script>";
    case "spreadsheet_to_html":
      return spreadsheetToHTML();
    //inspired by plugin WP JQuery Datatable
    case "datatable":
      if (!$isdatatableloaded)
      {
         wp_enqueue_style( 'currentpost-datatables-css', 'https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css', array('jquery') );
         wp_enqueue_script( 'currentpost-datatables-js', 'https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js', array('jquery') );
         $isdatatableloaded=true;
      }
      echo "<script type='text/javascript' language='javascript'>jQuery(document).ready(function() { jQuery('#".$label."').DataTable({ 'info': true, 'paging': true, 'pageLength': 100, 'pagingType': 'simple', 'bLengthChange': true, 'ordering': false, 'order': [0,'desc'], 'searching': true, } ); jQuery('#".$label."_wrapper select').prepend('<option value=100>Pilih</option>').val('');} );</script>";
    default: 
      return "";
  }
}
add_shortcode ('currentpost', 'get_current_post');

function variable_output_formula ($formula)
{
   $class      = "";
   $callsetvar = "";
   $callfill   = "";
   $allclass   = preg_replace("/&gt;|&lt;|\\.|(\"|')[^(\"|')]+(\"|')|(\.|\w|\s)+\(|[^a-zA-Z_\.]/", ' ', $formula);
   $oc         = explode(" ", $allclass);
   $ocdistinct = [];
   foreach ($oc as $voc)
   {
      if (trim($voc) != "")
      {
         $ocdistinct[$voc] = $voc;
      }
   }
   foreach ($ocdistinct as $voc)
   {
      $class      .= " o".$voc;
      $callsetvar .= "setvar".$voc."();";
      $callfill    = "fill".$voc."();";
   }
   return ["class" => trim($class), "callsetvar" => $callsetvar, "callfill" => $callfill];
}

function from_left ($str, $length) 
{ 
    return substr ($str, $length); 
} 

function from_left_right ($str, $left_length, $right_length) 
{ 
    $left_str = from_left ( $str, $left_length );
    return substr ( $left_str, 0, strlen ( $left_str ) - $right_length );
}

function subtring_exists($substring, $string)
{
    return strpos( trim ( $string ), $substring ) !== false;
}

/*copy of wordpress wp_timezone_string */
function wp_timezone_string_() 
{
    $timezone_string = get_option( 'timezone_string' );
 
    if ( $timezone_string ) {
        return $timezone_string;
    }
 
    $offset  = (float) get_option( 'gmt_offset' );
    $hours   = (int) $offset;
    $minutes = ( $offset - $hours );
 
    $sign      = ( $offset < 0 ) ? '-' : '+';
    $abs_hour  = abs( $hours );
    $abs_mins  = abs( $minutes * 60 );
    $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
 
    return $tz_offset;
}

//https://stackoverflow.com/questions/2095703/php-convert-datetime-to-utc
function to_ISO8601 ( $string_date )
{
 //$string_date example: "2010-01-19 00:00:00"
 $the_date = strtotime($string_date);
 return date('Y-m-d\TH:i:s+00:00', $the_date);;
}

/**
 * source: https://w-shadow.com/blog/2009/10/20/how-to-extract-html-tags-and-their-attributes-with-php/
 * extract_tags()
 * Extract specific HTML tags and their attributes from a string.
 *
 * You can either specify one tag, an array of tag names, or a regular expression that matches the tag name(s). 
 * If multiple tags are specified you must also set the $selfclosing parameter and it must be the same for 
 * all specified tags (so you can't extract both normal and self-closing tags in one go).
 * 
 * The function returns a numerically indexed array of extracted tags. Each entry is an associative array
 * with these keys :
 *  tag_name    - the name of the extracted tag, e.g. "a" or "img" . 
 *  offset      - the numberic offset of the first character of the tag within the HTML source.
 *  contents    - the inner HTML of the tag. This is always empty for self-closing tags.
 *  attributes  - a name -> value array of the tag's attributes, or an empty array if the tag has none.
 *  full_tag    - the entire matched tag, e.g. '<a href="http://example.com">example.com</a>'. This key 
 *                will only be present if you set $return_the_entire_tag to true.      
 *
 * @param string $html The HTML code to search for tags.
 * @param string|array $tag The tag(s) to extract.                           
 * @param bool $selfclosing Whether the tag is self-closing or not. Setting it to null will force the script to try and make an educated guess. 
 * @param bool $return_the_entire_tag Return the entire matched tag in 'full_tag' key of the results array.  
 * @param string $charset The character set of the HTML code. Defaults to ISO-8859-1.
 * @param array what the number of tags do you want to get? Array() means all. -- addition by Basit AP
 * @param int minimum length of the content. 0 means no minimum. -- addition by Basit AP
 *
 * @return array An array of extracted tags, or an empty array if no matching tags were found. 
 */
function extract_tags_current_post ( $html, $tag, $selfclosing = null, $return_the_entire_tag = false, $charset = 'ISO-8859-1', $number = array(), $min_length = 0)
{
     
    if ( is_array($tag) )
    {
        $tag = implode('|', $tag);
    }
     
    //If the user didn't specify if $tag is a self-closing tag we try to auto-detect it
    //by checking against a list of known self-closing tags.
    $selfclosing_tags = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta', 'col', 'param' );
    if ( is_null($selfclosing) )
    {
        $selfclosing = in_array( $tag, $selfclosing_tags );
    }
     
    //The regexp is different for normal and self-closing tags because I can't figure out 
    //how to make a sufficiently robust unified one.
    if ( $selfclosing )
    {
        //addition by Basit AP, capture nextpage (page break)
        $tag_pattern = 
            '@((<(?P<tag>'.$tag.')           # <tag
            (?P<attributes>\s[^>]+)?       # attributes, if any
            \s*/?>)                   # /> or just >, being lenient here 
            | (<(?P<next>(?<=<)!--nextpage-->)))
            @xsi';
    } 
    else 
    {
        //addition by Basit AP, capture nextpage (page break)
        $tag_pattern = 
            '@((<(?P<tag>'.$tag.')           # <tag
            (?P<attributes>\s[^>]+)?       # attributes, if any
            \s*>                 # >
            (?P<contents>.*?)         # tag contents
            </(?P=tag)>)               # the closing </tag>
            | (<(?P<next>(?<=<)!--nextpage-->)))
            @xsi';
    }
     
    $attribute_pattern = 
        '@
        (?P<name>\w+)                         # attribute name
        \s*=\s*
        (
            (?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)    # a quoted value
            |                           # or
            (?P<value_unquoted>[^\s"\']+?)(?:\s+|$)           # an unquoted value (terminated by whitespace or EOF) 
        )
        @xsi';
 
    //Find all tags 
    if ( !preg_match_all($tag_pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) )
    {
        //Return an empty array if we didn't find anything
        return array();
    }
     
    $tags = array();
	$filtersize	= count($number);
	$i			= 1;
    foreach ($matches as $match)
    {
         
        //Parse tag attributes, if any
        $attributes = array();
        if ( !empty($match['attributes'][0]) )
        { 
             
            if ( preg_match_all( $attribute_pattern, $match['attributes'][0], $attribute_data, PREG_SET_ORDER ) )
            {
                //Turn the attribute data into a name->value array
                foreach($attribute_data as $attr){
                    if( !empty($attr['value_quoted']) ){
                        $value = $attr['value_quoted'];
                    } else if( !empty($attr['value_unquoted']) ){
                        $value = $attr['value_unquoted'];
                    } else {
                        $value = '';
                    }
                     
                    //Passing the value through html_entity_decode is handy when you want
                    //to extract link URLs or something like that. You might want to remove
                    //or modify this call if it doesn't fit your situation.
                    $value = html_entity_decode( $value, ENT_QUOTES, $charset );
                     
                    $attributes[$attr['name']] = $value;
                }
            }
             
        }
         
        $tag = array(
            'tag_name' => (array_key_exists('next', $match)) ? 'nextpage' : $match['tag'][0], //addition by Basit AP, capture nextpage (page break)
            'offset' => $match[0][1], 
            'contents' => !empty($match['contents'])?$match['contents'][0]:'', //empty for self-closing tags
            'attributes' => $attributes, 
        );
        if ( $return_the_entire_tag )
        {
            $tag['full_tag'] = $match[0][0];    
        }
        
		//addition by Basit AP
		if ($filtersize == 0)
		{
			if ($min_length == 0 || strlen ( plain_text ( $tag['contents'] ) ) >= $min_length)
			{
				$tags[] = $tag;
			}
		}
        elseif ( in_array($i, $number) )
		{
			if ($min_length == 0 || strlen ( plain_text ( $tag['contents'] ) ) >= $min_length)
			{
				$tags[] = $tag;
			}
			if ($filtersize == 1)
			{
				return $tags;
			}
			$filtersize--;
		}
		$i++;
    }
     
    return $tags;
}

function get_all_heading_current_post($id = null)
{
    return extract_tags_current_post ( get_post_field('post_content', $id), 'h\d+', false );
}

//is link shown in address bar same (or part of) with post permalink
function isadbar ()
{
    return subtring_exists(untrailingslashit ( get_permalink() ), untrailingslashit ( permalink_address_bar() ));
}

/*
if isneighbour is true then $label become link to a post
*/
function generate_toc_current_post($label = "", $isanchor = true, $neighbourmode = "")
{
    $isneighbour = $neighbourmode != "";
    if ($isneighbour)
    {
        $url   = trim( $neighbourmode == "by_id" ? get_permalink( $label ) : $label );
        $pid   = $neighbourmode == "by_id" ? $label : url_to_postid( $label );
        $nodes = get_all_heading_current_post($pid);
        //what if there is no heading?
        if (empty($nodes))
        {
            return '<div><a href="' . esc_url( $url ) . '">' . get_post_field("post_title", $pid) . '</a></div>';
        }
        $toc   = '<a href="' . esc_url( $url ) . '">' . get_post_field("post_title", $pid) . '</a>';
        $listy = '';
    }
    else
    {
        //is link shown in address bar same (or part of) with post permalink
        $isadbar    = isadbar ();
        //set url
        $url           = get_permalink();
        //get all heading information of post
        $nodes      = get_all_heading_current_post();
        //what if there is no heading?
        if (empty($nodes))
        {
            return '';
        }
        $label = ($label != "") ? $label : "Contents";
        $toc   = '<div ' . (($isanchor)?'id="toc_current_post"':'') . ' style="background: #f9f9f9 none repeat scroll 0 0;border: 1px solid #aaa;display: table;font-size: 95%;margin-bottom: 1em;padding: 20px;width: auto;">'
                         . '<p style="font-weight: 700;text-align: center;">' . $label . '</p>';
        $listy = 'style="list-style: outside none none !important;margin-left: ' . ( $current_tag_level*4 ) . 'px;"';
    }
    //what is the last tag level? H1 is level 1, H2 is level 2, so on
    $last_tag_level = 0;
    //how many heading found?
    $headingcount   = 0;
    //we need to close ul, but how many?
    $closingul      = 0;
    //page number in pagebreak
    $pagebreaknum    = 0;
    foreach($nodes as $node)
    {
        if ($node['tag_name'] == "nextpage")
        {
            $pagebreaknum++;
        }
        else
        {
            if ($node['attributes']['id'] != "exclude_toc")
            {	
                //current level of heading
                $current_tag_level = substr ($node['tag_name'], 1);
                //test tag level, then give ul opening or closing
                if ($current_tag_level > $last_tag_level)
                {
                    $closingul++;
                    $toc .= '<ul ' . ($isneighbour ? '' : 'style="list-style: outside none none !important;margin-left: ' . ( $current_tag_level*4 ) . 'px;"') . '>';
                }
                elseif ($current_tag_level < $last_tag_level)
                {
                    $closingnum = $last_tag_level - $current_tag_level;
                    $closingul -= $closingnum;
                    $toc .= str_repeat('</ul>', $closingnum);
                }
                //print heading (optional: print link to anchor if any and give page number from page break effect)
                if (array_key_exists("id", $node['attributes']))
                    $toc .= '<li ' . $listy . '><a href="' . esc_url( $url ) . ($pagebreaknum > 0 ? ($pagebreaknum + 1)."/" : "") . '#' . $node['attributes']['id'] . '" />' . $node['contents']. '</a></li>';
                else
                    $toc .= '<li ' . $listy . '>' . $node['contents'] . '</li>';
                //mark last level of heading
                $last_tag_level = $current_tag_level;
                $headingcount++;
            }
        }
    }
    for ($i = 0; $i < $closingul; $i++)
    {
        $toc .= '</ul>';
    }
    //we don't need link to anchor if URL of address bar different from permalink (smart link to TOC)
    $toc .= ( ($isneighbour) ? '' : '</div>' )
            . ( ($isanchor && $isadbar) ? create_back_to_toc_current_post($label) : '' ); 
    return $toc;
}

function create_back_to_toc_current_post($label)
{
    return '<div style="display: flex;flex-direction: column;justify-content: flex-end;width: 100%;height: 1px;"><div style="position: fixed;bottom: 69px;right: 10px;z-index: 200;border-radius: 3px 0 0;background-clip: padding-box;opacity: 0.5;"><a href="#toc_current_post" title="Back to ' . $label . '"><u>&equiv;</u></a></div></div>';
}

function permalink_address_bar()
{
    global $wp;
    return esc_url ( home_url( add_query_arg( array(), $wp->request ) ) );
}

function get_post_featured_image ()
{
	$thumbid = get_post_meta( get_the_ID (), '_thumbnail_id', true);
	if ( $thumbid != '' )
	{
  return get_post_field ('guid', $thumbid);
 }
	return '';
}

//based on https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/#comment-330
function get_post_first_uploaded_image ( ) 
{
	$post_id = get_the_ID ();
 $args = [
          'posts_per_page' => 1,
          'order'          => 'ASC',
          'post_mime_type' => 'image',
          'post_parent'    => $post_id,
          'post_status'    => null,
          'post_type'      => 'attachment',
         ];
 $attachments = get_children( $args );
 if ( is_array ( $attachments ) ) 
	{
		foreach ( $attachments as $attachment )
			return $attachment->guid;
	}
	else 
	{
		return '';
	}
}

function get_post_first_image ( $post_content = '')
{
	if ( $post_content == '' )
	{
		$post_content = get_post_field ( 'post_content' );
	}
	$firstimage = extract_tags_current_post ( $post_content , 'img', true, false, 'ISO-8859-1', [ 1 ] );
	return ( is_array ($firstimage) ) ? $firstimage[0]['attributes']['src'] : '';
}

function get_post_thumbnail ()
{
	$imagesrc = get_post_featured_image ();
	if ( $imagesrc == '' )
	{
		$imagesrc = get_post_first_image ();
	}
	if ( $imagesrc == '' )
	{
		$imagesrc = get_post_first_uploaded_image ();
	}
	return $imagesrc;
}

//indonesian stopword: https://github.com/masdevid/ID-Stopwords/blob/master/id.stopwords.02.01.2016.txt
//english stopword: https://www.shoutmeloud.com/seo-stop-words
function array_remove_stopword( $str )
{
	$stopword = [ "a", "able", "about", "above", "abroad", "according", "accordingly", "across", "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain’t", "all", "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren’t", "around", "as", "a’s", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind", "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both", "brief", "but", "by", "came", "can", "cannot", "cant", "can’t", "caption", "cause", "causes", "certain", "certainly", "changes", "clearly", "c’mon", "co", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn’t", "course", "c’s", "currently", "dare", "daren’t", "definitely", "described", "despite", "did", "didn’t", "different", "directly", "do", "does", "doesn’t", "doing", "done", "don’t", "down", "downwards", "during", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely", "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "had", "hadn’t", "half", "happens", "hardly", "has", "hasn’t", "have", "haven’t", "having", "he", "he’d", "he’ll", "hello", "help", "hence", "her", "here", "hereafter", "hereby", "herein", "here’s", "hereupon", "hers", "herself", "he’s", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred", "i’d", "ie", "if", "ignored", "i’ll", "i’m", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn’t", "it", "it’d", "it’ll", "its", "it’s", "itself", "i’ve", "just", "keep", "keeps", "kept", "know", "known", "knows", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let’s", "like", "liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn’t", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn’t", "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must", "mustn’t", "my", "myself", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn’t", "needs", "neither", "never", "neverf", "neverless", "nevertheless", "new", "next", "nine", "ninety", "no", "nobody", "non", "none", "nonetheless", "noone", "no-one", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "ones", "one’s", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn’t", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provided", "provides", "que", "quite", "qv", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding", "regardless", "regards", "relatively", "respectively", "right", "round", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "shan’t", "she", "she’d", "she’ll", "she’s", "should", "shouldn’t", "since", "six", "so", "some", "somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that’ll", "thats", "that’s", "that’ve", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "there’d", "therefore", "therein", "there’ll", "there’re", "theres", "there’s", "thereupon", "there’ve", "these", "they", "they’d", "they’ll", "they’re", "they’ve", "thing", "things", "think", "third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "t’s", "twice", "two", "un", "under", "underneath", "undoing", "unfortunately", "unless", "unlike", "unlikely", "until", "unto", "up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually", "value", "various", "versus", "very", "via", "viz", "vs", "want", "wants", "was", "wasn’t", "way", "we", "we’d", "welcome", "well", "we’ll", "went", "were", "we’re", "weren’t", "we’ve", "what", "whatever", "what’ll", "what’s", "what’ve", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "where’s", "whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who", "who’d", "whoever", "whole", "who’ll", "whom", "whomever", "who’s", "whose", "why", "will", "willing", "wish", "with", "within", "without", "wonder", "won’t", "would", "wouldn’t", "yes", "yet", "you", "you’d", "you’ll", "your", "you’re", "yours", "yourself", "yourselves", "you’ve", "zero", "ada", "adalah", "adanya", "adapun", "agak", "agaknya", "agar", "akan", "akankah", "akhir", "akhiri", "akhirnya", "aku", "akulah", "amat", "amatlah", "anda", "andalah", "antar", "antara", "antaranya", "apa", "apaan", "apabila", "apakah", "apalagi", "apatah", "artinya", "asal", "asalkan", "atas", "atau", "ataukah", "ataupun", "awal", "awalnya", "bagai", "bagaikan", "bagaimana", "bagaimanakah", "bagaimanapun", "bagi", "bagian", "bahkan", "bahwa", "bahwasanya", "baik", "bakal", "bakalan", "balik", "banyak", "bapak", "baru", "bawah", "beberapa", "begini", "beginian", "beginikah", "beginilah", "begitu", "begitukah", "begitulah", "begitupun", "bekerja", "belakang", "belakangan", "belum", "belumlah", "benar", "benarkah", "benarlah", "berada", "berakhir", "berakhirlah", "berakhirnya", "berapa", "berapakah", "berapalah", "berapapun", "berarti", "berawal", "berbagai", "berdatangan", "beri", "berikan", "berikut", "berikutnya", "berjumlah", "berkali-kali", "berkata", "berkehendak", "berkeinginan", "berkenaan", "berlainan", "berlalu", "berlangsung", "berlebihan", "bermacam", "bermacam-macam", "bermaksud", "bermula", "bersama", "bersama-sama", "bersiap", "bersiap-siap", "bertanya", "bertanya-tanya", "berturut", "berturut-turut", "bertutur", "berujar", "berupa", "besar", "betul", "betulkah", "biasa", "biasanya", "bila", "bilakah", "bisa", "bisakah", "boleh", "bolehkah", "bolehlah", "buat", "bukan", "bukankah", "bukanlah", "bukannya", "bulan", "bung", "cara", "caranya", "cukup", "cukupkah", "cukuplah", "cuma", "dahulu", "dalam", "dan", "dapat", "dari", "daripada", "datang", "dekat", "demi", "demikian", "demikianlah", "dengan", "depan", "di", "dia", "diakhiri", "diakhirinya", "dialah", "diantara", "diantaranya", "diberi", "diberikan", "diberikannya", "dibuat", "dibuatnya", "didapat", "didatangkan", "digunakan", "diibaratkan", "diibaratkannya", "diingat", "diingatkan", "diinginkan", "dijawab", "dijelaskan", "dijelaskannya", "dikarenakan", "dikatakan", "dikatakannya", "dikerjakan", "diketahui", "diketahuinya", "dikira", "dilakukan", "dilalui", "dilihat", "dimaksud", "dimaksudkan", "dimaksudkannya", "dimaksudnya", "diminta", "dimintai", "dimisalkan", "dimulai", "dimulailah", "dimulainya", "dimungkinkan", "dini", "dipastikan", "diperbuat", "diperbuatnya", "dipergunakan", "diperkirakan", "diperlihatkan", "diperlukan", "diperlukannya", "dipersoalkan", "dipertanyakan", "dipunyai", "diri", "dirinya", "disampaikan", "disebut", "disebutkan", "disebutkannya", "disini", "disinilah", "ditambahkan", "ditandaskan", "ditanya", "ditanyai", "ditanyakan", "ditegaskan", "ditujukan", "ditunjuk", "ditunjuki", "ditunjukkan", "ditunjukkannya", "ditunjuknya", "dituturkan", "dituturkannya", "diucapkan", "diucapkannya", "diungkapkan", "dong", "dua", "dulu", "empat", "enggak", "enggaknya", "entah", "entahlah", "guna", "gunakan", "hal", "hampir", "hanya", "hanyalah", "hari", "harus", "haruslah", "harusnya", "hendak", "hendaklah", "hendaknya", "hingga", "ia", "ialah", "ibarat", "ibaratkan", "ibaratnya", "ibu", "ikut", "ingat", "ingat-ingat", "ingin", "inginkah", "inginkan", "ini", "inikah", "inilah", "itu", "itukah", "itulah", "jadi", "jadilah", "jadinya", "jangan", "jangankan", "janganlah", "jauh", "jawab", "jawaban", "jawabnya", "jelas", "jelaskan", "jelaslah", "jelasnya", "jika", "jikalau", "juga", "jumlah", "jumlahnya", "justru", "kala", "kalau", "kalaulah", "kalaupun", "kalian", "kami", "kamilah", "kamu", "kamulah", "kan", "kapan", "kapankah", "kapanpun", "karena", "karenanya", "kasus", "kata", "katakan", "katakanlah", "katanya", "ke", "keadaan", "kebetulan", "kecil", "kedua", "keduanya", "keinginan", "kelamaan", "kelihatan", "kelihatannya", "kelima", "keluar", "kembali", "kemudian", "kemungkinan", "kemungkinannya", "kenapa", "kepada", "kepadanya", "kesampaian", "keseluruhan", "keseluruhannya", "keterlaluan", "ketika", "khususnya", "kini", "kinilah", "kira", "kira-kira", "kiranya", "kita", "kitalah", "kok", "kurang", "lagi", "lagian", "lah", "lain", "lainnya", "lalu", "lama", "lamanya", "lanjut", "lanjutnya", "lebih", "lewat", "lima", "luar", "macam", "maka", "makanya", "makin", "malah", "malahan", "mampu", "mampukah", "mana", "manakala", "manalagi", "masa", "masalah", "masalahnya", "masih", "masihkah", "masing", "masing-masing", "mau", "maupun", "melainkan", "melakukan", "melalui", "melihat", "melihatnya", "memang", "memastikan", "memberi", "memberikan", "membuat", "memerlukan", "memihak", "meminta", "memintakan", "memisalkan", "memperbuat", "mempergunakan", "memperkirakan", "memperlihatkan", "mempersiapkan", "mempersoalkan", "mempertanyakan", "mempunyai", "memulai", "memungkinkan", "menaiki", "menambahkan", "menandaskan", "menanti", "menanti-nanti", "menantikan", "menanya", "menanyai", "menanyakan", "mendapat", "mendapatkan", "mendatang", "mendatangi", "mendatangkan", "menegaskan", "mengakhiri", "mengapa", "mengatakan", "mengatakannya", "mengenai", "mengerjakan", "mengetahui", "menggunakan", "menghendaki", "mengibaratkan", "mengibaratkannya", "mengingat", "mengingatkan", "menginginkan", "mengira", "mengucapkan", "mengucapkannya", "mengungkapkan", "menjadi", "menjawab", "menjelaskan", "menuju", "menunjuk", "menunjuki", "menunjukkan", "menunjuknya", "menurut", "menuturkan", "menyampaikan", "menyangkut", "menyatakan", "menyebutkan", "menyeluruh", "menyiapkan", "merasa", "mereka", "merekalah", "merupakan", "meski", "meskipun", "meyakini", "meyakinkan", "minta", "mirip", "misal", "misalkan", "misalnya", "mula", "mulai", "mulailah", "mulanya", "mungkin", "mungkinkah", "nah", "naik", "namun", "nanti", "nantinya", "nyaris", "nyatanya", "oleh", "olehnya", "pada", "padahal", "padanya", "pak", "paling", "panjang", "pantas", "para", "pasti", "pastilah", "penting", "pentingnya", "per", "percuma", "perlu", "perlukah", "perlunya", "pernah", "persoalan", "pertama", "pertama-tama", "pertanyaan", "pertanyakan", "pihak", "pihaknya", "pukul", "pula", "pun", "punya", "rasa", "rasanya", "rata", "rupanya", "saat", "saatnya", "saja", "sajalah", "saling", "sama", "sama-sama", "sambil", "sampai", "sampai-sampai", "sampaikan", "sana", "sangat", "sangatlah", "satu", "saya", "sayalah", "se", "sebab", "sebabnya", "sebagai", "sebagaimana", "sebagainya", "sebagian", "sebaik", "sebaik-baiknya", "sebaiknya", "sebaliknya", "sebanyak", "sebegini", "sebegitu", "sebelum", "sebelumnya", "sebenarnya", "seberapa", "sebesar", "sebetulnya", "sebisanya", "sebuah", "sebut", "sebutlah", "sebutnya", "secara", "secukupnya", "sedang", "sedangkan", "sedemikian", "sedikit", "sedikitnya", "seenaknya", "segala", "segalanya", "segera", "seharusnya", "sehingga", "seingat", "sejak", "sejauh", "sejenak", "sejumlah", "sekadar", "sekadarnya", "sekali", "sekali-kali", "sekalian", "sekaligus", "sekalipun", "sekarang", "sekarang", "sekecil", "seketika", "sekiranya", "sekitar", "sekitarnya", "sekurang-kurangnya", "sekurangnya", "sela", "selain", "selaku", "selalu", "selama", "selama-lamanya", "selamanya", "selanjutnya", "seluruh", "seluruhnya", "semacam", "semakin", "semampu", "semampunya", "semasa", "semasih", "semata", "semata-mata", "semaunya", "sementara", "semisal", "semisalnya", "sempat", "semua", "semuanya", "semula", "sendiri", "sendirian", "sendirinya", "seolah", "seolah-olah", "seorang", "sepanjang", "sepantasnya", "sepantasnyalah", "seperlunya", "seperti", "sepertinya", "sepihak", "sering", "seringnya", "serta", "serupa", "sesaat", "sesama", "sesampai", "sesegera", "sesekali", "seseorang", "sesuatu", "sesuatunya", "sesudah", "sesudahnya", "setelah", "setempat", "setengah", "seterusnya", "setiap", "setiba", "setibanya", "setidak-tidaknya", "setidaknya", "setinggi", "seusai", "sewaktu", "siap", "siapa", "siapakah", "siapapun", "sini", "sinilah", "soal", "soalnya", "suatu", "sudah", "sudahkah", "sudahlah", "supaya", "tadi", "tadinya", "tahu", "tahun", "tak", "tambah", "tambahnya", "tampak", "tampaknya", "tandas", "tandasnya", "tanpa", "tanya", "tanyakan", "tanyanya", "tapi", "tegas", "tegasnya", "telah", "tempat", "tengah", "tentang", "tentu", "tentulah", "tentunya", "tepat", "terakhir", "terasa", "terbanyak", "terdahulu", "terdapat", "terdiri", "terhadap", "terhadapnya", "teringat", "teringat-ingat", "terjadi", "terjadilah", "terjadinya", "terkira", "terlalu", "terlebih", "terlihat", "termasuk", "ternyata", "tersampaikan", "tersebut", "tersebutlah", "tertentu", "tertuju", "terus", "terutama", "tetap", "tetapi", "tiap", "tiba", "tiba-tiba", "tidak", "tidakkah", "tidaklah", "tiga", "tinggi", "toh", "tunjuk", "turut", "tutur", "tuturnya", "ucap", "ucapnya", "ujar", "ujarnya", "umum", "umumnya", "ungkap", "ungkapnya", "untuk", "usah", "usai", "waduh", "wah", "wahai", "waktu", "waktunya", "walau", "walaupun", "wong", "yaitu", "yakin", "yakni", "yang" ];
	return array_diff ( $str, $stopword );
}

//https://stackoverflow.com/questions/19948660/how-to-replace-everything-between-braces-from-a-string
function remove_between_bracket ( $string )
{
	return preg_replace ( '/[\[{].*?[\]}]/' , '', $string );
}

function keyword_single_string ( $string )
{
	return trim ( implode( ", ", array_unique ( array_remove_stopword ( preg_split('/[\s!"#$%&()*+,\-.\/:;<=>?@\[\]^_`{|}~]+/', strtolower ( plain_text ( remove_between_bracket ( $string ) ) ) ) ) ) ) );
}

function keyword_intersect_two_string ( $string1, $string2 )
{
	return trim ( implode( ", ", array_unique ( array_remove_stopword ( array_intersect ( preg_split('/[\s!"#$%&()*+,\-.\/:;<=>?@\[\]^_`{|}~]+/', strtolower ( plain_text ( remove_between_bracket ( $string1 ) ) ) ), preg_split('/[\s!"#$%&()*+,\-.\/:;<=>?@\[\]^_`{|}~]+/', strtolower ( plain_text ( remove_between_bracket ( $string2 ) ) ) ) ) ) ) ) );
}

function plain_text( $sourcetext )
{
	return html_entity_decode ( strip_shortcodes ( str_replace(["&ldquo;", "&rdquo;", "&lsquo;", "&rsquo;"], "'",  htmlentities( wp_strip_all_tags ( $sourcetext ) ) ) ) );
}

function hook_meta() 
{
 $blog_url  = get_bloginfo ( "url" );
 $is_index  = untrailingslashit ( permalink_address_bar() ) == untrailingslashit ( $blog_url );
 if ( isadbar () || $is_index )
 {
  if ( !$is_index )
  {
	  $post_content    = get_post_field( 'post_content' );
	  if ( !is_gutenberg($post_content) )
	  {
	   $post_content    = wpautop ( $post_content );
	  }
	  $title_component = get_post_field ( 'post_title' );
	  //first priority is 3 first paragraph (>=50 characters length)
	  $blockquote      = extract_tags_current_post ( $post_content , 'p', false, false, 'ISO-8859-1', [ 1, 2, 3 ], 50 );
	  //then first block quote  (>=50 characters length)
	  if ( count ($blockquote) == 0 )
	  {
	   $blockquote = extract_tags_current_post ( $post_content, 'blockquote', false, false, 'ISO-8859-1', [ 1, 2, 3 ], 50 );
	  }
	  //then no description
	  if ( count ($blockquote) == 0 )
	  {
	   $blockquote[0]['contents'] = "";
	  }
	  $blockquote_component	=  $blockquote[0]['contents'];
	  $keyword                = keyword_intersect_two_string ( $title_component, $blockquote_component );
	  if ( $blockquote[0]['contents'] != "" )
   {
    $blog_name = get_bloginfo ( "name" );
    $blog_desc = get_bloginfo ( "description" );
    $post_url  = get_permalink();
    $post_date = to_ISO8601 ( get_post_field ( "post_date" ) );
    $post_modi = to_ISO8601 ( get_post_field ( "post_modified" ) );
    $image_url = get_post_thumbnail();
    $author    = get_userdata ( get_post_field( 'post_author' ) );
    $auth_nick = $author->nickname;
    $auth_name = $author->display_name;
    echo '<meta name="description" content="' . plain_text ( $blockquote[0]['contents'] ) .'"/>';
    //it's for post improvement purpose
    echo '<meta name="keywords" content="' . $keyword . '"/>';
    //open graph
    echo '<meta name="og:type" content="article"/>';
    echo '<meta name="article:published_time" content="' . $post_date . '"/>';
    echo '<meta name="article:modified_time" content="' . $post_modi . '"/>';
    echo '<meta name="og:title" content="' . $title_component . '"/>';
    echo '<meta name="og:description" content="' . plain_text ( $blockquote[0]['contents'] ) . '"/>';
    echo '<meta name="og:url" content="' . $post_url . '"/>';
    if ( $image_url != "" )
    {
     echo '<meta name="og:image" content="' . $image_url . '"/>';
    }
    echo '<meta name="og:site_name" content="' . $blog_name . '"/>';
    //json-ld
    echo '<script type="application/ld+json" class="currentpost-schema-graph">{"@context":"https://schema.org","@graph":[{"@type":"WebSite","@id":"' . $blog_url . '/#website","url":"' . $blog_url.'","name":"' . $blog_name . '/","description":"' . $blog_desc .'","potentialAction":[{"@type":"SearchAction","target":"' . $blog_url . '/?s={search_term_string}","query-input":"required name=search_term_string"}]},{"@type":"ImageObject","@id":"' . $post_url . '#primaryimage","url":"' . $image_url . '"},{"@type":"WebPage","@id":"' . $post_url . '#webpage","url":"' . $post_url . '","name":"' . $title_component . '","isPartOf":{"@id":"' . $blog_url . '/#website"},"primaryImageOfPage":{"@id":"' . $post_url . '#primaryimage"},"datePublished":"' . $post_date . '","dateModified":"' . $post_modi . '","author":{"@id":"' . $blog_url . '/#' . $auth_nick . '"},"potentialAction":[{"@type":"ReadAction","target":["' . $post_url . '"]}]},{"@type":"Person","@id":"' . $blog_url . '/#' . $auth_id. $auth_nick . '","name":"' . $auth_name . '"}]}</script>';
	  }
  }
  else 
  {
	  $blockquote[0]['contents'] = get_bloginfo ( "description" );
	  $title_component           = get_bloginfo ( "name" );
	  $keyword                   = "index";
	  $blog_name = get_bloginfo ( "name" );
	  $blog_desc = get_bloginfo ( "description" );
	  $post_url  = $blog_url;
	  echo '<meta name="description" content="' . plain_text ( $blockquote[0]['contents'] ) .'"/>';
	  //it's for post improvement purpose
	  echo '<meta name="keywords" content="' . $keyword . '"/>';
	  //open graph
	  echo '<meta name="og:type" content="article"/>';
	  echo '<meta name="og:title" content="' . $title_component . '"/>';
	  echo '<meta name="og:description" content="' . plain_text ( $blockquote[0]['contents'] ) . '"/>';
	  echo '<meta name="og:url" content="' . $post_url . '"/>';
	  echo '<meta name="og:site_name" content="' . $blog_name . '"/>';
	  //json-ld
   echo '<script type="application/ld+json" class="currentpost-schema-graph">{"@context":"https://schema.org","@graph":[{"@type":"WebSite","@id":"' . $blog_url . '/#website","url":"' . $blog_url.'","name":"' . $blog_name . '/","description":"' . $blog_desc .'","potentialAction":[{"@type":"SearchAction","target":"' . $blog_url . '/?s={search_term_string}","query-input":"required name=search_term_string"}]},{"@type":"WebPage","@id":"' . $post_url . '#webpage","url":"' . $post_url . '","name":"' . $title_component . '","isPartOf":{"@id":"' . $blog_url . '/#website"},"potentialAction":[{"@type":"ReadAction","target":["' . $post_url . '"]}]}]}</script>';
  }
 }
}
add_action('wp_head', 'hook_meta');

//https://wordpress.org/support/topic/check-if-page-is-using-gutenberg-or-if-content-is-in-a-block/
function is_gutenberg($content) 
{
    return false !== strpos( $content, '<!-- wp:' );
}

//inspired from https://www.tableizer.journalistopia.com
function spreadsheetToHTML()
{
	return "
 <div>
 <p>Paste Your Table from Spreadsheet Here<br/><span style='font-size:smaller'>work well with Wordpress <a href='https://wordpress.org/plugins/current-post/' target='blank_'>Currentpost</a> plugin</span><br/>
 <textarea title='Paste Your Table from Spreadsheet Here' id='table-holder' rows='10' style='min-width:400px'></textarea><br/>
 <button id='btnProceed' onClick='toHtml()'>Convert To HTML!</button><button  id='btnReset' style='visibility:hidden' onClick='btnReset.style.visibility=\"hidden\";btnProceed.style.visibility=\"visible\";clearHtml()'>Reset</button></p>
 </div>
 <script type='text/javascript'>
  function clearHtml()
  {
     document.getElementById('table-holder').value='';
  }
  function toHtml()
  {
     //get value from table-holder
     var str=document.getElementById('table-holder').value;
     //split by row
     var arrstr=str.split('\\n');
     if (arrstr.length >= 2)
     {
      document.getElementById('btnProceed').style.visibility = 'hidden';
      //get first row as thead
      var thead='<thead><tr><th>'+arrstr[0].replace(/\\t/g, '</th><th>')+'</th></tr></thead>';
      //exclude first row from tbody
      arrstr=arrstr.splice(1);
      //proceed tbody and finalizing
      arrstr.forEach(tbody);
      document.getElementById('table-holder').value='[currentpost type=\"datatable\" label=\"table_data\"]<table id=\'table_data\'>'+thead+'<tbody>'+arrstr.join('').toString()+'</tbody></table>';
      copyTextToClipboard(document.getElementById('table-holder').value);
      document.getElementById('btnReset').style.visibility = 'visible';
     }
     else
     {
      alert('Sorry, please provide at least two rows');
     }
  }
  function tbody(item, index, arr) {
     arr[index] = (item != '')?'<tr><td>'+item.replace(/\\t/g, '</td><td>')+'</td></tr>':'';
  } 
  async function copyTextToClipboard(text) {
     try 
     {
       await navigator.clipboard.writeText(text);
       alert('Text copied to clipboard');
     } 
     catch(err) 
     {
       alert('Error in copying text: ', err);
     }
  }
 </script>";
}

//override the_time, for hijri calendar (Bahasa Indonesia Only)
/*add_filter('the_time','custom_the_time');

function custom_the_time($format = '') { 
if (get_option( 'date_format' ) == "Y-m-d-#")
{
	    $d = explode("-", $format);
	    echo get_hijriah($d[0], $d[1], $d[2]);
}
elseif (get_option( 'date_format' ) == "Y-m-d-$")
{
     $d = explode("-", $format);
	    echo get_hijriah($d[0], $d[1], $d[2], true);
}
else
     echo apply_filters( 'custom_the_time', get_the_time( $format ), $format );
}*/

//override get_the_time, for hijri calendar (Bahasa Indonesia Only)
add_filter('get_the_time', 'custom_get_the_time');

function custom_get_the_time( $format = '', $post = null ) {
   $post = get_post( $post );

   if ( ! $post ) {
       return false;
   }

   /**
    * Filters the time a post was written.
    *
    * @since 1.5.0
    *
    * @param string      $the_time The formatted time.
    * @param string      $format   Format to use for retrieving the time the post
    *                              was written. Accepts 'G', 'U', or PHP date format.
    * @param int|WP_Post $post     WP_Post object or ID.
    */
    $date_status = datetimestatus($format);
    if (!($date_status == CPost_DT_TIME || $date_status == CPost_DT_NOTDATE) && get_option( 'date_format' ) == "Y-m-d-4")
    {
        $the_time = get_post_time( $format, false, $post, true );
        $d = explode("-", $the_time);
        return " ".get_hijriah($d[0], $d[1], $d[2]);
    }
    elseif (!($date_status == CPost_DT_TIME || $date_status == CPost_DT_NOTDATE) && get_option( 'date_format' ) == "Y-m-d-5")
    {
        $the_time = get_post_time( $format, false, $post, true );
        $d = explode("-", $the_time);
        return " ".get_hijriah($d[0], $d[1], $d[2], true);
    }
    else    
    {
        $_format = ! empty( $format ) ? $format : get_option( 'time_format' );
        $the_time = ($date_status != CPost_DT_TIME) ? get_post_time( $_format, false, $post, true ) : $_format;
        return apply_filters( 'custom_get_the_time', $the_time, $format, $post );
    }
}

function get_hijriah($year, $month, $date, $short=false)
{
	//$response = file_get_contents("https://service.unisayogya.ac.id/kalender/api/masehi2hijriah/muhammadiyah/".$year."/".$month."/".$date);
 $url = "https://service.unisayogya.ac.id/kalender/api/masehi2hijriah/muhammadiyah/".$year."/".$month."/".$date;
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
 curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, [
	  'Content-Type: application/json'
	]);
	$response = curl_exec($curl);
	curl_close($curl);
	if ($response) 
	{
    	$date = json_decode($response, true);
     $mode = "terbilang".($short?"simpel":"");
     return ((array_key_exists($mode, $date)) ? $date[$mode] : date_($year, $month, $date));
	}
	else
	{
		   return date_($year, $month, $date);
	}
}

function date_($year, $month, $date)
{
	$date=date_create();
	date_date_set($date, $year, $month, $date);
	return date_format($date,"Y-m-d");
}

//based on https://stackoverflow.com/questions/11029769/function-to-check-if-a-string-is-a-date
function datetimestatus($myDateString)
{
	if (DateTime::createFromFormat('Y/m/dh:ia', $myDateString) !== false || DateTime::createFromFormat(get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $myDateString) !== false) 
	{
  		return CPost_DT_DATETIME;
	}
	elseif (DateTime::createFromFormat('Y/m/d', $myDateString) !== false || DateTime::createFromFormat(get_option( 'date_format' ), $myDateString) !== false) 
	{
  		return CPost_DT_DATE;
	}
	elseif (DateTime::createFromFormat('h:ia', $myDateString) !== false || DateTime::createFromFormat(get_option( 'time_format' ), $myDateString) !== false) 
	{
  		return CPost_DT_TIME;
	}
	else 
	{
		return CPost_DT_NOTDATE;
	}
}

/* Introduction
add_action 1.2.0
add_filter 0.71
add_menu_page 1.5.0
add_query_arg 1.5.0
add_shortcode 2.5.0
apply_filters 0.71
blog_info 0.71
esc_attr 2.8.0
esc_url 2.8.0
get_author_posts_url 2.1.0
get_children 2.0.0
get_permalink 1.0.0
get_post_field 2.3.0
get_post_meta 1.5.0
get_the_author 1.5.0
get_the_author_meta 2.8.0
get_the_time 1.5.0
get_userdata 0.71
home_url 3.0.0
strip_shortcodes 2.5.0
the_time 0.71
untrailingslashit 2.2.0
wpautop 0.71
wp_register_script 2.1.0
wp_register_style 2.1.0
wp_strip_all_tags 2.9.0
*/