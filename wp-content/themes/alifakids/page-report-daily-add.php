<?php 
/* Template Name: Report Daily Add */
get_header();


//$table = new Reports();
//$items = $table->prepare_items();
global $wpdb;

$student = getStudentByID($_REQUEST['student_id']);

$selectAmanah = getDailyReportPoints('amanah');
$selectLoyal = getDailyReportPoints('loyal');
$selectInisiatif = getDailyReportPoints('inisiatif');
$selectFathonah = getDailyReportPoints('fathonah');
$selectAdil = getDailyReportPoints('adil');

$default = array(
    'report_id' => '',
    'student_id' => $_REQUEST['student_id'],
    'points_id' => '',
    'date' => current_time( 'Y-m-d' ),
    'amanah' => array(),
    'loyal' => array(),
    'inisiatif' => array(),
    'fathonah' => array(),
    'adil' => array()
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
            LEFT JOIN {$wpdb->prefix}reports_daily r 
              ON s.student_id = r.student_id
          WHERE r.report_id = '%d'
          ", 
        $_REQUEST['id'] 
      ), 
      ARRAY_A
    );

  $item['amanah'] = getDailyReportPointsByID($_REQUEST['id'],'amanah');
  $item['loyal'] = getDailyReportPointsByID($_REQUEST['id'],'loyal');
  $item['inisiatif'] = getDailyReportPointsByID($_REQUEST['id'],'inisiatif');
  $item['fathonah'] = getDailyReportPointsByID($_REQUEST['id'],'fathonah');
  $item['adil'] = getDailyReportPointsByID($_REQUEST['id'],'adil');

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
                    <span><?php echo date_i18n("l, d F Y", strtotime( $_REQUEST['date'] ) ); ?></span>
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
          <h5>List Perilaku Anak A.L.I.F.A</h5>
        </div>
        <div class="card-body">
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" id="dailyReport">
          <input name='action' type="hidden" value='new_report_daily'>
          <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('new_report_daily')?>"/>
          <input type="hidden" name="report_id" value="<?php echo $item['report_id'] ?>"/>
          <input type="hidden" name="student_id" value="<?php echo $item['student_id'] ?>"/>
          <input type="hidden" name="date" value="<?php echo $item['date'] ?>"/>
          <div class="row">
            <div class="col-xl">
              <select 
                multiple="multiple" 
                id="reportAmanah" 
                name="amanah[]"
                class="form-control select-report" 
              >
                <?php 
                  $optgroups = array(
                    'integritas' => 'Integritas',
                    'tanggungjawab' => 'Tanggung Jawab', 
                    'produktif' => 'Produktif'
                  );

                ?>
                <?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
                  <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
                    <?php foreach ($selectAmanah as $value): ?>
                      <?php if ($value['points_dimension'] == $optgroup_key): ?>
                          <option 
                            value="<?php echo $value['points_id'] ?>" 
                            <?php echo ( in_array( $value['points_id'] , $item['amanah']) ) ? 'selected' : '' ; ?> 
                          ><?php echo esc_html( $value['name']   ); ?></option>
                      <?php endif ?>
                    <?php endforeach ?>
                  </optgroup>
                  <?php endforeach ?>
                </select>
            </div>
          </div>
          <div class="divider"></div>
          <div class="row">
            <div class="col-xl">
                <select 
                  multiple="multiple" 
                  id="reportLoyal" 
                  name="loyal[]"
                  class="form-control" 
                >
                <?php 
                  $optgroups = array(
                    'spiritual' => 'Spiritual',
                    'tangguh' => 'Tangguh',
                    'pengendaliandiri' => 'Pengendalian Diri'
                  );
                ?>
                <?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
                  <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
                    <?php foreach ($selectLoyal as $value): ?>
                      <?php if ($value['points_dimension'] == $optgroup_key): ?>
                          <option 
                            value="<?php echo $value['points_id'] ?>" 
                           <?php echo ( in_array( $value['points_id'] , $item['loyal']) ) ? 'selected' : '' ; ?> 
                          ><?php echo esc_html($value['name']) ?></option>
                      <?php endif ?>
                    <?php endforeach ?>
                  </optgroup>
                  <?php endforeach ?>
                </select>
            </div>
          </div>
          <div class="divider"></div>
          <div class="row">
            <div class="col-xl">
              <select 
                multiple="multiple" 
                id="reportInisiatif" 
                name="inisiatif[]"
                class="form-control select-report" 
              >
                <?php 
                  $optgroups = array(
                    'mandiri' => 'Mandiri',
                    'pengambilresiko' => 'Pengambil Resiko',
                    'berkolaborasi' => 'Berkolaborasi'
                  );
                ?>
                <?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
                  <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
                    <?php foreach ($selectInisiatif as $value): ?>
                      <?php if ($value['points_dimension'] == $optgroup_key): ?>
                          <option 
                            value="<?php echo $value['points_id'] ?>" 
                           <?php echo ( in_array( $value['points_id'] , $item['inisiatif']) ) ? 'selected' : '' ; ?> 
                          ><?php echo esc_html($value['name']) ?></option>
                      <?php endif ?>
                    <?php endforeach ?>
                  </optgroup>
                  <?php endforeach ?>
                </select>
            </div>
          </div>
            <div class="divider"></div>
            <div class="row">
             <div class="col-xl">
              <select 
                multiple="multiple" 
                id="reportFathonah" 
                name="fathonah[]"
                class="form-control select-report" 
              >
                <?php 
                  $optgroups = array(
                    'intelijen' => 'Intelijen',
                    'komunikasi' => 'Komunikasi',
                    'kreasi' => 'Kreasi'
                  );
                ?>
                <?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
                  <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
                    <?php foreach ($selectFathonah as $value): ?>
                      <?php if ($value['points_dimension'] == $optgroup_key): ?>
                          <option 
                            value="<?php echo $value['points_id'] ?>" 
                             <?php echo ( in_array( $value['points_id'] , $item['fathonah']) ) ? 'selected' : '' ; ?> 
                          ><?php echo esc_html($value['name']) ?></option>
                      <?php endif ?>
                    <?php endforeach ?>
                  </optgroup>
                  <?php endforeach ?>
                </select>
            </div>
          </div>
            <div class="divider"></div>
          <div class="row">
           <div class="col-xl">
            <select 
              multiple="multiple" 
              id="reportAdil" 
              name="adil[]"
              class="form-control" 
            >
              <?php 
                $optgroups = array(
                  'kesantunan' => 'Kesantunan',
                  'menghargai' => 'Menghargai',
                  'berpikirkritis' => 'Berpikir Kritis'
                );
              ?>
              <?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
                <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
                  <?php foreach ($selectAdil as $value): ?>
                    <?php if ($value['points_dimension'] == $optgroup_key): ?>
                        <option 
                          value="<?php echo $value['points_id'] ?>" 
                           <?php echo ( in_array( $value['points_id'] , $item['adil']) ) ? 'selected' : '' ; ?> 
                        ><?php echo esc_html($value['name']) ?></option>
                    <?php endif ?>
                  <?php endforeach ?>
                </optgroup>
                <?php endforeach ?>
              </select>
           </div>
          </div>
          <div class="xl mt-4 text-right">
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