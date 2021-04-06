<?php 

global $user_ID;
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$table = new DashboardReports();

$daily_report = $table->get_completed_daily_report();
$daily_progress =  ($daily_report['all']) ? ($daily_report['completed']/$daily_report['all'] ) * 100 : 0 ;

$weekly_report = $table->get_completed_weekly_report();
$weekly_progress = ($daily_report['all']) ? ($weekly_report['completed']/$weekly_report['all'] ) * 100 : 0 ;
?>

<div class="row">
  <div class="col-md-6">
      <div class="card stat-card">
          <div class="card-body">
              <h5 class="card-title">Report Harian</h5>
              <h2 class="float-right"><?php echo $daily_report['completed'].'<small>/'.$daily_report['all'].' siswa</small>' ?> </h2>
              <p>Report ditulis</p>
              <div class="progress" style="height: 10px;">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $daily_progress ?>%" aria-valuenow="<?php echo $daily_report['completed'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
          </div>
      </div>
  </div>
  <div class="col-md-6">
      <div class="card stat-card">
          <div class="card-body">
              <h5 class="card-title">Report Mingguan</h5>
               <h2 class="float-right"><?php echo $weekly_report['completed'].'<small>/'.$weekly_report['all'].' siswa</small>' ?> </h2>
              <p>Report ditulis</p>
              <div class="progress" style="height: 10px;">
                  <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $weekly_progress ?>%" aria-valuenow="<?php echo $weekly_report['completed'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
          </div>
      </div>
  </div>
</div>