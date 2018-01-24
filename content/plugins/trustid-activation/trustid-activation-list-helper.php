<?php

###PluginSharedLibrary::include_file(__FILE__, 'debugging.php');

//Our class extends the WP_List_Table class, so we need to make sure that it's there
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Native admin table
 * @source http://wp.smashingmagazine.com/2011/11/03/native-admin-tables-wordpress/
 * Also see this awesome followup: http://wordpress.org/extend/plugins/custom-list-table-example/
 * @example
 * $display_table = new Generic_Admin_List_Table(
 * 	$display_items			// items
 * 	, array()				// wp-admin-table settings
 * 	, array(
 * 		'columns' => array(
 * 			'shortpath'=>__('Include')
 * 			, 'path'=>__('Path')
 * 			, 'source' => __('First Called In')
 * 			, 'required'=>__('Required?')
 * 			, 'once'=>__('Once?')
 * 		)
 *
 * 		//, 'sortable' => array(
 * 		//	'include'=>'shortpath',
 * 		//	'path'=>'path',
 * 		//	'required'=>'required',
 * 		//	'once'=>'once',
 * 		//)
 *
 * 		, 'per_page' => 10
 * 		, 'top' => '<p>These are the files currently being shared.</p>'
 * 	)
 * );
 *
 * //Prepare Table of elements
 * $display_table->prepare_items()->display();
 * ?>
 * @endexample
 */
class Generic_Admin_List_Table extends WP_List_Table {

	private $config;

	/**
	 * Constructor, we override the parent to pass our own arguments
	 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	 * @param array $items the data to display
	 * @param array $properties the default labels and other WP_List_Table properties to override
	 */
	 function __construct($items, $properties = array(), $config = array(), $hiddenCols = array()) {
		$properties = wp_parse_args($properties,  array(
			'singular'=> 'wp_list_text_link', //Singular label
			'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
			'ajax'	=> false //We won't support Ajax for this table
		) );
		 parent::__construct( $properties );

		$this->items = $items;

		$this->config = wp_parse_args($config,  array(
			'top' => ''
			, 'bottom' => ''
			, 'columns' => array()	// display columns
			, 'sortable' => array()	// sortable columns
			, 'per_page' => 50		// how many per pagination
			, 'searchUrl' => $plugins_url."admin.php?page=tid_all_keys&registrationkey="
		));
		
		$this->hiddenCols = $hiddenCols;

		// if sortable not actively denied, default to all columns
		$sortable = &$this->config['sortable'];
		if( false !== $sortable && empty($sortable) ) {
			$sortable = $this->config['columns'];
			foreach($sortable as $column => &$mapping){
				$mapping = array($column);
			}
		}
	 }//--	fn	__construct

	 /**
	 * Add extra markup in the toolbars before or after the list
	 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
	 */
	function extra_tablenav( $which ) {
		$topText = "";
		$hiddenCols = $this->hiddenCols;
		$columns = $this->config['columns'];
		$perPage = $this->config['per_page'];
		
		//Build info to tell the user which columns are hidden
		if(!empty($hiddenCols))
			{
				$topText =  count($hiddenCols)." hidden columns (see bottom of page for details) ".$perPageText;
				$bottomText = "Hidden columns (".count($hiddenCols).") :- ";
				foreach($hiddenCols as $col)
				{
					$bottomText = $bottomText.$columns[$col].'; ';
				}
				unset($col);
				unset($val);
				$bottomText = $bottomText."<br><br> (Hint: to hide columns, select the checkboxes for each unwanted column then click 'Hide')";
			}
			else
			{
				$topText = "(No hidden columns)".$perPageText;
				$bottomText = "(No hidden columns)<br><br> (Hint: to hide columns, select the checkboxes for each unwanted column then click 'Hide')";
			}
		
		if ( $which == "top" ){
			//The code that goes before the table is here
			
			//  Place records per page sector in table header
			$text25 = "";
			$text50 = "";
			$text75 = "";
			$text100 = "";
			switch ($perPage){
				case "25":
					$text25 = "selected";
					break;
		
				case "50":
					$text50 = "selected";
					break;
		
				case "75":
					$text75 = "selected";
					break;
		
				case "100":
					$text100 = "selected";
					break;
		
				default:
					$text50 = "selected";
			}
			$topText = $topText.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<label for="perPage">Records per page: </label><select name="perPage" onchange="submitSearch();">
			<option value="25" '.$text25.' >25</option>
			<option value="50" '.$text50.' >50</option>
			<option value="75" '.$text75.' >75</option>
			<option value="100" '.$text100.' >100</option>
			</select>';

			echo $topText;
			//echo $this->config['top'];
		}
		if ( $which == "bottom" ){
			//The code that goes after the table is there
			
			echo $bottomText;
			//echo $this->config['bottom'];
		}
	}

