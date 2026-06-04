<?php
require 'vendor/autoload.php';

$m = ImageProxy::getManager();
var_dump(method_exists($m, 'read'));
$img = ImageProxy::getManager()->read('F:/akmm/updatejum/public/images/logo/628fb7f897c1e.png');
var_dump(get_class($img));
