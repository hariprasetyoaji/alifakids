<?php 
/* Template Name: Report Daily Add */
get_header();

//$table = new Reports();
//$items = $table->prepare_items();
global $wpdb;

$student = getStudentByID($_REQUEST['student_id']);

$table = new ReportsMonthly();

$temp_report = $table->getStudentMonthlyReport($_REQUEST['student_id'], $_REQUEST['date']);
$report = [];

foreach ($temp_report as $key => $value) {
  $report[$value['points_key']][$value['points_dimension']][$value['points_id']] = $value['score'];
}

$temp_points = getDailyReportPoints();
$points = [];
foreach ($temp_points as $key => $value) {
  $points[$value['points_key']][$value['points_dimension']][$value['points_id']] = $value['name'];
}

//echo "<pre>",print_r($report,1),"</pre>";
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
                    <span><?php echo date_i18n("F Y", strtotime( $_REQUEST['date'] ) ); ?></span>
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
      <?php foreach ($points as $points_key => $dimensions): ?>
        <div class="card">
          <div class="card-header" style="background: #ca4d9c; color: #fff;">
            <h5 class="mb-0" style="text-transform: uppercase;"><?php echo $points_key ?></h5>
          </div>
          <div class="card-body" style="padding-top: 0;">

        <?php foreach ($dimensions as $dimensions_key => $dimension): ?>
          
          <?php 
          $report_dimension = $dimensions_key;

          $count_dimension = ( !empty($report[$points_key][$report_dimension]) ) ? array_sum($report[$points_key][$report_dimension]) : 0;

          if ($dimensions_key == 'integritas') {
            $dimensions_key = 'Integritas';
          } elseif ($dimensions_key == 'tanggungjawab') {
            $dimensions_key = 'Tanggung Jawab';
          } elseif ($dimensions_key == 'produktif') {
            $dimensions_key = 'Produktif';
          } elseif ($dimensions_key == 'spiritual') {
            $dimensions_key = 'Spiritual';
          } elseif ($dimensions_key == 'tangguh') {
            $dimensions_key = 'Tangguh';
          } elseif ($dimensions_key == 'pengendaliandiri') {
            $dimensions_key = 'Pengendalian Diri';
          } elseif ($dimensions_key == 'mandiri') {
            $dimensions_key = 'Mandiri';
          } elseif ($dimensions_key == 'pengambilresiko') {
            $dimensions_key = 'Pengambil Resiko';
          } elseif ($dimensions_key == 'berkolaborasi') {
            $dimensions_key = 'Berkolaborasi';
          } elseif ($dimensions_key == 'intelijen') {
            $dimensions_key = 'Intelijen';
          } elseif ($dimensions_key == 'komunikasi') {
            $dimensions_key = 'Komunikasi';
          } elseif ($dimensions_key == 'kreasi') {
            $dimensions_key = 'Kreasi';
          } elseif ($dimensions_key == 'kesantunan') {
            $dimensions_key = 'Kesantunan';
          } elseif ($dimensions_key == 'menghargai') {
            $dimensions_key = 'Menghargai';
          } elseif ($dimensions_key == 'berpikirkritis') {
            $dimensions_key = 'Berpikir Kritis';
          }
          ?>

          <h5 class="card-title mb-4" style="background: #f26e8a; color: #fff; margin: 0 -25px; padding: 10px 15px;"><?php echo $dimensions_key ?></h5>

          <?php 
            foreach ($dimension as $id => $name): 
              if (isset($report[$points_key][$report_dimension][$id])) {
                $score = $report[$points_key][$report_dimension][$id];
                $score_percent = ($score / $count_dimension)*100;
              }  else {
                $score = 0;
                $score_percent = 0;
              }
          ?>
            <div class="mb-0" style="font-weight: initial;"><?php echo $name ?></div>
            <div class="progress mb-4">
                <div <?php echo ($score == 0) ? 'style="color:#000;width:0;"' : '' ; ?> class="progress-bar" role="progressbar" style="width: <?php echo $score_percent ?>%;" aria-valuenow="<?php echo $score_percent ?>" aria-valuemin="0" aria-valuemax="100"><?php echo number_format($score_percent,2, '.', '') ?>% (<?php echo $score ?>)</div>
            </div>

          <?php endforeach ?>
        <?php endforeach ?>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>