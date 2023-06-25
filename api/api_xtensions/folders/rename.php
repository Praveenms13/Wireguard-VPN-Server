<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST") {
        if ($this->isAuthenticated()) {
            if (isset($this->_request['id']) and isset($this->_request['name'])) {
                $id = $this->_request['id'];
                $name = $this->_request['name'];
                $rename = new Folder($id);
                if ($rename->rename($this->_request['name'])) {
                    $data = [
                        "Status" => "Folder renamed successfully"
                    ];
                    $this->response($this->json($data), 200);
                } else {
                    $data = [
                        "Status" => "Folder rename failed"
                    ];
                    $this->response($this->json($data), 400);
                }
            } else {
                $data = [
                    "Status" => "Invalid Inputs"
                ];
                $this->response($this->json($data), 417);
            }
        } else {
            $data = [
                "Status" => "Authentication failed, please login again"
            ];
            $this->response($this->json($data), 400);
        }
    } else {
        $data = [
            "Status" => "Method not allowed"
        ];
        $this->response($this->json($data), 400);
    }
};
