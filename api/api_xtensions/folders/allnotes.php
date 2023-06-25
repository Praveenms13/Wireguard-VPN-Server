<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST") {
        if ($this->isAuthenticated()) {
            if (isset($this->_request['id'])) {
                $id = $this->_request['id'];
                $new = new Folder($id);
                if ($new->getallNotes()) {
                    $data = [
                        "Status" => "Displaying all notes in folder",
                        "Total Notes in this Folder" => $new->countallNotes(),
                        "Notes" => $new->getallNotes()
                    ];
                    $this->response($this->json($data), 200);
                } else {
                    $data = [
                        "Status" => "No Notes in this Folder"
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
