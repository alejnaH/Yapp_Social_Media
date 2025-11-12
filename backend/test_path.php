<?php
echo "BACKEND PATH: " . __FILE__ . "<br><br>";

$autoload = dirname(__DIR__) . "/vendor/autoload.php";

echo "Looking for autoload at:<br>$autoload<br><br>";

if (file_exists($autoload)) {
    echo "<span style='color:green;font-weight:bold;'>FOUND ✔</span>";
} else {
    echo "<span style='color:red;font-weight:bold;'>NOT FOUND ❌</span>";
}
