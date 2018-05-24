<?php

/**
 * twigify.php - helper tool to convert a web app from CS to Twig
 *
 * PHP version 5-ish
 *
 * LICENSE: University of Chicago proprietary code
 *
 * @author     Scott Bassett <sbassett@uchicago.edu>
 * @copyright  2018 The University of Chicago
 * @license    University of Chicago proprietary code
 * @version    0.1
 * @usage: (/usr/local/php/bin/)php twigify.php (from command line)

@cli args:
-composer : 1st step, check for composer config
-twig : install twig
-app : "Application.php" check
-search : gather clearSilver files, put them in json config
-convert : create conversion for given files,
second arg = specific file, no additional arg = json file db from -search
 */

// config
error_reporting(E_ERROR | E_PARSE);

// vars
$templates_dir = "_templates";

$valid_args = array("-composer", "-twig", "-app", "-search", "-convert");
$valid_args_error = "You must enter at least one argument (".implode(", ", $valid_args).")\n";
$twigify_log_file = "./twigify.log";

$composer_json = "./composer.json";
$packager_name = "composer";

$template_name = "twig";
$template_file_ext = "twig";
$template_array_blank = "\$TwigTemplateVariables";
$template_package_url = "https://packagist.org/packages/twig/twig";
$composer_command = "~sbassett/bin/composer";

$json_file_association_search = "./twigifyFileSearch.json";

// check for _some_ args
if(count($argv) <= 1)
{
    exit($valid_args_error);
}

