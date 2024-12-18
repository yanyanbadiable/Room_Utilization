<!DOCTYPE html>
<html lang="en">

<?php session_start(); ?>

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>ADRUFWMS</title>


  
</head>
<style>
  /* *{
    outline: 1px solid black;
  } */
  body {
    background: #80808045;
  }

  .modal-dialog.large {
    width: 80% !important;
    max-width: unset;
  }

  .modal-dialog.mid-large {
    width: 50% !important;
    max-width: unset;
  }

  #viewer_modal .btn-close {
    position: absolute;
    z-index: 999999;
    /*right: -4.5em;*/
    background: unset;
    color: white;
    border: unset;
    font-size: 27px;
    top: 0;
  }

  #viewer_modal .modal-dialog {
    width: 80%;
    max-width: unset;
    height: calc(90%);
    max-height: unset;
  }

  #viewer_modal .modal-content {
    background: black;
    border: unset;
    height: calc(100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #viewer_modal img,
  #viewer_modal video {
    max-height: calc(100%);
    max-width: calc(100%);
  }

  .select2-container .select2-selection--single {
    height: 38px;
    line-height: 40px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    border: 1px solid #ccc;

  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px;
  }

  #alert_toast.bg-success {
    background-color: rgba(40, 167, 69, 0.85) !important;
  }

  #alert_toast.bg-danger {
    background-color: rgba(220, 53, 69, 0.85) !important;
  }

  #alert_toast.bg-info {
    background-color: rgba(23, 162, 184, 0.85) !important;
  }

  #alert_toast.bg-warning {
    background-color: rgba(255, 193, 7, 0.85) !important;
  }

  .big-icon {
    font-size: 20px;
  }

  .toast-body .message {
    font-size: 12px;
  }

  .toast-body h6 {
    font-size: 12px;
  }
</style>

<body>



  <!-- Generic Modal -->
  <div class="modal fade" id="uni_modal" tabindex="-1" role="dialog" aria-labelledby="uni_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="uni_modal_title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="submit" onclick="$('#uni_modal form').submit()">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div class="modal fade" id="confirm_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirm_modal_title">Confirmation</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="delete_content"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="confirm">Continue</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Viewer Modal -->
  <div class="modal fade" id="viewer_modal" tabindex="-1" role="dialog" aria-labelledby="viewer_modal_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <div class="modal-body">
          <img src="" alt="">
        </div>
      </div>
    </div>
  </div>

</body>
<script>
  window.start_load = function() {
    $('body').prepend('<div id="preloader2"></div>')
  }
  window.end_load = function() {
    $('#preloader2').fadeOut('fast', function() {
      $(this).remove();
    })
  }
  window.viewer_modal = function($src = '') {
    start_load()
    var t = $src.split('.')
    t = t[1]
    if (t == 'mp4') {
      var view = $("<video src='" + $src + "' controls autoplay></video>")
    } else {
      var view = $("<img src='" + $src + "' />")
    }
    $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
    $('#viewer_modal .modal-content').append(view)
    $('#viewer_modal').modal({
      show: true,
      backdrop: 'static',
      keyboard: false,
      focus: true
    })
    end_load()

  }
  window.uni_modal = function($title = '', $url = '', $size = "") {
    start_load()
    $.ajax({
      url: $url,
      error: err => {
        console.log()
        alert("An error occurred")
      },
      success: function(resp) {
        if (resp) {
          $('#uni_modal .modal-title').html($title)
          $('#uni_modal .modal-body').html(resp)
          if ($size != '') {
            $('#uni_modal .modal-dialog').addClass($size)
          } else {
            $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md")
          }
          $('#uni_modal').modal({
            show: true,
            backdrop: 'static',
            keyboard: false,
            focus: true
          })
          end_load()
        }
      }
    })
  }
  window._conf = function($msg = '', $func = '', $params = []) {
    $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + "); $('#confirm_modal').modal('hide');");
    $('#confirm_modal .modal-body').html($msg);
    $('#confirm_modal').modal('show');
  }
  window.alert_toast = function($msg = 'TEST', $bg = 'success') {

    $('#alert_toast').removeClass('bg-success bg-danger bg-info bg-warning');
    $('#alert_toast .icon').removeClass('fa-check fa-exclamation-circle fa-info fa-exclamation-triangle');

    if ($bg === 'success' || $bg === 'danger') {
      $('#notificationText').html('<b>Notification!</b>');
    } else {
      $('#notificationText').html(''); 
    }

    if ($bg === 'success') {
      $('#alert_toast').addClass('bg-success');
      $('#alert_toast .icon').addClass('fa fa-check');
    }
    if ($bg === 'danger') {
      $('#alert_toast').addClass('bg-danger');
      $('#alert_toast .icon').addClass('fa fa-exclamation-triangle');
    }
    if ($bg === 'info') {
      $('#alert_toast').addClass('bg-info');
      $('#alert_toast .icon').addClass('fa fa-info');
    }
    if ($bg === 'warning') {
      $('#alert_toast').addClass('bg-warning');
      $('#alert_toast .icon').addClass('fa fa-exclamation-circle');
    }

    $('#alert_toast .message').html($msg);
    $('#alert_toast').toast({
      delay: 3000
    }).toast('show');
  }


  $(document).ready(function() {
    $('#preloader').fadeOut('fast', function() {
      $(this).remove();
    })
  })
  $('.select2').select2({
    placeholder: "Please select here",
    // allowClear: true,
    width: "100%",
  })
</script>

</html>