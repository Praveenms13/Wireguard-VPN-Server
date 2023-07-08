<?php

try {
    ${basename(__FILE__, ".php")} = function () {
        if ($this->get_request_method() == "POST") {
            if ($this->isAuthenticated()) {
                if (isset($this->_request['publickey']) and isset($this->_request['email'])) {
                    $wg = new wireguard("wg0");
                    $data = [
                        "Result" => "Success",
                        "Your VPN IP" => $wg->addPeer($this->_request['publickey'], $this->_request['email'])
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
