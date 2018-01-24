<?php

    global $wpdb, $table_name_activations, $table_name;
    date_default_timezone_set('UTC');

    $sql = "SELECT COUNT(*) FROM '{$wpdb->prefix}trustid_activations'";
    echo "Record count: ".$wpdb->get_var( $sql );

    $results = $wpdb->get_results("SELECT * FROM 'tid_trustid_activations'");
    foreach ($results as $result) {
         echo '<p>' .$result->id. '</p>';
    }

//---------------------------------------------------------------------------------
//  Start session and check if session data needs to be retrieved
//---------------------------------------------------------------------------------

	// if (empty($_POST['searchField'])&& empty($_POST['perPage']))
	// { //This must be a new page, so check if there's any session data to carry across from previous page
	// 	if (!empty($_GET[registrationkey]))
	// 	{ // retrieve GET data
	// 		$_POST['searchField'] = "registrationKey";
	// 		$_POST['searchTerm'] = $_GET[registrationkey];
	// 	}
	// 	else if (!empty($_SESSION['saSearchField']))
	// 	{ //retrieve session data
	// 		$_POST['searchField'] = $_SESSION['saSearchField'];
	// 		if (isset($_SESSION['saSearchTerm']))
	// 			$_POST['searchTerm'] = $_SESSION['saSearchTerm'];
	// 		if (isset($_SESSION['saFrom']))
	// 			$_POST['from'] = $_SESSION['saFrom'];
	// 		if (isset($_SESSION['saTo']))
	// 			$_POST['to'] = $_SESSION['saTo'];
	// 	}
  //
	// 	if (!empty($_SESSION['saHiddenColumns']))
	// 	{
	// 		$hiddenColumns = $_SESSION['saHiddenColumns'];
	// 		if(!empty($_POST['hideColumn']))
	// 		{
	// 			foreach($_POST['hideColumn'] as $col)
	// 				if(!array_search($col, $hiddenColumns))
	// 					$hiddenColumns[] = $col;
	// 			$_SESSION['saHiddenColumns'] = $hiddenColumns;
	// 		}
	// 	}
	// 	else
	// 		if (empty($_SESSION['saPerPage']))
	// 		{	//There's definitely no session data, so set initial view columns (all except requestCode and responseCode)
	// 			$hiddenColumns = array("requestCode","responseCode");
	// 			$_SESSION['saHiddenColumns'] = $hiddenColumns;
	// 		}
	// 	if (!empty($_SESSION['saPerPage']))
	// 		$_POST['perPage'] = $_SESSION['saPerPage'];
	// 	else
	// 		$_POST['perPage'] = "50"; //There's no session data so set default rows per page at 50
	// }
	// else
	// {	//if not a new page, update hidden column choices and store session data
	// 	if (!empty($_SESSION['saHiddenColumns']))
	// 		$hiddenColumns = $_SESSION['saHiddenColumns'];
	// 	else
	// 		$hiddenColumns = array();
  //
	// 	if(!empty($_POST['hideColumn']))
	// 	{
	// 		foreach($_POST['hideColumn'] as $col)
	// 			if(array_search($col, $hiddenColumns) === FALSE)
	// 				$hiddenColumns[] = $col;
	// 		$_SESSION['saHiddenColumns'] = $hiddenColumns;
	// 	}
  //
	// 	$_SESSION['saSearchField'] = $_POST['searchField'];
	// 	$_SESSION['saPerPage'] = $_POST['perPage'];
	// }

