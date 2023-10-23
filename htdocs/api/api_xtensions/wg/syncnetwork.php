<?php

try {
    ${basename(__FILE__, ".php")} = function () {
        if ($this->isAuthenticated()) {
            // TODO : To Make a check point here to check if the user is admin or not...
            // TODO : With the help of the JWT Token...
            // TODO : Email and the password Check...
            $wg = new wireguard("wg0");
            $syncData = $wg->syncNetwork();
            $data = [
                "Result" => $syncData
            ];
            $this->response($this->json($data), 201);

        } else {
            $data = [
                "Status" => "Unauthorized"
            ];
            $this->response($this->json($data), 401);
        }

    };
} catch (Exception $e) {
    $data = [
        "Error" => $e->getMessage()
    ];
    $this->response($this->json($data), 404);
}
