<?php

try {
    ${basename(__FILE__, ".php")} = function () {
        if ($this->get_request_method() == "POST") {
            if ($this->isAuthenticated()) {
                $wg = new wireguard("wg0");
                $this->response($this->json($wg->getPeers()), 201);
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