//---------------------------------------------------------------------------------
//  Determine Search criteria
//---------------------------------------------------------------------------------

	// set initial date range
	// $from = "0" ;
	// $to = time();

	// If a search field is selected, set appropriate search values
	// if(!empty($_POST['searchField']))
	// {
	// 	$searchField = $_POST['searchField'];
	// 	switch ($searchField){
	// 		case "removesearch":
	// 			unset($_POST['searchField']);
	// 			unset($_SESSION['saSearchField']);// clear this session data
	// 			unset($searchField);
	// 			unset($_POST['searchTerm']);
	// 			unset($_SESSION['saSearchTerm']); // clear this session data
	// 			unset($_POST['from']);
	// 			unset($_SESSION['saFrom']); // clear this session data
	// 			unset($_POST['to']);
	// 			unset($_SESSION['saTo']); // clear this session data
	// 			break;
  //
	// 		case "activated":
	// 			unset($_POST['searchTerm']);
	// 			unset($_SESSION['saSearchTerm']); // clear this session data
	// 			if (!empty($_POST['from']))
	// 			{
	// 				$from = strtotime($_POST['from']);
	// 				$_SESSION['saFrom'] = $_POST['from']; // store session data
	// 			}
	// 			if (!empty($_POST['to']) && strtotime($_POST['to']))
	// 			{
	// 				$to = strtotime($_POST['to']." +1 day");
	// 				$_SESSION['saTo'] = $_POST['to']; // store session data
	// 			}
	// 			break;
  //
	// 		case "registrationKey":
	// 			unset($_POST['from']);
	// 			unset($_SESSION['saFrom']); // clear this session data
	// 			unset($_POST['to']);
	// 			unset($_SESSION['saTo']); // clear this session data
	// 			if (isset($_POST['searchTerm']))
	// 			{
	// 				//make input easier ... remove all spaces and non-digit characters, then add "-"s
	// 				$str = preg_replace('/\D/', '', $_POST['searchTerm']);
	// 				$str = chunk_split($str,4,"-");
	// 				// limit string to 19 characters and add search wildcards
	// 				$searchTerm = "%".substr($str,0,19)."%";
	// 				$_SESSION['saSearchTerm'] = $_POST['searchTerm']; // store session data
	// 			}
	// 			break;
  //
	// 		default:
	// 			unset($_POST['from']);
	// 			unset($_SESSION['saFrom']); // clear this session data
	// 			unset($_POST['to']);
	// 			unset($_SESSION['saTo']); // clear this session data
	// 			if (isset($_POST['searchTerm']))
	// 			{
	// 				$searchTerm = "%".$_POST['searchTerm']."%";
	// 				$_SESSION['saSearchTerm'] = $_POST['searchTerm']; // store session data
	// 			}
	// 	}
	// }

//---------------------------------------------------------------------------------
//  Retrieve data from database (filtered by search criteria) into a temporary table
//  Then set columns to display
//---------------------------------------------------------------------------------

	//Set the selection criteria for the database SELECT query
  //   if (!empty($searchField) && !empty($searchTerm))
	// 	$writeSearchField = 'WHERE '.$searchField.' LIKE %s AND a.activated BETWEEN %d AND %d';
	// else
	// {
		// $writeSearchField = 'WHERE a.id LIKE %s AND a.activated BETWEEN %d AND %d';
		// $searchTerm = "%%";
	// }

	//Prepare and do the database query
