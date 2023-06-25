<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST") {
        if ($this->isAuthenticated()) {
            $listfolders = new Folder();
            $files = $listfolders->getallFolders();
            if (isset($files)) {
                $data = [
                    "Status" => "Listing Folders....",
                    "Folders" => $files
                ];
                $this->response($this->json($data), 200);
            } else {
                $data = [
                    "Status" => "Folders Empty.."
                ];
                $this->response($this->json($data), 400);
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
