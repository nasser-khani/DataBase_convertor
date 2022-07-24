<?php

$options = array(
    'db_host' => 'localhost',  //mysql host
    'db_uname' => 'root',  //user
    'db_password' => 'root', //pass
    'db_to_backup' => 'wordpress', //database name
    'db_exclude_tables' => array(), //tables to exclude
    'db_backup_path' => './', //where to backup
);

$DB = array();
$mtables = array();
$contents = "-- Database: `" . $options['db_to_backup'] . "` --\n\n";
$DB_name = $contents;

$mysqli = new mysqli($options['db_host'], $options['db_uname'], $options['db_password'], $options['db_to_backup']);
if ($mysqli->connect_error) {
    die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}


$results = $mysqli->query("SHOW TABLES");

while ($row = $results->fetch_array()) {
    if (!in_array($row[0], $options['db_exclude_tables'])) {
        $mtables[] = $row[0];
    }
}

foreach ($mtables as $table) {
    $DB[$table] = [];

    $contents .= "-- Table `" . $table . "` --\n";

    $results = $mysqli->query("SHOW CREATE TABLE " . $table);
    while ($row = $results->fetch_array()) {
        $contents .= str_replace($table, $table, $row[1]) . ";\n\n";
        $DB[$table]['CREATE_TABLE'] = str_replace($table, $table, $row[1]) . ";\n\n";
    }

    $results = $mysqli->query("SELECT * FROM " . $table);
    $row_count = $results->num_rows;
    $fields = $results->fetch_fields();

    $DB[$table]['FILDS'] = $fields;

    $fields_count = count($fields);

    $insert_head = "INSERT INTO `" . $table . "` (";
    for ($i = 0; $i < $fields_count; $i++) {
        $insert_head  .= "`" . $fields[$i]->name . "`";
        if ($i < $fields_count - 1) {
            $insert_head  .= ', ';
        }
    }
    $insert_head .=  ")";
    $insert_head .= " VALUES\n";

    if ($row_count > 0) {
        $r = 0;
        while ($row = $results->fetch_array()) {
            if (($r % 400)  == 0) {
                $contents_ = $insert_head;
            }
            $contents_ .= "(";
            for ($i = 0; $i < $fields_count; $i++) {
                $row_content =  str_replace("\n", "\\n", $mysqli->real_escape_string($row[$i]));

                switch ($fields[$i]->type) {
                    case 8:
                    case 3:
                        $contents_ .=  $row_content;
                        break;
                    default:
                        $contents_ .= "'" . $row_content . "'";
                }
                if ($i < $fields_count - 1) {
                    $contents_  .= ', ';
                }
            }
            if (($r + 1) == $row_count || ($r % 400) == 399) {
                $contents_ .= ");\n\n";
            } else {
                $contents_ .= "),\n";
            }
            $r++;
        }
        $contents .= $contents_;
        $DB[$table]['INSERT_INTO'] = $contents_;
    } else {
        $DB[$table]['INSERT_INTO'] = '';
    }
}

// echo json_encode($DB['wp_users']);
// return;

/*

    if (!is_dir($options['db_backup_path'])) {
        mkdir($options['db_backup_path'], 0777, true);
    }

    return $contents;
    $backup_file_name = $options['db_to_backup'] . " sql-backup- " . date("d-m-Y--h-i-s") . ".sql";

    $fp = fopen($options['db_backup_path'] . '/' . $backup_file_name, 'w+');
    if (($result = fwrite($fp, $contents))) {
        echo "Backup file created '$backup_file_name' ($result)";
    }
    fclose($fp);
    return $backup_file_name;

*/


// try {
//     $DBname = "wordpress";
//     $DBuser = "root";
//     $DBpass = "root";
//     $host = 'localhost';

//     $dbh = new PDO("mysql:host=$host;dbname=$DBname", $DBuser, $DBpass);
//     $dbh->exec("set names utf8");
// } catch (PDOException $error) {
//     echo $error->getMessage();
// }

// // $data = $dbh->prepare("SELECT * FROM Sys.Tables");
// // $data->execute();
// // $data = $data->fetch();


// $database = $dbh->prepare("show tables");
// $database->execute();
// $database = $database->fetchAll(PDO::FETCH_COLUMN);
// $data = [];
// foreach ($database as $value) {
//     $data[$value] = [];

//     // $data0 = $dbh->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS where TABLE_NAME= '$value'");
//     // foreach ($data0 as $value0) {
//     //     $data[$value][] = $value0['COLUMN_NAME'];
//     // }
//     $data1 = $dbh->query("SELECT * FROM $value");
//     foreach ($data1 as $key => $value1) {
//         $data[$value][$key] = $value1;
//     }
// }

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

<body class="bg-light" dir="rtl">

    <div class="container">
        <main>
            <div class="py-5 text-center">
                <h4 dir="ltr"><?= $DB_name ?></h4>
                <!-- <p class="lead">Below is an example form built entirely with Bootstrap’s form controls. Each required form group has a validation state that can be triggered by attempting to submit the form without completing it.</p> -->
            </div>

            <div class="row g-5 mt-3 mb-3">

                <?php foreach ($DB as $key => $value) { ?>
                    <div class="card">
                        <div class="row card-title p-2">
                            <div class="input-group w-50" dir="ltr">
                                <a class="me-3" data-bs-toggle="collapse" href="#<?= $key ?>" role="button">نمایش جزئیات</a>
                                <span class="input-group-text btn btn-danger" title="حذف">X</span>
                                <input type="text" class="form-control" placeholder="<?= $key ?>">
                                <span class="input-group-text"><?= $key ?></span>
                            </div>
                        </div>
                        <div class="collapse multi-collapse show" id="<?= $key ?>">
                            <div class="card card-body mb-3">
                                <div class="row g-3">

                                    <?php foreach ($value['FILDS'] as $value2) { ?>
                                        <div class="col-sm-3">
                                            <label for="firstName" class="form-label"><?= $value2->name ?></label>
                                            <input type="text" class="form-control" id="firstName" placeholder="" value="<?= $value2->name ?>" required>
                                        </div>
                                    <?php } ?>


                                    <div class="row col-12 mt-3">
                                        <div class="col-sm-6">
                                            <label for="firstName" class="form-label">CREATE_TABLE</label>
                                            <textarea class="form-control" dir="ltr" name="" id="" rows="10" required><?= $value['CREATE_TABLE'] ?></textarea>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="firstName" class="form-label">INSERT_INTO</label>
                                            <textarea class="form-control" dir="ltr" name="" id="" rows="10" required><?= $value['INSERT_INTO'] ?></textarea>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>



            </div>
        </main>

        <!-- <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">© 2017–2022 Company Name</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#">Privacy</a></li>
                <li class="list-inline-item"><a href="#">Terms</a></li>
                <li class="list-inline-item"><a href="#">Support</a></li>
            </ul>
        </footer> -->

    </div>


</body>

</html>