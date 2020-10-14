<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
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
       $this->load->view('keyword/index');
    }

    public function datatable(){
        // $data['keywords'] = $this->DashboardModel->select('id');
      $path = "data/main_relatedkeywords.txt";
      if(file_exists($path)){
        $file = fopen($path,"r");
        $indexdata = array();
        while (($row = fgetcsv($file,0, "|")) !== FALSE) {
            
            if(!empty($row)){ ?>
                <tr>
                    <td><label><?php echo $row[0]; ?></label></td>
                    <td>
                        <a href="<?php echo base_url('Dashboard/download/'.$row[1]); ?>" class="btn btn-link" >Download</a> |
                       <button class="btn btn-link" onclick="seeResultsRow('<?php echo $row[1]; ?>','<?php echo $row[0]; ?>')"> See Results</button>
                    </td>
                </tr>
            <?php
            }
           
        }
      }
        // var_dump($indexdata);die;
        
    }

    public function show(){
        $filename = $this->input->post('name');
        $keyword = $this->input->post('key');
        $path = "data/related-keywords/".$filename;
        $related_keywords_html = '';

        if(file_exists($path)){
            $file = fopen($path,"r");
            $row = fgetcsv($file, 0, "|");
            // var_dump($row);
            $related_keywords_html .= "<h3>Related to ".$keyword."</h3><ul>";
            $related_keywords_html .= '<li>'.$keyword.'</li><ul>';

            if(!empty($row)){
                foreach ($row as $r) {
                    $related_keywords_html .='<li>'.$r.'</li>';
                }
            }
            $related_keywords_html .="</ul>";
            $related_keywords_html .="</ul>";
        }else{
            $related_keywords_html = "File not found.";
        }
        

        echo json_encode(array("result" => true ,"data" => $related_keywords_html));


    }

    public function download($name){
        
        header("Content-Description: File Transfer"); 
        header('Content-Type: application/txt');
        header('Content-Disposition: attachment; filename='.$name);
        header('Pragma: no-cache');
        readfile("data/related-keywords/".$name);

    }

    // function flip_row_col_array($array) {
    //     $out = array();
    //     foreach ($array as  $rowkey => $row) {
    //         if(!empty($row)){
    //             foreach($row as $colkey => $col){
    //                 $out[$colkey][$rowkey]=$col;
    //             }
    //         }
    //     }
    //     return $out;
    // }

   
    public function raletedKeyword(){
        
        $this->form_validation->set_error_delimiters('<p style="color:red;">', '</p>');
        $this->form_validation->set_rules('keyword', 'keyword', 'required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo json_encode(array('result' => false,'data'=>$errors));
            return false;
        } else {
            $keyword = $_POST['keyword'];

            // You can download this file from here https://cdn.dataforseo.com/v3/examples/php/php_RestClient.zip
            // require('RestClient.php');
            $api_url = 'https://api.dataforseo.com/';

            // Instead of 'login' and 'password' use your credentials from https://app.dataforseo.com/api-dashboard
            $client = new RestClient($api_url, null, API_LOGIN, API_PASSWORD);
            // $client = new RestClient($api_url, null, 'rashmita.gangani@gmail.com', 'cd9a9515025ac3ee');

            // simple way to set a task
            $post_array = array();
            $post_array[] = array(
               "keyword" => $keyword,
               "language_name" => "English",
               "location_name" => 'INDIA',
            );
            try {
                $related_keywords_html = '';
                // POST /v3/dataforseo_labs/related_keywords/live
                $result = $client->post('/v3/dataforseo_labs/related_keywords/live', $post_array);
                // $result = array(
                //   "version"=> "0.1.20200923",
                //   "status_code"=>20000,
                //   "status_message"=> "Ok.",
                //   "time"=>"0.0516 sec.",
                //   "cost"=>0.0109,
                //   "tasks_count"=> 1,
                //   "tasks_error"=> 0,
                //   "tasks"=>array(
                //       0 => array(
                //             "id" =>"10130955-2367-0124-0000-f7e92e747ab7",
                //             "status_code" =>20000,
                //             "status_message" =>"Ok.",
                //             "time" =>"0.0276 sec.",
                //             "cost" => 0.0109,
                //             "result_count" =>1,
                //             "path" =>array(
                //               0=>"v3",
                //               1=> "dataforseo_labs",
                //               2=> "related_keywords",
                //               3=>"live"
                //             ),
                //             "data"=>
                //             array(
                //               "api" => "dataforseo_labs",
                //               "function" => "related_keywords",
                //               "keyword" => "cricket",
                //               "language_name" => "English",
                //               "location_name"=> "INDIA"
                //             ),
                //             "result"=>
                //               array(
                //                 0 =>
                //                 array(
                //                   "seed_keyword"=>"cricket",
                //                   "seed_keyword_data"=>NULL,
                //                   "location_code"=>2356,
                //                   "language_code"=>"en",
                //                   "total_count"=>9,
                //                   "items_count"=>9,
                //                   "items" =>
                //                   array(
                //                     0=>
                //                     array(
                //                       "keyword_data"=>
                //                       array(
                //                         "keyword"=>"cricket",
                //                         "location_code"=>2356,
                //                         "language_code"=>"en",
                //                         "keyword_info"=>
                //                         array(
                //                           "last_updated_time"=> "2020-09-25T10:52:20.6967677Z",
                //                           "competition"=>0.012620933765024,
                //                           "cpc"=>0.252268,
                //                           "search_volume"=>7480000,
                //                           "categories"=>
                //                           array(
                //                             0=>10013,
                //                             1=>10014,
                //                             2=>10108,
                //                             3=>10110,
                //                             4=>10114,
                //                             5=>10597,
                //                             6=>13605,
                //                             7=>13607
                //                           ),
                //                           "monthly_searches"=>
                //                           array(
                //                             0=>
                //                             array(
                //                               "year"=>2020,
                //                               "month"=>8,
                //                               "search_volume"=>2740000
                //                             ),
                //                             1=>
                //                             array(
                //                               "year"=>2020,
                //                               "month"=>7,
                //                               "search_volume"=>2240000
                //                             ),
                //                             2=>
                //                             array(
                //                               "year"=>2020,
                //                               "month"=>6,
                //                               "search_volume"=>1220000
                //                             ),
                //                             3=>
                //                             array(
                //                               "year"=>2020,
                //                               "month"=>5,
                //                               "search_volume"=>1500000
                //                             ),
                //                             4=>
                //                             array(
                //                               "year"=>2020,
                //                               "month"=>4,
                //                               "search_volume"=>1500000
                //                             ),
                //                             5=>
                //                             array(
                //                               "year"=>2020,
                //                               "month"=>3,
                //                               "search_volume"=>5000000
                //                             ),
                //                             6=>
                //                             array(
                //                               "year"=> 2020,
                //                               "month"=>2,
                //                               "search_volume"=>13600000
                //                             ),
                //                             7=>
                //                             array(
                //                               "year"=>2020,
                //                               "month"=>1,
                //                               "search_volume"=>20400000
                //                             ),
                //                             8=>
                //                             array(
                //                               "year"=> 2019,
                //                               "month"=> 12,
                //                               "search_volume"=> 11100000
                //                             ),
                //                             9=>
                //                             array(
                //                               "year"=> 2019,
                //                               "month"=> 11,
                //                               "search_volume"=>9140000
                //                             ),
                //                             10=>
                //                             array(
                //                               "year"=>2019,
                //                               "month"=>10,
                //                               "search_volume"=> 9140000
                //                             ),
                //                             11=>
                //                             array(
                //                               "year"=>2019,
                //                               "month"=> 9,
                //                               "search_volume"=>
                //                               6120000
                //                             )
                //                           )
                //                         ),
                //                         "impressions_info"=>
                //                         array(
                //                           "last_updated_time"=> "2020-09-24T12:00:06.6529367Z",
                //                           "bid"=>999,
                //                           "match_type"=>"exact",
                //                           "ad_position_min"=>1.11,
                //                           "ad_position_max"=> 1,
                //                           "ad_position_average"=>1.06,
                //                           "cpc_min"=>4.14,
                //                           "cpc_max"=> 5.06,
                //                           "cpc_average"=> 4.6,
                //                           "daily_impressions_min"=>2226.92,
                //                           "daily_impressions_max"=>2721.79,
                //                           "daily_impressions_average"=>2474.35,
                //                           "daily_clicks_min"=>175.85,
                //                           "daily_clicks_max"=>214.93,
                //                           "daily_clicks_average"=>195.39,
                //                           "daily_cost_min"=>809.47,
                //                           "daily_cost_max"=>989.36,
                //                           "daily_cost_average"=>899.41
                //                         )
                //                       ),
                //                       "depth"=> 0,
                //                       "related_keywords"=>
                //                       array(
                //                         0=> "cricket score",
                //                         1=> "cricket live",
                //                         2=>  "live cricket score cricbuzz",
                //                         3=> "cricket live score",
                //                         4=> "cricket news",
                //                         5=> "live cricket online",
                //                         6=>  "cricket live video",
                //                         7=>  "cricket india"
                //                       )
                //                     )
                                    
                //                   )
                //                 )
                //               )
                //       )
                //   )
                // );
                
                
                $res = $result['tasks'][0]['result'][0]['items'][0];
               
                if(!empty($res)){
                    $filename = slug($keyword.'-'.date('Ymd')).'.txt';
                    if(file_exists('data/related-keywords/'.$filename)){
                        $file = fopen('data/related-keywords/'.$filename,'w');
                    }else{
                         $file = fopen('data/related-keywords/'.$filename,'a');
                    }

                    $related_keywords_html .= "<h3>Searche Related to ".$keyword."</h3><ul>";
                    $related_keywords_html .= '<li>'.$res['keyword_data']['keyword'].'</li><ul>';
                    $related_keywords = isset($res['related_keywords']) ? $res['related_keywords'] : array('');

                    // var_dump($related_keywords);die;

                    if(!empty($related_keywords)){
                        foreach ($related_keywords as $rows) {
                            $related_keywords_html .='<li>'.$rows.'</li>';
                        }
                        
                        fputcsv($file,$related_keywords,'|');
                        fclose($file); 
                    }
                    $related_keywords_html .="</ul>";
                    $related_keywords_html .="</ul>";

                    //row enrty added
                    $mainfile = 'data/main_relatedkeywords.txt';
                    $mainrecord = fopen($mainfile, 'a');
                    fputcsv($mainrecord,array($keyword,$filename),"|");
                    fclose($mainrecord);
                }

                echo json_encode(array("result" => true,"data" => $related_keywords_html));
                return;

            } catch (RestClientException $e) {
               // echo "\n";// print "HTTP code: {$e->getHttpCode()}\n";
               // print "Error code: {$e->getCode()}\n";// print "Message: {$e->getMessage()}\n";// print  $e->getTraceAsString();// echo "\n";

               echo json_encode(array("result" => false,"data" => "<p style='color:red;'>Your API login and API password expired.</p>"));
               return;
            }
        }
    }
}
