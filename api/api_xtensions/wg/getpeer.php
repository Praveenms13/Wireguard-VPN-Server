<?php

try {
    ${basename(__FILE__, ".php")} = function () {
        if ($this->get_request_method() == "POST") {
            if ($this->isAuthenticated()) {
                if (isset($this->_request['peer'])) {
                    $wg = new wireguard("wg0");
                    $data = [
                        "User" => $wg->getPeer($this->_request['peer'])
                    ];
                    $this->response($this->json($data), 201);
                } else {
                    $data = [
                        "Error" => "Invalid Inputs:(, isset failed"
                    ];
                    $this->response($this->json($data), 417);
                }
            } else {
                $data = [
                    "Status" => "Unauthorized"
                ];
                $this->response($this->json($data), 401);
            }
        } else {
            $data = [
                "Status" => "Method not allowed....",
                "Method" => $this->get_request_method()
            ];
            $this->response($this->json($data), 405);
        }
    };
} catch (Exception $e) {
    $data = [
        "Error" => $e->getMessage()
    ];
    $this->response($this->json($data), 404);
}