// check for good args now
if((2 == count($argv) || 3 == count($argv)) && in_array($argv[1], $valid_args))
{
    // -composer
    if($valid_args[0] == $argv[1])
    {
        if(! file_exists($composer_json))
        {
            $prompt = "There doesn't seem to be a '".$composer_json."' file in your webapp root.\nShould we create one?\n";
            $prompt .= "Yes [y] or No [n]: ";
            $usermsg = readUser($prompt);
            if($usermsg == "y")
            {
                // try to touch a composer.json file
                if(! file_put_contents($composer_json, "{\n\n}\n"))
                {
                    exit("Whoops.  For some reason I couldn't write a '".$composer_json."' file :(\n");
                }
                else
                {
                    writeLog("'$composer_json' file was created.");
                }
            }
            else
            {
                writeLog("'$composer_json' file was NOT created, user choice.");
                exit("Ok, I won't create a '".$composer_json."' file.  Exiting now.\n");
            }
        }
        else
        {
            writeLog("'$composer_json' file was NOT created, already exists.");
            exit("Sweet: a '".$composer_json."' file exists!  Please proceed to the next command option (".$valid_args[1].").\n");
        }
    }
    // -twig
    else if($valid_args[1] == $argv[1])
    {
        if(! file_exists($composer_json))
        {
            exit("Whoops.  Looks like there isn't a '".$composer_json."' file.  Please run the '".$valid_args[0]."' option first.");
        }
        else
        {
            echo "Sweet.  A '".$composer_json."' file exists!\n";
            echo "Let's see if ".ucfirst($template_name)." has been included...\n";
            $composer_data = file_get_contents($composer_json);
            if(! stristr($composer_data, $template_name))
            {
                echo "Looks like ".ucfirst($template_name)." isn't included via ".ucfirst($packager_name).".  Shoudld we try to do that now?\n";
                $usermsg = readUser("Yes [y] or No [n]: ");
                if($usermsg == "y")
                {
                    $composerFileString = file_get_contents($composer_json);
                    // if php version > 7, twig 2.0+, if php version < 7 twig 1.35.3
                    if(phpversion() >= 7)
                    {
                        $twigVersion = "2.0";
                        writeLog("PHP version >= 7 found, going with '$template_name' $twigVersion.");
                    }
                    else
                    {
                        $twigVersion = "1.35.3";
                        writeLog("PHP version < 7 found, going with '$template_name' $twigVersion.");
                    }
                    $comOutput = shell_exec($composer_command." require \"".$template_name."/".$template_name.":^".$twigVersion."\"");
                    $checkRequire = shell_exec($composer_command." show \"".$template_name."/".$template_name."\"");
                    $comUpdate = shell_exec($composer_command." update");

                    if(! stristr($checkRequire, "not found"))
                    {
                        echo "\nAlright, looks like ".$template_name.":".$twigVersion." was successfully installed!\n";
                        writeLog("'$template_name' $twigVersion successfully installed via $packager_name.");
                        exit("Please proceed to the next command option (".$valid_args[2].").\n");
                    }
                    else
                    {
                        echo "Whoops.  Looks like something weird happened with ".ucfirst($packager_name)." :(\n";
                        writeLog("'$template_name' $twigVersion NOT installed - should be configured manually.");
                        exit("Please try debugging '".$composer_json."' manually.  Sorry.\n");
                    }
                }
            }
            else
            {
                writeLog("'$template_name' already seems to be installed.");
                echo "Good news!  Looks like '$template_name' is already installed via ".ucfirst($packager_name).":\n\n";
                echo "\"".preg_split("/\n/", stristr($composer_data, $template_name))[0]."\n\n";
                echo "Though you might want to verify the version and supported PHP version here: <".$template_package_url.">\n";
                echo "Please proceed to the next command option (".$valid_args[2].").\n";
                exit();
            }
        }
    }
    // -app
    else if($valid_args[2] == $argv[1])
    {
        $appPath = trim(readUser("Please enter the relative path of your 'Application.php' file: "));
        if($appFileData = file_get_contents($appPath))
        {
            echo "\n";
            if(! stristr($appFileData, "WS_Application"))
            {
                writeLog("Could not find extended WS_Application class.");
                exit("Huh, looks like your Application file isn't extending WS_Application?  Exiting this script for now until that is resolved.\n");
            }
            if(stristr($appFileData, " template("))
            {
                echo "Looks like your Application file is already overwriting template()?  Well, we can probably clean that up.\n";
            }
            if(! stristr($appFileData, "vendor/autoload.php"))
            {
                writeLog("Provided script runner with information on how to include 'vendor/autoload.php' manually in WS_Application file.");
                echo "It doesn't look like your Application file is requiring 'vendor/autoload.php'.\n";
                echo "It doesn't have to, but it might make sense to 'require_once()' at the top of the file, like so:\n\n";
                echo "// composer autoload\n";
                echo "require_once(\"".__DIR__."/vendor/autoload.php\");\n\n";
                echo "Otherwise you'll need to ensure that the vendor/autoload.php file is required on all appropriate pages.\n";
            }

            // template() override suggestion
            writeLog("Provided script runner with information on how to update 'template()' manually within WS_Application file.");
            echo "\nLet's override the base template function to look something like:\n\n";
            echo "/**\n";
            echo "  * template\n";
            echo "  * @return template (".ucfirst($template_name).") object\n";
            echo "  * @access public\n";
            echo "*/\n";
            echo "public function template(\$filename='')\n";
            echo "{\n";
            echo "\t\$loader = new Twig_Loader_Filesystem('".__DIR__."/$templates_dir');\n";
            echo "\t\$twig = new Twig_Environment(\$loader, array('autoescape' => true));\n";
            echo "\tif(\$filename != '')\n";
            echo "\t{\n";
            echo "\t\treturn \$twig->load(\$filename);\n";
            echo "\t}\n";
            echo "\telse\n";
            echo "\t{\n";
            echo "\t\treturn \$twig;\n";
            echo "\t}\n";
            echo "}\n\n";
            echo "BE SURE TO VERIFY THE TEMPLATE PATH ABOVE ^^^\n\n";
            echo "Also: your app will probably be broken now, which is what's supposed to happen :)\n\n";
            echo "Note: there are other ways to change the app to use ".ucfirst($template_name).", but this is probably the simplest.\n";
            echo "(without having to redo WS_Template or start using dependency-injection or some other more complicated stuff.)\n\n";
            echo "Please proceed to the next command option (".$valid_args[3].").\n";
        }
        else
        {
            writeLog("Could not find 'Application.php' file.");
            exit("Whoops.  Looks like PHP couldn't stat '".$appPath."'!  Please try this command option (".$valid_args[2].") again.\n");
        }
    }
    // -search
    else if($valid_args[3] == $argv[1])
    {
        // user input template path
        $templatePath = $templates_dir;

        if(is_dir(realpath($templatePath)) && realpath($templatePath) != __DIR__)
        {
            // status msg
            echo "\nSearching files in '".__DIR__."'...\n";

            // find list of template files w/ "<?cs" tags
            $comOutput = shell_exec("ack -l --ignore-dir={workingcopy,vendor,sandbox,workingdir,.git,.svn} --ignore-file='match:/.log$/' '\<\?cs'");
            $ackForCSTags = explode("\n", $comOutput);
            foreach($ackForCSTags as $k => $v)
            {
                // check for blanks, this file
                if($v == "" || $v == basename(__FILE__))
                    $ackForCSTags[$k] = null;
                // strip off template dir, from input above
                $ackForCSTags[$k] = str_replace("$templatePath/", "", $v);
            }

            // also search for files that end in a ClearSilver extension but _may_ just contain regular HTML
            $comOutput = shell_exec("/bin/find . -name '*.cs'");
            $ackForCSExtension = explode("\n", $comOutput);
            foreach($ackForCSExtension as $k1 => $v1)
            {
                if($v1 != "")
                {
                    $v1 = str_replace("./$templates_dir/", "", $v1);
                    if(! in_array($v1, $ackForCSTags))
                    {
                        $ackForCSTags[] = $v1;
                    }
                }
            }

            // search for php controller and cs dependent files
            $fileAssociations = array();
            foreach($ackForCSTags as $v)
            {
                $com = "ack -l --sort-files --ignore-file='match:/.git*/' --ignore-file='match:/.md$/' --ignore-file='match:/.log$/' ";
                $com .= "--ignore-dir={workingcopy,vendor,sandbox,workingdir,.git,.svn} ".$v." 2>/dev/null";
                $comOutput = shell_exec($com);

                if($v != basename(__FILE__) && $v != '' && count(explode("\n", $comOutput)) != 0)
                {
                    $fileAssociations[$v] = array_filter(explode("\n", $comOutput), function($value) { return $value !== ''; });
                }
            }

            // write above data to json file
            $jsonFileAssociations = json_encode($fileAssociations, JSON_PRETTY_PRINT);
            file_put_contents($json_file_association_search, $jsonFileAssociations);
            writeLog("List of old template files and calling controllers searched and written to '$json_file_association_search'.");
            echo "\nDatabase of file associations created in '{$json_file_association_search}'.\n";
            exit("\nPlease verify the above file and proceed to the next command option (".$valid_args[4].").\n");
        }
        else
        {
            // invalid template path
            writeLog("Could not find valid existing template path from script runner input.");
            exit("Whoops.  Looks like PHP couldn't stat '".$templatePath."'!  Please try this command option (".$valid_args[3].") again.\n");
        }
    }
    // -convert
    else if($valid_args[4] == $argv[1])
    {
        // import json file db
        if(! $jsonFileAssociations = json_decode(file_get_contents($json_file_association_search), TRUE))
        {
            echo "Whoops.  Looks like PHP couldn't stat '".$json_file_association_search."'!\n\n";
            echo "(it may not exist or may be corrupt :/)\n\n";
            exit("Please try this command option (".$valid_args[4].") again.\n");
        }

        // user input template path
        $templatePath = $templates_dir;

        // check template path
        if(!is_dir($templatePath))
        {
            writeLog("Error: bad template path '$templatePath' provided by script runnner.");
            echo "Whoops, looks like '$templatePath' doesn't exist?\n";
            exit("Please try this command option (".$valid_args[4].") again.\n");
        }

        // determine argv[2] - nothing = json db, file = individual file
        $currentFileName = "";
        $dbFileName = "";
        if(isset($argv[2]) && is_file("./" . trim($argv[2])))
        {
            echo "Let's start converting '".trim($argv[2])."'...\n\n";

            // file key for json db store - remove template dir ref at beginning if it exists
            $dbFileName = trim($argv[2]);
            if(stristr($dbFileName, $templatePath))
            {
                $dbFileName = str_replace($templatePath . "/", "", $dbFileName);
            }

            // check if file already appears to be converted to desired (twig) template
            if($template_file_ext == pathinfo($dbFileName, PATHINFO_EXTENSION))
            {
                $currentFileName = "./" . trim($argv[2]);
                writeLog("'$templatePath/$dbFileName' provided by script runner - already seems to be converted to '.$template_file_ext'.");
                echo "Whoops.  Looks like '$templatePath/$dbFileName' already appears to be of type '$template_file_ext' based on the extension.\n";
                echo "Skipping this step.\n\n";
                $fileExtAlreadyConverted = true;
            }

            // check for key in json db store
            if(array_key_exists($dbFileName, $jsonFileAssociations))
            {
                $currentFileName = "./" . trim($argv[2]);
            }
            else if(!isset($fileExtAlreadyConverted))
            {
                writeLog("Error: could not locate '$dbFileName' within '$json_file_association_search'.  It should be there.");
                echo "Whoops.  PHP couldn't find file '$dbFileName' in the db store '".$json_file_association_search."'.  Is your source reliable?\n";
                exit("Please try this command option (".$valid_args[4].") again.\n");
            }
        }
        else if(isset($argv[2]) && !is_file("./" . trim($argv[2])))
        {
            writeLog("Invalid file '".trim($argv[2])."' providided for template remediation from script runner.");
            exit("Whoops.  Looks like PHP couldn't stat './".trim($argv[2])."'!  Please try this command option (".$valid_args[4].") again.\n");
        }
        else
        {
            echo "Let's start converting the first file within '".$json_file_association_search."'...\n\n";
            reset($jsonFileAssociations);
            $dbFileName = key($jsonFileAssociations);
            $currentFileName = $templatePath . "/" . $dbFileName;
        }

        /////////////////////////////
        // begin conversion processes
        /////////////////////////////
        //   1) rename template file to twig
        if(!isset($fileExtAlreadyConverted))
        {
            $fileStatus = readUser("Should I overwrite '$currentFileName' to have the file extension '".$template_file_ext."'?  Yes [y] or No [n]: ");
            if($fileStatus == "y")
            {
                $newFileName = pathinfo($currentFileName, PATHINFO_DIRNAME) . "/" . pathinfo($currentFileName, PATHINFO_FILENAME) . "." . $template_file_ext;
                $comOutput = exec("/bin/mv $currentFileName $newFileName 2>/dev/null");
                if(file_exists($newFileName))
                {
                    echo "\nAlright, '$currentFileName' has been moved to '$newFileName'\n\n";
                    writeLog("Template file '$currentFileName' moved to new file '$newFileName.'");
                    $fileExtAlreadyConverted = true;
                }
                else
                {
                    writeLog("Error: '$currentFileName' was not able to be moved for some reason.");
                    exit("Sorry, I had trouble moving '$currentFileName'!  Please try this command option (".$valid_args[4].") again.\n");
                }
            }
            else if($fileStatus == "n")
            {
                $fileStatus = readUser("\nWould you like to rename '$currentFileName' manually right now?  Yes [y] or No [n]: ");
                if($fileStatus == "y")
                {
                    $newFileName = trim(readUser("\nPlease type the relative path of the new template file you'd like to create: "));
                    $comOutput = exec("/bin/mv $currentFileName $newFileName 2>/dev/null");
                    if(file_exists($newFileName))
                    {
                        echo "Alright, '$currentFileName' has been moved to '$newFileName'\n\n";
                        writeLog("Template file '$currentFileName' moved to new file '$newFileName.'");
                        $fileExtAlreadyConverted = true;
                    }
                    else
                    {
                        writeLog("Error: '$currentFileName' was not MANUALLY able to be moved for some reason.");
                        exit("Sorry, I had trouble moving '$currentFileName'!  Please try this command option (".$valid_args[4].") again.\n");
                    }
                }
                else
                {
                    writeLog("Script runner decided NOT to convert '$currentFileName' for some reason.");
                    echo "\nOk, I won't convert '$currentFileName' to the '$template_file_ext' extension.\n";
                    echo "You'll need to do that manually, if you actually want to convert it.\n\n";
                }
            }
            else
            {
                writeLog("Error: '$currentFileName' was not able to be moved for some reason.");
                exit("Hmm, I can't seem to help you on this one!  Perhaps the file was already converted?  Please try this command option (".$valid_args[4].") again.\n");
            }
        }

        // check new file name - could be user didn't convert it above for some reason
        if($newFileName == '' && !file_exists($newFileName))
        {
            $newFileName = $currentFileName;
        }

        //   2) fix twig template code
        $status = readUser("Should we try to convert this existing template file '$newFileName' to use $template_name stuff?  Yes [y] or No [n]: ");
        if($status == "y")
        {
            echo "\nOk, let's walk through some $template_name replacement suggestions for '$newFileName':\n\n";

            // open current/newly-renamed template file
            $fileContents = file_get_contents($newFileName);
            $fileChangeAudit = array();

            // try to find and convert cs var statements
            $matches = null;
            preg_match_all("/\<\?cs\ var:(.*)\ \?"."\>/iU", $fileContents, $matches);
            if(count($matches) > 0)
            {
                for($i=0;$i<count($matches[0]);$i++)
                {
                    $newTemplateVar = "{{ ".$matches[1][$i]." }}";
                    $status = readUser("Should I replace:  ".$matches[0][$i]."  with:  $newTemplateVar?  Yes [y] or No [n]: ");
                    if($status == 'y')
                    {
                        $fileContents = str_replace($matches[0][$i], $newTemplateVar, $fileContents);
                        $fileChangeAudit[$matches[0][$i]] = $newTemplateVar;
                        writeLog("Changed " . $matches[0][$i] . "  to:  $newTemplateVar in '$newFileName'.");
                        echo "\nREPLACED!\n\n";
                    }
                    else
                    {
                        writeLog("Script runner skipped converting ".$matches[0][$i]." to: $newTemplateVar in '$newFileName'");
                        echo "Ok, skipping this one for now.\n\n";
                    }
                }
            }

            // try to find and replace cs include statements - relative path, no '../' anymore
            $matches = null;
            preg_match_all("/\<\?cs\ include:(\"|\')(.+)(\"|\')\ \?"."\>/iU", $fileContents, $matches);
            if(count($matches) > 0)
            {
                for($i=0;$i<count($matches[0]);$i++)
                {
                    $oldIncludedFileName = $matches[2][$i];
                    if($template_file_ext != pathinfo($oldIncludedFileName, PATHINFO_EXTENSION))
                    {
                        echo "Looking at a CS include: - it doesn't look like '$oldIncludedFileName' is a $template_name file.\n\n";
                        $status = readUser("Should we include it as one in '$newFileName'?  Yes [y] or No [n]: ");
                        if($status == 'y')
                        {
                            $newIncludedFileName = pathinfo($oldIncludedFileName, PATHINFO_DIRNAME)."/".pathinfo($oldIncludedFileName, PATHINFO_FILENAME);
                            $newIncludedFileName .= ".$template_file_ext";
                            echo "\nOk, I've converted it to: '$newIncludedFileName'\n";
                        }
                        else
                        {
                            $newIncludedFileName = $oldIncludedFileName;
                            echo "\nOk, I'll leave it as-is.\n";
                        }
                    }
                    $newIncludedFileName = preg_replace("/".preg_quote("../", "/")."/", "", $newIncludedFileName);

                    $newTemplateVar = "{{ include('$newIncludedFileName') }}";
                    $status = readUser("\nShould I replace:  ".$matches[0][$i]."  with:  $newTemplateVar?  Yes [y] or No [n]: ");
                    if($status == 'y')
                    {
                        $fileContents = str_replace($matches[0][$i], $newTemplateVar, $fileContents);
                        $fileChangeAudit[$matches[0][$i]] = $newTemplateVar;
                        writeLog("Changed " . $matches[0][$i] . "  to:  $newTemplateVar in '$newFileName'.");
                        echo "\nREPLACED!\n\n";
                    }
                    else
                    {
                        writeLog("Script runner skipped converting ".$matches[0][$i]." to: $newTemplateVar in '$newFileName'");
                        echo "Ok, skipping this one for now.\n\n";
                    }
                }
            }

            // try to find and replace cs if statements, sorta
            $matches = null;
            preg_match_all("/\<\?cs\ if:(.*)\ \?"."\>/iU", $fileContents, $matches);
            if(count($matches) > 0)
            {
                for($i=0;$i<count($matches[0]);$i++)
                {
                    echo "I found the following 'if' statement: '".$matches[0][$i]."'\n\n";
                    $status = trim(readUser("Tell me what JUST the part after 'if:' should be: "));
                    $newIfStatement = "{% if $status %}";
                    $status = readUser("\nI'd like to replace '".$matches[0][$i]."' with '$newIfStatement'.  Yes [y] or No [n]: ");
                    if($status == 'y')
                    {
                        $fileContents = str_replace($matches[0][$i], $newIfStatement, $fileContents);
                        $fileChangeAudit[$matches[0][$i]] = $newIfStatement;
                        writeLog("Changed " . $matches[0][$i] . "  to:  $newIfStatement in '$newFileName'.");
                        echo "\nREPLACED!\n\n";
                    }
                    else
                    {
                        writeLog("Script runner skipped converting ".$matches[0][$i]." to: $newIfStatement in '$newFileName'");
                        echo "Ok, skipping this 'if' statement for now.\n\n";
                    }
                }
            }

            // close if tags twig replacement - all file
            $matches = null;
            preg_match_all("/\<\?cs\ \/if\ \?"."\>/iU", $fileContents, $matches);
            $matchesExist = false;
            foreach($matches as $m1 => $m2)
            {
                if(!empty($m2))
                    $matchesExist = true;
            }
            if($matchesExist)
            {
                $status = readUser("I also found at least one closing 'if' statement - should I convert these to $template_name?  Yes [y] or No [n]: ");
                if($status == "y")
                {
                    if(strlen($matches[0][0]) > 0)
                    {
                        $fileContents = preg_replace("/\<\?cs\ \/if \?"."\>/iU", "{% endif %}", $fileContents);
                        $fileChangeAudit['<?cs /if ?'.'>'] = "{% endif %}";
                        writeLog("Script runner converted closing 'if' statements to $template_name in '$newFileName'");
                        echo "\nOk, I've converted all of the closing 'if' statements to $template_name in '$newFileName'\n\n";
                    }
                }
                else
                {
                    writeLog("Script runner skipped converting closing 'if' statements to $template_name in '$newFileName'");
                    echo "Ok, skipping the closing 'ifs' for now.\n\n";
                }
            }

            // replace <?cs else ? > else w/ {% else %} - all file
            $matches = null;
            preg_match_all("/\<\?cs\ else\ \?"."\>/iU", $fileContents, $matches);
            $matchesExist = false;
            foreach($matches as $m1 => $m2)
            {
                if(!empty($m2))
                    $matchesExist = true;
            }
            if($matchesExist)
            {
                $status = readUser("I also found at least one 'else' statement - should I convert these to $template_name?  Yes [y] or No [n]: ");
                if($status == "y")
                {
                    if(strlen($matches[0][0]) > 0)
                    {
                        $fileContents = preg_replace("/\<\?cs\ else \?"."\>/iU", "{% else %}", $fileContents);
                        $fileChangeAudit['<?cs else ?'.'>'] = "{% else %}";
                        writeLog("Script runner converted 'else' statements to $template_name in '$newFileName'");
                        echo "\nOk, I've converted all of the 'else' statements to $template_name in '$newFileName'\n\n";
                    }
                }
                else
                {
                    writeLog("Script runner skipped converting 'else' statements to $template_name in '$newFileName'");
                    echo "Ok, skipping the 'else's for now.\n\n";
                }
            }

            // try to find and replace each: loop statements, sorta
            $matches = null;
            preg_match_all("/\<\?cs\ each:(.*)\ \?"."\>/iU", $fileContents, $matches);
            if(count($matches) > 0)
            {
                for($i=0;$i<count($matches[0]);$i++)
                {
                    echo "I found the following 'each' statement: '".$matches[0][$i]."'\n\n";
                    $status = trim(readUser("Tell me what JUST the part after 'each:' should be: "));
                    $newEachStatement = "{% for $status %}";
                    $status = readUser("\nI'd like to replace '".$matches[0][$i]."' with '$newEachStatement'.  Yes [y] or No [n]: ");
                    if($status == 'y')
                    {
                        $fileContents = str_replace($matches[0][$i], $newEachStatement, $fileContents);
                        $fileChangeAudit[$matches[0][$i]] = $newEachStatement;
                        writeLog("Changed " . $matches[0][$i] . "  to:  $newEachStatement in '$newFileName'.");
                        echo "\nREPLACED!\n\n";
                    }
                    else
                    {
                        writeLog("Script runner skipped converting ".$matches[0][$i]." to: $newEachStatement in '$newFileName'");
                        echo "Ok, skipping this 'each' statement for now.\n\n";
                    }
                }
            }

            // replace /each w/ endfor - all file
            $matches = null;
            preg_match_all("/\<\?cs\ \/each\ \?"."\>/iU", $fileContents, $matches);
            $matchesExist = false;
            foreach($matches as $m1 => $m2)
            {
                if(!empty($m2))
                    $matchesExist = true;
            }
            if($matchesExist)
            {
                $status = readUser("I also found at least one closing 'each' statement - should I convert these to $template_name?  Yes [y] or No [n]: ");
                if($status == "y")
                {
                    if(strlen($matches[0][0]) > 0)
                    {
                        $fileContents = preg_replace("/\<\?cs\ \/each \?"."\>/iU", "{% endfor %}", $fileContents);
                        $fileChangeAudit['<?cs /each ?'.'>'] = "{% endfor %}";
                        writeLog("Script runner converted closing '/each' statements to $template_name in '$newFileName'");
                        echo "\nOk, I've converted all of the closing '/each' statements to $template_name in '$newFileName'\n\n";
                    }
                }
                else
                {
                    writeLog("Script runner skipped converting closing 'each' statements to $template_name in '$newFileName'");
                    echo "Ok, skipping the closing '/each's for now.\n\n";
                }
            }

            // try to find any other lingering cs to replace
            // BUT NOT vars, include, ifs, /ifs (since we found them above)
            $matches = null;
            preg_match_all("/\<\?cs\ (?!var)(?!include)(?!if)(?!\/if)(.+)\ \?"."\>/iU", $fileContents, $matches);
            $matchesExist = false;
            foreach($matches as $m1 => $m2)
            {
                if(!empty($m2))
                    $matchesExist = true;
            }
            if($matchesExist)
            {
                $status = readUser("I found a bunch of oddball Clearsilver tags.  Should we review them?  Yes [y] or No [n]: ");
                if($status == "y")
                {
                    for($i=0;$i<count($matches[0]);$i++)
                    {
                        echo "I found this oddball Clearsilver tag:  '".$matches[0][$i]."'\n\n";
                        $replaceFromUser = trim(readUser("Please provide a replacement tag or type 'n' if you don't want to replace it: "));
                        if($status != "n")
                        {
                            $fileContents = str_replace($matches[0][$i], $replaceFromUser, $fileContents);
                            $fileChangeAudit[$matches[0][$i]] = $replaceFromUser;
                            writeLog("Changed " . $matches[0][$i] . "  to:  $replaceFromUser in '$newFileName'.");
                            echo "\nREPLACED!\n\n";
                        }
                        else
                        {
                            writeLog("Script runner skipped converting ".$matches[0][$i]." to: $replaceFromUser in '$newFileName'");
                            echo "Ok, skipping this one for now.  You should DEFINITELY review these manually.\n\n";
                        }
                    }
                }
                else
                {
                    writeLog("Script runner skipped converting oddball Clearsilver tags to $template_name in '$newFileName'");
                    echo "Ok, skipping the oddball Clearsilver tag review for now.  You should DEFINITELY review those manually.\n\n";
                }
            }

            // write cs template changes to file and log
            $msg = "Should I overwrite file '$newFileName' with all of the Clearsilver to $template_name changes we just made? ";
            $msg .= "Yes [y] or No [n]: ";
            $status = readUser($msg);
            if("y" == $status)
            {
                file_put_contents($newFileName, $fileContents);
                writeLog("Changed file '$newFileName': ".base64_encode(serialize($fileChangeAudit)));
            }
            else
            {
                echo "Ok, I won't make these changes now.\n\n";
                writeLog("Script runner abandoned the following changes to file '$newFileName': ".base64_encode(serialize($fileChangeAudit)));
            }
        }
        else
        {
            writeLog("Script runner chose to skip conversion of '$newFileName' to use $template_name");
            echo "Ok, we'll skip this step of attempting to convert '$newFileName' to use $template_name.\n\n";
        }

        //   3) fix dependent file code, etc.
        if(is_array($jsonFileAssociations[$dbFileName]))
        {
            echo "\nI've also found these dependent files where '$currentFileName' has been included:\n\n";
            foreach($jsonFileAssociations[$dbFileName] as $v)
            {
                echo "   * ".trim($v)."\n";
            }
            echo "\n";

            //  A) find include statements in dependents (controllers, other templates)
            $fileChangeAudit = array();
            foreach($jsonFileAssociations[$dbFileName] as $k => $v)
            {
                // pull in dependent file
                $dependentFile = file_get_contents($v);

                // first regex - template file setup and include
                // php controller - match php ext
                if("php" == pathinfo($v, PATHINFO_EXTENSION))
                {
                    // ->template() change, add TwigTemplateVariabls array, ->show()
                    $firstPattern = $dbFileName;
                    $newFileName = pathinfo($dbFileName, PATHINFO_DIRNAME) . "/" . pathinfo($dbFileName, PATHINFO_FILENAME) . "." . $template_file_ext;
                    $matches = null;
                    preg_match("/(.*)(".preg_quote($firstPattern, "/").")(.*)/", $dependentFile, $matches);

                    if(is_array($matches))
                    {
                        $oldLine = $matches[0];
                        $newLine = str_replace($firstPattern, $newFileName, $oldLine);

                        // try to find leading whitespace, if any
                        $indent = preg_match("/^\s*/", $oldLine, $matches);
                        // include new twig template variable
                        $newLine .= "\n";
                        if(is_array($matches))
                        {
                            $newLine .= $matches[0];
                        }
                        $newLine .= "$template_array_blank = array();\n";

                        // overwrite "file" as string
                        $dependentFile = str_replace($oldLine, $newLine, $dependentFile);
                        $fileChangeAudit[$oldLine] = $newLine;
                    }

                    // ->show() replace to ->render(arr)
                    $secondPattern = "->show();";
                    preg_match("/(.*)(\\$.+)(".preg_quote($secondPattern, "/").")(.*)/", $dependentFile, $matches);
                    if(is_array($matches))
                    {
                        $oldLine = $matches[0];
                        $newLine = $matches[1] . "echo " . $matches[2] . "->render($template_array_blank);";
                        $dependentFile = str_replace($oldLine, $newLine, $dependentFile);
                        $fileChangeAudit[$oldLine] = $newLine;
                        $matches = null;
                    }

                    // change over add_data() calls to use new template data array
                    $thirdPattern = "\\$(.+)\-\>add\_data\((.+)\,\ (.+[\)]?)\);";
                    preg_match_all("/".$thirdPattern."/iU", $dependentFile, $matches);
                    // 0 - originals, 2 - keys or possibly need to be split due to 3rd arg, 3 - val
                    foreach($matches[0] as $k1 => $v1)
                    {
                        $oldLine = $v1;
                        $key = '';
                        $val = '';

                        $unescapedData = false;
                        if(stristr($matches[3][$k1], ", "))
                        {
                            $keyVals = preg_split("/\,\ /", $matches[3][$k1]);
                            $key = $keyVals[0];
                            $val = implode(array_slice($keyVals, 1));
                            $unescapedData = true;
                        }
                        else
                        {
                            $key = $matches[2][$k1];
                            $val = $matches[3][$k1];
                        }
                        $newLine = "{$template_array_blank}[$key] = $val;";

                        $msg = "\nShould I change:\n\n$oldLine\n\nTO:\n\n$newLine\n\nYes [y] or No [n]: ";
                        $status = readUser($msg);
                        if("y" == $status)
                        {
                            $dependentFile = str_replace($oldLine, $newLine, $dependentFile);
                            $fileChangeAudit[$oldLine] = $newLine;
                            if($unescapedData)
                            {
                                // find and try to do a template replace in newly-named twig template file
                                $templateFile = $currentFileName;
                                if($newFileName != '')
                                {
                                    $templateFile = $templatePath."/".$newFileName;
                                }
                                echo "\n\nThis data appears unescaped - should we try to add a '|raw' filter in '$templateFile'?\n\n";
                                $rawstatus = readUser("Yes [y] or No [n]: ");
                                if($rawstatus == "y")
                                {
                                    $oldTemplateFileRawCall = "{{ ".str_replace(array("'","'"),'',$key)." }}";
                                    $newTemplateFileRawCall = "{{ ".str_replace(array("'","'"),'',$key)."|raw }}";
                                    $currFileContents = file_get_contents($templateFile);
                                    $currFileContents = str_replace($oldTemplateFileRawCall, $newTemplateFileRawCall, $currFileContents);
                                    file_put_contents($templateFile, $currFileContents);
                                    writeLog("Added raw key replace from '$oldTemplateFileRawCall' to '$newTemplateFileRawCall' in file '$templateFile'");
                                    echo "\n\nOK! - I've changed '$oldTemplateFileRawCall' to '$newTemplateFileRawCall' in file '$templateFile'\n\n";
                                }
                                else
                                {
                                    writeLog("Script runner chose NOT to do: '$oldTemplateFileRawCall' to '$newTemplateFileRawCall' in file '$templateFile'");
                                    echo "\n\nOk, I won't replace this to a '|raw' $template_name filter for now.\n\n";
                                }
                            }
                            else
                            {
                                echo "\n\nOK!\n\n";
                            }
                        }
                        else
                        {
                            echo "\n\nAlright, skipping this change.\n\n";
                        }
                    }

                    // look good, write changes to dependent file
                    $msg = "\n\nI'd like to overwrite file '$v' with these changes:\n\n";
                    foreach($fileChangeAudit as $k1 => $v1)
                    {
                        $msg .= "   * $k1 TO $v1\n";
                    }
                    $msg .= "\n\nYes [y] or No [n]: ";
                    $status = readUser($msg);
                    if("y" == $status)
                    {
                        file_put_contents($v, $dependentFile);
                        writeLog("Changed file '$v': ".base64_encode(serialize($fileChangeAudit)));
                        echo "Success!  File changes for '$v' have been made!\n";
                    }
                    else
                    {
                        echo "Ok, I won't make these changes now.\n";
                        writeLog("Script runner abandoned the following changes to file '$v': ".base64_encode(serialize($fileChangeAudit)));
                    }
                }
                // cs templates - match cs ext
                else
                {
                    // for dependent cs file includes, replace with {{ include('file.twig') }} - see above
                    $matches = null;
                    preg_match_all("/\<\?cs\ include:(\"|\')(.*)(".preg_quote($dbFileName, "/").")(\"|\')\ \?"."\>/", $dependentFile, $matches);
                    for($i=0;$i<count($matches[0]);$i++)
                    {
                        $newIncludedFileName = pathinfo($matches[3][$i], PATHINFO_DIRNAME)."/".pathinfo($matches[3][$i], PATHINFO_FILENAME);
                        $newIncludedFileName .= ".$template_file_ext";
                        $newTemplateVar = "{{ include('$newIncludedFileName') }}";

                        echo "\nIn '$v': \n";
                        echo "Should I replace:  ".$matches[0][$i]."  with:  $newTemplateVar?\n";
                        $status = readUser("Yes [y] or No [n]: ");
                        if($status == 'y')
                        {
                            $dependentFile = str_replace($matches[0][$i], $newTemplateVar, $dependentFile);
                            $fileChangeAudit[$matches[0][$i]] = $newTemplateVar;
                            writeLog("Changed " . $matches[0][$i] . "  to:  $newTemplateVar in '$newFileName'.");
                            echo "\nREPLACED!\n";
                        }
                        else
                        {
                            writeLog("Script runner skipped converting ".$matches[0][$i]." to: $newTemplateVar in '$newFileName'");
                            echo "Ok, skipping this one for now.\n\n";
                        }
                    }

                    // look good, write changes to dependent file
                    $msg = "\nI'd like to overwrite '$v' now with the changes we just made above ^^^:\n\n";
                    foreach($fileChangeAudit as $k1 => $v1)
                    {
                        $msg .= "   * $k1 TO $v1\n";
                    }
                    $msg .= "\nYes [y] or No [n]: ";
                    $status = readUser($msg);
                    if("y" == $status)
                    {
                        file_put_contents($v, $dependentFile);
                        writeLog("Changed file '$v': ".base64_encode(serialize($fileChangeAudit)));
                        echo "\n";
                    }
                    else
                    {
                        echo "Ok, I won't make these changes now.\n";
                        writeLog("Script runner abandoned the following changes to file '$v': ".base64_encode(serialize($fileChangeAudit)));
                    }

                }
            }
            echo "\nSweet, looks like we've wrapped up the conversions for: '$dbFileName'.  Let's look at the next file!\n";
        }
        else
        {
            writeLog("Error: couldn't find '$dbFileName' as a key within the '$json_file_association_search'.  Possible conversion halt issue.");
            echo "Whoops.  Couldn't find '$dbFileName' as a key within the '$json_file_association_search'.\n";
            echo "Possibly the conversion process stopped mid-way through?\n";
            exit("Please try this command option (".$valid_args[4].") again after you reset things for this file.  Exiting now.\n");
        }
    }
    // default
    else
    {
        exit($valid_args_error);
    }
}
else
{
    exit($valid_args_error);
}

// helper functions
// - slurp user input from cli
function readUser($prompt)
{
    $line = readline($prompt);
    return $line;
}

// write log
function writeLog($msg='')
{
    global $twigify_log_file;
    $log_msg = date("m-d-Y g:i:s a")."|".$msg."\n";
    file_put_contents($twigify_log_file, $log_msg, FILE_APPEND);
}
