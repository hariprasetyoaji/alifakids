<?php 

global $user_ID;
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$table = new DashboardReports();
$items = $table->prepare_items()

?>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Report Terbaru</h5>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Nama Siswa</th>
            <th scope="col">Kelas</th>
            <th scope="col">Report</th>
            <th scope="col">Status</th>
            <th scope="col" class="text-right"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items['daily'] as $item): ?>
            <tr>
              <td class="align-middle"><?php echo $item['name'] ?></td>
              <td class="align-middle"><?php echo getClassName($item['class_id']) ?></td>
              <td class="align-middle">Harian</td>
              <td class="align-middle">
                <?php echo printReportStatus($item['status']) ?>
              </td>
              <td class="align-middle text-right">
                  <a href="<?php echo site_url('report-harian'); ?>" type="button" class="btn btn-primary">Lihat</a>
              </td>
            </tr>
          <?php endforeach ?>
           <?php foreach ($items['weekly'] as $item): ?>
            <tr>
              <td class="align-middle"><?php echo $item['name'] ?></td>
              <td class="align-middle"><?php echo getClassName($item['class_id']) ?></td>
              <td class="align-middle">Mingguan</td>
              <td class="align-middle">
                <?php echo printReportStatus($item['status']) ?>
              </td>
              <td class="align-middle text-right">
                  <a href="<?php echo site_url('report-mingguan'); ?>" type="button" class="btn btn-primary">Lihat</a>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</div>