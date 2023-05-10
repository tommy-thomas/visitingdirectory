<?php
require __DIR__ . "/../vendor/autoload.php";
$app = new \UChicago\AdvisoryCouncil\Application();
$template = $app->template('./placeholder.html.twig');
echo $template->render();
?>
