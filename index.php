<?php

$options = array(
    'db_host' => 'localhost',  //mysql host
    'db_uname' => 'root',  //user
    'db_password' => 'root', //pass
    'db_to_backup' => 'wordpress', //database name
    'db_backup_path' => './', //where to backup
    'db_exclude_tables' => array(), //tables to exclude
);

$tablesfack = $options['tablesfack'] ?? [];

$DB = array();
$mtables = array();
$contents = "-- Database: `" . $options['db_to_backup'] . "` --\n\n";

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
$DB->table_name = $mtables;
echo json_encode($DB);
return;


foreach ($mtables as $table) {
    $contents .= "-- Table `" . $tablesfack[$table] . "` --\n";

    $results = $mysqli->query("SHOW CREATE TABLE " . $table);
    while ($row = $results->fetch_array()) {

        // echo json_encode($row);
        // exit;
        $contents .= str_replace($table, $tablesfack[$table], $row[1]) . ";\n\n";
    }

    $results = $mysqli->query("SELECT * FROM " . $table);
    $row_count = $results->num_rows;
    $fields = $results->fetch_fields();
    $fields_count = count($fields);

    $insert_head = "INSERT INTO `" . $tablesfack[$table] . "` (";
    for ($i = 0; $i < $fields_count; $i++) {

        // echo $i+1 . "<br>";
        // echo json_encode($fields);
        // echo "<br><br>----<br><br><br>";

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
                $contents .= $insert_head;
            }
            $contents .= "(";
            for ($i = 0; $i < $fields_count; $i++) {
                $row_content =  str_replace("\n", "\\n", $mysqli->real_escape_string($row[$i]));

                switch ($fields[$i]->type) {
                    case 8:
                    case 3:
                        $contents .=  $row_content;
                        break;
                    default:
                        $contents .= "'" . $row_content . "'";
                }
                if ($i < $fields_count - 1) {
                    $contents  .= ', ';
                }
            }
            if (($r + 1) == $row_count || ($r % 400) == 399) {
                $contents .= ");\n\n";
            } else {
                $contents .= "),\n";
            }
            $r++;
        }
    }
}

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
            <!-- <div class="py-5 text-center">
                <h2>Checkout form</h2>
                <p class="lead">Below is an example form built entirely with Bootstrap’s form controls. Each required form group has a validation state that can be triggered by attempting to submit the form without completing it.</p>
            </div> -->

            <div class="row g-5 mt-3">

                <?php foreach ($data as $key => $value) { ?>
                    <div class="card">
                        <div class="row card-title p-2">
                            <div class="input-group w-50" dir="ltr">
                                <a class="me-3" data-bs-toggle="collapse" href="#<?= $key ?>" role="button">Toggle first element</a>
                                <span class="input-group-text btn btn-danger" title="حذف">X</span>
                                <input type="text" class="form-control" placeholder="<?= $key ?>">
                                <span class="input-group-text"><?= $key ?></span>
                            </div>
                        </div>
                        <div class="collapse multi-collapse show" id="<?= $key ?>">
                            <div class="card card-body mb-3">
                                <div class="row g-3">

                                    <?php foreach ($value as $key2 => $value2) { ?>
                                        <div class="col-sm-6">
                                            <label for="firstName" class="form-label"><?= $key2 ?></label>
                                            <input type="text" class="form-control" id="firstName" placeholder="" value="<?= $key2 ?>" required="">
                                            <div class="invalid-feedback">
                                                Valid first name is required.
                                            </div>
                                        </div>
                                    <?php } ?>


                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>


                <div class="col-md-5 col-lg-4 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary">Your cart</span>
                        <span class="badge bg-primary rounded-pill">3</span>
                    </h4>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Product name</h6>
                                <small class="text-muted">Brief description</small>
                            </div>
                            <span class="text-muted">$12</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Second product</h6>
                                <small class="text-muted">Brief description</small>
                            </div>
                            <span class="text-muted">$8</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Third item</h6>
                                <small class="text-muted">Brief description</small>
                            </div>
                            <span class="text-muted">$5</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between bg-light">
                            <div class="text-success">
                                <h6 class="my-0">Promo code</h6>
                                <small>EXAMPLECODE</small>
                            </div>
                            <span class="text-success">−$5</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total (USD)</span>
                            <strong>$20</strong>
                        </li>
                    </ul>

                    <form class="card p-2">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Promo code">
                            <button type="submit" class="btn btn-secondary">Redeem</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-7 col-lg-8">
                    <h4 class="mb-3">Billing address</h4>
                    <form class="needs-validation" novalidate="">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="firstName" class="form-label">First name</label>
                                <input type="text" class="form-control" id="firstName" placeholder="" value="" required="">
                                <div class="invalid-feedback">
                                    Valid first name is required.
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="lastName" class="form-label">Last name</label>
                                <input type="text" class="form-control" id="lastName" placeholder="" value="" required="">
                                <div class="invalid-feedback">
                                    Valid last name is required.
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group has-validation">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control" id="username" placeholder="Username" required="">
                                    <div class="invalid-feedback">
                                        Your username is required.
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
                                <input type="email" class="form-control" id="email" placeholder="you@example.com">
                                <div class="invalid-feedback">
                                    Please enter a valid email address for shipping updates.
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" placeholder="1234 Main St" required="">
                                <div class="invalid-feedback">
                                    Please enter your shipping address.
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="address2" class="form-label">Address 2 <span class="text-muted">(Optional)</span></label>
                                <input type="text" class="form-control" id="address2" placeholder="Apartment or suite">
                            </div>

                            <div class="col-md-5">
                                <label for="country" class="form-label">Country</label>
                                <select class="form-select" id="country" required="">
                                    <option value="">Choose...</option>
                                    <option>United States</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a valid country.
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="state" class="form-label">State</label>
                                <select class="form-select" id="state" required="">
                                    <option value="">Choose...</option>
                                    <option>California</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please provide a valid state.
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="zip" class="form-label">Zip</label>
                                <input type="text" class="form-control" id="zip" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Zip code required.
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="same-address">
                            <label class="form-check-label" for="same-address">Shipping address is the same as my billing address</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="save-info">
                            <label class="form-check-label" for="save-info">Save this information for next time</label>
                        </div>

                        <hr class="my-4">

                        <h4 class="mb-3">Payment</h4>

                        <div class="my-3">
                            <div class="form-check">
                                <input id="credit" name="paymentMethod" type="radio" class="form-check-input" checked="" required="">
                                <label class="form-check-label" for="credit">Credit card</label>
                            </div>
                            <div class="form-check">
                                <input id="debit" name="paymentMethod" type="radio" class="form-check-input" required="">
                                <label class="form-check-label" for="debit">Debit card</label>
                            </div>
                            <div class="form-check">
                                <input id="paypal" name="paymentMethod" type="radio" class="form-check-input" required="">
                                <label class="form-check-label" for="paypal">PayPal</label>
                            </div>
                        </div>

                        <div class="row gy-3">
                            <div class="col-md-6">
                                <label for="cc-name" class="form-label">Name on card</label>
                                <input type="text" class="form-control" id="cc-name" placeholder="" required="">
                                <small class="text-muted">Full name as displayed on card</small>
                                <div class="invalid-feedback">
                                    Name on card is required
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="cc-number" class="form-label">Credit card number</label>
                                <input type="text" class="form-control" id="cc-number" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Credit card number is required
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="cc-expiration" class="form-label">Expiration</label>
                                <input type="text" class="form-control" id="cc-expiration" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Expiration date required
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label for="cc-cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cc-cvv" placeholder="" required="">
                                <div class="invalid-feedback">
                                    Security code required
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button>
                    </form>
                </div>
            </div>
        </main>

        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">© 2017–2022 Company Name</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#">Privacy</a></li>
                <li class="list-inline-item"><a href="#">Terms</a></li>
                <li class="list-inline-item"><a href="#">Support</a></li>
            </ul>
        </footer>
    </div>


</body>

</html>