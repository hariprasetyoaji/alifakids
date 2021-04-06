<?php 

function payment_page_handler() {
	global $wpdb;

    $table = new Payment_List();
    $table->prepare_items();
	
	/*$page['new'] = TRUE;

	$page['page'] = 'payment_add';*/
	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );
	?>

	<form method="get" class="posts-filter">
		<input type="hidden" name="page" value="payment"/>

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

	<style>
	  .ui-datepicker-calendar {
	      display: none;
	  }
	</style>

	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}