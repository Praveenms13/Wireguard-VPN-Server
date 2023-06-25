<?php

${basename(__FILE__, ".php")} = function () {
    if ($this->get_request_method() == "POST") {
        if (isset($this->_request['username']) and isset($this->_request['email']) and isset($this->_request['password'])) {
            $username = $this->_request['username'];
            $email = $this->_request['email'];
            $password = $this->_request['password'];
            try {
                $newObj = new Signup($username, $password, $email);
                $data = [
                    "ID" => $newObj->InsertID(),
                    "Username" => $this->_request['username'],
                    "Email" => $this->_request['email'],
                    "Status" => "Login Success, Please Verify your Email :)"
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
