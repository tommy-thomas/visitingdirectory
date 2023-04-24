<?php
require __DIR__ . "/../vendor/autoload.php";
$app = new Application;
$twig = $app->template('siteclosed.twig');
echo $twig->render([]);
?>
