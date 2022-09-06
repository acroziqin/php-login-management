<?php

namespace KrisnaBeaute\BelajarPhpMvc\App {

    function header(string $value)
    {
        echo $value;
    }
}

namespace KrisnaBeaute\BelajarPhpMvc\Service {

    function setcookie(string $name, string $value)
    {
        echo "$name: $value";
    }
}