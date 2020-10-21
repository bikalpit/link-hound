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
            // $row = fgetcsv($file, 0, "|");
            $res = array();
            $header = NULL;
            $heading = NULL;
            $data = array();
            while (($row = fgetcsv($file, 0, "|")) !== FALSE) {
                array_push($res, $row);
            }
            //row wise data convert in to column
            $retData = array();
            foreach ($res as $row => $columns) {
                foreach ($columns as $row2 => $column2) {
                  $retData[$row2][$row] = $column2;
                }
            }
            $related_keywords_html .= "<h3>Related to ".$keyword."</h3><ul>";
            foreach ($retData as $rows) {
                $flag = 1;
                foreach ($rows as $row) {
                    if($flag == 1){
                      $related_keywords_html .= '<li>'.$row.'</li><ul>';
                      $flag = 0;
                    }else{
                        if(!empty($row))
                            $related_keywords_html .= '<li>'.$row.'</li>'; 
                    }
                }
                $related_keywords_html .="</ul>";
            }
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

    function flip_row_col_array($array) {
        $out = array();
        foreach ($array as  $rowkey => $row) {
            if(!empty($row)){
                foreach($row as $colkey => $col){
                    $out[$colkey][$rowkey]=$col;
                }
            }
        }
        return $out;
    }

   
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
                //---start related_keyword API parameter-----
                "keyword" => $keyword,
                "language_name" => "English",
                "location_name" => 'United States',
                "order_by" => ["keyword_data.keyword,desc"]
                //---end related_keyword API parameter-----

                //---start keyword_idea API parameter-----
                // "keywords" => [
                //   $keyword
                // ],
                // "language_name" => "English",
                // "closely_variants" =>true,
                // "limit" => 10,
                // "location_name" => 'United States',
                // "order_by" => ["relevance,desc"]
                //---end keyword_idea API parameter-----
            );
            try {
                $related_keywords_html = '';
                // POST /v3/dataforseo_labs/related_keywords/live
                
                //---start related_keyword API parameter-----
                $result = $client->post('/v3/dataforseo_labs/related_keywords/live', $post_array);
                // var_dump($result);die;
                //---end related_keyword API parameter-----

                //---start keyword_idea API parameter-----
                // $result = $client->post('/v3/dataforseo_labs/keyword_ideas/live', $post_array);
                //---end keyword_idea API parameter-----
                // echo "<pre>";var_dump($result);die;
                $res = $result['tasks'][0]['result'][0]['items'];
                
                if(!empty($res)){
                    $filename = slug($keyword.'-'.date('Ymd')).'.txt';
                    if(file_exists('data/related-keywords/'.$filename)){
                        $file = fopen('data/related-keywords/'.$filename,'w');
                    }else{
                         $file = fopen('data/related-keywords/'.$filename,'a');
                    }
                    //---------title and subtile code start---------
                 //    $related_keywords_html = "<h3>Searche Related to ".$keyword."</h3><ul>";
                    // $related_keywords = array();
                    // $heading = array();
                    // $subkeywords = array();
                    //foreach ($res as $rows) {
                        // var_dump($rows['keyword']."<br/>");
                        // die;
                        // $related_keywords_html .= '<li>'.$rows['keyword_data']['keyword'].'</li><ul>';
                        // array_push($heading,strtoupper($rows['keyword_data']['keyword']));
                        // $keyword_row = array();
                        // if(!empty($rows['related_keywords'])){
                        //     array_push($subkeywords, $rows['related_keywords']);
                        //     foreach ($rows['related_keywords'] as $row1) {
                        //         $related_keywords_html .= "<li>".$row1."</li>";
                        //         array_push($keyword_row,$row1);
                        //     }
                        // }else{
                        //     array_push($subkeywords, array(0 =>'',1 =>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>''));
                        // }
                        // array_push($related_keywords, array($rows['keyword_data']['keyword'] => $keyword_row));
                        // $related_keywords_html .="</ul>";
                    //}
                    //$related_keywords_html .="</ul>";
                    // $header = $heading; 
                    // fputcsv($file, $header,"|");
                    // $rowwisedata = $this->flip_row_col_array($subkeywords);
                    // foreach($rowwisedata as $row){ 
                    //     fputcsv($file,$row,"|"); 
                    // }
                    // fclose($file);
                    //--------title and subtile code end-------------

                    //---- start related_keyword API display only first related---
                    $related_keywords_html .= "<h3>Searche Related to ".$keyword."</h3><ul>";
                    $i=0;
                    $related_keywords = array();
                    $notmatch_related_keywords = array();
                    foreach ($res as $row) {
                        if(strtolower($row['keyword_data']['keyword']) == strtolower($keyword)){
                            $related_keywords = $row['related_keywords'];
                            break;
                        }
                        array_push($notmatch_related_keywords,$row['keyword_data']['keyword']);
                        $i++;
                    }
                    if(!empty($related_keywords)){
                        foreach ($related_keywords as $rows) {
                            $related_keywords_html .='<li>'.$rows.'</li>';
                        }
                        
                        fputcsv($file,$related_keywords,'|');
                        fclose($file); 
                    }else{
                        if(!empty($notmatch_related_keywords)){
                            foreach ($notmatch_related_keywords as $rows) {
                                $related_keywords_html .='<li>'.$rows.'</li>';
                            }
                            
                            fputcsv($file,$notmatch_related_keywords,'|');
                            fclose($file); 
                        }else{
                            echo json_encode(array("result" => false,"data" => "<p style='color:red;'>API not give any responce for  related searche Keyword : \"".$keyword."\"</p>","api_responce"=>$res));
                            return;
                        }
                    }
                    $related_keywords_html .="</ul>";
                   
                    //---- end related_keyword API display only first related---

                    //---start keyword_idea API call display all keyword---
                    // $related_keywords_html .= "<h3>Searche Related to ".$keyword."</h3><ul>";
                    // if(!empty($res)){
                    //     $keywordstore = array();
                    //     foreach ($res as $rows) {
                    //         $related_keywords_html .='<li>'.$rows['keyword'].'</li>';
                    //         array_push($keywordstore,$rows['keyword']);
                    //     }
                    //     fputcsv($file,$keywordstore,'|');
                    //     fclose($file); 
                    // }
                    // $related_keywords_html .="</ul>";
                    //---end keyword_idea API call display all keyword---

                    
                    //row enrty added
                    $mainfile = 'data/main_relatedkeywords.txt';
                    $mainrecord = fopen($mainfile, 'a');
                    fputcsv($mainrecord,array($keyword,$filename),"|");
                    fclose($mainrecord);
                }else{
                    echo json_encode(array("result" => false,"data" => "<p style='color:red;'>API not give any responce for  related searche Keyword : \"".$keyword."\"</p>","api_responce"=>$res));
                    return;
                }

                echo json_encode(array("result" => true,"data" => $related_keywords_html,"api_responce"=>$res));
                return;

            } catch (RestClientException $e) {
               // echo "\n";// print "HTTP code: {$e->getHttpCode()}\n";
               // print "Error code: {$e->getCode()}\n";// print "Message: {$e->getMessage()}\n";// print  $e->getTraceAsString();// echo "\n";

               echo json_encode(array("result" => false,"data" => "<p style='color:red;'>Your API login and API password expired.</p>","api_responce"=>''));
               return;
            }
        }
    }
}
