<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- bootstrap -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style type="text/css">
    .loader-wrapper {
      width: 100%;
      height: 100%;
      z-index: 99999;
      position: absolute;
      top: 0;
      left: 0;
      background-color: #242f3f4a;
      display:flex;
      justify-content: center;
      align-items: center;
    }
    .loader {
      display: inline-block;
      width: 30px;
      height: 30px;
      position: relative;
      border: 4px solid #Fff;
      animation: loader 2s infinite ease;
    }
    .loader-inner {
      vertical-align: top;
      display: inline-block;
      width: 100%;
      background-color: #fff;
      animation: loader-inner 2s infinite ease-in;
    }

    @keyframes loader {
      0% { transform: rotate(0deg);}
      25% { transform: rotate(180deg);}
      50% { transform: rotate(180deg);}
      75% { transform: rotate(360deg);}
      100% { transform: rotate(360deg);}
    }

    @keyframes loader-inner {
      0% { height: 0%;}
      25% { height: 0%;}
      50% { height: 100%;}
      75% { height: 100%;}
      100% { height: 0%;}
    }
  </style>
</head>
<body>
  <div class="loader-wrapper">
    <span class="loader"><span class="loader-inner"></span></span>
  </div>
  <div class="container">
    <h2>Link Hound</h2>
    <form action="" method="post" id="send">
      <div class="form-group">
        <label for="">Get Related Keyword:</label>
        <input type="text" class="form-control" id="keyword" placeholder="Keyword" name="keyword">
      </div>
      <button type="submit" id="submit"  class="btn btn-default">Get Related Keyword</button>
    </form>
    <div id="result"></div>
  </div>

</body>
</html>

<script type="text/javascript">
$(document).ready(function() {
      $(".loader-wrapper").fadeOut("slow");
      $('#send').on('submit', function(event){
        //alert("hii");
        var fd = new FormData(this);
        event.preventDefault();

        

        $.ajax({
            url:"<?php echo base_url();?>Dashboard/raletedKeyword",
            method:"POST",
            data:fd,
            contentType: false,
            cache: false,
            processData:false,
            beforeSend:function(){
                $('#submit').attr('disabled', 'disabled');
                $(".loader-wrapper").show();
                $(".loader-wrapper").fadeIn("slow");
            },
            success:function(data){
              // alert(data);
              // console.log(data);
              $(".loader-wrapper").fadeOut("slow");
              $('#submit').attr('disabled', false);
              $("#result").html(data);
                // if(data == 1){
                //   $("#addquotation").find("input[type=file],textarea").val('');
                //   $("#addquotation .error_msg").html('<div class="alert alert-success alert-dismissible fade show" role="alert">Send Quotation Successfully<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                //   table.ajax.reload();
                // }else{
                //   $("#addquotation .error_msg").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+data+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                // }
            } 
        });
    });
});
</script>