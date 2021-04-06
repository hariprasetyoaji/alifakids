<?php 
global $user_ID;
global $current_user;
global $flash;

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$parent_students = getParentStudents($user_ID);

$table = new Payment_Parent();

if (isset($_REQUEST['student_id'])) {
  $student_id = $_REQUEST['student_id'];
} else {
  $student_id = $parent_students[0]['student_id'];
}

$items = $table->prepare_items($student_id , 10, $paged);

$total_record = ($table->record_count() != 0) ? $table->record_count() : '0' ; 

$record_start = ($total_record != 0) ? ( ( $paged - 1 ) * 10) + 1 : '0' ; 
$record_end = ($total_record != 0) ? min( ( $paged * 10 )  , $total_record) : '0' ; 

$total_page = ceil($total_record / 10);

$date = (isset($_REQUEST['date'])) ? $_REQUEST['date'] : current_time('Y-m-d');

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
    <div class="col-lg-4">
      <?php foreach ($parent_students as $parent_student): ?>
      <a href="<?php echo site_url('payment?student_id='.$parent_student['student_id'].''); ?>" class="card card-payment <?php echo ($parent_student['student_id']==$student_id) ? 'card-selected' : '' ; ?>" >
        <div class="card-body">
          <h5 class="card-title"><?php echo $parent_student['name'] ?></h5>
          <ul class="list-unstyled profile-about-list">
            <li>
              <i class="material-icons">school</i>
              <span>Kelas : <?php echo getClassName($parent_student['class_id']) ?></span>
            </li>
            <li>
              <i class="material-icons">my_location</i>
              <span>Cabang : <?php echo getBranchName($parent_student['branch_id']) ?></span>
            </li>
            <li>
              <i class="material-icons">person</i>
              <span>
                Nama Orang Tua : <?php echo $current_user->first_name.' '.$current_user->last_name ?>
              </span>
            </li>
          </ul>
        </div>
      </a>
      <?php endforeach ?>
    </div>
    <div class="col-md-8">
      <?php echo $flash->show(); ?>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Histori Pembayaran</h5>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Bulan / Tahun</th>
                  <th scope="col" class="text-right"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items  as $item): ?>
                <tr>
                  <td>
                     <?php if ($item['status']): ?>
                      <a class="paymentDetailBtn" href="#" data-toggle="modal" data-target="#paymentDetail" data-payment_id="<?php echo $item['payment_id'] ?>" data-student_id="<?php echo $item['student_id']?>"  data-payment_status="<?php echo $item['status']?>" data-payment_period="<?php echo $period ?>" >
                          <?php echo date_i18n("F Y", strtotime( $item['period'] ) ); ?>
                          <?php echo printPaymentStatus($item['status']) ?>
                      </a>
                    <?php else: ?>
                      <span>
                            <?php echo date_i18n("F Y", strtotime( $item['period'] ) ); ?>
                          <?php echo printPaymentStatus($item['status']) ?>
                      </span>
                    <?php endif ?>
                   
                  </td>
                  <td class="text-right">
                    <?php if (!$item['status']): ?>
                      <a href="<?php echo add_query_arg(array('period' => $item['period'],'student_id'=> $item['student_id']  ),site_url( 'payment/form' )); ?>" type="button" class="btn btn-sm btn-primary">Konfirmasi Pembayaran</a>
                    <?php else: ?>
                      <button type="button" class="btn btn-sm btn-secondary disabled">Konfirmasi Pembayaran</button>
                    <?php endif ?>
                  </td>
                </tr>
                <?php endforeach ?>
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