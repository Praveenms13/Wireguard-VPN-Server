<?php

${basename(__FILE__, ".php")} = function () {
    if ($this->isAuthenticated()) {
        $data = [
            "Status" => "Already logged in :)",
            "Username" => $this->getUsername()
        ];
        $this->response($this->json($data), 200);
    } 
    if ($this->get_request_method() == "POST") {
        if (isset($this->_request['username']) and isset($this->_request['password'])) {
            $username = $this->_request['username'];
            $password = $this->_request['password'];
            try {
                $auth = new Auth($username, $password);
                if ($auth) {
                    $data = [
                        "Status" => "Login Success :)",
                        "Username" => $auth->username,
                        "Tokens" => $auth->getToken()
                    ];
                    $this->response($this->json($data), 200);
                } else {
                    $data = [
                        "Status" => "Invalid Credentials:("
                    ];
                    $this->response($this->json($data), 401);
                }
            } catch (Exception $e) {
                $data = [
                    "Status" => "Bad Request...",
                    "Internal Error" => $e->getMessage()
                ];
                $this->response($this->json($data), 406);
            }
        } else {
            $data = [
                "Status" => "Invalid Inputs:(, isset failed"
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
