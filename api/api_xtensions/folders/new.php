<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST") {
        if ($this->isAuthenticated()) {
            if (isset($this->_request['name'])) {
                $folder_name = $this->_request['name'];
                $new = new Folder();
                if ($new->createNew($folder_name)) {
                    $data = [
                        "Status" => "Folder created successfully",
                        "Folder ID" => $new->id,
                        "Folder Name" => $new->data['folder_name'],
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
