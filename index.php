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
    ?>
    </body>
</html>