// 	if (!empty($writeSearchField) && !empty($searchTerm))
// 	{
//         //Set what info the user has rights to view
// 		$restricted_info = "";
//
//         if (current_user_can("view_PII"))
// 		{
// 			$columns=array(
//                 'id'=>__('Rec ID'),
//                 'registrationKey'=>__('Activation key'),
// 				'registrationId'=>__('Key ID No'),
//                 'fname'=>__('First name'),
//                 'lname'=>__('Last name'),
//                 'email'=>__('Email address'),
//                 'cname'=>__('Company'),
//                 'country'=>__('Country'),
//                 'ip'=>__('IP address'),
//                 'installed'=>__('Installed date/time'),
//                 'activated'=>__('Activated date/time'),
//                 'requestCode'=>__('Request code'),
//                 'responseCode'=>__('Response code'),
// 				'manualActivate'=>__('Activation method'),
//                 'state'=>__('Status')
//             );
// 			$restricted_info = "a.email,";
// 		}
// 		else
// 		{
// 			$columns = array(
//                 'id'=>__('Rec ID'),
//                 'registrationKey'=>__('Activation key'),
// 				'registrationId'=>__('Key ID No'),
//                 'fname'=>__('First name'),
//                 'lname'=>__('Last name'),
//                 'cname'=>__('Company'),
//                 'country'=>__('Country'),
//                 'ip'=>__('IP address'),
//                 'installed'=>__('Installed date/time'),
// 				'activated'=>__('Activated date/time'),
//                 'requestCode'=>__('Request code'),
//                 'responseCode'=>__('Response code'),
// 				'manualActivate'=>__('Activation method'),
//                 'state'=>__('Status')
//             );
// 		}
// }
		//Do the SELECT query
    $from = "0" ;
    $to = time();
    $writeSearchField = 'WHERE a.id LIKE %s AND a.activated BETWEEN %d AND %d';
		$searchTerm = "%%";

    $display_items = $wpdb->get_results($wpdb->prepare("SELECT
		a.id,
		CASE
			WHEN a.registrationId > 0 THEN k.registrationKey
			ELSE ''
		END AS registrationKey,
		a.registrationId,
		a.fname,
		a.lname,
		-- $restricted_info
    a.email,
		a.cname,
		a.country,
		a.ip,
		a.installed,
		FROM_UNIXTIME(a.activated) AS activated,
		a.requestCode,
		a.responseCode,
		CASE
			WHEN a.manualActivate = 1 THEN 'manual'
			WHEN a.manualActivate = 0 THEN 'automatic'
			else a.manualActivate
		END AS manualActivate,
		CASE
			WHEN a.state = 0 THEN 'Success'
			WHEN a.state = 1 THEN 'Server error'
			WHEN a.state = 2 THEN 'Invalid request'
			WHEN a.state = 3 THEN 'Activation denied'
			else a.state
		END AS state
		FROM $table_name_activations a
		LEFT JOIN $table_name k
			ON (k.id = a.registrationId)
		$writeSearchField",$searchTerm,$from,$to), ARRAY_A);

    echo "{$writeSearchField} - search: {$searchTerm} from: {$from} To: {$to}";


	//Tidy up the hidden columns
	// if($_POST['showAll'] == "show all")
	// {
	// 	$hiddenColumns = array();
	// 	unset($_SESSION['saHiddenColumns']);
	// 	unset($_POST['showAll']);
	// }
	// else
	// {
	// 	if(!isset($hiddenColumns))
	// 		$hiddenColumns = array();
	// }

	// session_write_close(); //Releases the session (but doesn't clear it).

//---------------------------------------------------------------------------------
//  Export and Page housekeeping
//---------------------------------------------------------------------------------

    // if (isset($_GET['download']) || isset($_POST['download']))
    // {
    //     tid_do_csv($display_items,"trustid-activations");
    //     exit;
    // }
    //
    // // We need to reconstruct the url to ensure we don't lose paging and ordering information from the url
    // $url = "/wp-admin/admin.php?";
    //
    // foreach($_GET as $key=>$value)
    // {
		// if (strpos($key,"search") === false)
		// {
		// 	if($key == "page")
		// 		$url.= urlencode($key)."=".urlencode($value);
		// 	else
		// 		if($key !== "paged")
		// 			$url.= "&".urlencode($key)."=".urlencode($value);
		// }
    // }

	//Set destination page for one-click search on Activation Key (the variable is used in List-Helper)
	// $searchUrl = $plugins_url."admin.php?page=tid_all_keys&registrationkey=";

//---------------------------------------------------------------------------------
//  Create the form
//---------------------------------------------------------------------------------
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<div class="wrap">
    <div id="icon-tools" class="icon32"></div><h2>Software Activations <a class="add-new-h2" href="?page=<?=$_GET['page']?>&amp;download=true" target="_blank">Export activations to CSV</a></h2>
</div>
<div class="wrap">

    <form id="search_activations" method="POST" action="<?php echo $url; ?>">

	<style type="text/css">
		#hideColumns {
		color: blue;
		text-decoration: underline;
		background-color: transparent;
		border:0;
		}

		#hideColumns:hover{
		color:red;
		font-style: italic;
		}

		#showAll {
		color: blue;
		text-decoration: underline;
		background-color: transparent;
		border:0;
		}

		#showAll:hover{
		color:red;
		font-style: italic;
		}
	</style>

	<div id="SearchArea">
		<label for="searchField">Search for a </label><select id="searchField" name="searchField" onchange="addSearchTermField(this.value);">
			<?php
			//Create options for the search selection box
			if(empty($_POST['searchField']))
				echo '<option value="">select...</option>';
			else
				echo '<option value="removesearch">Remove search?</option>';
			?>
			<option value="registrationKeyk" <?php if($_POST['searchField'] == "registrationKeyk") echo 'selected';?> >Activation Key</option>
			<option value="registrationId" <?php if($_POST['searchField'] == "registrationId") echo 'selected';?> >Key ID No</option>
			<option value="fname" <?php if($_POST['searchField'] == "fname") echo 'selected';?> >First Name</option>
			<option value="lname" <?php if($_POST['searchField'] == "lname") echo 'selected';?> >Last Name</option>
			<?php if($restricted_info == "a.email,") ?>
				<option value="email" <?php if($_POST['searchField'] == "email") echo 'selected';?> >Email Address</option>
			<option value="cname" <?php if($_POST['searchField'] == "cname") echo 'selected';?> >Company</option>
			<option value="country" <?php if($_POST['searchField'] == "country") echo 'selected';?> >Country</option>
			<option value="ip" <?php if($_POST['searchField'] == "ip") echo 'selected';?> >IP Address</option>
			<option value="activated" <?php if($_POST['searchField'] == "activated") echo 'selected';?> >Activated Date/Time</option>
		</select>  <?php

		// create a either a single search box or two boxes (for a term or a date range search)
		if(!empty($_POST['searchField']))
		{
			?> <OUTPUT id="searchFilter"> <?php
			if($_POST['searchField'] != "activated")
			{
				unset($_POST['from']);
				unset($_POST['to']);
				?><label for="searchTerm"> containing ... </label><input type="text" id="searchTerm" name="searchTerm" <?php if (isset($_POST['searchTerm'])) echo " value='".$_POST['searchTerm']."'";
			}
			else
			{
				unset($_POST['searchTerm']);
				?><label for="from">.... activated between </label><input type="text" class="date" id="from" name="from" <?php if(!empty($_POST['from'])) echo "value='".$_POST['from']."'";?> > &nbsp;
				<label for="to">and </label><input type="text" class="date" name="to" <?php if(!empty($_POST['to'])) echo "value='".$_POST['to']."'";
			}
			?> > &nbsp;&nbsp;&nbsp;&nbsp;
			<input type='button' onclick='submitSearch()' value='Show results'> &nbsp;
			</OUTPUT>
			<?php
		} ?>

	</div>
	<br>


	<?php
    // $display_table = new Generic_Admin_List_Table(
    //     $display_items          // items
    //     , array()               // wp-admin-table settings (empty for defaults)
    //     , array(
    //         'top' => '',
    //         'columns' => $columns,
    //         'per_page' => $_POST['perPage'],
    //         'bottom' => '',
		// 	'searchUrl' => $searchUrl,
    //     )
		// ,$hiddenColumns
    // );
    //
    //
    // //Prepare Table of elements and render
    // $display_table->prepare_items()->display();
    ?>

    <?php
      print_r($_SESSION);
      print_r($display_items);
      // if( ! class_exists( 'WP_List_Table' ) ) {
      //   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
      // }
      // class Activations_Table extends WP_List_Table { // See tutorial https://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
      //
      //   public function __construct() {
      //     parent::__construct( [
      //       'singular' => 'Activation',
      //       'plural'   => 'Activationss',
      //       'ajax'     => false
      //     ] );
      //   }
      //
      //   function get_columns() {
      //     return $columns = array('id'=>__('Rec ID'),
      //       'registrationKey'=>__('Activation key'),
      //       'registrationId'=>__('Key ID No'),
      //       'fname'=>__('First name'),
      //       'lname'=>__('Last name'),
      //       'email'=>__('Email address'),
      //       'cname'=>__('Company'),
      //       'country'=>__('Country'),
      //       'ip'=>__('IP address'),
      //       'installed'=>__('Installed date/time'),
      //       'activated'=>__('Activated date/time'),
      //       'requestCode'=>__('Request code'),
      //       'responseCode'=>__('Response code'),
      //       'manualActivate'=>__('Activation method'),
      //       'state'=>__('Status')
      //      );
      //   }
      //
      //   function prepare_items() {
      //     global $wpdb, $_wp_column_headers;
      //     $screen = get_current_screen();
      //     /* -- Preparing your query -- */
      //     $query = "SELECT * FROM {$wpdb->prefix}trustid_activations";
      //     /* -- Ordering parameters -- */
      //     //Parameters that are going to be used to order the result
      //     $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
      //     $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
      //     if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
      //     /* -- Pagination parameters -- */
      //     //Number of elements in your table?
      //     $totalitems = $wpdb->query($query); //return the total number of affected rows
      //     //How many to display per page?
      //     $perpage = 5;
      //     //Which page is this?
      //     $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
      //     //Page Number
      //     if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; } //How many pages do we have in total?
      //     $totalpages = ceil($totalitems/$perpage); //adjust the query to take pagination into account
      //     if(!empty($paged) && !empty($perpage)){ $offset=($paged-1)*$perpage; $query.=' LIMIT '.(int)$offset.','.(int)$perpage; } /* -- Register the pagination -- */
      //     $this->set_pagination_args( array(
      //      "total_items" => $totalitems,
      //      "total_pages" => $totalpages,
      //      "per_page" => $perpage,
      //     ) );
      //     //The pagination links are automatically built according to those parameters
      //     /* -- Register the Columns -- */
      //     $columns = $this->get_columns();
      //     $_wp_column_headers[$screen->id]=$columns;
      //     /* -- Fetch the items -- */
      //     $this->items = $wpdb->get_results($query);
      //   }
      //
      //   function display_rows() {
      //     //Get the records registered in the prepare_items method
      //     $records = $this->items;
      //     //Get the columns registered in the get_columns and get_sortable_columns methods
      //     list( $columns, $hidden ) = $this->get_column_info();
      //     //Loop for each record
      //     if(!empty($records)) {
      //       foreach($records as $rec) {
      //         //Open the line
      //         echo '< tr id="record_'.$rec->id.'">';
      //         foreach ( $columns as $column_name => $column_display_name ) {
      //           //Style attributes for each col
      //           $class = "class='$column_name column-$column_name'";
      //           $style = "";
      //           if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
      //           $attributes = $class . $style;
      //           //edit link
      //           $editlink  = '/wp-admin/link.php?action=edit&link_id='.(int)$rec->id;
      //           //Display the cell
      //           switch ( $column_name ) {
      //             case "id": echo '<td '.$attribute.'>'.stripslashes($rec->id).'</td>'; break;
      //             case "registrationKey": echo '<td '.$attribute.'>'.stripslashes($rec->registrationKey).'</td>'; break;
      //             case "registrationId": echo '<td '.$attribute.'>'.stripslashes($rec->registrationId).'</td>'; break;
      //             // case "col_link_id":  echo '< td '.$attributes.'>'.stripslashes($rec->link_id).'< /td>';   break;
      //             // case "col_link_name": echo '< td '.$attributes.'>'.stripslashes($rec->link_name).'< /td>'; break;
      //             // case "col_link_url": echo '< td '.$attributes.'>'.stripslashes($rec->link_url).'< /td>'; break;
      //             // case "col_link_description": echo '< td '.$attributes.'>'.$rec->link_description.'< /td>'; break;
      //             // case "col_link_visible": echo '< td '.$attributes.'>'.$rec->link_visible.'< /td>'; break;
      //           }
      //         }
      //       //Close the line
      //       echo'< /tr>';
      //       }
      //     }
      //   }
      //
      // } // Close Activation table class
      //
      // $ReviewsTable = new Activations_Table();
      // $ReviewsTable->prepare_items();
      // $ReviewsTable->display();
    ?>
    <br>&nbsp;<br>



