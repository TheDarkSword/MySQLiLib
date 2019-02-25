<?php
    //include_once 'dbh.inc.php';
    include_once 'mysqllib.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Title of the document</title>
    </head>

    <body>

    <?php
        $mysql = new MySQLLib("login", "lg_");

        $mysql->connect("localhost", "phplessons", "root", "");
        $mysql->createTable(array(
            "username VARCHAR(100) NOT NULL",
            "password VARCHAR(100) NOT NULL"
        ));

        //$mysql->removeLine("username", "TheDarkSword01");
        if($mysql->lineExists("username", "TheDarkSword01")){
            echo "<br>Line exist";
            echo "<br>";
            $result = $mysql->getValue("username", "TheDarkSword01", "password");
            echo $result;
        } else {
            echo "<br>Line not exist";
            $mysql->addLineArray(array("username", "password"), array("TheDarkSword01", "ciao1234"));
        }

        $mysql->close();
    ?>
    </body>
</html>

