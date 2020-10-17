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
        <a class="btn btn-default mr-2  cs-button" href="<?php echo base_url(); ?>TopRanked">URLs</a>
        <a class="btn btn-default mr-2 active cs-button" href="<?php echo base_url(); ?>BackLinks">Backlinks</a>
      </div>
    </div>
    <div class="row mt-3">
      <h5>{Name} : Backlinks</h5>
    </div>
    <div class="row">
      <div class="col-md-12 mb-3">
        <div class="row mt-2">
          <table id="example" class="display" cellspacing="0" width="100%">
             <thead>
                <tr>
                   <th>Ranking URL</th>
                   <th>Backlink</th>
                   <th>Title</th>
                   <th>Anchor</th>
                   <th>Do follow</th>
                   <th>is image link</th>
                   <th>Backlink domain authority</th>
                   <th>Backlink page rank</th>
                   <th>URL count</th>
                   <th>Domain count</th>
                </tr>
             </thead>
             <tfoot>
                <tr>
                   <th>Ranking URL</th>
                   <th>Backlink</th>
                   <th>Title</th>
                   <th>Anchor</th>
                   <th>Do follow</th>
                   <th>is image link</th>
                   <th>Backlink domain authority</th>
                   <th>Backlink page rank</th>
                   <th>URL count</th>
                   <th>Domain count</th>
                </tr>
             </tfoot>
          </table>
        </div>
        <div class="row">
          <a type="submit" id="submit"  class="btn btn-primary" style="color:white;">Download</a>
        </div>
      </div>
      <div class="col-md-12 pl-0 mb-5">
        <h5 class="mr-5">PREVIOUS BACKLINKS LISTS</h5>
        <table style="width: 100%;" id="data-previous">
          
        </table>
        <div id="error-msg"></div>
      </div>
    </div>
  </div>
  
</body>
</html>
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" ></script>

<!-- datatable js -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/se-1.2.0/datatables.min.js"></script>
<script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.7/js/dataTables.checkboxes.min.js"></script>

<script>
$(document).ready(function (){
   var table = $("#example").DataTable({ 
                "columnDefs": [
                     {
                        "targets": 0,
                        
                     }
                ],
                
                "order": [[1, "asc"]]
            });
});
index();
function index(){
    $.ajax({
      url:"<?php echo base_url();?>Dashboard/datatable",
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
</script>


