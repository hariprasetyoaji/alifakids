<?php 

function report_daily_page_handler() {
	global $wpdb;

    $table = new Report_Daily_List();
    $table->prepare_items();
	
	$page['new'] = FALSE;
	$page['page'] = 'report_daily_add';

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );

	?>

	<form method="get" class="posts-filter">
		<input type="hidden" name="page" value="reports_daily"/>

		<?php $table->views() ?>

		<?php $table->search_box( __('Cari','alifakids'), null ) ?>
		<?php
			$table->display(); 
		?>
	</form>

	<?php 
		if ( isset($_REQUEST['date']) ) {
			$getDate = $_REQUEST['date'];
		} else {
			$getDate = '';
		}
	?>
	<script>
		jQuery(document).ready(function($) {

			var getDate = '<?php echo $getDate ?>';
			if (getDate) {
				var date = new Date(getDate);
			} else {
				var	date = new Date();
			}

			jQuery('#date').datepicker(
				jQuery.extend( {
					      showMonthAfterYear: false,
					      altFormat: "yy-mm-dd",
					      altField: "#actualDate"
					    }, jQuery.datepicker.regional['id']
				    )
		  		).datepicker("setDate", date);
		});
	</script>

	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}