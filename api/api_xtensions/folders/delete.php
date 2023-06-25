<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST") {
        if ($this->isAuthenticated()) {
            if (isset($this->_request['id'])) {
                $id = $this->_request['id'];
                $new = new Folder($id);
                if ($new->delete()) {
                    $data = [
                        "Status" => "Success, Folder Deleted..."
                    ];
                    $this->response($this->json($data), 200);
                } else {
                    $data = [
                        "Status" => "Display Folder failed"
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
