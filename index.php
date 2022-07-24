<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
    <link rel="fluid-icon" href="./fluidicon.png" title="GitHub">

    <link href="./bootstrap.min.css" rel="stylesheet">
    <script language="Javascript" type="text/javascript" src="./edit_area/edit_area_full.js"></script>

</head>

<body class="bg-light" dir="rtl">


    <?php
    if (!isset($_GET['DB_NAME'])) {
    ?>

        <div class="container">
            <main>
                <div class="row g-5 mt-3">
                    <div class="col-md-12">
                        <form class="needs-validation" novalidate="">
                            <div class="row g-3 d-flex align-items-center justify-content-center">
                                <div class="col-sm-2">
                                    <label class="form-label">هاست</label>
                                    <input type="text" class="form-control" name="DB_HOST" value="localhost" placeholder="هاست" required>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">نام</label>
                                    <input type="text" class="form-control" name="DB_NAME" value="wordpress" placeholder="نام" required>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">نام کاربری</label>
                                    <input type="text" class="form-control" name="DB_USERNAME" value="root" placeholder="نام کاربری" required>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">رمز عبور</label>
                                    <input type="text" class="form-control" name="DB_PASS" value="root" placeholder="رمز عبور" required>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <button class="btn btn-primary" type="submit">بررسی</button>
                                </div>
                        </form>
                    </div>
                </div>
            </main>

        </div>

    <?php
        return;
    }
    try {

        $options = array(
            'db_host' => $_GET['DB_HOST'],  //localhost
            'db_uname' => $_GET['DB_USERNAME'],  //root
            'db_password' => $_GET['DB_PASS'], //root
            'db_to_backup' => $_GET['DB_NAME'], //wordpress  file name
            'db_exclude_tables' => array(), //tables to exclude
            'db_backup_path' => './', //where to backup
        );

        $DB = array();
        $mtables = array();
        $contents = "-- Database: `" . $options['db_to_backup'] . "` --\n\n";
        $DB_name = $contents;

        $mysqli = new mysqli($options['db_host'], $options['db_uname'], $options['db_password'], $options['db_to_backup']);
    } catch (\Throwable $th) {
        echo '<div class="alert alert-danger m-5" role="alert">خطایی رخ داده لطفا پس از بررسی مجددا امتحان نمایید  <a href="/">ادامه</></div>';
        return;
    }
    if ($mysqli->connect_error) {
        // die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
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
    <div class="container">
        <main>
            <div class="py-5 text-center">
                <h4 dir="ltr"><?= $DB_name ?></h4>
                <!-- <p class="lead">Below is an example form built entirely with Bootstrap’s form controls. Each required form group has a validation state that can be triggered by attempting to submit the form without completing it.</p> -->
            </div>

            <div class="row g-5 mb-3">

                <?php foreach ($DB as $key => $value) { ?>
                    <div class="card tables">
                        <div class="row card-title p-2">
                            <div class="col-6">
                                <div class="input-group" dir="ltr">
                                    <span class="input-group-text btn btn-danger remove_table" title="حذف">X</span>
                                    <input type="text" name="table_name[<?= $key ?>]" class="form-control" old_val="<?= $key ?>" value="<?= $key ?>">
                                    <span class="input-group-text"><?= $key ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <a class="me-3 float-start" data-bs-toggle="collapse" href="#<?= $key ?>" role="button">جزئیات</a>
                            </div>
                        </div>
                        <div class="collapse multi-collapse show" id="<?= $key ?>">
                            <div class="card card-body mb-3">
                                <div class="row g-3">

                                    <?php foreach ($value['FILDS'] as $value2) { ?>

                                        <div class="col-sm-3">
                                            <div class="input-group" dir="ltr">
                                                <!-- <span class="input-group-text btn btn-danger" title="حذف">X</span> -->
                                                <input type="text" name="fild_name[<?= $key ?>][<?= $value2->name ?>]" class="form-control" id="" old_val="<?= $value2->name ?>" value="<?= $value2->name ?>" required>
                                                <span class="input-group-text"><?= $value2->name ?></span>
                                            </div>
                                        </div>

                                    <?php } ?>


                                    <div class="row col-12 mt-3">
                                        <div class="col-sm-6">
                                            <label for="" class="form-label">CREATE_TABLE</label>
                                            <textarea class="form-control" id="CREATE_TABLE_<?= $key ?>" dir="ltr" name="CREATE_TABLE[<?= $key ?>]" id="" rows="10" required><?= $value['CREATE_TABLE'] ?></textarea>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="" class="form-label">INSERT_INTO</label>
                                            <textarea class="form-control" id="INSERT_INTO_<?= $key ?>" dir="ltr" name="INSERT_INTO[<?= $key ?>]" id="" rows="10" required><?= $value['INSERT_INTO'] ?></textarea>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <script>
                        editAreaLoader.init({
                            id: "CREATE_TABLE_<?= $key ?>", // id of the textarea to transform		
                            start_highlight: true, // if start with highlight
                            allow_resize: "both",
                            allow_toggle: true,
                            word_wrap: false,
                            language: "fa",
                            syntax: "sql",
                            change_callback: "BASE_SQL"
                        });
                        editAreaLoader.init({
                            id: "INSERT_INTO_<?= $key ?>", // id of the textarea to transform		
                            start_highlight: true, // if start with highlight
                            allow_resize: "both",
                            allow_toggle: true,
                            word_wrap: false,
                            language: "fa",
                            syntax: "sql",
                            EA_load_callback: "collapse_this",
                            change_callback: "BASE_SQL"
                        });
                    </script>
                <?php } ?>


                <div class="row col-sm-12 d-none0">
                    <!-- <label for="" class="form-label">SQL</label> -->
                    <textarea class="form-control" id="ALL_SQL_" dir="ltr" name="ALL_SQL" id="" rows="20" required><?= $contents ?></textarea>
                </div>

                <div class="d-flex align-items-end justify-content-end">
                    <div class="row col-sm-2 p-0">
                        <a class="btn btn-info save_file">ذخیره</a>
                    </div>
                </div>

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


    <script src="./jquery.js"></script>
    <script src="./bootstrap.bundle.min.js"></script>

    <script language="Javascript" type="text/javascript">
        // editAreaLoader.init({
        //     id: "ALL_SQL_", // id of the textarea to transform		
        //     start_highlight: true, // if start with highlight
        //     allow_resize: "both",
        //     allow_toggle: true,
        //     word_wrap: false,
        //     language: "fa",
        //     syntax: "sql"
        // });

        /*
        $("[data-bs-toggle]").click(function() {
            if (!$(this).hasClass("opened")) {
                $(this).closest(".tables").find("textarea").each(function() {
                    var id = $(this).attr("id");
                    editAreaLoader.init({
                        id: id, // id of the textarea to transform		
                        start_highlight: true, // if start with highlight
                        allow_resize: "both",
                        allow_toggle: true,
                        word_wrap: false,
                        language: "fa",
                        syntax: "sql"
                    });
                });
                $(this).addClass("opened");
            }
        });
        */

        $(".tables input").change(function() {
            var input_old_val = $(this).attr("old_val");
            var input_val = $(this).val();
            if (!input_val || !input_old_val) {
                input_val = $(this).attr("value");
                $(this).val(input_val);
            }
            $(this).closest(".tables").find("textarea").each(function() {
                var id = $(this).attr("id");
                var textarea_val = editAreaLoader.getValue(id);
                textarea_val = textarea_val.replaceAll("`" + input_old_val + "`", "`" + input_val + "`");
                editAreaLoader.setValue(id, textarea_val);
            });
            $(this).attr("old_val", input_val);
            // BASE_SQL();
        });

        $(".remove_table").click(function() {
            $(this).closest(".tables").remove();
            BASE_SQL();
        });

        function BASE_SQL() {
            var sql = '';
            $(".tables textarea").each(function() {
                sql += editAreaLoader.getValue($(this).attr("id")); //$(this).val();
            });
            $("#ALL_SQL_").val(sql);
        }

        $(".save_file").click(function() {
            // $("body").append('<div class="d-flex align-items-center justify-content-center" id="loading">در حال بارگیری ... </div>');
            const sql = $("#ALL_SQL_").val(); //editAreaLoader.getValue("ALL_SQL_");
            const d = new Date();
            download("<?= $options['db_to_backup'] ?>_" + d + ".sql", sql);
        });

        function download(filename, text) {
            var element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', filename);
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }

        function collapse_this(id) {
            $("#" + id).closest(".collapse").collapse('hide');
        };
    </script>
</body>

</html>