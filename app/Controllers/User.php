<?php

namespace App\Controllers;

use App\Models\UsersModel;
use CodeIgniter\API\ResponseTrait;

class User extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        return view('welcome_message');
    }

    public function getUsers($slug = false){
        $usersModel = new UsersModel();
        if ($slug !== false) {
            $users = $usersModel->where(['id' => $slug])->orWhere(['email' => $slug])->first();
        } else {
            $users = $usersModel->findAll();
        }
        return $this->respond(['users' => $users], 200);
    }

    public function createUser(){
        $rules = [
            'name' => ['rules' => 'required|min_length[4]|max_length[255]'],
            'email' => ['rules' => 'required|min_length[4]|max_length[255]|valid_email|is_unique[users.email]'],
            'password' => ['rules' => 'required|min_length[8]|max_length[255]'],
            'confirm_password'  => [ 'label' => 'confirm password', 'rules' => 'required|matches[password]']
        ];
            
  
        if($this->validate($rules)){
            $model = new UsersModel();
            $data = [
                'name'     => $this->request->getVar('name'),
                'email'    => $this->request->getVar('email'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                'user_group'    => 1
            ];
            $model->save($data);
             
            return $this->respond([
                'status' => "200",
                'message' => 'Registered Successfully'
            ], 200);
        }else{
            $response = [
                'status' => 400,
                'messages' => [
                    "errName" => $this->validator->getError('name'),
                    "errEmail" => $this->validator->getError('email'),
                    "errPassword" => $this->validator->getError('password'),
                    "errConfirmPassword" => $this->validator->getError('confirm_password'),
                ],
            ];
            return $this->respond($response, 200);
             
        }
    }
}
