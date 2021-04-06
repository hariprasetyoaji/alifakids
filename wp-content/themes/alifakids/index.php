<?php 
get_header();
?>
  <div class="container">
    <div class="row">
      <div class="col-xl-12">
        <div class="profile-cover"></div>
        <div class="profile-header">
          <div class="profile-img">
            <img src="<?php echo get_template_directory_uri() ?>/assets/images/default-avatar.jpg"/>
          </div>
          <div class="profile-name">
            <h3>Hari Prasetyo Aji</h3>
            <h5>Guru</h5>
          </div>
          <div class="profile-header-menu">
            <a href="#" class="btn btn-warning m-t-xs">Edit Profil</a>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Absensi Anda Bulan Ini</h5>
            <div class="popular-product-list">
              <ul class="list-unstyled">
                <li>
                  <span>Masuk</span>
                  <span class="badge badge-pill badge-success">27</span>
                </li>
                <li>
                  <span>Izin/Sakit</span>
                  <span class="badge badge-pill badge-warning">3</span>
                </li>
                <li>
                  <span>Tanpa Keterangan</span>
                  <span class="badge badge-pill badge-danger">0</span>
                </li>
              </ul>
              <button type="button" class="btn btn-primary w-100">Absen Hari Ini</button>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Alifa Kids Antapani</h5>
            <ul class="list-unstyled profile-about-list">
              <li>
                <i class="material-icons">local_phone</i>
                <span>+62 812 2272 2226</span>
              </li>
              <li>
                <i class="material-icons">home</i>
                <span>Jl. Subang Raya No.53 Antapani</span>
              </li>
              <li>
                <i class="material-icons">mail_outline</i>
                <span>info@alifakids.com</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Learning</h5>
            <div class="story-list">
              <div class="story">
                <a href="#">
                  <img src="<?php echo get_template_directory_uri() ?>/assets/images/calistung.jpg" alt=""/>
                </a>
                <div class="story-info">
                  <a href="#">
                    <span class="story-author">Logika & Matematika</span>
                  </a>
                  <span class="story-time">Guru</span>
                </div>
                <div class="story-right">
                  <a href="#" class="btn btn-warning m-t-xs">Lihat</a>
                </div>
              </div>
              <div class="story">
                <a href="#">
                  <img src="<?php echo get_template_directory_uri() ?>/assets/images/calistung.jpg" alt=""/>
                </a>
                <div class="story-info">
                  <a href="#">
                    <span class="story-author">Logika & Matematika</span>
                  </a>
                  <span class="story-time">Guru</span>
                </div>
                <div class="story-right">
                  <a href="#" class="btn btn-warning m-t-xs">Lihat</a>
                </div>
              </div>
              <div class="story">
                <a href="#">
                  <img src="<?php echo get_template_directory_uri() ?>/assets/images/calistung.jpg" alt=""/>
                </a>
                <div class="story-info">
                  <a href="#">
                    <span class="story-author">Logika & Matematika</span>
                  </a>
                  <span class="story-time">Guru</span>
                </div>
                <div class="story-right">
                  <a href="#" class="btn btn-warning m-t-xs">Lihat</a>
                </div>
              </div>
              <div class="story">
                <a href="#">
                  <img src="<?php echo get_template_directory_uri() ?>/assets/images/calistung.jpg" alt=""/>
                </a>
                <div class="story-info">
                  <a href="#">
                    <span class="story-author">Logika & Matematika</span>
                  </a>
                  <span class="story-time">Guru</span>
                </div>
                <div class="story-right">
                  <a href="#" class="btn btn-warning m-t-xs">Lihat</a>
                </div>
              </div>
              <div class="story">
                <a href="#">
                  <img src="<?php echo get_template_directory_uri() ?>/assets/images/calistung.jpg" alt=""/>
                </a>
                <div class="story-info">
                  <a href="#">
                    <span class="story-author">Logika & Matematika</span>
                  </a>
                  <span class="story-time">Guru</span>
                </div>
                <div class="story-right">
                  <a href="#" class="btn btn-warning m-t-xs">Lihat</a>
                </div>
              </div>
            </div>
            <hr/>
            <div class="w-100 mt-3 text-center">
              <button type="button" class="btn btn-default">Lihat Semua</button>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Report Terbaru</h5>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Nama Siswa</th>
                    <th scope="col">Kelas</th>
                    <th scope="col" class="text-right"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="align-middle">Adinda Putri</td>
                    <td class="align-middle">TK A</td>
                    <td class="align-middle text-right">
                      <button type="button" class="btn btn-sm btn-primary">Lihat</button>
                    </td>
                  </tr>
                  <tr>
                    <td class="align-middle">Adinda Putri</td>
                    <td class="align-middle">TK A</td>
                    <td class="align-middle text-right">
                      <button type="button" class="btn btn-sm btn-primary">Lihat</button>
                    </td>
                  </tr>
                  <tr>
                    <td class="align-middle">Adinda Putri</td>
                    <td class="align-middle">TK A</td>
                    <td class="align-middle text-right">
                      <button type="button" class="btn btn-sm btn-primary">Lihat</button>
                    </td>
                  </tr>
                  <tr>
                    <td class="align-middle">Adinda Putri</td>
                    <td class="align-middle">TK A</td>
                    <td class="align-middle text-right">
                      <button type="button" class="btn btn-sm btn-primary">Lihat</button>
                    </td>
                  </tr>
                  <tr>
                    <td class="align-middle">Adinda Putri</td>
                    <td class="align-middle">TK A</td>
                    <td class="align-middle text-right">
                      <button type="button" class="btn btn-sm btn-primary">Lihat</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <hr/>
            <div class="w-100 mt-3 text-center">
              <button type="button" class="btn btn-default">Lihat Semua</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
get_footer();