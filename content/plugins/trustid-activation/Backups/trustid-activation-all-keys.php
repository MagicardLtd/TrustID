<?php
    global $wpdb, $table_name;
	date_default_timezone_set('UTC');

    if (!empty($_GET['id']) && isset($_GET['status']))
    {
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name SET active=%d WHERE id=%d",
                $_GET['status'],
                $_GET['id']
            )
        );
    }

//---------------------------------------------------------------------------------
//  Start session and check if session data needs to be retrieved
//---------------------------------------------------------------------------------

	if (!session_id())
		session_start();
	
	if (empty($_POST['searchField'])&& empty($_POST['perPage']))
	{ //This must be a new page, so check if there's any session data to carry across from previous page
		if (!empty($_SESSION['skSearchField']))
		{ //retrieve session data
			$_POST['searchField'] = $_SESSION['skSearchField'];
			if (isset($_SESSION['skSearchTerm']))
				$_POST['searchTerm'] = $_SESSION['skSearchTerm'];
			if (isset($_SESSION['skFrom']))
				$_POST['from'] = $_SESSION['skFrom'];
			if (isset($_SESSION['skTo']))
				$_POST['to'] = $_SESSION['skTo'];
		}
		
		if (!empty($_SESSION['skHiddenColumns']))
		{
			$hiddenColumns = $_SESSION['skHiddenColumns'];
			if(!empty($_POST['hideColumn']))
			{
				foreach($_POST['hideColumn'] as $col)
					if(!array_search($col, $hiddenColumns))
						$hiddenColumns[] = $col;
				$_SESSION['skHiddenColumns'] = $hiddenColumns;
			}
		}
		else
			if (empty($_SESSION['skPerPage']))
			{	//There's definitely no session data, so set default viewable columns (all except upgradeId, brandId and copyId)
				$hiddenColumns = array("upgradeId","brandId","copyId");
				$_SESSION['skHiddenColumns'] = $hiddenColumns;
			}
		if (!empty($_SESSION['skPerPage']))
			$_POST['perPage'] = $_SESSION['skPerPage'];
		else  
			$_POST['perPage'] = "50"; //There's no session data so set default rows per page at 50
	}
	else
	{	//if not a new page, update hidden column choices and store session data
		if (!empty($_SESSION['skHiddenColumns']))
			$hiddenColumns = $_SESSION['skHiddenColumns'];
		else
			$hiddenColumns = array();
		
		if(!empty($_POST['hideColumn']))
		{
			foreach($_POST['hideColumn'] as $col)
				if(array_search($col, $hiddenColumns) === FALSE)
					$hiddenColumns[] = $col;
			$_SESSION['skHiddenColumns'] = $hiddenColumns;
		}

		$_SESSION['skSearchField'] = $_POST['searchField'];
		$_SESSION['skPerPage'] = $_POST['perPage'];
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
				unset($_SESSION['skSearchField']);// clear this session data
				unset($searchField);
				unset($_POST['searchTerm']);
				unset($_SESSION['skSearchTerm']); // clear this session data
				unset($_POST['from']);
				unset($_SESSION['skFrom']); // clear this session data
				unset($_POST['to']);
				unset($_SESSION['skTo']); // clear this session data
				break;
		
			case "issued":
				unset($_POST['searchTerm']);
				unset($_SESSION['skSearchTerm']); // clear this session data
				if (!empty($_POST['from']))
				{
					$from = strtotime($_POST['from']);
					$_SESSION['skFrom'] = $_POST['from']; // store session data
				}
				if (!empty($_POST['to']) && strtotime($_POST['to']))
				{
					$to = strtotime($_POST['to']." +1 day");
					$_SESSION['skTo'] = $_POST['to']; // store session data
				}
				break;
			
			default:
				unset($_POST['from']);
				unset($_SESSION['skFrom']); // clear this session data
				unset($_POST['to']);
				unset($_SESSION['skTo']); // clear this session data
				if (isset($_POST['searchTerm'])) 
				{
					$searchTerm = "%".$_POST['searchTerm']."%";
					$_SESSION['skSearchTerm'] = $_POST['searchTerm']; // store session data
				}
		}
	}

