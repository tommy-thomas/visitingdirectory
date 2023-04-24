<?php
require __DIR__ . "/../vendor/autoload.php";
$app = new \UChicago\AdvisoryCouncil\Application();
$template = $app->template('./siteclosed.html.twig');
echo $template->render();
?>
