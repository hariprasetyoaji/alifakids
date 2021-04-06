<?php  
$reports = getUserCourseDayReportTeacher( $_REQUEST['y'], $_REQUEST['month'], $_REQUEST['week'], $_REQUEST['d']);
?>
 <div class="col-md-12 card card-transparent file-list recent-files">
  <div class="card-body pt-0">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h5>Pembelajaran Siswa</h5>
            <div class="post-comments">
              <?php 
                if ($reports) {
                  foreach ($reports as $report): 
              ?>
              <div class="post-comm">
                  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/default-avatar.jpg" class="comment-img">
                  <div class="comment-container">
                      <span class="comment-author">
                          <?php echo $report['name'] ?> 
                          <span class="badge badge-pill badge-success">
                              Selesai
                          </span>
                          <small class="comment-date">
                            <?php echo date_i18n("l, d F Y H:i:s", strtotime( $report['date'] ) ); ?>
                            <button type="button" class="btn btn-primary btn-sm courseReportDetail" data-toggle="modal" data-target="#modalReportDetail" data-report_id='<?php echo $report['ID'] ?>' data-course_id='<?php echo $report['course_id'] ?>' data-student_id='<?php echo $report['student_id'] ?>'>Lihat</button>
                          </small>
                      </span>
                  </div>
                </div>
               <?php 
                  endforeach; 
                } else {
                  ?>
                    <p class="text-center">Belum ada yang mengirimkan pembelajaran untuk materi ini.</p>
                  <?php
                }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalReportDetail" tabindex="-1" role="dialog" aria-labelledby="courseReportDetail" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Pembelajaran Siswa</h5>
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

<script>
  $(document).ready(function() {
    $('.courseReportDetail').click(function(event) {
      event.preventDefault();

      var modal = $('#modalReportDetail');
      var modal_content = modal.find('#reportContent');

      var report_id = $(this).attr('data-report_id');
      var student_id = $(this).attr('data-student_id');
      var course_id = $(this).attr('data-course_id');

      modal_content.html("");

      $.ajax({
        url: ajax.ajaxurl,
        dataType: "html",
        contentType: 'text/html',
        data: {
            action: "ajax_get_course_report_detail", 
            course_id : course_id,
            student_id : student_id,
            report_id : report_id
        }, beforeSend: function(){
            modal.find('#reportLoading').show();
        },
        complete: function(){
            modal.find('#reportLoading').hide();
        },success: function(res) {
          //if (res != false) {
            modal_content.html(res);
          /*} else {
            modal.modal('toggle');
          }*/
        }
      });
    });
  });
</script>