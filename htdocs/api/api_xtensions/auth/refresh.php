<?php

${basename(__FILE__, ".php")} = function () {
    if ($this->get_request_method() == "POST") {
        if (isset($this->_request['refresh_token'])) {
            $refresh_token = $this->_request['refresh_token'];
            try {
                $oauth = new OAuth($refresh_token);
                if ($oauth) {
                    $data = [
                        "Status" => "Refresh Success :)",
                        "Tokens" => $oauth->refreshSession()
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
                    "Status" => "Bad Request",
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
