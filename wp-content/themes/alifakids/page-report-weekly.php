<?php 
/* Template Name: Report Weekly */
get_header();

if(!is_parent()):

  get_template_part( 'page-parts/report/weekly-table', 'admin' );
else: 
  get_template_part( 'page-parts/report/weekly-table', 'parent' );
endif; 

    if ( isset($_REQUEST['date']) ) {
      $getDate = $_REQUEST['date'];
    } else {
      $getDate = '';
    }
  ?>
  iv>
<script>
    $(document).ready(function($) {
      var getDate = '<?php echo $getDate ?>';
      if (getDate) {
        var date = new Date(getDate);
      } else {
        var date = new Date();
      }

      var startDate;
      var endDate;

      var selectCurrentWeek = function() {
          window.setTimeout(function() {
              $('.ui-weekpicker').find('.ui-datepicker-current-day a').addClass('ui-state-active').removeClass('ui-state-default');
          }, 1);
      }

      var setDates = function(input) {
          var $input = $(input);
          var date = $input.datepicker('getDate');
          if (date !== null) {
              var firstDay = $input.datepicker("option", "firstDay");
              var dayAdjustment = date.getDay() - firstDay;
              if (dayAdjustment < 0) {
                  dayAdjustment += 7;
              }
              startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - dayAdjustment+ 1);
              endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - dayAdjustment + 6);

              var inst = $input.data('datepicker');
              var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;

              /*$('#startDate').text($.datepicker.formatDate(dateFormat, startDate, inst.settings));
              $('#endDate').text($.datepicker.formatDate(dateFormat, endDate, inst.settings));*/
              
              $('.week-picker').datepicker("setDate", startDate);
              $('#actualDate').val( $.datepicker.formatDate('yy-mm-dd', startDate) );

          }
      }

      var week_selector = function() {
          var $calendarTR = $('.ui-weekPicker .ui-datepicker-calendar tr');
          $calendarTR.on('mousemove', function() {
              $(this).find('td a').addClass('ui-state-hover');
          });
          $calendarTR.on('mouseleave', function() {
              $(this).find('td a').removeClass('ui-state-hover');
          });
      }

      
      $('.week-picker').datepicker(
        $.extend( {
          beforeShow: function() {
              $('#ui-datepicker-div').addClass('ui-weekpicker');
              selectCurrentWeek();
              window.setTimeout(function() {
                  week_selector();
              }, 10);
          },
          onClose: function() {
              $('#ui-datepicker-div').removeClass('ui-weekpicker');
          },
          showOtherMonths: true,
          selectOtherMonths: true,
          onSelect: function(dateText, inst) {
              setDates(this);
              selectCurrentWeek();
              $(this).change();
          },
          beforeShowDay: function(date) {
              var cssClass = '';
              if (date >= startDate && date <= endDate) cssClass = 'ui-datepicker-current-day';
              week_selector();
              return [true, cssClass];
          },
          onChangeMonthYear: function(year, month, inst) {
              selectCurrentWeek();
              window.setTimeout(function() {
                  week_selector();
              }, 10);
          }
        }, $.datepicker.regional['id']
            )
      ).datepicker("setDate", date);

      setDates('.week-picker');

      $('.reportDetailBtn').click(function(event) {
        event.preventDefault();

        var modal = $('#reportDetail');
        var modal_content = modal.find('#reportContent');

        var student_id = $(this).attr('data-student_id');
        var report_id = $(this).attr('data-report_id');
        var report_date = $(this).attr('data-report_date');
        var report_status = $(this).attr('data-report_status');

        modal_content.html("");

        /* Act on the event */
        $.ajax({
            url: ajax.ajaxurl,
            dataType: "html",
            contentType: 'text/html',
            data: {
                action: "ajax_get_report_weekly_detail", 
                report_id : report_id,
                report_date : report_date,
                report_status : report_status,
                student_id : student_id
            }, beforeSend: function(){
                modal.find('#reportLoading').show();
            },
            complete: function(){
                modal.find('#reportLoading').hide();
            },success: function(res) {
              if (res != false) {
                modal_content.html(res);
              } else {
                modal.modal('toggle');
              }
            }
        });
      });
    });
  </script>
<?php get_footer(); ?>