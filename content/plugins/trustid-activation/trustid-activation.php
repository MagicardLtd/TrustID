<?php
/**
 * Plugin Name: TrustID Activation
 * Description: Activation plugin for TrustID
 * Author: Sarah@Zeta.net
 * Version: 1.0
 */

/**
 * Copyright 2011 Zeta
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

define('TRUSTID_ACTIVATE_VERSION', '1.0');
define('TRUSTID_ACTIVATE_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require(plugin_dir_path(__FILE__)."trustid-activation-luhn.php");
require(plugin_dir_path(__FILE__)."trustid-activation-list-helper.php");
require(plugin_dir_path(__FILE__)."trustid-activation-request-response.php");

$table_name = $wpdb->prefix . "trustid_keys";
$table_name_activations = $wpdb->prefix . "trustid_activations";
$tidActivationResult = array();


// Process any request to generate keys.
if (!empty($_POST['generate']))
{
    // perform server side validation
    // No errors displayed as that has already been handled client side this is just to prevent gaming the form.
    $error = false;
    if (empty($_POST['qty']) || !ctype_digit($_POST['qty']) || $_POST['qty'] < 1 || $_POST['qty'] > 100)
        $error = true;
    if (!isset($_POST['edition']) || !ctype_digit($_POST['edition']) || $_POST['edition'] < 0 || $_POST['edition'] > 3)
        $error = true;
    if (!isset($_POST['upgrade']) || !ctype_digit($_POST['upgrade']) || $_POST['upgrade'] < 0 || $_POST['upgrade'] > 31)
        $error = true;
    if (empty($_POST['distributor']) || strlen($_POST['distributor']) < 2 || strlen($_POST['distributor']) > 200)
        $error = true;
	if (!isset($_POST['purpose']) || !ctype_digit($_POST['purpose']) || $_POST['purpose'] < 0 || $_POST['purpose'] > 1)
        $error = true;
	if (!isset($_POST['upgrading']) || !ctype_digit($_POST['upgrading']) || $_POST['upgrading'] < 0 || $_POST['upgrading'] > 1)
        $error = true;

	if ($_POST['upgrading'] == 1 && $_POST['qty'] >0 && $_POST['qty'] <=100) //Also check if any keys are to be upgraded
	{
		//are any of them duplicated?
		if(count(array_unique($_POST['originalKey']))<count($_POST['originalKey']))
			$error = true;
		//are all of them valid keys?
		for($i=0; $i< $_POST['qty']; ++$i)
		{
			$validation = trustid_activate_validateKey($_POST['originalKey'][$i], $_POST['edition']);
			if ($validation["result"] != "Valid Key")
			{
				$error = true;
				break 1;
			}
		}

	}

    // ok done validating now lets generate keys.
    if (!$error)
    {
        $keybase = "1"; // first char is Brand ID and Brand id 1 is Trust ID
        $keybase.= $_POST['edition']; // Char 2 is the edition id
        $keybase.= strlen($_POST['upgrade']) == 1 ? "0".$_POST['upgrade'] : $_POST['upgrade']; // Char 3 and 4 is the 2 digit upgrade id

        for($i=0; $i< $_POST['qty']; ++$i)
        {
            $copyid = trustid_activate_get_copyid();// Char 5-14 is the zero padded copy id which is different for every key
            $key = $keybase.$copyid.str_pad( GetLuhnChecksum(str_split($keybase.$copyid,2),100),2,"0",STR_PAD_LEFT);// Char 15-16 is a 2 digit checksum which is different for every key
            $dashedKey = implode("-",str_split($key,4));
            $displayKeys[$i]["newKey"] = $dashedKey;
			if ($_POST['upgrading'] == 1)
				$displayKeys[$i]['originalKey'] = $_POST['originalKey'][$i];


			$wpdb->insert(
				$table_name,
				array(
					'registrationKey' => $dashedKey,
					'distributor' => $_POST['distributor'],
					'purpose' => $_POST['purpose'],
					'issued' => time(),
					'brandId' => 1,
					'editionId' => $_POST['edition'],
					'upgradeKey' => $_POST['upgrading'],
					'upgradeFromKey' => $_POST['originalKey'][$i],   //$origKey,
					'upgradeId' => $_POST['upgrade'],
					'copyId' => $copyid,
				),
				array(
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%d',
					'%d'
				)
			);
        }
    }
}



function trustid_activate_get_copyid(){
    global $wpdb, $table_name;
    $copyid = $wpdb->get_var("SELECT LPAD(newcopyid,10,0) FROM
        (SELECT FLOOR(1 + RAND() * 9999999999) AS newcopyid  FROM $table_name t1) AS newcopyid
        WHERE newcopyid NOT IN (SELECT copyid FROM $table_name)");

    return $copyid;

}


function trustid_activate_validateKey($key, $newEdition){
	global $wpdb, $table_name;
    //1st - check that the Key matches the key 'type' pattern
	$pattern = '/^([0-9]{4})-([0-9]{4})-([0-9]{4})-([0-9]{4}$)/';
	if (empty($key) || !preg_match($pattern,$key))
	{
		$results["result"] = "Invalid Key";
		return $results;
	}
	//2nd - Check if the Key exists in the db
	$keyData = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name  WHERE registrationKey = %s",$key));
	if(empty($keyData))   //If it doesn't exist
	{
		$results["result"] = "Invalid Key";
		return $results;
	}
	//3rd - Check the Key's edition is lower than the edition of the new key(s) to be created
	if($keyData->editionId >= $newEdition)
	{
		$results["result"] = "Invalid edition";
		$results["keyEdition"] = $keyData->editionId;
		return $results;
	}
	//4th - Check if the key has already been upgraded
	$upgradeKeyData = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name  WHERE upgradeFromKey = %s",$key));
	if (!empty($upgradeKeyData))
	{
		$results["result"] = "Already upgraded";
		$results["upgradeKey"] = $upgradeKeyData->registrationKey;
		$results["upgradeKeyEdition"] = $upgradeKeyData->editionId;
		return $results;
	}
	//if it passes all checks return OK
	$results["result"] = "Valid Key";
	$results["keyEdition"] = $keyData->editionId;
    return $results;
}







function trustid_activate_admin() {
}

function trustid_activate_generate_keys() {

    include('trustid-activation-generate-keys.php');
}

function trustid_activate_all_keys() {

    include('trustid-activation-all-keys.php');
}

function trustid_activate_all_activations() {

    include('trustid-activation-all-activations.php');
}

add_action('init', 'trustid_activate_init');
add_action('plugins_loaded', 'trustid_activate_do_something');
add_action('admin_menu', 'trustid_activate_admin_actions');
add_action('update_option_trustid_activateoption', 'trustid_activate_do_something_else');


// Add our admin menus
function trustid_activate_admin_actions() {
    global $tidMenuHook;
    $tidMenuHook = add_menu_page("TrustID Activation", "TrustID Activation", "produce_report", "tid_activation", "trustid_activate_admin");
    add_submenu_page("tid_activation", "TrustID Activation", "All Activations", "produce_report", "tid_activation", "trustid_activate_all_activations");
    add_submenu_page("tid_activation", "TrustID Activation", "All Keys", "produce_report", "tid_all_keys", "trustid_activate_all_keys");
    add_submenu_page("tid_activation", "TrustID Activation", "Generate Keys", "manage_options", "tid-generate-keys", "trustid_activate_generate_keys");


}

function trustid_activate_init() {

    add_role('reporter', 'Reporter', array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
        'produce_report' => true,
    ));

    $role = get_role( 'administrator' );
    $role->add_cap( 'produce_report', true );
    $role->add_cap( 'view_PII', true );

    wp_enqueue_script( "jquery");
    wp_enqueue_script( "jquery-validation", plugins_url( "/trustid-activation/" )."js/jquery-validation-1.11.1/jquery.validate.min.js","jquery" );
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
}

function trustid_activate_install() {
    global $wpdb,$table_name_activations,$table_name ;

    // Create default options if needed
    // add_option('trustid_activate_option', 'the option');


    // Create trustid_activate table if not existing
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

        $sql = "
            CREATE TABLE  `" . $table_name . "` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `registrationKey` varchar(19) NOT NULL,
  `distributor` varchar(200) NOT NULL,
  `purpose` tinyint(1) unsigned NOT NULL,
  `issued` varchar(12) NOT NULL,
  `brandId` tinyint(1) unsigned NOT NULL,
  `editionId` tinyint(1) unsigned NOT NULL,
  `upgradeKey` tinyint(1) unsigned NOT NULL,
  `upgradeFromKey` varchar(19) NOT NULL,
  `upgradeId` tinyint(2) unsigned NOT NULL,
  `copyId` bigint(10) unsigned NOT NULL,
  `firstUsed` varchar(10) NOT NULL,
  `activationCount` tinyint(3) unsigned NOT NULL,
  `active` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `copyId` (`copyId`),
  KEY `registrationKey` (`registrationKey`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

        $sql = "
            CREATE TABLE `" . $table_name_activations . "` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `registrationId` int(10) unsigned NOT NULL,
  `registrationKeyk` varchar(19) NOT NULL,
  `activated` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `installed` varchar(50) NOT NULL,
  `requestCode` char(22) NOT NULL,
  `responseCode` char(13) NOT NULL,
  `state` tinyint(1) unsigned NOT NULL,
  `country` char(3) NOT NULL,
  `cname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `manualActivate` tinyint(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `registrationId` (`registrationId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

}



function trustid_activate_generate_key()
{

}


function trustid_activate_do_something() {
     // something to do when plugins are loaded
}

function trustid_activate_do_something_else() {
     // do something when options are updated
}


function trustid_activate_show_result()
{
    global $tidActivationResult;
    return $tidActivationResult['ResponseCode'];

}



/**
 * Replaces shortcode "[trustid_activate]" with plugin stuff
 * Can be called anywhere in template with
 * <?php echo do_shortcode('[trustid_activate]'); ?>
 * @param array $atts
 * @param string $content
 * @param string $code
 */