	/**
	 * Define the columns that are going to be used in the table
	 * @return array $columns, the array of columns to use with the table
	 */
	function get_columns() {
		return $this->config['columns'];
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 * @return array $sortable, the array of columns that can be sorted by the user; given as $ui_column_name => $datasource_column_name
	 */
	public function get_sortable_columns() {
		return $this->config['sortable'];
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function prepare_items() {
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();

		/* -- Ordering parameters -- */
			//Parameters that are going to be used to order the result
			$orderby = !empty($_GET["orderby"]) ? ($_GET["orderby"]) : 'ASC';
			$order = !empty($_GET["order"]) ? ($_GET["order"]) : '';

        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            if (is_numeric($a[$orderby]) && is_numeric($b[$orderby]))
                $result = $a[$orderby] - $b[$orderby];
            else
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        $data = $this->items;
        usort($data, 'usort_reorder');
        $this->items = $data;


		/* -- Pagination parameters -- */
			//Number of elements in your table?
			$totalitems = count($this->items);
			//How many to display per page?
			$perpage = $this->config['per_page'];
			//How many pages do we have in total?
			$totalpages = ceil($totalitems/$perpage);
			//Which page is this?
			$paged = !empty($_GET["paged"]) ? ($_GET["paged"]) : '';
			$paged = $this->get_pagenum();
			//Page Number
			if(empty($paged) || !is_numeric($paged) || $paged<=0 )
				$paged=1;
			else
				if($paged>$totalpages)
					$paged = $totalpages;
			//adjust the query to take pagination into account
			if(!empty($paged) && !empty($perpage)){
				$offset=($paged-1)*$perpage;
			}

		/* -- Register the pagination -- */
			$this->set_pagination_args( array(
				"total_items" => $totalitems,
				"total_pages" => $totalpages,
				"per_page" => $perpage,
			) );
			//The pagination links are automatically built according to those parameters

		/* -- Register the Columns -- */
            $columns = $this->get_columns();
            //$hidden = array();
			$hidden = $this->hiddenCols;
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);

		/* -- Fetch the items -- */
			// items already provided in constructor
			//$this->items = $wpdb->get_results($query);

			// sort items
			///TODO:

			// paginate items
			$this->items = array_slice($this->items, $offset, $perpage);
		
			$searchUrl = $this->config['searchUrl'];
			foreach($this->items as &$record)
				$record[registrationKey] = '<a href="'.$searchUrl.$record[registrationKey].'"><fontcolor="blue">'.$record[registrationKey].'</font></a>';

		return $this; // chaining
	}//--	fn	prep_items

	/**
	 * Display the rows of records in the table
	 * @return string, echo the markup of the rows
	 */
	function display_rows() {

		//Create selection checkboxes for hiding each unhidden column
		$cColumns = $this->config['columns'];
		$selectionRow = array();
		foreach($cColumns as $col=>$val)
		{
			$selectionRow[$col] = '<input type="checkbox" name="hideColumn['.$col.']" value="'.$col.'"/>';
		}
		unset($col);
		unset($val);
		
		$selectionRow[id] = '<input type="submit" id="hideColumns" name="hideColumns" value="Hide" />/<input type="submit" id="showAll" name="showAll" value="show all" />';
		
		//Get the records registered in the prepare_items method
		$records = &$this->items;
		array_unshift($records, $selectionRow);

		###hbug('config', $this->config);

		//Get the columns registered in the get_columns and get_sortable_columns methods
		list( $columns, $hidden ) = $this->get_column_info();
		###hbug( 'columns', $columns, $hidden );

		//Loop for each record
		if(!empty($records)) :
		foreach($records as $id => $rec) :
			###hbug( 'record', $rec );

			//Open the line
			?>
			<tr id="record_<?php echo esc_attr($id) ?>">
			<?php
			foreach ( $columns as $column_name => $column_display_name ) {
				###hbug('column name+display', $column_name, $column_display_name);

				//Style attributes for each col
				$class = "class='$column_name column-$column_name'";
				$style = "";
				if ( in_array( $column_name, $hidden ) ) $style = ' style="display:none;"';
				$attributes = $class . $style;

				?>
				<td <?php echo $attributes; ?>><?php echo $rec[$column_name]; ?></td>
				<?php
				/*
				//Display the cell
				switch ( $column_name ) {
					case "col_link_id":	echo '< td '.$attributes.'>'.stripslashes($rec->link_id).'< /td>';	break;
					case "col_link_name": echo '< td '.$attributes.'><strong><a href="'.$editlink.'" title="Edit">'.stripslashes($rec->link_name).'</a></strong>< /td>'; break;
					case "col_link_url": echo '< td '.$attributes.'>'.stripslashes($rec->link_url).'< /td>'; break;
					case "col_link_description": echo '< td '.$attributes.'>'.$rec->link_description.'< /td>'; break;
					case "col_link_visible": echo '< td '.$attributes.'>'.$rec->link_visible.'< /td>'; break;
				}
				*/
			}

			//Close the line
			?>
			</tr>
			<?php
		endforeach; // $records
		endif;// has $records
	}//--	fn	display_rows

}///---	class	My_Admin_List_Table