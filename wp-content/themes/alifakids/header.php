<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="keywords" content="alifakids"/>
    <meta name="author" content="digidea"/>

    <?php wp_head(); ?>
  </head>
  <body>
    <div class="lime-sidebar">
      <?php 
        echo  wp_nav_menu( array(
            'menu' => 'primary',
            'menu_id' => 'primary',
            'menu_class' => 'accordion-menu',
            'container_class' => 'lime-sidebar-inner slimscroll'
          )); 
        ?>
     <!--  <div class="lime-sidebar-inner slimscroll">
        <ul class="accordion-menu">
          <li>
            <a href="index.html" class="active">
              <i class="material-icons">dashboard</i>
              Dashboard
            </a>
          </li>
          <li>
            <a href="learning.html">
              <i class="material-icons">library_books</i>
              Learning
            </a>
          </li>
          <li>
            <a href="report.html">
              <i class="material-icons">insert_chart</i>
              Report
              <i class="material-icons has-sub-menu">keyboard_arrow_left</i>
            </a>
            <ul class="sub-menu">
              <li class="animation">
                <a href="report-daily.html">Harian</a>
              </li>
              <li class="animation">
                <a href="report-weekly.html">Mingguan</a>
              </li>
              <li class="animation">
                <a href="report-monthly.html">Bulanan</a>
              </li>
            </ul>
          </li>
          <li>
            <a href="payment.html">
              <i class="material-icons">payment</i>
              Payment
            </a>
          </li>
          <li>
            <a href="#">
              <i class="material-icons">exit_to_app</i>
              Log Out
            </a>
          </li>
        </ul>
      </div> -->
    </div>
    <div class="lime-header">
      <nav class="navbar navbar-expand-lg">
        <section class="material-design-hamburger navigation-toggle">
          <a href="javascript:void(0)" class="button-collapse material-design-hamburger__icon">
            <span class="material-design-hamburger__layer"></span>
          </a>
        </section>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="material-icons">keyboard_arrow_down</i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <a class="navbar-brand" href="<?php echo get_home_url(''); ?>">
            <img src="<?php echo get_template_directory_uri() ?>/assets/images/logo.png" class="img-logo" alt="Logo Alifa Kids"/>
          </a>
          <!-- <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="material-icons">more_vert</i>
              </a>
              <ul class="dropdown-menu dropdown-menu-right">
                <li>
                  <a class="dropdown-item" href="#">Edit Profil</a>
                </li>
                <li class="divider"></li>
                <li>
                  <a class="dropdown-item" href="<?php echo wp_logout_url() ?>">Log Out</a>
                </li>
              </ul>
            </li>
          </ul> -->
        </div>
      </nav>
    </div>
    <div class="lime-container">
      <div class="lime-body">
