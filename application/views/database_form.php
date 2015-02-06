<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* This is a simple form, meant to be handled using CI's Input class.
* The only difference is the action attribute.
*/
?><!DOCTYPE html>
<html lang="en">
<body>
    <h1>Database Setup:</h1>
    <div id="body">
        <form action="/DBLoader/handle_database_form" method="post">
            <div>Username:</div>
            <input type="text" name="username" value=""><br>
            <div>Password:</div>
            <input type="password" name="password" value=""><br>
            <div>New Database Name:</div>
            <input type="text" name="database_name" value=""><br><br>
            <input type="submit" name="submit" value="Submit"><br>
        </form>
    </div>
    <hr/>
</body>
</html>
