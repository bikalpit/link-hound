<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TopRanked extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->helper('common_helper');
       
        include APPPATH . 'third_party/RestClient.php';
        
    }

    public function index()
    {
       $this->load->view('toprank/index');
    }

    // public function show()
    // {
    //    $this->load->view('toprank/show_top_rankurl');
    //    // $this->load->view('toprank/index12');
    // }

    public function data(){
        $filename = $this->input->post('filename');
        $name = $this->input->post('name');
        $filenamejson = $this->input->post('filenamejson');
        // $filename = "pizza-20201019.json";
        // $name = "pizza";
        // $filenamejson = "toppizza-20201019.json";
        $maindata = json_decode(file_get_contents("data/top-ranked-urls/".$filename));
        // array_insert($arr,1,"one-half");
        $main_data = array_merge($maindata[0],$maindata[1],$maindata[2],$maindata[3],$maindata[4]);
        // var_dump($main_data);die;
        $data = json_decode(file_get_contents("data/top-ranked-urls/".$filenamejson));
        $top_5 = $data->top5;
        $top_10 = $data->top10;
        $top_100 = $data->top100;
       

        $result5 = array();
        foreach ($top_5 as $row) {
          $res = $this->substr_count_array($top_5,$row);
          array_push($result5,array("URL" => $row,"count" =>$res));
        }
        $top5result = $this->unique_multidim_array($result5,"URL");
        array_splice($top5result, 0, 0);


        $result10 = array();
        foreach ($top_10 as $row) {
          $res = $this->substr_count_array($top_10,$row);
          array_push($result10,array("URL" => $row,"count" =>$res));
        }
        $top10result = $this->unique_multidim_array($result10,"URL");
        array_splice($top10result, 0, 0);

        $result100 = array();
        foreach ($top_100 as $row) {
          $res = $this->substr_count_array($top_100,$row);
          array_push($result100,array("URL" => $row,"count" =>$res));
        }
        $top100result = $this->unique_multidim_array($result100,"URL");
        array_splice($top100result, 0, 0);

        

        
        if(!empty($main_data)){
           $res_main_data = $this->unique_multidim_array($main_data,"url");
           // echo "<pre>";
           // var_dump($res_main_data);die;
            echo "<style>.tiptext {
    color:#069; 
    cursor:pointer;
}
.description {
    display:none;
    position:absolute;
    border:1px solid #000;
    width:400px;
    height:400px;
}</style><div class='row ml-2'><label style='font-size: 23px;font-weight: 400;margin-bottom:2%;text-transform: capitalize;'><span style='font-weight:500;'>".$name.' </span>: Top ranked URLs</label> <br/>
               <table id="example" class="table-striped display">
               <thead>
                  <tr>
                     <th></th>
                     <th>URL</th>
                     <th>Value</th>
                     <th>Title</th>
                     <th>Description</th>
                     <th>Top 10 Count</th>
                     <th>Top 100 Count</th>
                  </tr>
               </thead>';
            // while (($row = fgetcsv($file,0, "|")) !== FALSE) { 
            foreach($res_main_data as $row) {
                $row0 = isset($row['url'])? $row['url'] : '-';
                $row1 = isset($row['title'])? $row['title'] : '-';
                $row2 = isset($row['description'])? $row['description'] : '-';

                $compertop5 = array_column($top5result,'URL');
                // echo "<pre>";
                // var_dump($top5result);
                // echo "-------------------------------";
                $index5 = array_search($row0,$compertop5);

                $compertop10 = array_column($top10result,'URL');
                // echo "<pre>";
                // var_dump($top10result);
                // echo "-------------------------------";
                $index10 = array_search($row0,$compertop10);

                $compertop100 = array_column($top100result,'URL');
                // echo "<pre>";
                // var_dump($top100result);
                $index100 = array_search($row0,$compertop100);
               // var_dump($top100result[$index100]['count']);die;

                if(1 == $row['rank_group']){
                    $value = 1 * 30 * .52 + $top5result[$index5]['count'];
                }else if(2== $row['rank_group']){
                    $value = 1 * 30 * .21 + $top5result[$index5]['count'];
                }else if(3 == $row['rank_group']){
                    $value = 1 * 30 * .13 + $top5result[$index5]['count'];
                }else if(4 == $row['rank_group']){
                    $value = 1 * 30 * .09 + $top5result[$index5]['count'];
                }else if(5 == $row['rank_group']){ 
                    $value = 1 * 30 * .05 + $top5result[$index5]['count'];
                }else{
                    $value = $top5result[$index5]['count'];
                }

               echo "<tr>
                    <td></td>
                    <td><a class='tiptext'>".$row0."</a></td>
                    <td>".$value."</td>
                    <td>".$row1."</td>
                    <td>".$row2."</td>
                    <td>".$top10result[$index10]['count']."</td>
                    <td>".$top100result[$index100]['count']."</td>
               </tr>";

            }
            echo '</table><script>
            var table = $("#example").DataTable({ 
                "columnDefs": [
                     {
                        "targets": 0,
                        "checkboxes": {
                           "selectRow": true
                        }
                     }
                ],
                "select": {
                     "style": "multi"
                },
                "order": [[1, "asc"]]
            });
            
            </script>
          <form id="frm-filter" action="" method="post">
            <div class="form-group">
              <input type="text" class="form-control" id="name" placeholder="Name this list" name="name">
              <span id="error-name"></span>
            </div>
            <button type="submit" id="submit" class="btn btn-primary">Filter & get metrics</button>
          </form>
        </div>';
            
        }
    }

    //previous filtered list
    public function filter_datatable(){
        $path = "data/filter_toprankedurls.txt";
        if(file_exists($path)){
            $file = fopen($path,"r");
            $indexdata = array();
            while (($row = fgetcsv($file,0, "|")) !== FALSE) {
                
                if(!empty($row)){ ?>
                    <tr>
                        <td><?php echo $row[0]; ?></td>
                        <td>
                            <a href="<?php echo base_url('TopRanked/download/'.$row[1]); ?>" class="btn btn-link" >Download</a> 
                           
                        </td>
                        <td ><button class="btn btn-link" onclick="seeResultsRow('<?php echo $row[1]; ?>','<?php echo $row[0]; ?>','<?php echo $row[2]; ?>')"> See Results</button>
                        </td>
                    </tr>
                <?php
                }
               
            }
        }
    }

    //previous seaching data
    public function datatable(){
        // $data['keywords'] = $this->DashboardModel->select('id');
        $path = "data/main_toprankedurls.txt";
        if(file_exists($path)){
            $file = fopen($path,"r");
            $indexdata = array();
            while (($row = fgetcsv($file,0, "|")) !== FALSE) {
                
                if(!empty($row)){ ?>
                    <tr>
                        <td><?php echo $row[0]; ?></td>
                        <td>
                            <a href="<?php echo base_url('TopRanked/download/'.$row[1]); ?>" class="btn btn-link" >Download</a> 
                           
                        </td>
                        <td ><button class="btn btn-link" onclick="seeResultsRow('<?php echo $row[1]; ?>','<?php echo $row[0]; ?>','<?php echo $row[2]; ?>')"> See Results</button>
                        </td>
                    </tr>
                <?php
                }
               
            }
        }
    }

    // public function showResult(){
    //     $filename = $this->input->post('name');
    //     $keyword = $this->input->post('key');
    //     $path = "data/related-keywords/".$filename;
    //     $related_keywords_html = '';

    //     if(file_exists($path)){
    //         $file = fopen($path,"r");
    //         $row = fgetcsv($file, 0, "|");
    //         // var_dump($row);
    //         $related_keywords_html .= "<h3>Related to ".$keyword."</h3><ul>";
    //         $related_keywords_html .= '<li>'.$keyword.'</li><ul>';

    //         if(!empty($row)){
    //             foreach ($row as $r) {
    //                 $related_keywords_html .='<li>'.$r.'</li>';
    //             }
    //         }
    //         $related_keywords_html .="</ul>";
    //         $related_keywords_html .="</ul>";
    //     }else{
    //         $related_keywords_html = "File not found.";
    //     }
        

    //     echo json_encode(array("result" => true ,"data" => $related_keywords_html));
    // }

    //donwload data
    public function download($name){
        
        header("Content-Description: File Transfer"); 
        header('Content-Type: application/txt');
        header('Content-Disposition: attachment; filename='.$name);
        header('Pragma: no-cache');
        readfile("data/top-ranked-urls/".$name);

    }

    public function substr_count_array( $haystackAr, $needle ) {
        $count = 0;
        foreach ($haystackAr as $haystack) {
        $count += substr_count( $haystack, $needle);
        }
        return $count;
    }

    public function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();
       
        foreach($array as $val) {
            if(is_object($val)){
                $val = (array)$val;
            }
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
    
    public function ranked_url(){
        
        $this->form_validation->set_error_delimiters('<p style="color:red;">', '</p>');
        $this->form_validation->set_rules('keyword', 'keyword', 'required');
        $this->form_validation->set_rules('name', 'Name', 'required');

        if ($this->form_validation->run() == FALSE) {
            // $errors = validation_errors();
            echo json_encode(array('result' => false,'data'=>array('keyword' => form_error('keyword'),'name' => form_error('name'))));
            return false;
        } else {
            $name = $_POST['name'];
            $keywords = nl2br($_POST['keyword']);
            $locationcodearray = explode('<br />',$keywords);
            $locationcode = explode('||',$locationcodearray[0]);
            $location_code = trim($locationcode[1]);
            $keywords_string = preg_replace('/\s{2,}/', ' ',str_replace("<br />",",",str_replace("||","",str_replace($location_code,"",$keywords))));


            // $keyword = explode(',',$keywords);
            $api_url = 'https://api.dataforseo.com/';
            // $api_url = 'https://sandbox.dataforseo.com/';
            
            // Instead of 'login' and 'password' use your credentials from https://app.dataforseo.com/api-dashboard
            // $client = new RestClient($api_url, null, API_LOGIN, API_PASSWORD);
            $client = new RestClient($api_url, null, 'rashmita.gangani@gmail.com', 'cd9a9515025ac3ee');
                       
            $post_array = array();
            // simple way to set a task
            $post_array[] = array(
               "keywords" => [$keywords_string],
               "language_name" => "English",
               "location_code" => $location_code,
               // "location_name" => 'INDIA',
               "limit" => 5
            );

            try {
                $flag = false;
                // POST /v3/dataforseo_labs/keyword_ideas/live
                // var_dump($post_array);
                $result = $client->post('/v3/dataforseo_labs/keyword_ideas/live', $post_array);
               
                $res = $result['tasks'][0]['result'][0]['items'];
                
                if(!empty($res)){

                    $filename = slug($name.'-'.date('Ymd')).'.json';
                    $filenamejson = slug("top".$name.'-'.date('Ymd')).'.json';
                    
                    $main_filename = "data/main_toprankedurls.txt";
                    $mainrecord = fopen($main_filename,'a');
                    fputcsv($mainrecord,array($name,$filename,$filenamejson,$res[0]['keyword'],$res[0]['impressions_info']['daily_clicks_average'],$res[1]['keyword'],$res[1]['impressions_info']['daily_clicks_average'],$res[2]['keyword'],$res[2]['impressions_info']['daily_clicks_average'],$res[3]['keyword'],$res[3]['impressions_info']['daily_clicks_average'],$res[4]['keyword'],$res[4]['impressions_info']['daily_clicks_average']),"|");
                    fclose($mainrecord);

                    
                    $j = 0; $k = 0; $l = 0; 
                    $main_data = array();
                    $top_5_position = array();
                    $top_10_position = array();
                    $top_100_position = array();
                    $top5words = array(
                        array('keyword' => $res[0]['keyword'],'daily_clicks_average' => $res[0]['impressions_info']['daily_clicks_average']),
                        array('keyword' => $res[1]['keyword'],'daily_clicks_average' => $res[1]['impressions_info']['daily_clicks_average']),
                        array('keyword' => $res[2]['keyword'],'daily_clicks_average' => $res[2]['impressions_info']['daily_clicks_average']),
                        array('keyword' => $res[3]['keyword'],'daily_clicks_average' => $res[3]['impressions_info']['daily_clicks_average']),
                        array('keyword' => $res[4]['keyword'],'daily_clicks_average' => $res[4]['impressions_info']['daily_clicks_average']));
                    foreach ($top5words as $row) {
                        
                        if(!empty($row['keyword'])){
                            // echo"<pre>";var_dump($row['keyword']);
                            $daily_clicks_average = isset($row['daily_clicks_average']) ? $row['daily_clicks_average'] : 0;
                            $post_array1 = array();
                             // You can set only one task at a time
                            $post_array1[] = array(
                              "language_name" => "English",
                              "location_code" => $location_code,
                              // "location_name" => 'INDIA',
                              "keyword" => mb_convert_encoding($row['keyword'], "UTF-8")
                            );
                            try {
                                $result = $client->post('/v3/serp/google/organic/live/regular', $post_array1);
                                $res = $result['tasks'][0]['result'][0]['items'];
                               
                                if(!empty($res)){
                                    array_push($main_data, $res);
                                    $top5_i=0;$top10_i=0;$top100_i=0;
                                    foreach ($res as $data) {
                                        if($top5_i < 5){
                                            $top_5_position[$j] = $data['url'];
                                            $top5_i++;
                                           $j++;
                                        }
                                        if($top10_i < 10){
                                            $top_10_position[$k] = $data['url'];
                                            $top10_i++;
                                           $k++;
                                        }
                                        if($top100_i < 100){
                                            $top_100_position[$l] = $data['url'];
                                            $top100_i++;
                                           $l++;
                                        }
                                        if($top100_i == 100){
                                            break;
                                        }
                                        // array($data['url'],$data['title'],$data['description'],$daily_clicks_average,$data['rank_group'])
                                        // fputcsv($file,array($data['url'],$data['title'],$data['description'],$daily_clicks_average,$data['rank_group']),'|');
                                    }
                                    $flag = true;
                                }
                            }catch(RestClientException $e){
                                
                            }
                        }
                    }
                   
                    if($flag){
                        $file = fopen("data/top-ranked-urls/".$filename,'a');
                        fwrite($file, json_encode($main_data, JSON_PRETTY_PRINT));
                        fclose($file);

                        $topfile = fopen("data/top-ranked-urls/".$filenamejson, 'w');
                        fwrite($topfile, json_encode(array("top5"=>$top_5_position,"top10"=>$top_10_position,"top100"=>$top_100_position), JSON_PRETTY_PRINT));
                        fclose($topfile);

                        echo json_encode(array("result" => true,"data" =>array('file'=> $filename,'name' => $name,"top_positions"=>$filenamejson)));
                        return; 
                    }
                    // fclose($file);
                }else{
                    echo json_encode(array("result" => false,"error" => "<p style='color:red;'>API RESULT NULL.".$result['tasks'][0]['status_message']."</p>"));
                    return; 
                }
            } catch (RestClientException $e) {
               // echo "\n";// print "HTTP code: {$e->getHttpCode()}\n";// print "Error code: {$e->getCode()}\n";// print "Message: {$e->getMessage()}\n";// print  $e->getTraceAsString();// echo "\n";
                echo json_encode(array("result" => false,"error" => "<p style='color:red;'>Your API login and API password expired.</p>"));
               return;
            }
        }

    }
}
