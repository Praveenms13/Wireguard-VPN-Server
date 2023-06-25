<?php
continue;
            }
            $basem = basename($r, ".php");
            echo "Trying to call $basem() for $method()...\n";
            if ($basem == $method) {
                include $dir . "/" . $r;
                $func = Closure::bind(${$basem}, $this, get_class());
                if (is_callable($basem)) {