function trustid_activate_shortcode($atts = array(), $content=null, $code="") {

    global $tidActivationResult;

    $bypassPostCheck = true;
    require dirname(__FILE__)."/trustid-activation-process-request.php";

    if (!empty($_POST['generate']))
    {
        $tidActivationResult = tid_process_request(true);

        if (isset($tidActivationResult['Status']) && $tidActivationResult['Status'] == 0)
        {
            //Success
            $copy = get_post_custom_values("Activation-Success");
            $response = $copy[0];
            $response .= "<br /> &nbsp; <br />{$tidActivationResult['ResponseCode']}";
            return nl2br($response);
        }
        else
        {
            //Fail
            $copy = get_post_custom_values("Activation-Failure");
            $response = $copy[0];
            $response .= "<br /> &nbsp; <br />Status =  {$tidActivationResult['Status']}";
            return nl2br($response);

        }

    }
    else
    {

    $response = get_post_custom_values("Activation-Default");

    // have to return, not echo
    $form = <<<END

<form method="post" id="activate">
        <ul>
            <li>

                <label for="RequestCode">Please enter your request code. *</label><input type="text" name="RequestCode"
                 allUpperCase="true" style="width: 400px;">

                <br><span class="hint">This is the request code generated by the Trust ID software</span>
                <br>&nbsp;<br>
            </li>
            <li>
                <label for="Fname">First name *</label><input type="text" name="Fname" maxlength="150">
                <br>&nbsp;<br>
            </li>
            <li>
                <label for="Lname">Last name *</label><input type="text" name="Lname" maxlength="50">
                <br>&nbsp;<br>
            </li>
            <li>
                <label for="Email">Email address *</label><input type="text" name="Email" maxlength="254">
                <br>&nbsp;<br>
            </li>
            <li>
                <label for="Cname">Company name </label><input type="text" name="Cname" maxlength="100">
                <br>&nbsp;<br>
            </li>
            <li>
                <label for="Country">Country *</label>
                <select name="Country">
                    <option value="" selected=selected  disabled=disabled>Select one</option>
                    <option value="GBR">United Kingdom</option>
                    <option value="USA">United States</option>
                    <optgroup label="---------"></optgroup>
                    <option value="AFG">Afghanistan</option>
                    <option value="ALA">Åland Islands</option>
                    <option value="ALB">Albania</option>
                    <option value="DZA">Algeria</option>
                    <option value="ASM">American Samoa</option>
                    <option value="AND">Andorra</option>
                    <option value="AGO">Angola</option>
                    <option value="AIA">Anguilla</option>
                    <option value="ATA">Antarctica</option>
                    <option value="ATG">Antigua and Barbuda</option>
                    <option value="ARG">Argentina</option>
                    <option value="ARM">Armenia</option>
                    <option value="ABW">Aruba</option>
                    <option value="AUS">Australia</option>
                    <option value="AUT">Austria</option>
                    <option value="AZE">Azerbaijan</option>
                    <option value="BHS">Bahamas</option>
                    <option value="BHR">Bahrain</option>
                    <option value="BGD">Bangladesh</option>
                    <option value="BRB">Barbados</option>
                    <option value="BLR">Belarus</option>
                    <option value="BEL">Belgium</option>
                    <option value="BLZ">Belize</option>
                    <option value="BEN">Benin</option>
                    <option value="BMU">Bermuda</option>
                    <option value="BTN">Bhutan</option>
                    <option value="BOL">Bolivia, Plurinational State of</option>
                    <option value="BES">Bonaire, Sint Eustatius and Saba</option>
                    <option value="BIH">Bosnia and Herzegovina</option>
                    <option value="BWA">Botswana</option>
                    <option value="BVT">Bouvet Island</option>
                    <option value="BRA">Brazil</option>
                    <option value="IOT">British Indian Ocean Territory</option>
                    <option value="BRN">Brunei Darussalam</option>
                    <option value="BGR">Bulgaria</option>
                    <option value="BFA">Burkina Faso</option>
                    <option value="BDI">Burundi</option>
                    <option value="KHM">Cambodia</option>
                    <option value="CMR">Cameroon</option>
                    <option value="CAN">Canada</option>
                    <option value="CPV">Cape Verde</option>
                    <option value="CYM">Cayman Islands</option>
                    <option value="CAF">Central African Republic</option>
                    <option value="TCD">Chad</option>
                    <option value="CHL">Chile</option>
                    <option value="CHN">China</option>
                    <option value="CXR">Christmas Island</option>
                    <option value="CCK">Cocos (Keeling) Islands</option>
                    <option value="COL">Colombia</option>
                    <option value="COM">Comoros</option>
                    <option value="COG">Congo</option>
                    <option value="COD">Congo, the Democratic Republic of the</option>
                    <option value="COK">Cook Islands</option>
                    <option value="CRI">Costa Rica</option>
                    <option value="CIV">Côte d'Ivoire</option>
                    <option value="HRV">Croatia</option>
                    <option value="CUB">Cuba</option>
                    <option value="CUW">Curaçao</option>
                    <option value="CYP">Cyprus</option>
                    <option value="CZE">Czech Republic</option>
                    <option value="DNK">Denmark</option>
                    <option value="DJI">Djibouti</option>
                    <option value="DMA">Dominica</option>
                    <option value="DOM">Dominican Republic</option>
                    <option value="ECU">Ecuador</option>
                    <option value="EGY">Egypt</option>
                    <option value="SLV">El Salvador</option>
                    <option value="GNQ">Equatorial Guinea</option>
                    <option value="ERI">Eritrea</option>
                    <option value="EST">Estonia</option>
                    <option value="ETH">Ethiopia</option>
                    <option value="FLK">Falkland Islands (Malvinas)</option>
                    <option value="FRO">Faroe Islands</option>
                    <option value="FJI">Fiji</option>
                    <option value="FIN">Finland</option>
                    <option value="FRA">France</option>
                    <option value="GUF">French Guiana</option>
                    <option value="PYF">French Polynesia</option>
                    <option value="ATF">French Southern Territories</option>
                    <option value="GAB">Gabon</option>
                    <option value="GMB">Gambia</option>
                    <option value="GEO">Georgia</option>
                    <option value="DEU">Germany</option>
                    <option value="GHA">Ghana</option>
                    <option value="GIB">Gibraltar</option>
                    <option value="GRC">Greece</option>
                    <option value="GRL">Greenland</option>
                    <option value="GRD">Grenada</option>
                    <option value="GLP">Guadeloupe</option>
                    <option value="GUM">Guam</option>
                    <option value="GTM">Guatemala</option>
                    <option value="GGY">Guernsey</option>
                    <option value="GIN">Guinea</option>
                    <option value="GNB">Guinea-Bissau</option>
                    <option value="GUY">Guyana</option>
                    <option value="HTI">Haiti</option>
                    <option value="HMD">Heard Island and McDonald Islands</option>
                    <option value="VAT">Holy See (Vatican City State)</option>
                    <option value="HND">Honduras</option>
                    <option value="HKG">Hong Kong</option>
                    <option value="HUN">Hungary</option>
                    <option value="ISL">Iceland</option>
                    <option value="IND">India</option>
                    <option value="IDN">Indonesia</option>
                    <option value="IRN">Iran, Islamic Republic of</option>
                    <option value="IRQ">Iraq</option>
                    <option value="IRL">Ireland</option>
                    <option value="IMN">Isle of Man</option>
                    <option value="ISR">Israel</option>
                    <option value="ITA">Italy</option>
                    <option value="JAM">Jamaica</option>
                    <option value="JPN">Japan</option>
                    <option value="JEY">Jersey</option>
                    <option value="JOR">Jordan</option>
                    <option value="KAZ">Kazakhstan</option>
                    <option value="KEN">Kenya</option>
                    <option value="KIR">Kiribati</option>
                    <option value="PRK">Korea, Democratic People's Republic of</option>
                    <option value="KOR">Korea, Republic of</option>
                    <option value="KWT">Kuwait</option>
                    <option value="KGZ">Kyrgyzstan</option>
                    <option value="LAO">Lao People's Democratic Republic</option>
                    <option value="LVA">Latvia</option>
                    <option value="LBN">Lebanon</option>
                    <option value="LSO">Lesotho</option>
                    <option value="LBR">Liberia</option>
                    <option value="LBY">Libya</option>
                    <option value="LIE">Liechtenstein</option>
                    <option value="LTU">Lithuania</option>
                    <option value="LUX">Luxembourg</option>
                    <option value="MAC">Macao</option>
                    <option value="MKD">Macedonia, the former Yugoslav Republic of</option>
                    <option value="MDG">Madagascar</option>
                    <option value="MWI">Malawi</option>
                    <option value="MYS">Malaysia</option>
                    <option value="MDV">Maldives</option>
                    <option value="MLI">Mali</option>
                    <option value="MLT">Malta</option>
                    <option value="MHL">Marshall Islands</option>
                    <option value="MTQ">Martinique</option>
                    <option value="MRT">Mauritania</option>
                    <option value="MUS">Mauritius</option>
                    <option value="MYT">Mayotte</option>
                    <option value="MEX">Mexico</option>
                    <option value="FSM">Micronesia, Federated States of</option>
                    <option value="MDA">Moldova, Republic of</option>
                    <option value="MCO">Monaco</option>
                    <option value="MNG">Mongolia</option>
                    <option value="MNE">Montenegro</option>
                    <option value="MSR">Montserrat</option>
                    <option value="MAR">Morocco</option>
                    <option value="MOZ">Mozambique</option>
                    <option value="MMR">Myanmar</option>
                    <option value="NAM">Namibia</option>
                    <option value="NRU">Nauru</option>
                    <option value="NPL">Nepal</option>
                    <option value="NLD">Netherlands</option>
                    <option value="NCL">New Caledonia</option>
                    <option value="NZL">New Zealand</option>
                    <option value="NIC">Nicaragua</option>
                    <option value="NER">Niger</option>
                    <option value="NGA">Nigeria</option>
                    <option value="NIU">Niue</option>
                    <option value="NFK">Norfolk Island</option>
                    <option value="MNP">Northern Mariana Islands</option>
                    <option value="NOR">Norway</option>
                    <option value="OMN">Oman</option>
                    <option value="PAK">Pakistan</option>
                    <option value="PLW">Palau</option>
                    <option value="PSE">Palestinian Territory, Occupied</option>
                    <option value="PAN">Panama</option>
                    <option value="PNG">Papua New Guinea</option>
                    <option value="PRY">Paraguay</option>
                    <option value="PER">Peru</option>
                    <option value="PHL">Philippines</option>
                    <option value="PCN">Pitcairn</option>
                    <option value="POL">Poland</option>
                    <option value="PRT">Portugal</option>
                    <option value="PRI">Puerto Rico</option>
                    <option value="QAT">Qatar</option>
                    <option value="REU">Réunion</option>
                    <option value="ROU">Romania</option>
                    <option value="RUS">Russian Federation</option>
                    <option value="RWA">Rwanda</option>
                    <option value="BLM">Saint Barthélemy</option>
                    <option value="SHN">Saint Helena, Ascension and Tristan da Cunha</option>
                    <option value="KNA">Saint Kitts and Nevis</option>
                    <option value="LCA">Saint Lucia</option>
                    <option value="MAF">Saint Martin (French part)</option>
                    <option value="SPM">Saint Pierre and Miquelon</option>
                    <option value="VCT">Saint Vincent and the Grenadines</option>
                    <option value="WSM">Samoa</option>
                    <option value="SMR">San Marino</option>
                    <option value="STP">Sao Tome and Principe</option>
                    <option value="SAU">Saudi Arabia</option>
                    <option value="SEN">Senegal</option>
                    <option value="SRB">Serbia</option>
                    <option value="SYC">Seychelles</option>
                    <option value="SLE">Sierra Leone</option>
                    <option value="SGP">Singapore</option>
                    <option value="SXM">Sint Maarten (Dutch part)</option>
                    <option value="SVK">Slovakia</option>
                    <option value="SVN">Slovenia</option>
                    <option value="SLB">Solomon Islands</option>
                    <option value="SOM">Somalia</option>
                    <option value="ZAF">South Africa</option>
                    <option value="SGS">South Georgia and the South Sandwich Islands</option>
                    <option value="SSD">South Sudan</option>
                    <option value="ESP">Spain</option>
                    <option value="LKA">Sri Lanka</option>
                    <option value="SDN">Sudan</option>
                    <option value="SUR">Suriname</option>
                    <option value="SJM">Svalbard and Jan Mayen</option>
                    <option value="SWZ">Swaziland</option>
                    <option value="SWE">Sweden</option>
                    <option value="CHE">Switzerland</option>
                    <option value="SYR">Syrian Arab Republic</option>
                    <option value="TWN">Taiwan, Province of China</option>
                    <option value="TJK">Tajikistan</option>
                    <option value="TZA">Tanzania, United Republic of</option>
                    <option value="THA">Thailand</option>
                    <option value="TLS">Timor-Leste</option>
                    <option value="TGO">Togo</option>
                    <option value="TKL">Tokelau</option>
                    <option value="TON">Tonga</option>
                    <option value="TTO">Trinidad and Tobago</option>
                    <option value="TUN">Tunisia</option>
                    <option value="TUR">Turkey</option>
                    <option value="TKM">Turkmenistan</option>
                    <option value="TCA">Turks and Caicos Islands</option>
                    <option value="TUV">Tuvalu</option>
                    <option value="UGA">Uganda</option>
                    <option value="UKR">Ukraine</option>
                    <option value="ARE">United Arab Emirates</option>
                    <option value="GBR">United Kingdom</option>
                    <option value="USA">United States</option>
                    <option value="UMI">United States Minor Outlying Islands</option>
                    <option value="URY">Uruguay</option>
                    <option value="UZB">Uzbekistan</option>
                    <option value="VUT">Vanuatu</option>
                    <option value="VEN">Venezuela, Bolivarian Republic of</option>
                    <option value="VNM">Viet Nam</option>
                    <option value="VGB">Virgin Islands, British</option>
                    <option value="VIR">Virgin Islands, U.S.</option>
                    <option value="WLF">Wallis and Futuna</option>
                    <option value="ESH">Western Sahara</option>
                    <option value="YEM">Yemen</option>
                    <option value="ZMB">Zambia</option>
                    <option value="ZWE">Zimbabwe</option>
                </select>
                <br>&nbsp;<br>
            </li>
            <li>
                * Required answers
                <br>&nbsp;<br>
            </li>
            <li>
                <input class="button-primary" type="submit" id="generate" name="generate" value="Activate my software">
            </li>
        </ul>
</form>
    <script>

     jQuery.validator.addMethod("allUpperCase",function(value,element,param)
	{  //Add a new validation method to the form validator (to stop the form being submitted if lowercase characters are used)
		var textUpper = value.toUpperCase();
		var n = textUpper.localeCompare(value);
		if(n <=0)
			return true;
		return false;
	},"Upper case characters only!");

    jQuery( "#activate" ).validate({
        rules: {
            RequestCode: {
            required: true,
            minlength: 22,
            maxlength: 22,
            },
            Fname: {
            required: true,
            rangelength: [1,50],
            },
            Lname: {
            required: true,
            rangelength: [1,50],
            },
            Cname: {
            required: false,
            rangelength: [1,100],
            },
            Country: {
            required: true,
            },
            Email: {
            required: true,
            email: true,
            rangelength: [3,254],
            }
        }
    });

    </script>
END;

     return nl2br($response[0]).$form;
    }
}

