<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

 public function __construct()
 {
  parent::__construct();
  $this->load->model('api_model');
  $this->load->library('form_validation');
  $this->load->helper('url');
 }

public function send_mail($arr) { 
        $this->load->config('email');
        $this->load->library('email');
        $msg=$arr;
        $from = $this->config->item('smtp_user');
        $to_email = "jainpayal0201@gmail.com"; 
        $subject = 'Email Test';
        $message = $msg['msg'];
   
        $this->email->set_newline("\r\n");
        $this->email->from($from);
        $this->email->to($to_email);
        $this->email->subject($subject);
        $this->email->message($message);
   
         //Send mail 
         if($this->email->send()) {
         
         $this->session->set_flashdata("email_sent","Email sent successfully."); 
          //   $array = array(
          //     'msg'=>'Email sent successfully.',
          //    'success'  => true
          //   );
          // echo json_encode($array, true);
        }
         else {
          $this->session->set_flashdata("email_sent","Error in sending Email."); 
         // $this->load->view('email_form'); 
          // $array = array(
          //     'msg'=>'Error in sending Email.',
          //    'success'  => false
          //   );
          // echo json_encode($array, false);
         }
         

} 


 function index()
 {
  $data = $this->api_model->fetch_all();
  echo json_encode($data->result_array());
 }
 
 function insert()
 {
  $this->form_validation->set_rules("first_name", "First Name", "required");
  $this->form_validation->set_rules("last_name", "Last Name", "required");
  $array = array();
  if($this->form_validation->run())
  {
   $data = array(
    'first_name' => trim($this->input->post('first_name')),
    'last_name'  => trim($this->input->post('last_name'))
   );
   $this->api_model->insert_api($data);
   $msg=array(
    'msg'=>'Data Inserted in database successfully' 
  );
   $this->send_mail($msg);
   $array = array(
    'success'  => true
   );
  }
  else
  {
   $array = array(
    'error'    => true,
    'first_name_error' => form_error('first_name'),
    'last_name_error' => form_error('last_name')
   );
  }
  echo json_encode($array, true);
 }

 function fetch_single()
 {
  if($this->input->post('id'))
  {
   $data = $this->api_model->fetch_single_user($this->input->post('id'));
   foreach($data as $row)
   {
    $output['first_name'] = $row["first_name"];
    $output['last_name'] = $row["last_name"];
   }
   echo json_encode($output);
  }
 }

 function update()
 {
  $this->form_validation->set_rules("first_name", "First Name", "required");
  $this->form_validation->set_rules("last_name", "Last Name", "required");
  $array = array();
  if($this->form_validation->run())
  {
   $data = array(
    'first_name' => trim($this->input->post('first_name')),
    'last_name'  => trim($this->input->post('last_name'))
   );
   $this->api_model->update_api($this->input->post('id'), $data);
   $msg=array(
    'msg'=>'Data Updated in database successfully' 
  );
   $this->send_mail($msg);
   $array = array(
    'success'  => true
   );
  }
  else
  {
   $array = array(
    'error'    => true,
    'first_name_error' => form_error('first_name'),
    'last_name_error' => form_error('last_name')
   );
  }
  echo json_encode($array, true);
 }

 function delete()
 {
  if($this->input->post('id'))
  {
   if($this->api_model->delete_single_user($this->input->post('id')))
   {
    $msg=array(
    'msg'=>'Data Deleted in database successfully' 
     );
   $this->send_mail($msg);
    $array = array(
     'success' => true
    );
   }
   else
   {
    $array = array(
     'error' => true
    );
   }
   echo json_encode($array);
  }
 }
 
}
