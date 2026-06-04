<?php
require 'vendor/autoload.php';
$m = new Intervention\Image\ImageManager();
var_dump(method_exists($m, 'make'));
