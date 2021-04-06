<?php 
/* Template Name: Report Daily Detail */
get_header();


//$table = new Reports();
//$items = $table->prepare_items();
global $wpdb;

$student = getStudentByID($_REQUEST['id']);

$selectAmanah = getDailyReportPoints('amanah');
$selectLoyal = getDailyReportPoints('loyal');
$selectInisiatif = getDailyReportPoints('inisiatif');
$selectFathonah = getDailyReportPoints('fathonah');
$selectAdil = getDailyReportPoints('adil');

$default = array(
    'rid' => '',
    'points_id' => '',
    'date' => current_time( 'Y-m-d' ),
    'amanah' => array(),
    'loyal' => array(),
    'inisiatif' => array(),
    'fathonah' => array(),
    'adil' => array()
);
$item = $default;

if (isset($_REQUEST['rid'])) {
    $item = $wpdb->get_row(
    $wpdb->prepare( " 
        ", 
      $_REQUEST['id'] 
    ), 
    ARRAY_A
  );

$students = getParentStudents($item['ID']);

  if (!$item) {
      $item = $default;
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
    <div class="col-lg-9">
      <div class="mailbox-options d-flex align-items-center">
        <button class="btn btn-secondary m-r-xxs">Kosong</button>
        <button class="btn btn-primary m-r-xxs">Telah Ditulis</button>
        <button class="btn btn-success m-r-xxs">Diterima Ortu</button>
      </div>
    </div>
    <div class="col-lg-3">
      <div class="mailbox-search">
        <form>
          <div class="input-group">
            <input type="text" class="form-control datepicker" data-date-format="dd/mm/yyyy" placeholder="Tanggal Report" value="20/02/2020"/>
            <div class="input-group-append">
              <button class="btn btn-secondary" type="button" id="button-addon1">Cari</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="divider"></div>
  <div class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Adinda Putri</h5>
          <ul class="list-unstyled profile-about-list">
            <li>
              <i class="material-icons">school</i>
              <span>Kelas : TK A</span>
            </li>
            <li>
              <i class="material-icons">my_location</i>
              <span>Cabang : Alifakids Antapani</span>
            </li>
            <li>
              <i class="material-icons">person</i>
              <span>
                Nama Orang Tua :
                <a href="#">Parjo</a>
              </span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Report Harian</h5>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col" class="text-left min">Hari / Tgl</th>
                  <th scope="col" colspan="2">Perkembangan</th>
                  <th scope="col" class="text-right min">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-left min">
                    <strong>Rabu, 19/02/2020</strong>
                  </td>
                  <td width="25%">Apa keterampilan baru yang dimiliki anak hari ini?</td>
                  <td width="auto">Tidak Ada</td>
                  <td class="text-right min">
                    <span class="badge badge-primary">Telah Ditulis</span>
                  </td>
                </tr>
                <tr>
                  <td class="text-left min"></td>
                  <td width="25%">Bagaimana sikap anak yang berbeda kepada teman/guru/diri sendiri hari ini?</td>
                  <td width="auto">Tidak Ada</td>
                  <td class="text-right min"></td>
                </tr>
                <tr>
                  <td class="text-left min"></td>
                  <td width="25%">Apa pengetahuan baru yang dimiliki anak hari ini?</td>
                  <td width="auto">Tidak Ada</td>
                  <td class="text-right min"></td>
                </tr>
                <tr>
                  <td class="text-left min">
                    <strong>Selasa, 18/02/2020</strong>
                  </td>
                  <td width="25%">Apa keterampilan baru yang dimiliki anak hari ini?</td>
                  <td width="auto">Tidak Ada</td>
                  <td class="text-right min">
                    <span class="badge badge-success">Diterima Ortu</span>
                  </td>
                </tr>
                <tr>
                  <td class="text-left min"></td>
                  <td width="25%">Bagaimana sikap anak yang berbeda kepada teman/guru/diri sendiri hari ini?</td>
                  <td width="auto">Tidak Ada</td>
                  <td class="text-right min"></td>
                </tr>
                <tr>
                  <td class="text-left min"></td>
                  <td width="25%">Apa pengetahuan baru yang dimiliki anak hari ini?</td>
                  <td width="auto">Tidak Ada</td>
                  <td class="text-right min"></td>
                </tr>
                <tr>
                  <td class="text-left min">
                    <strong>Senin, 17/02/2020</strong>
                  </td>
                  <td width="25%">Apa keterampilan baru yang dimiliki anak hari ini?</td>
                  <td width="auto">Tidak Ada</td>
                  <td class="text-right min">
                    <span class="badge badge-success">Diterima Ortu</span>
                  </td>
                </tr>
                <tr>
                  <td class="text-left min"></td>
                  <td width="25%">Bagaimana sikap anak yang berbeda kepada teman/guru/diri sendiri hari ini?</td>
                  <td width="auto">Tidak Ada</td>
                  <td class="text-right min"></td>
                </tr>
                <tr>
                  <td class="text-left min"></td>
                  <td width="25%">Apa pengetahuan baru yang dimiliki anak hari ini?</td>
                  <td width="auto">Tidak Ada</td>
                  <td class="text-right min"></td>
                </tr>
              </tbody>
            </table>
          </div>
          <hr/>
          <div class="w-100 mt-3 text-right">
            <span class="mail-count ml-auto">1-5 dari 160</span>
            <button class="btn btn-secondary m-l-xxs mail-left-btn">&lt;</button>
            <button class="btn btn-secondary float-right m-l-xxs no-m-r mail-right-btn">&gt;</button>
          </div>
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

      $("#dailyReport").submit(function(event) {
        event.preventDefault();
        $("#reportAdil").each(function(i, e) {
            length = $(e).children('option:selected').length;
            console.log(e);
            console.log(length);
            if ( length == 2) {

            } else if ( length == 4 && $(e).is('#intelijen') ) {

            } else {
                //$('.alert').show();
                return false;
            }
        });

        $(this).submit();

      });



    });
  </script>
<?php get_footer(); ?>