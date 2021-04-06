<?php 
require get_template_directory() . '/core/class-flash.php';
//require get_template_directory() . '/core/class-phpspreadsheet.php';

require get_template_directory() . '/inc/admin/users.php';

/* Include Students Module */
require get_template_directory() . '/inc/admin/course.php';
require get_template_directory() . '/page-parts/admin/course/index.php';

/* Include Students Module */
require get_template_directory() . '/inc/admin/students.php';
require get_template_directory() . '/page-parts/admin/students/index.php';
require get_template_directory() . '/page-parts/admin/students/add.php';

/* Include Students Module */
require get_template_directory() . '/inc/admin/branch.php';
require get_template_directory() . '/page-parts/admin/branch/index.php';
require get_template_directory() . '/page-parts/admin/branch/add.php';


/* Include Parents Module */
require get_template_directory() . '/inc/admin/parents.php';
require get_template_directory() . '/page-parts/admin/parents/index.php';
require get_template_directory() . '/page-parts/admin/parents/add.php';

/* Include Teachers Module */
require get_template_directory() . '/inc/admin/teachers.php';
require get_template_directory() . '/page-parts/admin/teachers/index.php';
require get_template_directory() . '/page-parts/admin/teachers/add.php';

/* Include Reports Module */
require get_template_directory() . '/inc/admin/reports.php';
require get_template_directory() . '/inc/reports.php';
require get_template_directory() . '/inc/reports-weekly.php';
require get_template_directory() . '/inc/reports-monthly.php';
require get_template_directory() . '/page-parts/admin/reports/daily/index.php';
require get_template_directory() . '/page-parts/admin/reports/daily/add.php';
require get_template_directory() . '/page-parts/admin/reports/weekly/index.php';
require get_template_directory() . '/page-parts/admin/reports/weekly/add.php';
require get_template_directory() . '/page-parts/admin/reports/monthly/index.php';
require get_template_directory() . '/page-parts/admin/reports/monthly/detail.php';

/* Include Payment Module */
require get_template_directory() . '/inc/admin/payment.php';
require get_template_directory() . '/page-parts/admin/payment/index.php';
require get_template_directory() . '/page-parts/admin/payment/detail.php';

/* Include Dashboard Module */
require get_template_directory() . '/inc/dashboard.php';

/* Include Course Module */
require get_template_directory() . '/inc/course.php';

/* Include Payment Module */
require get_template_directory() . '/inc/payment.php';

/* Include Profile Module */
require get_template_directory() . '/inc/profile.php';

/* Include Class Module */
require get_template_directory() . '/inc/admin/class.php';

/* Include Login */
require get_template_directory() . '/inc/login.php';
require get_template_directory() . '/inc/posts.php';

/* Page Template*/
require get_template_directory() . '/page-parts/breadcrumb.php';

/* API Service*/
require get_template_directory() . '/api/init.php';
require get_template_directory() . '/api/users.php';
require get_template_directory() . '/api/learning.php';
require get_template_directory() . '/api/course.php';
require get_template_directory() . '/api/report.php';
require get_template_directory() . '/api/payment.php';