<!DOCTYPE html>
<html lang="en">
<head>
  <title>Link Hound</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" >

  <!-- datatable css -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.12/se-1.2.0/datatables.min.css">
  <link rel="stylesheet" type="text/css" href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.7/css/dataTables.checkboxes.css">
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
    .cs-button{
      height: 35px;
      line-height: 17px;
      font-size: 13px;
      color:#4c8cdb !important;
      letter-spacing: 1px;
      border: 2px solid #efefef;
      border-radius: 40px;
      background: #efefef;
    }
    .active.cs-button{
      height: 35px;
      line-height: 17px;
      font-size: 13px;
      color:#ffffff !important;
      background: #707070;
      letter-spacing: 1px;
      border: 2px solid #707070;
      border-radius: 40px;
    }

    .hedding{
      padding-bottom: 5px;
      border-bottom: 1px solid #ced4da;
    }
    body{
      color:#707070;
    }

  </style>
  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" ></script>

</head>
<body>
  <div class="loader-wrapper" style="display: none;">
    <span class="loader"><span class="loader-inner"></span></span>
  </div>
  <div class="container mt-5 pl-0">
    <div  class="row hedding" >
      <h3 class="mr-5">Link Hound</h3> 
      <div class="row">
        <a class="btn btn-default mr-2 cs-button" href="<?php echo base_url(); ?>Dashboard">Keywords</a>
        <a class="btn btn-default mr-2 active cs-button" href="<?php echo base_url(); ?>TopRanked">URLs</a>
        <a class="btn btn-default mr-2 cs-button" >Backlinks</a>
      </div>
    </div>
      
    <div class="row mt-4 mb-4">
      <div class="col-md-6 pl-0" id="frm-data">
        <form action="" method="post" id="send" class="mb-5">
          <div class="form-group">
            <label for="">Get top ranked URLs:</label>
            <input type="text" class="form-control" id="name" placeholder="Name" name="name">
            <span id="error-name"></span>
          </div>
          <div class="form-group">
          	<textarea class="form-control" rows="5" id="keyword" placeholder="Keyword" name="keyword"></textarea>
            <span id="error-keyword"></span>
          </div>
          <div id="msg-error"></div>
          <button type="submit" id="submit"   class="btn btn-primary">Get top ranked URLs</button>
        </form>
      </div>
      <div class="col-md-6" style="padding-top:3%;">
        <h5 class="mr-5">PREVIOUS SEARCHES</h5>
        <table  class="display" id="data-previous">
          
        </table>
        <div id="error-msg"></div>
      </div>
      <div id="result" class=""></div>
      
    </div>
    </div>
    
  </div>

</body>
</html>
<!-- datatable js -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/se-1.2.0/datatables.min.js"></script>
<script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.7/js/dataTables.checkboxes.min.js"></script>

<script type="text/javascript">
  

  $(document).ready(function() {
    index();
    // $(".loader-wrapper").fadeOut("slow");
    $('#send').on('submit', function(event){
        var fd = new FormData(this);
        event.preventDefault();
        $.ajax({
            url:"<?php echo base_url();?>TopRanked/ranked_url",
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
            success:function(res){
              $("#msg-error").html("")
              $("#error-name").html("")
              $("#error-keyword").html("");
              
              var jsonResults =  JSON.parse(res);
              if(jsonResults.result){
                $("#frm-data").addClass('col-md-12');
                
                $.ajax({
                    url:'<?php echo base_url(); ?>TopRanked/data/' + jsonResults.data,
                   success:function(res){
                    $(".loader-wrapper").fadeOut("slow");
                    $('#submit').attr('disabled', false);
                    $("#frm-data").html(res);
                    
                   }
                });
                // $("#frm-data").html(jsonResults.data);
              }else if(jsonResults.error){
                $(".loader-wrapper").fadeOut("slow");
                $('#submit').attr('disabled', false);
                $("#msg-error").html(jsonResults.error);
              }else{
                $(".loader-wrapper").fadeOut("slow");
                $('#submit').attr('disabled', false);
                $("#error-name").html(jsonResults.data.name);
                $("#error-keyword").html(jsonResults.data.keyword);
              }
            } 
        });
    });
  });

  function index(){
    $.ajax({
      url:"<?php echo base_url();?>TopRanked/datatable",
      method:"POST",
      success:function(res){
        if(res){
          $("#data-previous").html(res);
        }else{
          $("#data-previous").html('<tr><td><label>No Previous Schema</label></td></tr>');
        }
      } 
    });
  }


  function resetform(){
    $("#keyword").val("");
    $("#result").html("");
    $("#error-keyword").html("");
  }

  function seeResultsRow(name,key){
    // console.log(id);
    $.ajax({
        url:"<?php echo base_url();?>Dashboard/show",
        method:"POST",
        data:{name:name,key:key},
        beforeSend:function(){
            $(".loader-wrapper").show();
            $(".loader-wrapper").fadeIn("slow");
        },
        success:function(res){
          $("#error-keyword").html("");
          $(".loader-wrapper").fadeOut("slow");
          var jsonResults =  JSON.parse(res);
          if(jsonResults.result){
            $("#result").html(jsonResults.data);
          }else{
            $("#error-msg").html(jsonResults.data);
          }
        } 
    });
  }
</script>