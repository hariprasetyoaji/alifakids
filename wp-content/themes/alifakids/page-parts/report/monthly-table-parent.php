<?php
global $flash;
global $payment;

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$table = new ReportsMonthly();
$items = $table->prepare_items(10, $paged, $user_ID);

$total_record = ($table->record_count() != 0) ? $table->record_count() : '0' ; 

$record_start = ($total_record != 0) ? ( ( $paged - 1 ) * 10) + 1 : '0' ; 
$record_end = ($total_record != 0) ? min( ( $paged * 10 )  , $total_record) : '0' ; 

$total_page = ceil($total_record / 10);

$date = (isset($_REQUEST['date'])) ? $_REQUEST['date'] : current_time( 'Y-m-01' ) ;
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
                'total' => ceil($total_record / 10),
                'current' => max( 1, get_query_var( 'paged' ) ),
            ));?>
      </nav>
    </div>
    <div class="col-lg-6">
      <div class="mailbox-search">
        <form method="get">
          <?php $table->search_box( __('Cari','alifakids'), null ) ?>
        </form>
      </div>
    </div>
  </div>
  <div class="divider"></div>
  <div class="row">
    <div class="col-md-12">
      <?php echo $flash->show(); ?>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Report Mingguan</h5>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Nama Siswa</th>
                  <th scope="col">Cabang</th>
                  <th scope="col">Kelas</th>
                  <th scope="col" class="text-right">Bulan</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  foreach ($items as $item) {
                    //$url_arg['id'] = $item['report_id'];
                    $url_arg['student_id'] = $item['student_id'];
                    $url_arg['date'] = $date;

                    if (!in_array($item['student_id'], $payment)) {
                ?>
                <tr>
                  <td class="align-middle">
                    <?php /*if ($item['report_id']): ?>
                      <a class="reportDetailBtn" href="#" data-toggle="modal" data-target="#reportDetail" data-report_id="<?php echo $item['report_id'] ?>" data-student_id="<?php echo $item['student_id']?>"  data-report_status="<?php echo $item['status']?>" data-report_date="<?php echo $date ?>" >
                        <?php echo $item['name'] ?>
                      </a>
                    <?php else: ?>
                      <span><?php echo $item['name'] ?></span>
                    <?php endif*/ ?>
                    <a href="<?php echo add_query_arg( $url_arg, site_url('report-bulanan/report-siswa') ) ?>" >
                        <?php echo $item['name'] ?>
                  </td>
                  <td class="align-middle"><?php echo getBranchName($item['branch_id']); ?></td>
                  <td class="align-middle"><?php echo getClassName($item['class_id']) ?></td>
                  <td class="align-middle text-right">
                    <?php echo date_i18n("F Y", strtotime( $date ) ) ?>
                  </td>
                </tr>
                <?php 
                  }
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
                'total' => ceil($total_record / 10),
                'current' => max( 1, get_query_var( 'paged' ) ),
            ));?>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="reportDetail" tabindex="-1" role="dialog" aria-labelledby="reportDetail" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Report Mingguan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="material-icons">close</i>
            </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <div id="reportContent"></div>
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