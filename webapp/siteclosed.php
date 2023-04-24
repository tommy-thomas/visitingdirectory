<?php
require('../classes/autoload.php');
$app = new Application;
$twig = $app->template('siteclosed.twig');
echo $twig->render([]);
?>
