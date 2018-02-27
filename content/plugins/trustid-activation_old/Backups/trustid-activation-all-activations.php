<?php

    global $wpdb, $table_name_activations, $table_name;
    date_default_timezone_set('UTC');
	
//---------------------------------------------------------------------------------
//  Start session and check if session data needs to be retrieved
//---------------------------------------------------------------------------------

	if (!session_id())
		session_start();
	
	if (empty($_POST['searchField'])&& empty($_POST['perPage']))
	{ //This must be a new page, so check if there's any session data to carry across from previous page
		if (!empty($_SESSION['saSearchField']))
		{ //retrieve session data
			$_POST['searchField'] = $_SESSION['saSearchField'];
			if (isset($_SESSION['saSearchTerm']))
				$_POST['searchTerm'] = $_SESSION['saSearchTerm'];
			if (isset($_SESSION['saFrom']))
				$_POST['from'] = $_SESSION['saFrom'];
			if (isset($_SESSION['saTo']))
				$_POST['to'] = $_SESSION['saTo'];
		}
		
		if (!empty($_SESSION['saHiddenColumns']))
		{
			$hiddenColumns = $_SESSION['saHiddenColumns'];
			if(!empty($_POST['hideColumn']))
			{
				foreach($_POST['hideColumn'] as $col)
					if(!array_search($col, $hiddenColumns))
						$hiddenColumns[] = $col;
				$_SESSION['saHiddenColumns'] = $hiddenColumns;
			}
		}
		else
			if (empty($_SESSION['saPerPage']))
			{	//There's definitely no session data, so set initial view columns (all except requestCode and responseCode)	
				$hiddenColumns = array("requestCode","responseCode");
				$_SESSION['saHiddenColumns'] = $hiddenColumns;
			}
		if (!empty($_SESSION['saPerPage']))
			$_POST['perPage'] = $_SESSION['saPerPage'];
		else  
			$_POST['perPage'] = "50"; //There's no session data so set default rows per page at 50
	}
	else
	{	//if not a new page, update hidden column choices and store session data
		if (!empty($_SESSION['saHiddenColumns']))
			$hiddenColumns = $_SESSION['saHiddenColumns'];
		else
			$hiddenColumns = array();
		
		if(!empty($_POST['hideColumn']))
		{
			foreach($_POST['hideColumn'] as $col)
				if(array_search($col, $hiddenColumns) === FALSE)
					$hiddenColumns[] = $col;
			$_SESSION['saHiddenColumns'] = $hiddenColumns;
		}
		
		$_SESSION['saSearchField'] = $_POST['searchField'];
		$_SESSION['saPerPage'] = $_POST['perPage'];
	}

//---------------------------------------------------------------------------------	
//  Determine Search criteria
//---------------------------------------------------------------------------------
	
	// set initial date range
	$from = "0" ;
	$to = time();
	
	// If a search field is selected, set appropriate search values
	if(!empty($_POST['searchField']))
	{
		$searchField = $_POST['searchField'];
		switch ($searchField){
			case "removesearch":
				unset($_POST['searchField']);
				unset($_SESSION['saSearchField']);// clear this session data
				unset($searchField);
				unset($_POST['searchTerm']);
				unset($_SESSION['saSearchTerm']); // clear this session data
				unset($_POST['from']);
				unset($_SESSION['saFrom']); // clear this session data
				unset($_POST['to']);
				unset($_SESSION['saTo']); // clear this session data
				break;
		
			case "activated":
				unset($_POST['searchTerm']);
				unset($_SESSION['saSearchTerm']); // clear this session data
				if (!empty($_POST['from']))
				{
					$from = strtotime($_POST['from']);
					$_SESSION['saFrom'] = $_POST['from']; // store session data
				}
				if (!empty($_POST['to']) && strtotime($_POST['to']))
				{
					$to = strtotime($_POST['to']." +1 day");
					$_SESSION['saTo'] = $_POST['to']; // store session data
				}
				break;
			
			default:
				unset($_POST['from']);
				unset($_SESSION['saFrom']); // clear this session data
				unset($_POST['to']);
				unset($_SESSION['saTo']); // clear this session data
				if (isset($_POST['searchTerm'])) 
				{
					$searchTerm = "%".$_POST['searchTerm']."%";
					$_SESSION['saSearchTerm'] = $_POST['searchTerm']; // store session data
				}
		}
	}
	
