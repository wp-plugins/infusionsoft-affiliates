<?php
/*
Plugin Name: Infusionsoft Affiliates
Plugin URI: http://asandia.com/wordpress-plugins/infusionsoft-affiliates/
Description: Short Codes to insert a given Infusionsoft affiliates' info
Version: 1.5
Author: Jeremy Shapiro
Author URI: http://www.asandia.com/
*/

/*
Infusionsoft Affiliates (Wordpress Plugin)
Copyright (C) 2011 Jeremy Shapiro
*/

//tell wordpress to register the shortcode
add_shortcode("affiliate", "infusionsoftaffiliate_print");

function infusionsoftaffiliate_print($atts, $content) {
  global $infusionsoftaffiliate;

  $atts = shortcode_atts(array(
	'field'		=> '',
	'format'	=> '',
	'dateshift'	=> '',
        'default'        => ''
        ), $atts);

  $val = ($infusionsoftaffiliate[strtolower($atts['field'])]) ? $infusionsoftaffiliate[strtolower($atts['field'])] : $atts['default'];

  if(($atts['dateshift'] != '') && strtotime($val.' '.$atts['dateshift'])) {
    $val = date('Y-m-d H:i:s', strtotime($val.' '.$atts['dateshift']));
  }
  if(($atts['format'] != '') && strtotime($val)) {
    $val = date($atts['format'], strtotime($val));
  }

  return $val;
}


function activate_infusionsoftaffiliates() {
  add_option('infusionsoft_apikey');
  add_option('infusionsoft_appname');
  add_option('affiliate_caching', '60');
  add_option('affiliate_load_param', '1');
  add_option('affiliate_load_root', '1');
  add_option('affiliate_load_cookie', '1');
  add_option('affiliatecode_names', 'code,affcode');
  add_option('affiliate_defaultpage');
  add_option('noaffiliate_defaultpage');
  add_option('affiliates_lastsync');
  add_option('affiliates_lastsync_start');

  # If this is from v0.4 or earlier, time to upgrade to the new option format
  if($legacyload = get_option('affiliate_load'))
  {
     delete_option('affiliate_load');
     update_option('affiliate_load_cookie', ($legacyload == 'cookie') || ($legacyload == 'request'));
     update_option('affiliate_load_param', ($legacyload == 'param') || ($legacyload == 'request'));
  }

   global $wpdb;
   $sql = "CREATE TABLE " . $wpdb->prefix . "infusionsoftaffiliates (
		id int NOT NULL,
		UNIQUE KEY id (id)
	);";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
}

function deactivate_infusionsoftaffiliates() {
  # for now, deactivate shouldn't do anything
}

function uninstall_infusionsoftaffiliates() {
  delete_option('infusionsoft_apikey');
  delete_option('infusionsoft_appname');
  delete_option('affiliate_caching');
  delete_option('affiliate_load');		# Legacy, pre v0.5
  delete_option('affiliate_load_param');
  delete_option('affiliate_load_root');
  delete_option('affiliate_load_cookie');
  delete_option('affiliatecode_names');
  delete_option('affiliate_defaultpage');
  delete_option('noaffiliate_defaultpage');
  delete_option('affiliates_lastsync');
  delete_option('affiliates_lastsync_start');

   global $wpdb;
   $wpdb->query("DROP TABLE ".$wpdb->prefix . "infusionsoftaffiliates;");
}

function admin_init_infusionsoftaffiliates() {
  register_setting('infusionsoftaffiliates', 'infusionsoft_appname');
  register_setting('infusionsoftaffiliates', 'infusionsoft_apikey');
  register_setting('infusionsoftaffiliates', 'affiliate_caching');
  register_setting('infusionsoftaffiliates', 'affiliate_load_param');
  register_setting('infusionsoftaffiliates', 'affiliate_load_root');
  register_setting('infusionsoftaffiliates', 'affiliate_load_cookie');
  register_setting('infusionsoftaffiliates', 'affiliatecode_names');
  register_setting('infusionsoftaffiliates', 'affiliate_defaultpage');
  register_setting('infusionsoftaffiliates', 'noaffiliate_defaultpage');

  add_meta_box("infusionsoftaffiliates-page", "Infusionsoft Affiliates Page Options", "infusionsoftaffiliates_page", "page", "normal", "high");
}

