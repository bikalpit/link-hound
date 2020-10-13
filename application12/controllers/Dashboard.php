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
        $this->load->model('DashboardModel');
        include APPPATH . 'third_party/RestClient.php';
        
    }

    public function index()
    {
        // $data['keywords'] = $this->DashboardModel->select('id');
        $file = fopen("assets/main.csv","r");
        $indexdata = array('');
        while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
            //var_dump($row[0]);
            if(!empty($row[0])){
                array_push($indexdata, $row);
            }
        }
        // var_dump($indexdata);die;
        $data['keywords'] = $indexdata;
        $this->load->view('keyword/index',$data);
    }

    public function show(){
        $id = $this->input->post('id');
        $path = "assets/csv/".$id;
        $indexdata = array('');
        $heading = array('');

        // var_dump($path);
        // var_dump(file_exists($path));

        if(file_exists($path)){
            $file = fopen($path,"r");
            // var_dump($file);die;
            $flag = true;
            while (($row = fgetcsv($file, 0, ",")) !== FALSE) {
                
                if(!empty($row[0]) && $flag){
                    array_push($heading, $row); 
                    $flag = false;
                }else{
                    array_push($indexdata, $row);
                }
            }
        }
        $count = count($heading);
        $related_keywords_html = "<ul>";
        // for($i=0;$i>$count;$i++){
        $array1 = '';
        foreach ($indexdata as $row) {
            // var_dump($row);die;
            $array1 .= "<li>".$row[0]."</li>";
            $array2 .= "<li>".$row[1]."</li>";
            $array3 .= "<li>".$row[2]."</li>";
            $array4 .= "<li>".$row[3]."</li>";
            $array5 .= "<li>".$row[4]."</li>";
            $array6 .= "<li>".$row[5]."</li>";
            $array7 .= "<li>".$row[6]."</li>";
            $array8 .= "<li>".$row[7]."</li>";
            $array9 .= "<li>".$row[8]."</li>";
        }

        var_dump($array1);die;

        // var_dump($indexdata);die;
        // $keyword = $this->DashboardModel->select_one(array("id"=>$id));
        $data = $indexdata;
        // $data = $this->flip_row_col_array($indexdata);
        // $related_keywords_html = "<ul>";
        // $newarray = '';
        // if(!empty($data)){
        //     foreach ($data as $row) {
        //         if(!empty($row[0])){
        //             // var_dump($row);die;

        //             $related_keywords_html .= "<li>".$row[1]."</li>";

        //             var_dump($row);die;
        //         }
        //     }
        // }
        // $related_keywords_html .= "<ul>";
        // var_dump($data);die;
       
        // var_dump($related_keywords_html);die;
        echo json_encode(array("result" => true ,"data" => $related_keywords_html));


    }

    public function download($name){
        
        header("Content-Description: File Transfer"); 
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename='.$name);
        header('Pragma: no-cache');
        readfile("assets/csv/".$name);

        //  // file name 
        // $filename = 'keyword_'.date('Ymd').'.csv'; 
        // header("Content-Description: File Transfer"); 
        // header("Content-Disposition: attachment; filename=$filename"); 
        // header("Content-Type: application/csv; ");
       
        // // get data 
        // // $id = $this->input->post('id');
        // $keyword = $this->DashboardModel->select_one(array("id"=>$id));
        // $response_data = json_decode($keyword['response_data']);
        // // file creation 
        // $file = fopen('php://output', 'w');
     
        
        // $heading = array();
        // $subkeywords = array();
        // foreach ($response_data as $row) {
        //     array_push($heading, strtoupper(key($row)));

        //     // $related_keywords_html .= '<li>'.key($row).'</li><ul>';
        //     $subarray = array_values(get_object_vars($row));
        //     if(!empty($subarray[0])){
        //         array_push($subkeywords, $subarray[0]);
        //         //foreach ($subarray[0] as $row) {
        //             // $related_keywords_html .= "<li>".$row."</li>";
        //         //}
        //     }else{
        //         array_push($subkeywords, array(0 =>'',1 =>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>''));
        //     }
        //     // $related_keywords_html .="</ul>";
        // }
       
        // $header = $heading; 
        // fputcsv($file, $header);
        // $rowwisedata = $this->flip_row_col_array($subkeywords);
        // foreach($rowwisedata as $row){ 
        //     fputcsv($file,$row); 
        // }
        // fclose($file); 
        // exit; 
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
        // var_dump($_POST);
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
            $client = new RestClient($api_url, null, 'rashmitag205@gmail.com', '395109962484d5a1');

            $post_array = array();
            // simple way to set a task
            $post_array[] = array(
               // "keyword" => "phone",
               "keyword" => $keyword,
               "language_name" => "English",
               "location_name" => 'INDIA',
               // "filters" => [
               //    ["keyword_data.impressions_info.ad_position_average", ">", 1],
               //    "and",
               //    [
               //       ["keyword_data.impressions_info.cpc_max", "<", 0.5],
               //       "or",
               //       ["keyword_data.impressions_info.daily_clicks_max", ">=", 10]
               //    ]
               // ]
            );
            try {
                // POST /v3/dataforseo_labs/related_keywords/live
                $result = $client->post('/v3/dataforseo_labs/related_keywords/live', $post_array);
                $res = $result['tasks'][0]['result'][0]['items'];
               
                $related_keywords_html = "<h3>Searche Related to ".$keyword."</h3><ul>";
                $related_keywords = array();
                $heading = array();
                $subkeywords = array();
                foreach ($res as $rows) {
                    $related_keywords_html .= '<li>'.$rows['keyword_data']['keyword'].'</li><ul>';
                    array_push($heading,strtoupper($rows['keyword_data']['keyword']));
                    $keyword_row = array();
                    if(!empty($rows['related_keywords'])){
                        array_push($subkeywords, $rows['related_keywords']);
                        foreach ($rows['related_keywords'] as $row1) {
                            $related_keywords_html .= "<li>".$row1."</li>";
                            array_push($keyword_row,$row1);
                        }
                    }else{
                        array_push($subkeywords, array(0 =>'',1 =>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>''));
                    }
                    array_push($related_keywords, array($rows['keyword_data']['keyword'] => $keyword_row));
                    $related_keywords_html .="</ul>";
                }
                $related_keywords_html .="</ul>";



                $filename = $keyword.'.csv';
                $file = fopen('assets/csv/'.$filename, 'a');
                
                $mainfile = 'assets/main.csv';
                $mainrecord = fopen($mainfile, 'a');
                fputcsv($mainrecord,array($keyword,$filename));
                fclose($mainrecord);

                $header = $heading; 
                fputcsv($file, $header);
                $rowwisedata = $this->flip_row_col_array($subkeywords);
                foreach($rowwisedata as $row){ 
                    fputcsv($file,$row); 
                }
                fclose($file); 

                // $insertArr = array(
                //     'keyword_name'  => $keyword,
                //     'response_data' => json_encode($related_keywords)
                // );
                // // var_dump($insertArr);die;
                // $insert_id = $this->DashboardModel->insert($insertArr);
                echo json_encode(array("result" => true,"data" => $related_keywords_html));
                return;
            } catch (RestClientException $e) {
               // echo "\n";// print "HTTP code: {$e->getHttpCode()}\n";
               // print "Error code: {$e->getCode()}\n";// print "Message: {$e->getMessage()}\n";// print  $e->getTraceAsString();// echo "\n";

               echo json_encode(array("result" => false,"data" => "Something was wrong."));
               return;
            }
        }
    }
}
