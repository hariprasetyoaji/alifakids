<?php 

function report_weekly_page_handler() {
	global $wpdb;

    $table = new Report_Weekly_List();
    $table->prepare_items();
	
	$page['new'] = FALSE;
	$page['page'] = 'report_weekly_add';

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );

	?>
	
	<form method="get" class="posts-filter">
		<input type="hidden" name="page" value="reports_weekly"/>
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
		        var date = new Date();
		      }

		      var startDate;
		      var endDate;

		      var selectCurrentWeek = function() {
		          window.setTimeout(function() {
		              jQuery('.ui-weekpicker').find('.ui-datepicker-current-day a').addClass('ui-state-active').removeClass('ui-state-default');
		          }, 1);
		      }

		      var setDates = function(input) {
		          var jQueryinput = jQuery(input);
		          var date = jQueryinput.datepicker('getDate');
		          if (date !== null) {
		              var firstDay = jQueryinput.datepicker("option", "firstDay");
		              var dayAdjustment = date.getDay() - firstDay;
		              if (dayAdjustment < 0) {
		                  dayAdjustment += 7;
		              }
		              startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - dayAdjustment+ 1);
		              endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - dayAdjustment + 6);

		              var inst = jQueryinput.data('datepicker');
		              var dateFormat = inst.settings.dateFormat || jQuery.datepicker._defaults.dateFormat;

		              /*jQuery('#startDate').text(jQuery.datepicker.formatDate(dateFormat, startDate, inst.settings));
		              jQuery('#endDate').text(jQuery.datepicker.formatDate(dateFormat, endDate, inst.settings));*/
		              
		              jQuery('.week-picker').datepicker("setDate", startDate);
		              jQuery('#actualDate').val( jQuery.datepicker.formatDate('yy-mm-dd', startDate) );

		          }
		      }

		      var week_selector = function() {
		          var jQuerycalendarTR = jQuery('.ui-weekPicker .ui-datepicker-calendar tr');
		          jQuerycalendarTR.on('mousemove', function() {
		              jQuery(this).find('td a').addClass('ui-state-hover');
		          });
		          jQuerycalendarTR.on('mouseleave', function() {
		              jQuery(this).find('td a').removeClass('ui-state-hover');
		          });
		      }

		      
		      jQuery('.week-picker').datepicker(
		        jQuery.extend( {
		          beforeShow: function() {
		              jQuery('#ui-datepicker-div').addClass('ui-weekpicker');
		              selectCurrentWeek();
		              window.setTimeout(function() {
		                  week_selector();
		              }, 10);
		          },
		          onClose: function() {
		              jQuery('#ui-datepicker-div').removeClass('ui-weekpicker');
		          },
		          showOtherMonths: true,
		          selectOtherMonths: true,
		          onSelect: function(dateText, inst) {
		              setDates(this);
		              selectCurrentWeek();
		              jQuery(this).change();
		          },
		          beforeShowDay: function(date) {
		              var cssClass = '';
		              if (date >= startDate && date <= endDate) cssClass = 'ui-datepicker-current-day';
		              week_selector();
		              return [true, cssClass];
		          },
		          onChangeMonthYear: function(year, month, inst) {
		              selectCurrentWeek();
		              window.setTimeout(function() {
		                  week_selector();
		              }, 10);
		          }
		        }, jQuery.datepicker.regional['id']
		            )
		      ).datepicker("setDate", date);

		      setDates('.week-picker');
		});
	</script>

	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}