<?php 
namespace App\Controllers;
use \CodeIgniter\Controller;
use App\Libraries\TestLibrary;

class CustomLib extends Controller
{
public $tl;
public function __construct() {
     $this->tl = new TestLibrary();
}
public function index() {
    $result = $this->tl->getData();
    print_r($result);
}
}
