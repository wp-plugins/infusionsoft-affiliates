<div class="wrap">
<div id="icon-plugins" class="icon32"></div>
<h2>Infusionsoft Affiliates</h2>

<p>
Please enter your Infusionsoft API Key below so that we can access affiliates from your system.

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('infusionsoftaffiliates'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Infusionsoft App Name:</th>
<td><input type="text" name="infusionsoft_appname" value="<?php echo get_option('infusionsoft_appname'); ?>" size="8" />.Infusionsoft.com</td>
</tr>


<tr valign="top">
<th scope="row">Infusionsoft API Key:</th>
<td><input type="text" name="infusionsoft_apikey" value="<?php echo get_option('infusionsoft_apikey'); ?>" size="48" /></td>
</tr>


<tr valign="top">
<th scope="row">Affiliate Caching:</th>
<td>
  <select name="affiliate_caching">
    <option value="none" <?php if (get_option('affiliate_caching') == 'none') echo "selected=selected" ?>>Do Not Cache</option>
    <option value="5"  <?php if (get_option('affiliate_caching') == '5') echo "selected=selected" ?> >Cache at most 5 minutes</option>
    <option value="60"  <?php if (get_option('affiliate_caching') == '60') echo "selected=selected" ?> >Cache at most Hourly</option>
    <option value="1440"  <?php if (get_option('affiliate_caching') == '1440') echo "selected=selected" ?> >Cache at most Daily</option>
  </select>
  &nbsp; <?php echo( (get_option('affiliates_lastsync') != '') ? "Last synced ".human_time_diff(get_option('affiliates_lastsync'))." ago" : "Not yet synched"); ?>
</td>
</tr>

<tr valign="top">
<th scope="row">Load Affiliates When:</th>
<td>
    <input type="radio" name="affiliate_load" value="request" <?php if (get_option('affiliate_load') == 'request') echo "checked" ?>>
	An affiliate code is in the param OR cookie <br/>
    <input type="radio" name="affiliate_load" value="param"   <?php if (get_option('affiliate_load') == 'param') echo "checked" ?>>
	An affiliate code is in just the param <br/>
    <input type="radio" name="affiliate_load" value="cookie"  <?php if (get_option('affiliate_load') == 'cookie') echo "checked" ?>>
	An affiliate code is in just the cookie <br/>
    <input type="radio" name="affiliate_load" value="root"    <?php if (get_option('affiliate_load') == 'root') echo "checked" ?>   disabled>
	An affiliate code is in the root of the URL or, as a backup, in a Cookie <br/>
</td>
</tr>

<tr valign="top">
<th scope="row">Affiliate Code Names:</th>
<td><input type="text" name="affiliatecode_names" value="<?php echo get_option('affiliatecode_names'); ?>" size="48" /></td>
</tr>

<tr valign="top">
<th scope="row">Default Affiliate Page:</th>
<td>
Select which page will be shown when there is a valid affiliate, but no page specified.<br/>
<?php wp_dropdown_pages(array('name' => 'affiliate_defaultpage', 'selected' => get_option('affiliate_defaultpage'), 'show_option_none' => '(Use Site Default)')); ?>
</td>
</tr>

<tr valign="top">
<th scope="row">Default No Affiliate Page:</th>
<td>Select which page will be shown when there is no valid affiliate code found:<br/>
<?php wp_dropdown_pages(array('name' => 'noaffiliate_defaultpage', 'selected' => get_option('noaffiliate_defaultpage'), 'show_option_none' => '(Show Requested Page)')); ?>
</td>
</tr>

</table>

<input type="hidden" name="action" value="update" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
