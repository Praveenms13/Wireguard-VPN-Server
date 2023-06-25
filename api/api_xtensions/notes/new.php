<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST") {
        if ($this->isAuthenticated()) {
            if (isset($this->_request['folder_id']) and isset($this->_request['title']) and isset($this->_request['body'])) {
                $title = $this->_request['title'];
                $folder_id = $this->_request['folder_id'];
                $body = $this->_request['body'];
                $new = new Notes();
                if ($new->createNew($folder_id, $title, $body)) {
                    $new->refresh();
                    $data = [
                        "Status" => "Notes created successfully",
                        "Note id" => $new->getId(),
                        "Folder id" => $new->getFolder_id(),
                        "Note Title" => $new->getTitle(),
                        "Note Body" => $new->getBody(),
                    ];
                    $this->response($this->json($data), 200);
                } else {
                    $data = [
                        "Status" => "Notes Creation failed"
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