//---------------------------------------------------------------------------------
//  Retrieve data from database (filtered by search criteria) into a temporary table
//  Then set columns to display
//---------------------------------------------------------------------------------
	
	//Set the selection criteria for the database SELECT query
    if (!empty($searchField) && !empty($searchTerm))
		$writeSearchField = 'WHERE '.$searchField.' LIKE %s AND issued BETWEEN %d AND %d'; 
	else
	{
		$writeSearchField = 'WHERE id LIKE %s AND issued BETWEEN %d AND %d';
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
				'distributor'=>__('Distributor'),
                'purpose'=>__('Purpose'),
                'issued'=>__('Issued Date/Time'),
                'brandId'=>__('Brand'),
                'editionId'=>__('Edition'),
                'upgradeKey'=>__('Upgrade Key?'),
                'upgradeFromKey'=>__('Upgraded From Key'),
                'upgradeId'=>__('Version'),
                'copyId'=>__('Copy ID'),
                'active'=>__('Status'),
				'edit'=>__('Edit')
            );
			$restricted_info = "distributor,";
		}
		else
		{
			$columns = array(
                'id'=>__('Rec ID'),
                'registrationKey'=>__('Activation key'),
				'distributor'=>__('Distributor'),
                'purpose'=>__('Purpose'),
                'issued'=>__('Issued Date/Time'),
                'brandId'=>__('Brand'),
                'editionId'=>__('Edition'),
                'upgradeKey'=>__('Upgrade Key?'),
                'upgradeFromKey'=>__('Upgraded From Key'),
				'upgradeId'=>__('Version'),
                'copyId'=>__('Copy ID'),
                'active'=>__('Status')
            );
			$restricted_info = "distributor,"; //Decided to 'un-restrict' distributor, so applied it to both column sets
		}
	
		//Do the SELECT query
		$items = $wpdb->get_results($wpdb->prepare("SELECT
		id,
		registrationKey,
		$restricted_info
		CASE
			WHEN purpose = 0 THEN 'Sale'
			WHEN purpose = 1 THEN 'Dealer Demo'
			ELSE 'Unknown'
		END AS purpose,
		FROM_UNIXTIME(issued) AS issued,
		CASE
			WHEN brandId = 0 THEN 'ID Maker'
			WHEN brandId = 1 THEN 'Trust ID'
			ELSE 'Unknown'
		END AS brandId,
		CASE
			WHEN editionId = 0 THEN 'Classic'
			WHEN editionId = 1 THEN 'Premium'
			WHEN editionId = 2 THEN 'Pro'
			WHEN editionId = 3 THEN 'Pro Smart'
			ELSE 'Unknown'
		END AS editionId,
		CASE
			WHEN upgradeKey = 0 THEN 'No'
			WHEN upgradeKey = 1 THEN 'Yes'
			ELSE 'Unknown'
		END AS upgradeKey,
		upgradeFromKey,
		upgradeId,
		copyId,
		CASE
			WHEN active = 1 THEN 'Usable'
			ELSE 'Blocked'
		END AS active
		FROM $table_name
		$writeSearchField",$searchTerm,$from,$to), ARRAY_A);
	}
	
	//If user has the correct rights, add a column to enable Allow/Block Activation
	if (!empty($items) && current_user_can("view_PII"))
    {
        // We need to reconstruct the url to ensure we don't lose paging and ordering information from the url
        $url = "/wp-admin/admin.php?";
        foreach($_GET as $key=>$value)
        {
            if ($key != "id" && $key != "status")
                $url.= "&".urlencode($key)."=".urlencode($value);
        }

        foreach($items as $item)
        {
            if ($item['active'] == 'Usable')
                $item['edit'] = "<a href='$url&id={$item['id']}&status=0'><font color='red'>Block Activation</font></a>";
            else
                $item['edit'] = "<a href='$url&id={$item['id']}&status=1'><font color='blue'>Allow Activation</font></a>";

            $display_items[] = $item;
        }
    }
    else
        $display_items = $items;

	
    global $displayKeys;
    if (isset($displayKeys))
        include('trustid-activation-keys.php');
	
	
	//Tidy up the hidden columns
	if($_POST['showAll'] == "show all")
	{
		$hiddenColumns = array();
		unset($_SESSION['skHiddenColumns']);
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
        tid_do_csv($items,"trustid-keys");
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
    <div id="icon-tools" class="icon32"></div><h2>Activation Keys <a class="add-new-h2" href="?page=<?=$_GET['page']?>&amp;download=true" target="_blank">Export all keys to CSV</a></h2>
</div>
<div class="wrap">

	<form id="search_keys" method="POST" action="<?php echo $url?>">

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
			//Create options for the search select box
			if(empty($_POST['searchField']))
				echo '<option value="">select...</option>';
			else
				echo '<option value="removesearch">Remove search?</option>'; 	
			?>
			<option value="registrationKey" <?php if($_POST['searchField'] == "registrationKey") echo 'selected';?> >Activation Key</option>
			<?php if($restricted_info == "distributor,") ?> 
				<option value="distributor" <?php if($_POST['searchField'] == "distributor") echo 'selected';?> >Distributor</option>
			<option value="purpose" <?php if($_POST['searchField'] == "purpose") echo 'selected';?> >Purpose</option>
			<option value="issued" <?php if($_POST['searchField'] == "issued") echo 'selected';?> >Issued Date/Time</option>
			<option value="editionId" <?php if($_POST['searchField'] == "editionId") echo 'selected';?> >Edition</option>
			<option value="upgradeKey" <?php if($_POST['searchField'] == "upgradeKey") echo 'selected';?> >Upgrade Key?</option>
			<option value="upgradeFromKey" <?php if($_POST['searchField'] == "upgradeFromKey") echo 'selected';?> >Upgraded From Key</option>
			<option value="active" <?php if($_POST['searchField'] == "active") echo 'selected';?> >Status</option>
		</select>
		<?php
		
		// create search box(es) for a selectable option, a date range or a text term search
		if(!empty($_POST['searchField']))
		{
			?> <OUTPUT id="searchFilter"> <?php
			if($_POST['searchField'] == "issued")
			{
				unset($_POST['searchTerm']); ?>
				<label for="from">.... issued between </label>
				<input type="text" class="date" id="from" name="from" <?php if(!empty($_POST['from'])) echo "value='".$_POST['from']."'";?> >
				<label for="to"> and </label>
				<input type="text" class="date" id="to" name="to" <?php if(!empty($_POST['to'])) echo "value='".$_POST['to']."'"; ?> >
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" onclick="submitSearch()" value="Show results"> <?php
			}
			else
			{
				unset($_POST['from']);
				unset($_POST['to']); ?>
				<label for="searchTerm"> containing ... </label> <?php
				
				Switch($_POST['searchField']){
					case "purpose":
						?>
						<select id="searchTerm" name="searchTerm" onchange="submitSearch();">
						<option value="" <?php if(empty($_POST['searchTerm'])) echo "selected";?> ></option>
						<option value="0" <?php if($_POST['searchTerm']=="0") echo "selected";?> >Sale</option>
						<option value="1" <?php if($_POST['searchTerm']=="1") echo "selected";?> >Dealer Demo</option>
						</select> <?php
						break;

					case "editionId"
						?>
						<select id="searchTerm" name="searchTerm" onchange="submitSearch();">
						<option value="" <?php if(empty($_POST['searchTerm'])) echo "selected";?> ></option>
						<option value="0" <?php if($_POST['searchTerm']=="0") echo "selected";?> >Classic</option>
						<option value="1" <?php if($_POST['searchTerm']=="1") echo "selected";?> >Premium</option>
						<option value="2" <?php if($_POST['searchTerm']=="2") echo "selected";?> >Pro</option>
						<option value="3" <?php if($_POST['searchTerm']=="3") echo "selected";?> >Pro-Smart</option>
						</select> <?php
						break;

					case "upgradeKey"
						?>
						<select id="searchTerm" name="searchTerm" onchange="submitSearch();">
						<option value="" <?php if(empty($_POST['searchTerm'])) echo "selected";?> ></option>
						<option value="0" <?php if($_POST['searchTerm']=="0") echo "selected";?> >No</option>
						<option value="1" <?php if($_POST['searchTerm']=="1") echo "selected";?> >Yes</option>
						</select> <?php
						break;

					case "active"
						?>
						<select id="searchTerm" name="searchTerm" onchange="submitSearch();">
						<option value="" <?php if(empty($_POST['searchTerm'])) echo "selected";?> ></option>
						<option value="0" <?php if($_POST['searchTerm']=="0") echo "selected";?> >Blocked</option>
						<option value="1" <?php if($_POST['searchTerm']=="1") echo "selected";?> >Usable</option>
						</select> <?php
						break;

					default:
						?>
						<input class="send" type="text" id="searchTerm" name="searchTerm" <?php if (isset($_POST['searchTerm'])) echo " value='".$_POST['searchTerm']."'";?> >
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" onclick="submitSearch()" value="Show results"> <?php
				}
			} ?>
			</OUTPUT> <?php
		} ?>
		<br><br>
	</div>

	
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
	
    </form>
	
	
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
			if(searchVal == "issued"){
				searchFilter.innerHTML = '<label for="from">.... activated between </label><input type="text" class="date" id="from" name="from" > &nbsp;<label for="to">and </label><input type="text" class="date" name="to" > &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="submitSearch()" value="Show results">';
				$('#searchField').after(searchFilter);
				jQuery('.date').datepicker({
					dateFormat : 'yy-mm-dd'
				});
				$('#from').focus();
			}
			else{
				switch(searchVal){
					case "purpose":
						searchFilter.innerHTML = '<label for="searchTerm"> containing ... </label><select id="searchTerm" name="searchTerm" onchange="submitSearch();"> <option value="" selected ></option> <option value="0" >Sale</option> <option value="1" >Dealer Demo</option> </select>';
						break;

					case "editionId":
						searchFilter.innerHTML = '<label for="searchTerm"> containing ... </label> <select id="searchTerm" name="searchTerm" onchange="submitSearch();"> <option value="" selected ></option> <option value="0" >Classic</option> <option value="1" >Premium</option> <option value="2" >Pro</option> <option value="3" >Pro-Smart</option> </select>';
						break;

					case "upgradeKey":
						searchFilter.innerHTML = '<label for="searchTerm"> containing ... </label> <select id="searchTerm" name="searchTerm" onchange="submitSearch();"> <option value="" selected ></option> <option value="0" >No</option> <option value="1" >Yes</option> </select>';
						break;

					case "active":
						searchFilter.innerHTML = '<label for="searchTerm"> containing ... </label> <select id="searchTerm" name="searchTerm" onchange="submitSearch();"> <option value="" selected ></option> <option value="0" >Blocked</option> <option value="1" >Usable</option> </select>';
						break;

					default:
						searchFilter.innerHTML = '<label for="searchTerm"> containing ... </label> <input class="send" type="text" id="searchTerm" name="searchTerm" > &nbsp;&nbsp;&nbsp;&nbsp; <input type="button" onclick="submitSearch()" value="Show results">';
				}
				
				//searchFilter.innerHTML = "<font for='searchTerm' id='txt2' > containing ... </font> <input type='text' id='searchTerm' //name='searchTerm'> <font for='searchTerm' id='txt2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font> <input type='submit' //id='btn1' name = 'search' value='Show results'>";
				$('#searchField').after(searchFilter);
				$('#searchTerm').focus();
			}
		}
	}

	function submitSearch(){
		document.getElementById("search_keys").elements.namedItem("paged").value = 1;
		document.getElementById("search_keys").submit();
	}
    </script>
	
</div>
	