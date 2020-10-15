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

    public function show()
    {
       $this->load->view('toprank/show_top_rankurl');
       // $this->load->view('toprank/index12');
    }

    public function data(){
        $filename = $this->input->post('filename');
        $name= $this->input->post('name');
        
        $path = "data/top-ranked-urls/".$filename;
        if(file_exists($path)){
            $file = fopen($path,"r");
            $indexdata = array();
            $i = 0;
            echo "<label style='font-size: 23px;'>".$name.': Top ranked URLs</label> <br/><table id="example" class="table-striped display">
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
            while (($row = fgetcsv($file,0, "|")) !== FALSE) { 
                if($i == 10){
                    break;
                }
               echo "<tr>
                    <td></td>
                    <td>".$row[0]."</td>
                    <td>-</td>
                    <td>".$row[1]."</td>
                    <td>".$row[2]."</td>
                    <td>-</td>
                    <td>-</td>
               </tr>";

                
                $i++;
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
            });</script>';
            
        }
    }

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
                            <a href="<?php echo base_url('Dashboard/download/'.$row[1]); ?>" class="btn btn-link" >Download</a> 
                           
                        </td>
                        <td ><button class="btn btn-link" onclick="seeResultsRow('<?php echo $row[1]; ?>','<?php echo $row[0]; ?>')"> See Results</button>
                        </td>
                    </tr>
                <?php
                }
               
            }
        }
    }

    public function showResult(){
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

    
    public function ranked_url(){
        
        $this->form_validation->set_error_delimiters('<p style="color:red;">', '</p>');
        $this->form_validation->set_rules('keyword', 'keyword', 'required');
        $this->form_validation->set_rules('name', 'Name', 'required');

        if ($this->form_validation->run() == FALSE) {
            // $errors = validation_errors();
            echo json_encode(array('result' => false,'data'=>array('keyword' => form_error('keyword'),'name' => form_error('name'))));
            return false;
        } else {
            $keyword = $_POST['keyword'];
            $name = $_POST['name'];
            // $api_url = 'https://api.dataforseo.com/';
            $api_url = 'https://sandbox.dataforseo.com/';
            
            // Instead of 'login' and 'password' use your credentials from https://app.dataforseo.com/api-dashboard
            $client = new RestClient($api_url, null, API_LOGIN, API_PASSWORD);
            // $client = new RestClient($api_url, null, 'rashmita.gangani@gmail.com', 'cd9a9515025ac3ee');
                       
            $post_array = array();
            // simple way to set a task
            $post_array[] = array(
               "keywords" => [
                  "phone",
                  "watch"
               ],
               "language_name" => "English",
               "location_code" => 2840,
               "limit" => 5
            );
            try {
                $flag = false;
                // POST /v3/dataforseo_labs/keyword_ideas/live
                $result = $client->post('/v3/dataforseo_labs/keyword_ideas/live', $post_array);
               
                $res = $result['tasks'][0]['result'][0]['items'];
                // echo "<pre>";
                // var_dump($res);
                if(!empty($res)){

                    $filename = slug($name.'-'.date('Ymd')).'.txt';

                    $main_filename = "data/main_toprankedurls.txt";
                    $mainrecord = fopen($main_filename,'a');
                    fputcsv($mainrecord,array($name,$filename),"|");
                    fclose($mainrecord);

                    $file = fopen("data/top-ranked-urls/".$filename,'a');
                    
                    foreach ($res as $row) {
                        
                        if(!empty($row['keyword'])){
                            // echo "<pre>";var_dump($row['keyword']);

                            $post_array1 = array();
                            // You can set only one task at a time
                            $post_array1[] = array(
                              "language_code" => "en",
                              "location_code" => 2840,
                              "keyword" => mb_convert_encoding($row['keyword'], "UTF-8")
                            );
                            try {
                                $result = $client->post('/v3/serp/google/organic/live/regular', $post_array1);
                                $res = $result['tasks'][0]['result'][0]['items'];
                                // echo "<pre>";var_dump($result);die;
                                if(!empty($res)){
                                    foreach ($res as $data) {
                                        
                                        fputcsv($file,array($data['url'],$data['title'],$data['description']),'|');
                                    }
                                    $flag = true;
                                }
                            }catch(RestClientException $e){
                                
                            }
                        }
                    }
                    if($flag){
                        echo json_encode(array("result" => true,"data" =>array('file'=> $filename,'name' => $name)));
                        return; 
                    }
                    fclose($file);
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