function admin_menu_infusionsoftaffiliates() {
  add_options_page('Infusionsoft Affiliates', 'Infusionsoft Affiliates', 8, 'infusionsoftaffiliates', 'options_page_infusionsoftaffiliates');
}

function options_page_infusionsoftaffiliates() {
  include(dirname(__FILE__).'/options.php');  
}

function infusionsoftaffiliates_page() {
  $nadefaultpage = get_post_meta(get_the_ID(),'noaffiliate_defaultpage_override',true);
  wp_nonce_field('ianonce','_ianonce');
?>

<p>When this page is requested and no affiliate code is found:

<p><input type="radio" name="noaffiliate_defaultpage_override" value="no" <?php echo ($nadefaultpage) ? "":"checked"; ?>
	onChange="document.getElementById('noaffiliate_defaultpage').disabled = this.checked;">
Use the <a href="options-general.php?page=infusionsoftaffiliates">Default Setting</a> of: Show 

<?php
  if($dpageid = get_option('noaffiliate_defaultpage'))
  {
     $dpage = get_post($dpageid);
     print '<a href="'.get_permalink($dpageid).'">'.$dpage->post_title.'</a>';
  } else {
     print "this page.";
  }
?>

<p><input type="radio" name="noaffiliate_defaultpage_override" value="yes" <?php echo ($nadefaultpage) ? "checked":""; ?>
	onChange="document.getElementById('noaffiliate_defaultpage').disabled = !this.checked;">
Override the default and show:
<?php wp_dropdown_pages(array('name' => 'noaffiliate_defaultpage', 'selected' => (($nadefaultpage) ? $nadefaultpage : get_the_ID() ))); ?>
<script language="javascript">document.getElementById('noaffiliate_defaultpage').disabled = <?php echo(($nadefaultpage) ? 'false':'true'); ?>;</script>
<?php

}


function infusionsoftaffiliates_updatemeta($id) {
   $naoverride = $_POST['noaffiliate_defaultpage_override'];

   # do we need to check the nonce, or is that a given?
   if (isset($naoverride) && !empty($naoverride) && wp_verify_nonce($_REQUEST['_ianonce'], 'ianonce'))
   {
     if($naoverride == 'yes')
     {
         update_post_meta($id, 'noaffiliate_defaultpage_override', $_POST['noaffiliate_defaultpage']);
     } else {
         delete_post_meta($id, 'noaffiliate_defaultpage_override');
     }
   }
}


function infusionsoftaffiliates_checkrequest() {
  global $infusionsoftaffiliate, $post;

  # if we a) found a code and b) we already loaded the code OR c) we are now able to load the code...
  if(($code = infusionsoftaffiliates_findcode()) && ($infusionsoftaffiliate || ($infusionsoftaffiliate = infusionsoftaffiliates_load($code))))
  {
	# If we have a valid code, are on the root page, and there's a default page for folks with affiliate codes, redirect!
	if (preg_match('/^\/(\?.*|)$/', $_SERVER['REQUEST_URI']) && get_option('affiliate_defaultpage'))
	{
		$newurl = get_permalink(get_option('affiliate_defaultpage'));
		wp_redirect($newurl);
		infusionsoftaffiliates_setcookie($code);
		exit;
	} else {
		infusionsoftaffiliates_setcookie($code);
	}

  } else if($newpageid = get_post_meta($post->ID, 'noaffiliate_defaultpage_override', true)) {
	$newurl = get_permalink($newpageid);
	$newpath = parse_url($newurl);

	if($newpath['path'] != $_SERVER['REQUEST_URI'])
	{
		wp_redirect($newurl);
		exit;
	}

  } else if(get_option('noaffiliate_defaultpage')) {
	$newurl = get_permalink(get_option('noaffiliate_defaultpage'));

	$newpath = parse_url($newurl);

	if($newpath['path'] != $_SERVER['REQUEST_URI'])
	{
		wp_redirect($newurl);
		exit;
	}
  }
}


