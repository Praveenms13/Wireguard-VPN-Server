<?php

${basename(__FILE__, ".php")} = function () {
    if ($this->get_request_method() == "POST") {
        if (isset($this->_request['username']) and isset($this->_request['email']) and isset($this->_request['password'])) {
            $username = $this->_request['username'];
            $email = $this->_request['email'];
            $password = $this->_request['password'];
            if ($_SERVER['REMOTE_ADDR']!='122.165.70.136') {
                $data = [
                    "Status" => "Forbidden, Signup not allowed from this IP"
                ];
                $this->response($this->json($data), 403);
            }
            try {
                $newObj = new Signup($username, $password, $email);
                $data = [
                    "ID" => $newObj->InsertID(),
                    "Username" => $this->_request['username'],
                    "Email" => $this->_request['email'],
                    "Status" => "Signup Success, Please Verify your Email to Login:)"
                ];
                $this->response($this->json($data), 200);
            } catch (Exception $e) {
                $data = [
                    "Status" => "Failed",
                    "Error" => $e->getMessage()
                ];
                $this->response($this->json($data), 417);
            }
        } else {
            $data = [
                "Status" => "Invalid Inputs:("
            ];
            $this->response($this->json($data), 417);
        }
    } else {
        $data = [
            "Status" => "Method not allowed....",
            "Method" => $this->get_request_method()
        ];
        $this->response($this->json($data), 405);
    }
};
