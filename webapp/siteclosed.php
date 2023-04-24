<?php
require __DIR__ . "/../vendor/autoload.php";
$app = new Application;
$template = $app->template('./siteclosed.html.twig');
echo $template->render();
?>
