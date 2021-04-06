<?php  
if ($reports) {
?>
<div class="card card-transparent file-list recent-files">
  <div class="card-body pt-0">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h5>Pembelajaran Ananda</h5>
            <div class="post-comments">
              <?php 
                foreach ($reports as $report): 
                $student = getStudentByID($_REQUEST['student_id']);
              ?>
              <div class="post-comm">
                  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/default-avatar.jpg" class="comment-img">
                  <div class="comment-container">
                      <span class="comment-author">
                          <?php echo $student->name ?>
                          <small class="comment-date">
                            <?php echo date_i18n("l, d F Y H:i:s", strtotime( $report['date'] ) ); ?>
                            <span class="badge badge-pill badge-success">Selesai</span>
                          </small>
                      </span>
                      <div class="divider mt-2"></div>
                  </div>
                  <span class="comment-text">
                    <div class="col-md-12">
                      <label style="font-weight: 800">1. Pada bagian sesi mana Ananda menunjukkan antusias?</label>
                      <p><?php echo $report['point_1'] ?></p>
                    </div>
                    <div class="col-md-12">
                      <label style="font-weight: 800">2. Apa hal yang sudah berjalan baik pada sesi kali ini?</label>
                      <p><?php echo $report['point_2'] ?></p>
                    </div>
                    <div class="col-md-12">
                      <label style="font-weight: 800">3. Apa aksi perbaikan untuk membersamai Ananda di sesi berikutnya?</label>
                      <p><?php echo $report['point_3'] ?></p>
                    </div>
                    <div class="col-md-12">
                      <label style="font-weight: 800">4. Bagian mana yang sangat dikuasai Ananda?</label>
                      <p><?php echo $report['point_4'] ?></p>
                    </div>
                    <div class="col-md-12">
                      <label style="font-weight: 800">5. Bagian mana yang menantang atau silit bagi Ananda?</label>
                      <p><?php echo $report['point_5'] ?></p>
                    </div>

                     <div class="divider mt-2"></div>
                     <img class="img-fluid" src="<?php echo wp_get_attachment_url( $report['attachment'], 'thumbnail'); ?>" />
                  </span>
              </div>
               <?php endforeach ;?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php }