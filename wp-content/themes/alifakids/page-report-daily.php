<?php 
/* Template Name: Report Daily */
get_header();

if(!is_parent()):

  get_template_part( 'page-parts/report/daily-table', 'admin' );
else: 
  get_template_part( 'page-parts/report/daily-table', 'parent' );
endif; 

    if ( isset($_REQUEST['date']) ) {
      $getDate = $_REQUEST['date'];
    } else {
      $getDate = '';
    }
  ?>
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
                showMonthAfterYear: false,
                altFormat: "yy-mm-dd",
                altField: "#actualDate"
              }, $.datepicker.regional['id']
            )
          ).datepicker("setDate", date);

      $('.reportDetailBtn').click(function(event) {
        event.preventDefault();

        var modal = $('#reportDetail');
        var modal_content = modal.find('#reportContent');

        var student_id = $(this).attr('data-student_id');
        var report_id = $(this).attr('data-report_id');
        var report_date = $(this).attr('data-report_date');
        var report_status = $(this).attr('data-report_status');

        modal_content.html("");

        /* Act on the event */
        $.ajax({
            url: ajax.ajaxurl,
            dataType: "html",
            contentType: 'text/html',
            data: {
                action: "ajax_get_report_detail", 
                report_id : report_id,
                report_date : report_date,
                report_status : report_status,
                student_id : student_id
            }, beforeSend: function(){
                modal.find('#reportLoading').show();
            },
            complete: function(){
                modal.find('#reportLoading').hide();
            },success: function(res) {
              if (res != false) {
                modal_content.html(res);
              } else {
                modal.modal('toggle');
              }
            }
        });
      });
    });
  </script>
<?php get_footer(); ?>