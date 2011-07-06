<?php
/*
Plugin Name: Infusionsoft Affiliates
Plugin URI: http://asandia.com/wordpress-plugins/infusionsoft-affiliates/
Description: Short Codes to insert a given Infusionsoft affiliates' info
Version: 0.2
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
        'default'        => ''
        ), $atts);

  $val = ($infusionsoftaffiliate[strtolower($atts['field'])]) ? $infusionsoftaffiliate[strtolower($atts['field'])] : $atts['default'];

  if(($atts['format'] != '') && strtotime($val)) {
    $val = date($atts['format'], strtotime($val));
  }

  return $val;
}

function activate_infusionsoftaffiliates() {
  add_option('infusionsoft_apikey');
  add_option('infusionsoft_appname');
  add_option('affiliate_caching', 'none');
  add_option('affiliate_load', 'param');
  add_option('affiliatecode_names', 'code,affcode');

   global $wpdb;
   $sql = "CREATE TABLE " . $wpdb->prefix . "infusionsoftaffiliates (
		id int NOT NULL,
		UNIQUE KEY id (id)
	);";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
}

function deactivate_infusionsoftaffiliates() {
  delete_option('infusionsoft_apikey');
  delete_option('infusionsoft_appname');
  delete_option('affiliate_caching');
  delete_option('affiliate_load');
  delete_option('affiliatecode_names');

   global $wpdb;
   $wpdb->query("DROP TABLE ".$wpdb->prefix . "infusionsoftaffiliates;");
}

function admin_init_infusionsoftaffiliates() {
  register_setting('infusionsoftaffiliates', 'infusionsoft_appname');
  register_setting('infusionsoftaffiliates', 'infusionsoft_apikey');
  register_setting('infusionsoftaffiliates', 'affiliate_caching');
  register_setting('infusionsoftaffiliates', 'affiliate_load');
  register_setting('infusionsoftaffiliates', 'affiliatecode_names');
}

function admin_menu_infusionsoftaffiliates() {
  add_options_page('Infusionsoft Affiliates', 'Infusionsoft Affiliates', 8, 'infusionsoftaffiliates', 'options_page_infusionsoftaffiliates');
}

function options_page_infusionsoftaffiliates() {
  include(dirname(__FILE__).'/options.php');  
}

function infusionsoftaffiliates_checkrequest() {
  global $infusionsoftaffiliate;

  if($code = infusionsoftaffiliates_findcode())
  {
	$infusionsoftaffiliate = infusionsoftaffiliates_load($code);
  }

}

function infusionsoftaffiliates_findcode() {
  foreach(preg_split('/\,\s*/', get_option('affiliatecode_names') ) as $codename)
  {
     if($_REQUEST[$codename])
     {
         return $_REQUEST[$codename];
     }
  }
}

function infusionsoftaffiliates_load($code)
{
	if (get_option('affiliate_caching') == 'none')
	{
		syncaffiliates();
	}
	global $wpdb;
	$sql = "SELECT * FROM ".$wpdb->prefix."infusionsoftaffiliates WHERE affcode = '".mysql_real_escape_string($code)."';";
	return $wpdb->get_row($sql, ARRAY_A);
}

function infusionsoftaffiliates_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( dirname(__FILE__).'/infusionsoftaffiliates.php' ) ) {
		$links[] = '<a href="options-general.php?page=infusionsoftaffiliates">'.__('Settings').'</a>';
	}

	return $links;
}

add_filter( 'plugin_action_links', 'infusionsoftaffiliates_plugin_action_links', 10, 2 );

register_activation_hook(__FILE__,     'activate_infusionsoftaffiliates');
register_deactivation_hook(__FILE__, 'deactivate_infusionsoftaffiliates');
add_action('wp_head', 'infusionsoftaffiliates_checkrequest');

if (is_admin()) {
  add_action('admin_init', 'admin_init_infusionsoftaffiliates');
  add_action('admin_menu', 'admin_menu_infusionsoftaffiliates');
}

function syncaffiliates() {
  global $infusion;
  if (!infusion_connect()) { return; }

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

   $affs = $infusion->dsFind('Affiliate', 1000, 0, 'Id', '%', $afffields);
 
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
