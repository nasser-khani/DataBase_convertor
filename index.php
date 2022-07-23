<?php
try {
    $DBname = "wordpress";
    $DBuser = "root";
    $DBpass = "root";
    $host = 'localhost';

    $dbh = new PDO("mysql:host=$host;dbname=$DBname", $DBuser, $DBpass);
    $dbh->exec("set names utf8");
} catch (PDOException $error) {
    echo $error->getMessage();
}

// $data = $dbh->prepare("SELECT * FROM Sys.Tables");
// $data->execute();
// $data = $data->fetch();


$database = $dbh->prepare("show tables");
$database->execute();
$database = $database->fetchAll(PDO::FETCH_COLUMN);
$data = [];
foreach ($database as $value) {
    $data[$value] = [];

    // $data0 = $dbh->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS where TABLE_NAME= '$value'");
    // foreach ($data0 as $value0) {
    //     $data[$value][] = $value0['COLUMN_NAME'];
    // }
    $data1 = $dbh->query("SELECT * FROM $value");
    foreach ($data1 as $key => $value1) {
        $data[$value][$key] = $value1;
    }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>

    <link href="./bootstrap.min.css" rel="stylesheet">
    <script src="./bootstrap.bundle.min.js"></script>
</head>

<body dir="rtl">

    <div class="row">

        <div class="card">
            <div class="row card-title p-2">
                <div class="input-group w-50" dir="ltr">
                    <a class="me-3" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button">Toggle first element</a>
                    <span class="input-group-text btn btn-danger" title="حذف">X</span>
                    <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username">
                    <span class="input-group-text">@example.com</span>
                </div>
            </div>
            <div class="collapse multi-collapse show" id="multiCollapseExample1">
                <div class="card card-body">
                    Some placeholder content for the second collapse component of this multi-collapse example. This panel is hidden by default but revealed when the user activates the relevant trigger.
                </div>
            </div>
        </div>

    </div>

</body>

</html>