function infusionsoftaffiliates_setcookie($code)
{
	$codename = infusionsoftaffiliates_defaultcodename();
	$url = parse_url(site_url());
	setcookie($codename, $code, time()+(3600*24*30), empty($url['path']) ? '/' : $url['path'], $url['host']);
}

function infusionsoftaffiliates_defaultcodename()
{
	if($codes = preg_split('/\,\s*/', get_option('affiliatecode_names')))
	{
		return $codes[0];
	} else {
		return 'affcode';
	}
}       


function infusionsoftaffiliates_findcode()
{
#  Bugger: When Infusionsoft redirects from an affiliate redirect URL (http://appname.infusionsoft.com/go/linkcode/affiliatecode)
#	the headers to this page don't provide any indication as to what page we came from . . . back to the drawing board!
#  if(get_option('affiliate_load_infusionurl') && preg_match('\.infusionsoft\.com/\/go\/([^\/]+)\/([^\/]+)/i', wp_get_referer(), $matches))
#  {
#	return $matches[2];	# $matches[1] is the referring affiliate code FWIW
#  }

  global $post;

  # we only care about checking the root URL if the page doesn't exist. This prevents affiliates from hijacking your site
  # by creating affiliate codes that match existing pages.
  if(!$post && get_option('affiliate_load_root'))
  {
	$urlparts = explode('/', $_SERVER['REQUEST_URI']);
	array_shift($urlparts);
	if($root = array_shift($urlparts))
	{
		global $infusionsoftaffiliate;
		if($infusionsoftaffiliate = infusionsoftaffiliates_load($root))
		{
# Until I can figure out how the heck to unshift the affiliate code from the url and have WordPress go about it's business normally
# parsing the URL and service up the page, I see no other solution than forcing a redirect. Not a great option, but it'll work if the
# param or cookie option are enabled... If you are reading this and can know how to have WP ignore the affiliate code at the root of
# the URL and process normally beyond that, please submit a patch to me and if it works, I'll send you a box of tasty cookies! :) - Jeremy
#			# [Magic URL Changing Code Goes Here]
#			return $root;

			wp_redirect(site_url().'/'.implode('/', $urlparts).'?'.infusionsoftaffiliates_defaultcodename().'='.$root);
			infusionsoftaffiliates_setcookie($root);
			exit;

		}	# were we able to load an affiliate based on the root?
	}	# was there a base to the URL?
  }	# check the URL root for an affiliate code

  foreach(preg_split('/\,\s*/', get_option('affiliatecode_names') ) as $codename)
  {
     if(get_option('affiliate_load_param') && $_REQUEST[$codename])
     {
         return $_REQUEST[$codename];
     }
     if(get_option('affiliate_load_cookie') && $_COOKIE[$codename])
     {
         return $_COOKIE[$codename];
     }
  }
  return false; # no code found!
}

function infusionsoftaffiliates_load($code)
{
	$caching = get_option('affiliate_caching');
	if (($caching == 'none') || (
		is_numeric($caching) && (
			(time() - get_option('affiliates_lastsync')) > (60 * $caching)
		)))
	{
		# Check if we just started syncing on another thread
		if((time() - get_option('affiliates_lastsync_start')) > 20)
		{
			syncaffiliates();
  		}
	}
	global $wpdb;
	$sql = "SELECT * FROM ".$wpdb->prefix."infusionsoftaffiliates WHERE affcode = '".mysql_real_escape_string($code)."';";
	return $wpdb->get_row($sql, ARRAY_A);
}

function infusionsoftaffiliates_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( dirname(__FILE__).'/infusionsoft-affiliates.php' ) ) {
		$links[] = '<a href="options-general.php?page=infusionsoftaffiliates">'.__('Settings').'</a>';
	}

	return $links;
}

add_filter( 'plugin_action_links', 'infusionsoftaffiliates_plugin_action_links', 10, 2 );

register_activation_hook(__FILE__,     'activate_infusionsoftaffiliates');
register_deactivation_hook(__FILE__, 'deactivate_infusionsoftaffiliates');
register_uninstall_hook(__FILE__, 'uninstall_infusionsoftaffiliates');

