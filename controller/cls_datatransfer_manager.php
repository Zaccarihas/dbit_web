<?php
class Datatransfer_manager{

    public $post;
    public $get;
    public $session;

    public function __construct()
    {
        $this->get = $_GET??null;
        $this->post = isset($_POST)?$this->post_cleaner():null;
        $this->session = isset($_SESSION)?$_SESSION:null;
    }

    // Replace empty string with null
    public function post_cleaner(){
        return array_map(function ($value) {
            return $value === "" ? null : $value;
        }, $_POST);
    }

    public function get_set(){
        return $this->get !== null;
    }

    public function post_set(){
        return $this->post !== null;
    }

    public function session_set(){
        return $this->session !== null;
    }

}