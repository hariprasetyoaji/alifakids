<?php 
/* Template Name: Payment */
get_header();

if(!is_parent()):

  get_template_part( 'page-parts/payment/table', 'admin' );
else: 
  get_template_part( 'page-parts/payment/table', 'parent' );
endif; 

?>

<div class="modal fade" id="paymentDetail" tabindex="-1" role="dialog" aria-labelledby="reportDetail" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Detail Pembayaran</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="material-icons">close</i>
            </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <div id="paymentContent"></div>
              <div class="spinner-border text-primary text-center" role="status" id="reportLoading">
                  <span class="sr-only">Loading...</span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
    </div>
</div>
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
  $(document).ready(function() {
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


    $('.paymentDetailBtn').click(function(event) {
      event.preventDefault();

      var modal = $('#paymentDetail');
      var modal_content = modal.find('#paymentContent');

      var payment_id = $(this).attr('data-payment_id');
      var student_id = $(this).attr('data-student_id');
      var payment_period = $(this).attr('data-payment_period');
      var payment_status = $(this).attr('data-payment_status');

      modal_content.html("");

      /* Act on the event */
      $.ajax({
          url: ajax.ajaxurl,
          dataType: "html",
          contentType: 'text/html',
          data: {
              action: "ajax_get_payment_detail", 
              payment_id : payment_id
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

    $(document).on('click', '.confirmPayment', function(event) {
      event.preventDefault();
      var payment_id = $(this).attr('data-payment_id');
              

       $.ajax({
          url: ajax.ajaxurl,
          dataType: "json",
          type: 'POST',
          data: {
              action: "ajax_confirm_payment", 
              payment_id : payment_id
          }, beforeSend: function(){
              $('#confirmLoading').show(); 
          },
          complete: function(){
              $('#confirmLoading').hide();
          },success: function(res) {
            console.log(res);
            if ( res != false ) {
              $('.confirmPayment').hide();
              $('.payment-table').find('.payment-status[data-payment_id='+payment_id+']').html('<span class="badge badge-success">Lunas</span>');
              $('#paymentContent').find('.payment-status[data-payment_id='+payment_id+']').html('<span class="badge badge-success">Lunas</span>');
            }
          }
      });
    });

  });

</script>

<?php get_footer(); ?>