<?php

${basename(__FILE__, ".php")} = function () {
    if ($this->get_request_method() == "POST") {
        if ($this->isAuthenticated()) {
            try {
                $username = $this->IgetUsername();
                $data = [
                    "User" => $username,
                    "Status" => "Authenticated :)"
                ];
                $this->response($this->json($data), 201);
            } catch (Exception $e) {
                $data = [
                    "Error" => $e->getMessage()
                ];
                $this->response($this->json($data), 404);
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