/**
* Drops the table and deletes the options
* when user clicks "delete plugin"
*/
function trustid_activate_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . "trustid_activate";

    // We are not deleting the database table on uninstall as it contains necessary data  that cannot be replicated


    delete_option('trustid_activate_option');
}


// Turns assoc array into downloadable csv
// $type should be the type of data exported eg keys or activations and is used in generated filename
function tid_do_csv($data,$type = "export")
{
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$type.'-'.date("Ymd").'.csv"');

    // Create a stream opening it with read / write mode
    $stream = fopen("php://temp", 'w+');

    // Iterate over the data, writing each line to the text stream


    foreach ($data as $val) {
        if (!isset($titlesDone))
        {
            $titles = array_keys($val);
            tid_fputcsv($stream, $titles);
            $titlesDone = true;
        }
        tid_fputcsv($stream, $val);
    }

    // Rewind the stream
    rewind($stream);

    // You can now echo it's content
    echo stream_get_contents($stream);

    // Close the stream
    fclose($stream);

    exit;
}

    // Write a line to a file
    // $filePointer = the file resource to write to
    // $dataArray = the data to write out
    // $delimeter = the field separator
    function tid_fputcsv($filePointer,$dataArray,$delimiter = ",",$enclosure = '"')
    {

        // Build the string
        $string = "";

        // No leading delimiter
        $writeDelimiter = FALSE;
        foreach($dataArray as $dataElement)
        {
            // Replaces a double quote with two double quotes
            $dataElement=str_replace("\"", "\"\"", $dataElement);

            // Adds a delimiter before each field (except the first)
            if($writeDelimiter) $string .= $delimiter;

            // Encloses each field with $enclosure and adds it to the string
            $string .= $enclosure . $dataElement . $enclosure;

            // Delimiters are used every time except the first.
            $writeDelimiter = TRUE;
        }

        // Append new line
        $string .= "\n";

        // Write the string to the file
        fwrite($filePointer,$string);
    }


    function tid_do_download_file(){
        global $pagenow;
        if((!empty($_GET['download']) || !empty($_POST['download']))&& !empty($_GET['page']))
        {
            if (strpos($_GET['page'],"key") !== false)
                require("trustid-activation-all-keys.php");
            else if (strpos($_GET['page'],"activation") !== false)
                require("trustid-activation-all-activations.php");
        }
    }

add_action( 'admin_init', 'tid_do_download_file' );



register_activation_hook( __FILE__, 'trustid_activate_install' );
add_shortcode( 'trustid_activate', 'trustid_activate_shortcode' );
add_shortcode( 'trustid_activate_result', 'trustid_activate_show_result' );
register_uninstall_hook( __FILE__, 'trustid_activate_uninstall' );