//---------------------------------------------------------------------------------
//  Retrieve data from database (filtered by search criteria) into a temporary table
//  Then set columns to display
//---------------------------------------------------------------------------------
	
	//Set the selection criteria for the database SELECT query
    if (!empty($searchField) && !empty($searchTerm))
		$writeSearchField = 'WHERE '.$searchField.' LIKE %s AND a.activated BETWEEN %d AND %d'; 
	else
	{
		$writeSearchField = 'WHERE a.id LIKE %s AND a.activated BETWEEN %d AND %d';
		$searchTerm = "%%";
	}
	
	//Prepare and do the database query
	if (!empty($writeSearchField) && !empty($searchTerm))
	{
        //Set what info the user has rights to view
		$restricted_info = "";
		
        if (current_user_can("view_PII"))
		{
			$columns=array(
                'id'=>__('Rec ID'),
                'registrationKey'=>__('Activation key'),
				'registrationId'=>__('Key ID No'),
                'fname'=>__('First name'),
                'lname'=>__('Last name'),
                'email'=>__('Email address'),
                'cname'=>__('Company'),
                'country'=>__('Country'),
                'ip'=>__('IP address'),
                'installed'=>__('Installed date/time'),
                'activated'=>__('Activated date/time'),
                'requestCode'=>__('Request code'),
                'responseCode'=>__('Response code'),
				'manualActivate'=>__('Activation method'),
                'state'=>__('Status')
            );
			$restricted_info = "a.email,";
		}
		else
		{
			$columns = array(
                'id'=>__('Rec ID'),
                'registrationKey'=>__('Activation key'),
				'registrationId'=>__('Key ID No'),
                'fname'=>__('First name'),
                'lname'=>__('Last name'),
                'cname'=>__('Company'),
                'country'=>__('Country'),
                'ip'=>__('IP address'),
                'installed'=>__('Installed date/time'),
				'activated'=>__('Activated date/time'),
                'requestCode'=>__('Request code'),
                'responseCode'=>__('Response code'),
				'manualActivate'=>__('Activation method'),
                'state'=>__('Status')
            );
		}
		
		//Do the SELECT query
        $display_items = $wpdb->get_results($wpdb->prepare("SELECT
		a.id,
		CASE
			WHEN a.registrationId > 0 THEN k.registrationKey
			ELSE ''
		END AS registrationKey,
		a.registrationId,
		a.fname,
		a.lname,
		$restricted_info
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
    }
	
	//Tidy up the hidden columns
	if($_POST['showAll'] == "show all")
	{
		$hiddenColumns = array();
		unset($_SESSION['saHiddenColumns']);
		unset($_POST['showAll']);
	}
	else
	{
		if(!isset($hiddenColumns))
			$hiddenColumns = array();
	}
	
	session_write_close(); //Releases the session (but doesn't clear it).

//---------------------------------------------------------------------------------
//  Export and Page housekeeping
//---------------------------------------------------------------------------------
		
    if (isset($_GET['download']) || isset($_POST['download']))
    {
        tid_do_csv($display_items,"trustid-activations");
        exit;
    }

    // We need to reconstruct the url to ensure we don't lose paging and ordering information from the url
    $url = "/wp-admin/admin.php?";
	
    foreach($_GET as $key=>$value)
    {
		if (strpos($key,"search") === false)
		{
			if($key == "page")
				$url.= urlencode($key)."=".urlencode($value);
			else
				if($key !== "paged")
					$url.= "&".urlencode($key)."=".urlencode($value);
		}
    }
	
//---------------------------------------------------------------------------------
//  Create the form
//---------------------------------------------------------------------------------
?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

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
    $display_table = new Generic_Admin_List_Table(
        $display_items          // items
        , array()               // wp-admin-table settings (empty for defaults)
        , array(
            'top' => '',
            'columns' => $columns,
            'per_page' => $_POST['perPage'],
            'bottom' => '',
        )
		,$hiddenColumns
    );
	
	
    //Prepare Table of elements and render
    $display_table->prepare_items()->display();
    ?>
    <br>&nbsp;<br>
	
	
	
	
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
    </script>

	</form>

</div>
