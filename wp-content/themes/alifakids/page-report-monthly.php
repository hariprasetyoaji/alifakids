<?php 
/* Template Name: Report Monthly */
get_header();

if(!is_parent()):

  get_template_part( 'page-parts/report/monthly-table', 'admin' );
else: 
  get_template_part( 'page-parts/report/monthly-table', 'parent' );
endif; 

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
    $(document).ready(function($) {
      var getDate = '<?php echo $getDate ?>';
      if (getDate) {
        var date = new Date(getDate);
      } else {
        var date = new Date();
      }

      $('.datepicker').datepicker(
        $.extend( {
            showMonthAfterYear: true,
            dateFormat: 'mm/yy',
            altFormat: "yy-mm-dd",
            altField: "#actualDate",
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            currentText: 'Bulan ini',
            onClose: function(dateText, inst) { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
          }/*, $.datepicker.regional['id']*/
        )
      ).datepicker("setDate", date);

    });
  </script>
<?php get_footer(); ?>