if (is_admin()) {
  add_action('admin_init', 'admin_init_infusionsoftaffiliates');
  add_action('admin_menu', 'admin_menu_infusionsoftaffiliates');
  add_action('save_post', 'infusionsoftaffiliates_updatemeta');
} else {
#  add_action('send_headers', 'infusionsoftaffiliates_checkrequest');
  add_action('wp', 'infusionsoftaffiliates_checkrequest');
}

function syncaffiliates() {
  global $infusion;
  if (!infusion_connect()) { return; }

   update_option('affiliates_lastsync_start', time());

   $fieldtypes = array('int NOT NULL', 'varchar(256)', 'varchar(50)', 'varchar(50)');
   $afffields = array('Id', 'AffName', 'AffCode', 'Password');
   $isfieldtypes = array(
		'',
		'varchar(20)',
		'varchar(15)',
		'float(8,2)',
		'float(8,2)',
		'varchar(20)',
		'varchar(3)',
		'int',
		'int',
		'int',
		'varchar(50)',
		'float(8,2)',
		'int',
		'date',
		'datetime',
		'varchar(254)',
		'text',
		'varchar(100)',
		'varchar(254)',
		'varchar(100)',
		'varchar(100)',
		'varchar(100)',
		'varchar(100)',
		'varchar(254)',
		'',
		'varchar(100)'
		);

   # FYI: FormId -3 is the Affiliate Table...
   if ($customfields = $infusion->dsFind('DataFormField', 100, 0, 'FormId', '-3', array('Name', 'Label', 'DataType')))
   {
	foreach($customfields as $cf)
	{
		$afffields[] = '_'.$cf['Name'];
		$fieldtypes[] = $isfieldtypes[$cf['DataType']];
	}
   }

   $affs = array();
   $page = 0;

   do
   {
	$newaffs = $infusion->dsFind('Affiliate', 1000, $page, 'Id', '%', $afffields);
	if(count($newaffs))
	{
		$affs = array_merge($affs, $newaffs);
	}
	$page++;
   } while(count($newaffs));

   global $wpdb;

   $wpdb->query("DROP TABLE ".$wpdb->prefix . "infusionsoftaffiliates;");

   $sql = "CREATE TABLE " . $wpdb->prefix . "infusionsoftaffiliates (";
   $i = 0;
   foreach ($afffields as $field) {
        $sql .= "\n\t".strtolower($field)." ".$fieldtypes[$i++].",";
   }

   $sql .= "\n\tUNIQUE KEY Id (Id)\n);";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);

   foreach($affs as $aff)
   {
      $i = 0;
      foreach ($afffields as $key)
      {
	if($aff[$key])
	{
		if($fieldtypes[$i] == 'datetime') {
			$aff[$key] = date('Y-m-d H:i:s', strtotime($aff[$key]));
		} else if($fieldtypes[$i] == 'date') {
			$aff[$key] = date('Y-m-d', strtotime($aff[$key]));
		}
	}
	$i++;
      }
      $wpdb->insert($wpdb->prefix . "infusionsoftaffiliates", array_change_key_case($aff));
   }

   update_option('affiliates_lastsync', time());
   update_option('affiliates_lastsync_start', 0);

}

function infusion_connect() {
  global $infusion;

  if ($infusion) { return true; }

  include_once dirname( __FILE__ ) . '/isdk.php';
  $infusion = new iSDK(get_option('infusionsoft_appname'), 'infusion', get_option('infusionsoft_apikey'));

  if(!$infusion->errorCode && $aff = $infusion->dsFind('Affiliate', 1, 0, 'Id', '%', array('Id')))
  {
    return true;
  } else {
    if(1) {	# In the future see if we're an admin or such
      if ($infusion->errorCode == 2) {
        print "Your Infusionsoft API Key is incorrect. Please update it.";
      } else {
        print "Unable to connect to Infusionsoft! ".$infusion->error;
      }
    } # admin?

    $infusion = false;
    return false;
  }

}



?>
