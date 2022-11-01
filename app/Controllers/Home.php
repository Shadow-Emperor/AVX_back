<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

use App\Models\firebaseConnect;

class Home extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $firebaseConnect = model('App\Models\firebaseConnect');
        $data = $firebaseConnect -> gConnect();
        return view('welcome_message');
    }

    public function songSearch($key)
    {
        $model = new firebaseConnect();
        $data = $model -> fireStore($key);
        $resp = json_encode($data);
        log_message(7,print_r($resp,TRUE));
        return $this->respond($resp);
    }
}