<!--
    <script type="text/javascript">

    jQuery(document).ready(function() {
		jQuery('.date').datepicker({
		dateFormat : 'yy-mm-dd'
		});
    });

	function addSearchTermField(searchVal){
		if ($('#searchTerm').length > 0 || $('#from').length > 0){  // if the searchTerm fields exist, remove them.
			$("#searchFilter").remove();
			if(searchVal == "removesearch"){
				submitSearch();
			}
		}
		if(searchVal != "" && searchVal != "removesearch"){
			var searchFilter = document.createElement('OUTPUT');
			$(searchFilter).attr({'id': 'searchFilter'});
			if(searchVal == "activated"){
				searchFilter.innerHTML = "<label for='from'>.... activated between </label><input type='text' class='date' id='from' name='from' > &nbsp;<label for='to'>and </label><input type='text' class='date' name='to' > &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' onclick='submitSearch()' value='Show results'>";
				$('#searchField').after(searchFilter);
				jQuery('.date').datepicker({
					dateFormat : 'yy-mm-dd'
				});
				$('#from').focus();
			}
			else{
				searchFilter.innerHTML = "<font for='searchTerm' id='txt2' > containing ... </font> <input type='text' id='searchTerm' name='searchTerm'> <font for='searchTerm' id='txt2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font> <input type='button' onclick='submitSearch()' value='Show results'>";
				$('#searchField').after(searchFilter);
				$('#searchTerm').focus();
			}
		}
	}

	function submitSearch(){
		document.getElementById("search_activations").elements.namedItem("paged").value = 1;
		document.getElementById("search_activations").submit();
	}
    </script> -->

	</form>

</div>
