<?php

class superhero
{
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __call($method, $args)
    {
        echo "Method : " . $method . "\n";
        echo "Arguements : ";
        print_r($args);

        $CheckMethod = get_class_methods('superhero');
        var_dump($CheckMethod);
        foreach ($CheckMethod as $r) {
            if ($r == $method) {
                echo "Method called : $method is presesnt as private function...\n";
                return $this->$r();
            }
        }

        $dir = __DIR__ . "/../api_xtensions/";
        $CheckMethod = scandir(__DIR__ . "/../api_xtensions/");
        foreach ($CheckMethod as $r) {
            if ($r == "." or $r == "..") {
                echo $r;
                continue;
            }
            $basem = basename($r, ".php");
            echo "Trying to call $basem() for $method()...\n";
            if ($basem == $method) {
                include $dir . $r;
                $func = Closure::bind(${$basem}, $this, get_class());
                if (is_callable($func)) {
                    echo "Method called if : $method is presesnt as external function........\n";
                    return call_user_func_array($func, $args);
                } else {
                    echo "Method called not if : $method is not presesnt as external function.......\n";
                    return false;
                }
            }
        }
    }

    private function getName()
    {
        return $this->name;
    }
}
$a = new superhero("batman");
echo $a->getName() . "\n";
echo $a->get_details() . "\n";
