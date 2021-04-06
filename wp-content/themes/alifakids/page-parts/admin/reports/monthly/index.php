<?php 

function reports_monthly_page_handler() {
	global $wpdb;

    $table = new Report_Monthly_List();
    $table->prepare_items();
	
	$page['new'] = FALSE;
	$page['page'] = 'report_weekly_add';

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );

	?>

	<form method="get" class="posts-filter">
		<input type="hidden" name="page" value="reports_monthly"/>
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
	<style>
	  .ui-datepicker-calendar {
	      display: none;
	  }
	</style>
	<script>
		jQuery(document).ready(function($) {

			var getDate = '<?php echo $getDate ?>';
		      if (getDate) {
		        var date = new Date(getDate);
		      } else {
		        var date = new Date();
		      }

		      jQuery('.datepicker').datepicker(
		        jQuery.extend( {
		            showMonthAfterYear: true,
		            dateFormat: 'mm/yy',
		            altFormat: "yy-mm-dd",
		            altField: "#actualDate",
		            changeMonth: true,
		            changeYear: true,
		            showButtonPanel: true,
		            currentText: 'Bulan ini',
		            onClose: function(dateText, inst) { 
		                jQuery(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
		            }
		          }/*, $.datepicker.regional['id']*/
		        )
		      ).datepicker("setDate", date);
		});
	</script>

	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}