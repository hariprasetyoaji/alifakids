<?php 
/* Template Name: Report Weekly Add */
get_header();


//$table = new Reports();
//$items = $table->prepare_items();
global $wpdb;

$student = getStudentByID($_REQUEST['student_id']);

$reportPoints = getWeeklyReportPoints();

$default = array(
    'report_id' => '',
    'student_id' => $_REQUEST['student_id'],
    'points' =>  array(),
    'date' => date('Y-m-d', strtotime('monday this week', strtotime(current_time('Y-m-d'))))
);
$item = $default;

if (isset($_REQUEST['id'])) {
    $item = $wpdb->get_row(
      $wpdb->prepare( " 
          SELECT  
              r.report_id as report_id,
              r.student_id as student_id,
              r.date as date

          FROM {$wpdb->prefix}students s 
            LEFT JOIN {$wpdb->prefix}reports_weekly r 
              ON s.student_id = r.student_id
          WHERE r.report_id = '%d'
          ", 
        $_REQUEST['id'] 
      ), 
      ARRAY_A
    );

  $item['points'] = getWeeklyReportPointsByID($_REQUEST['id']);

  $item = shortcode_atts($default, $item);

  if (!$item) {
      $notice = __('Item not found', 'alifakids');
  }
}

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
    <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="mail-info">
                <div class="mail-author">
                    <img src="<?php echo get_template_directory_uri() ?>/assets/images/default-avatar.jpg" alt="">
                    <div class="mail-author-info">
                        <span class="mail-author-name"><?php echo $student->name ?></span>
                        <span class="mail-author-address">
                          <?php echo $student->number ?>
                          (<?php echo getClassNameByID($student->class_id); ?>)  
                        </span>
                    </div>
                </div>
                <div class="mail-other-info">
                    <span>
                      Minggu Ke <?php echo weekOfMonth($_REQUEST['date']) ?>, 
                      Bulan <?php echo date_i18n("F", strtotime( $_REQUEST['date'] ) ); ?>    
                    </span>
                </div>
            </div>
          </div>
        </div>
    </div>
  </div>
  <div class="divider"></div>
  <div class="row">
    <div class="col-12">
      <?php echo $flash->show(); ?>
      <div style="display: none;" class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Submit Error!</strong> Setiap dimensi harus memiliki 2 poin & khusus dimensi intelejensi harus 4 poin.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">×</span>
          </button>
      </div>
      <div class="card">
        <div class="card-header">
          <h5>Laporan Mingguan Adinda</h5>
        </div>
        <div class="card-body">
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" id="dailyReport">
          <input name='action' type="hidden" value='new_report_weekly'>
          <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('new_report_weekly')?>"/>
          <input type="hidden" name="report_id" value="<?php echo $item['report_id'] ?>"/>
          <input type="hidden" name="student_id" value="<?php echo $item['student_id'] ?>"/>
          <input type="hidden" name="date" value="<?php echo $item['date'] ?>"/>

          <?php 
          $i = 1;
          $j = 1;
          foreach ($reportPoints as $points): 
          ?>
              <?php if ($points['type'] == 1): ?>
                <p class="m-t-sm"><strong><?php echo $i.'. '.$points['name'] ?></strong></p>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" type="radio" name="<?php echo 'points_'.$points['points_id'] ?>" id="points_<?php echo $points['points_id'].'_1' ?>" value="A" <?php echo (!empty($item['points']) && $item['points'][ $points['points_id'] ] == 'A' ) ? 'checked' : '' ; ?> required>
                    <label class="custom-control-label" for="points_<?php echo $points['points_id'].'_1' ?>">
                        A. Baik
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" type="radio" name="<?php echo 'points_'.$points['points_id'] ?>" id="points_<?php echo $points['points_id'].'_2' ?>" value="B" <?php echo (!empty($item['points']) && $item['points'][ $points['points_id'] ] == 'B' ) ? 'checked' : '' ; ?> required>
                    <label class="custom-control-label" for="points_<?php echo $points['points_id'].'_2' ?>">
                        B. Cukup Baik
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input" type="radio" name="<?php echo 'points_'.$points['points_id'] ?>" id="points_<?php echo $points['points_id'].'_3' ?>" value="C" <?php echo (!empty($item['points']) && $item['points'][ $points['points_id'] ] == 'C' ) ? 'checked' : '' ; ?> required>
                    <label class="custom-control-label" for="points_<?php echo $points['points_id'].'_3' ?>">
                        C. Belum Baik
                    </label>
                </div>
              <?php $i++; else: ?>
                <?php if ($j == 1): ?>
                  <div class="divider"></div>
                  <p class="m-t-sm"><em>Narasikan minimal 3 kalimat yang menjelaskan perkembangan Ananda dalam menguasai Mata pelajaran selama 1 minggu ini. </em></p>
                <?php endif ?>
                <p class="m-t-sm"><strong><?php echo $j.'. '.$points['name'] ?></strong></p>
                <textarea name="<?php echo 'points_'.$points['points_id'] ?>" class="form-control" required><?php echo (!empty( $item['points'] )) ? $item['points'][ $points['points_id'] ] : ''; ?></textarea>
              <?php $j++; endif; ?>
          <?php 
          endforeach 
          ?>

          <div class="xl mt-4 text-left">
             <button class="btn btn-primary" type="submit">Submit Report</button>
           </div>
         </form>
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
    $(document).ready(function() {

      $("#dailyReport").on('submit', function(event) {

        var res = true;

        $(".select-report").each(function(i, e) {

            $(e).children('optgroup').each(function(index, el) {

              length = $(el).children('option:selected').length;

              if (length != 2 && !$(el).is('#intelijen') ){
                res = false;
              } else if ( $(el).is('#intelijen') && length != 4  ) {
                res = false;
              }

            });
        });

        if (res == false) {
          $('.alert').show();
          $(window).scrollTop(0);
          return res;
        } else {
          return res;
        }

      });


      $('#reportAmanah').multiSelect({
        keepOrder: true,
        selectableHeader: "<div class='ms-header'>Amanah</div>",
        selectionHeader: "<div class='ms-header'>Amanah</div>",
        afterSelect: function(values){
          $.each( $('#reportAmanah optgroup'),function (i,e) {
            
            length = $(e).children('option:selected').length;

            if (length > 2 ) {
              $('#reportAmanah').multiSelect('deselect',values);
              return false;
            };
          });
        }
      });

      $('#reportLoyal').multiSelect({
        keepOrder: true,
        selectableHeader: "<div class='ms-header'>Loyal</div>",
        selectionHeader: "<div class='ms-header'>Loyal</div>",
        afterSelect: function(values){
          $.each( $('#reportLoyal optgroup'),function (i,e) {
            
            length = $(e).children('option:selected').length;

            if (length > 2 ) {
              $('#reportLoyal').multiSelect('deselect',values);
              return false;
            };
          });
        }
      });

      $('#reportInisiatif').multiSelect({
        keepOrder: true,
        selectableHeader: "<div class='ms-header'>Inisiatif</div>",
        selectionHeader: "<div class='ms-header'>Inisiatif</div>",
        afterSelect: function(values){
          $.each( $('#reportInisiatif optgroup'),function (i,e) {
            
            length = $(e).children('option:selected').length;

            if (length > 2 ) {
              $('#reportInisiatif').multiSelect('deselect',values);
              return false;
            };
          });
        }
      });

      $('#reportFathonah').multiSelect({
        keepOrder: true,
        selectableHeader: "<div class='ms-header'>Fathonah</div>",
        selectionHeader: "<div class='ms-header'>Fathonah</div>",
        afterSelect: function(values){
          $.each( $('#reportFathonah optgroup'),function (i,e) {
            length = $(e).children('option:selected').length;

            if ( $(e).is('#intelijen') ){
              max_length = 4;
            } else {
              max_length = 2;
            }

            if (length > max_length ) {
              $('#reportFathonah').multiSelect('deselect',values);
              return false;
            }
          });
        }
      });

      $('#reportAdil').multiSelect({
        keepOrder: true,
        selectableHeader: "<div class='ms-header'>Adil</div>",
        selectionHeader: "<div class='ms-header'>Adil</div>",
        afterSelect: function(values){
          $.each( $('#reportAdil optgroup'),function (i,e) {
            
            length = $(e).children('option:selected').length;

            if (length > 2 ) {
              $('#reportAdil').multiSelect('deselect',values);
              return false;
            };
          });
        }
      });

    });
  </script>
<?php get_footer(); ?>