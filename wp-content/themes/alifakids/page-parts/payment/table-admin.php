<?php 
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$table = new Payment();
$items = $table->prepare_items(20, $paged);


$total_record = ($table->record_count() != 0) ? $table->record_count() : '0' ; 

$record_start = ($total_record != 0) ? ( ( $paged - 1 ) * 20) + 1 : '0' ; 
$record_end = ($total_record != 0) ? min( ( $paged * 20 )  , $total_record) : '0' ; 

$total_page = ceil($total_record / 20);

$date = (isset($_REQUEST['date'])) ? $_REQUEST['date'] : current_time('Y-m-d');

//echo "<pre>",print_r($items,1),"</pre>";
 ?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="page-title">
        <?php the_breadcrumb() ?>
        <h3><?php echo get_the_title(); ?></h3>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6">
      <nav class="mailbox-options d-flex align-items-center">
          <?php $table->views() ?>
          <p class="mail-count ml-auto">
            <?php echo $record_start.' - '.$record_end.' dari '.$total_record ?>
          </p>
          <?php 
              echo paginate_links( array(
                  'base' => add_query_arg( 'paged', '%#%' ),
                  'format' => '',
                  'mid_size' => 0,
                  'prev_text' => '<button class="btn btn-secondary m-l-xxs mail-left-btn">&lt;</button>',
                  'next_text' => '<button class="btn btn-secondary float-right m-l-xxs no-m-r mail-right-btn">&gt;</button>',
                  'total' => ceil($total_record / 20),
                  'current' => max( 1, get_query_var( 'paged' ) ),
            ));?>
      </nav>
    </div>
    <div class="col-lg-6">
      <div class="mailbox-search">
       <div class="mailbox-search">
        <form method="get">
          <?php $table->search_box( __('Cari','alifakids'), null ) ?>
        </form>
      </div>
      </div>
    </div>
  </div>
  <div class="divider"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Histori Pembayaran</h5>
          <div class="table-responsive">
            <table class="table payment-table">
              <thead>
                <tr>
                  <th scope="col">Nama Siswa</th>
                  <th scope="col">Cabang</th>
                  <th scope="col">Kelas</th>
                  <th scope="col">Bulan / Tahun</th>
                  <th scope="col" class="text-right">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  foreach ($items as $item) {
                    $url_arg['id'] = $item['payment_id'];
                    $url_arg['student_id'] = $item['student_id'];
                    $url_arg['period'] = $date;

                    $period =  date_i18n("F Y", strtotime( $item['period'] ) );

                ?>
                <tr>
                  <td>
                     <?php if ($item['status']): ?>
                      <a class="paymentDetailBtn" href="#" data-toggle="modal" data-target="#paymentDetail" data-payment_id="<?php echo $item['payment_id'] ?>" data-student_id="<?php echo $item['student_id']?>"  data-payment_status="<?php echo $item['status']?>" data-payment_period="<?php echo $period ?>" >
                        <?php echo $item['name'] ?>
                      </a>
                    <?php else: ?>
                      <span><?php echo $item['name'] ?></span>
                    <?php endif ?>
                  </td>
                  <td><?php echo getBranchName($item['branch_id']) ?></td>
                  <td><?php echo getClassName($item['class_id']) ?></td>
                  <td>
                    <?php echo $period; ?>
                  </td>
                  <td class="text-right payment-status" data-payment_id="<?php echo $item['payment_id'] ?>">
                    <?php echo printPaymentStatus($item['status']) ?>
                  </td>
                </tr>
              <?php 
                } ?>
              </tbody>
            </table>
          </div>
          <hr/>
          <nav class="w-100 mt-3 text-right">
            <span class="mail-count ml-auto">
              <?php echo $record_start.' - '.$record_end.' dari '.$total_record ?>
            </span>
            <?php 
            echo paginate_links( array(
                'base' => add_query_arg( 'paged', '%#%' ),
                'format' => '',
                'mid_size' => 0,
                'prev_text' => '<button class="btn btn-secondary m-l-xxs mail-left-btn">&lt;</button>',
                'next_text' => '<button class="btn btn-secondary float-right m-l-xxs no-m-r mail-right-btn">&gt;</button>',
                'total' => ceil($total_record / 20),
                'current' => max( 1, get_query_var( 'paged' ) ),
            ));?>
          </nav>
        </div>
      </div>
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
<script>
    $(document).ready(function($) {

      /*var getDate = '<?php echo $getDate ?>';
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
          ).datepicker("setDate", date);*/

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