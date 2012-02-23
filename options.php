<script type="text/javascript">
function iaregister_reg() {
        jQuery.post(ajaxurl, jQuery('#regform').serialize(), function(response) {
                if(response == 'Success')
                {
                        jQuery('#regbox').html('Thanks! Registration was successful!');
                } else {
                        alert("Oh, no! We weren't able to complete your registration. :(");
                }
        });
};
</script>
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
<td>https://<input type="text" name="infusionsoft_appname" value="<?php echo get_option('infusionsoft_appname'); ?>" size="8" />.Infusionsoft.com</td>
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
    <input type="checkbox" name="affiliate_load_root"   value="1" <?php checked(get_option('affiliate_load_root'));  ?>>
	An affiliate code is in the root of the URL (will redirect)<br/>
    <input type="checkbox" name="affiliate_load_param"  value="1" <?php checked(get_option('affiliate_load_param')); ?>>
	An affiliate code is in the query string <br/>
    <input type="checkbox" name="affiliate_load_cookie" value="1"<?php checked(get_option('affiliate_load_cookie')); ?>>
	An affiliate code is in a cookie <br/>
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
</form>

</p>



<a name="register"></a>
<?php if(infusionsoftaffiliates_isregistered()) { ?>
<h3>"Thank you for registering your plugin! You're a rock star!" - Jeremy Shapiro</h3>
<?php
  } else {
        global $current_user;
        get_currentuserinfo();
?>
<div id="regbox" class="updated">
<p>
<strong>Please register your copy of this awesome Infusionsoft Affiliates Plugin!</strong> Your registration helps keep Jeremy motivated and let's him know how widespread the use of this plugin is.
He'll keep you up to date on plugin changes and might even email you some cool non-spammy goodies from time to time. Your email will be kept super safe and won't be shared,
swapped or given away.
<p>
<form accept-charset="UTF-8" id="regform"
        action="javascript:iaregister_reg();"
        name="Wordpress Infusionsoft-Affiliates Registration" style="height:100%; margin:0" target=""
        onsubmit="iaregister_reg(); return false;">
<input name="action" type="hidden" value="infusionsoftaffiliates_reg">
<input name="inf_form_xid" type="hidden" value="beef1ec44c35dfa93fc12e976ce91103" />
<input name="inf_form_name" type="hidden" value="Wordpress Infusionsoft-Affiliates Registration" />
<input name="infusionsoft_version" type="hidden" value="1.24.1.81" />
<label for="inf_field_FirstName">First Name: </label>
<input class="infusion-field-input" id="inf_field_FirstName" name="inf_field_FirstName" type="text" value="<?php
        global $current_user; echo((isset($current_user->user_firstname) && $current_user->user_firstname) ?
        $current_user->user_firstname : $current_user->display_name);
        ?>" />
<label for="inf_field_Email">Email: </label>
<input class="infusion-field-input" id="inf_field_Email" name="inf_field_Email" type="text" value="<?php echo(get_bloginfo('admin_email')); ?>" />
<input id="inf_field_Website" name="inf_field_Website" type="hidden" value="<?php echo(get_bloginfo('url')); ?>" />
<input type="submit" value="Register!" class="button-primary" />
</form>
</div>
<?php } ?>

<p>
<h2>Frequently Asked Questions</h2>
</p>

<p>
<ol>
<li><b>How do I find my Application Name?</b><br/>
Log into your Infusionsoft application. Look in your address bar. It's the word that comes between the https:// and infusionsoft.com.

<li><b>How do I find my API Key?</b><br/>
Log into your Infusionsoft application. Click on <code>Setup</code> | <code>Misc Settings</code> | <code>Application Settings</code> | <code>Miscellaneous</code> | <code>API</code> | 
<code>Encrypted Key</code>. 
Copy and paste that key. If there is no encrypted key and API Passphrase is blank, enter an API passphrase, click Save, and then copy and paste in the new Encrypted Key.

<li><b>How do I embed information about an affiliate on a page?</b><br/>
Use the <code>[affiliate]</code> shortcode with a field, for example <code>[affiliate field="AffName" /]</code> for the affiliate name.
<br/>
You can specify a default value with <code>default</code> as in <code>[affiliate field="AffName" default="Your Host" /]</code>

<li><b>Can I format dates?</b><br/>
Yes. Add in <code>dateformat</code> as in <code>[affiliate field="_WebinarDate" format="F Js" /]</code>.
See this <a href="http://php.net/manual/en/function.date.php" target="_new">Formatting Guide</a> 
for more information on how to format the dates.

<li><b>How do I print a relative date, like "3 days before" ?</b><br/>
Great question! This is super powerful as it lets you "timeshift" and show a relative date to your date field.
Add in the <code>dateshift</code> option with an adjustment <code>+</code> or <code>-</code>, followed by a number, followed by the time unit,
like <code>[affiliate field="_WebinerDate" dateshift="-3 days" /]</code>.

<li><b>Are my custom fields from Infusionsoft available here, too?</b><br/>
Yes! Just remember to use the field names as Infusionsoft knows them in the API, i.e. put an underscore before the name. See your custom fields
in your Infusionsoft application for exactly how they should be written.

<li><b>I'm returning HTML and WordPress keeps encoding it. How do I keep the raw HTML?</b><br/>
Just specify <code>htmldecode=1</code> to decode the HTML. This is useful if you're returning raw HTML that you want to use and not have escaped.

<li><b>Can you just build me a custom Affiliate Resource Center?</b><br/>
Sure. <a href="http://asandia.com/" target="_blank">Contact Jeremy B. Shapiro</a> to discuss options.

</ol>

</div>
