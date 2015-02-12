<?php
header("HTTP/1.0 404 Not Found");
if (isset($_GET['p']))
{
    header('My-Header: ' . $_GET['p']);
}

flush();
