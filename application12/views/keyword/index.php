<!DOCTYPE html>
<html lang="en">
<head>
  <title>Link Hound</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
      color:#4c8cdb;
      letter-spacing: 1px;
      line-height: 15px;
      border: 2px solid #efefef;
      border-radius: 40px;
    }
    .active.cs-button{
      height: 35px;
      line-height: 17px;
      font-size: 13px;
      color:#ffffff;
      background: #707070;
      letter-spacing: 1px;
      line-height: 15px;
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
        <button class="btn btn-default mr-2 active cs-button">Keywords</button>
        <button class="btn btn-default mr-2 cs-button" >URLs</button>
        <button class="btn btn-default mr-2 cs-button" >Backlinks</button>
      </div>
    </div>
      
    <div class="row mt-4">
      <div class="col-md-6 pl-0">
        <form action="" method="post" id="send">
          <div class="form-group">
            <label for="">Get Related Keyword:</label>
            <input type="text" class="form-control" id="keyword" placeholder="Keyword" name="keyword">
            <span id="error-keyword"></span>
          </div>
          <button type="submit" id="submit"  class="btn btn-primary">Get Related Keyword</button> 
          <button type="button" class="btn btn-default" onclick="resetform()">Reset</button>
        </form>

      </div>
      <div class="col-md-6 row " style="padding-top:3%;">
        <h5 class="mr-5">PREVIOUS SEARCHES</h5>
        <table style="width: 100%;">
          <?php 
          // var_dump($keywords);die;
          if(!empty($keywords)){
            foreach ($keywords as $row) { 
              // var_dump($row);die;
              if(!empty($row)){
                ?>
                <tr>
                  <td><label><?php echo $row[0]; ?></label></td>
                  <td>
                    <!-- onclick="donwloadRow('<?php //echo $row[1]; ?>')" -->
                      <a  href="<?php echo base_url('Dashboard/download/'.$row[1]); ?>" class="btn btn-link" >Download</a> |
                      <button class="btn btn-link" onclick="seeResultsRow('<?php echo $row[1]; ?>')"> See Results</button>
                  </td>
                </tr>
                <?php 
              } 
            }
          } ?>
          
        </table>
        <div id="error-msg"></div>
      </div>
      <div id="result"></div>
    </div>
    </div>
    
  </div>

</body>
</html>


<script type="text/javascript">
  $(document).ready(function() {
    // $(".loader-wrapper").fadeOut("slow");
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
            success:function(res){
              $("#error-keyword").html("");
              $(".loader-wrapper").fadeOut("slow");
              $('#submit').attr('disabled', false);
              var jsonResults =  JSON.parse(res);
              if(jsonResults.result){
                $("#result").html(jsonResults.data);
              }else{
                $("#error-keyword").html(jsonResults.data);
              }
            } 
        });
    });
  });
  function resetform(){
    $("#keyword").val("");
    $("#result").html("");
    $("#error-keyword").html("");
  }

  // function donwloadRow(name){
  //   $.ajax({
  //       url:"<?php //echo base_url();?>Dashboard/download/",
  //       method:"POST",
  //       data:{name:name},
  //       success:function(res){
          
  //       } 
  //   });
  // }

  function seeResultsRow(id){
    // console.log(id);
    $.ajax({
        url:"<?php echo base_url();?>Dashboard/show",
        method:"POST",
        data:{id:id},
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