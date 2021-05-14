<?php

namespace App\Controllers;
use CodeIgniter\Controller;

use App\Models\RegisterModel;

class Register extends Controller
{
 public $registerModel;
     public $session;
     public $email;
    public function __construct() {
        helper("form");
        $this->registerModel = new RegisterModel();
         $this->session = \Config\Services::session();
        $this->email = \Config\Services::email();
    }
	public function index()
	{
        $data = [];
         $data['validation'] = null;
        if($this->request->getMethod() == 'post') {
            $rules =[
                'uname' => 'required',
                'password' => 'required',
                'rpassword' => 'required|matches[password]',
                'email' => 'required|valid_email',
                'mobile' => 'required|numeric',
               
            ];
            if($this->validate($rules))
            {
                // echo "save data";
                 $uniid =md5(str_shuffle('abcdefghijklmnopqrstuvwxyz'.time() ));
                 $userdata =[
                     'username' => $this->request->getVar('uname',FILTER_SANITIZE_STRING),         
                     'email' => $this->request->getVar('email'),   
                     'password' => password_hash($this->request->getVar('password'),PASSWORD_DEFAULT),         
                   'mobile' => $this->request->getVar('mobile'),            
                     'uniid' =>   $uniid,
                  'activation_date'  => date("Y-m-d h:i:s"),         
                         ];
                   if($this->registerModel->createUser($userdata)){
                        $to =  $this->request->getVar('email');
                       $subject = 'Email sending';
                        $message = "Hi thanks, for your test mail"
                        .  "<a href='". base_url()."/register/activate/".$uniid."'>Activate Link </a>";
                        // $email = \Config\Services::email();
                        $this->email->setTo($to);
                        $this->email->setFrom('shanimolschikku@gmail.com', 'test');
                        $this->email->setSubject($subject);
                        $this->email->setMessage($message);
                        $filepath ='public/assets/images/logo.jpg';
                        $this->email->attach($filepath);

                //         $this->email->attach($filepath);
                       if($this->email->send())
                       {
                        $this->session->setTempdata('success', 'Thanks, we will get you soon...', 3);
                            return redirect()->to(current_url());
                            //  echo "sucess";
                         }
                        else{
                           $data = $this->email->printDebugger(['headers']);
                           print_r($data);
                        // echo "errrrrrr";
                         }
                    
                    }
                       else{

                       echo "sadas";
                       $this->session->setTempdata('error', 'Errror', 3);
                 return redirect()->to(current_url());
                        }
            }
            else{
                $data['validation'] =$this->validator; 
            }
        }
		return view("register_view",$data);

	}

    public function activate($uniid=null) 
    {
        $data=[];
        if(!empty($uniid)) {
            echo "dsfdg";
            return view("activate_view",  $data);

        $data = $this->registerModel->verifyUniid($uniid);
        // echo $this->registerModel->verifyUniid($uniid);
        print_r($data);
        exit;
        //  $this->registerModel->verifyUniid($uniid);
        }else{
            $data['error'] ='Sorry! unable to process your request';
        }
        return view("activate_view",  $data);
    }
}