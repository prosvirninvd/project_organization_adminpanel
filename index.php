<?php
    session_start();
    if (isset($_GET['logout'])){
        unset($_SESSION['login-user']);
        header("location:index.php");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <header>
    <h1><?php
            if (!isset($_SESSION['login-user'])) {
                echo 'Авторизуйтесь</h1>';
            }
            else {
                echo "Админка</h1><p><a href='?logout=true'>Выйти</a></p>";
            }
        ?>
    
    </header>
    <main>
        
            <?php
            if (isset($_SESSION['login-user'])) {
                echo '<div class="menu">
                <p>Таблицы</p>';
                require_once('config.php');
                $connection_schema = mysqli_connect(host, user, password, "information_schema");
                if (!$connection_schema) exit();
                $result = mysqli_query($connection_schema,"select table_name from information_schema.tables where table_schema = '".database."';");
                echo("<ul class='table-list'>");
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<li><a href='index.php?table=$row[table_name]'>$row[table_name]</a></li>";
                }
                echo('</ul></div>');
                mysqli_close($connection_schema);
                echo '<div class="table-output">';
                if (!empty($_GET['table'])) {
                    $table_name = $_GET['table'];
                    $connection_schema = mysqli_connect(host, user, password, "information_schema");
                    if (!$connection_schema) exit();
                    $result = mysqli_query($connection_schema, "select column_name from information_schema.columns where table_schema = '".database."' and table_name = '$table_name';");
                    echo("<form action='#' method='post' id='table-form'>");
                    echo("<input name='table-name' value='$table_name' readonly />");
                    echo("<table class='data-table'>");
                    echo("<thead>");
                    echo("<tr>");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo("<th>$row[column_name]</th>");
                    }
                    echo("<th>Действия</th>");
                    echo("</tr>");
                    echo("</thead>");
                    echo("<tbody id='table-body'>");
                    $conn = mysqli_connect(host,user,password,database);
                    $result_rows = mysqli_query($conn, "select * from ".database.".$table_name;");
                    $rows = mysqli_fetch_all($result_rows, MYSQLI_ASSOC);
                    foreach ($rows as $row) {
                        echo("<tr>");
                        foreach ($row as $key => $value) {
                            echo("<td class='value-cell'><input name='update[$key]' value='$value' disabled /></td>");
                        }
                        echo("<td><button class='update-button'>Обновить</button><button class='delete-button' onclick='onDeleteClick(this);'>Удалить</button></td>");
                        echo("</tr>");
                    }
                    echo("</tbody>");
                    echo("<tfoot>");
                    echo("<tr>");
                    $result = mysqli_query($connection_schema, "select column_name from information_schema.columns where table_schema = '".database."' and table_name = '$table_name';");
                    $table_body = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    foreach ($table_body as $table_row) {
                        foreach ($table_row as $table_data) {
                            if (str_ends_with($table_data, '_id')) {
                                echo("<td>");
                                $get_table = mysqli_query($connection_schema, "SELECT referenced_table_name, referenced_column_name
                                FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = 'db_project_organization' AND TABLE_NAME = '$table_name' and column_name = '$table_data' and referenced_table_name is not null and referenced_column_name is not null;");
                                $table_result = mysqli_fetch_array($get_table);
                                $option_ids = mysqli_query($conn, "SELECT $table_result[1] from $table_result[0]");
                                $ids = mysqli_fetch_all($option_ids);
                                echo "<select name='value[$table_data]' required>";
                                echo("<option value='' disabled selected></option>");
                                foreach ($ids as $id_value) {
                                   echo("<option value='$id_value[0]'>$id_value[0]</option>");
                                }
                                echo("</select></td>");
                            }
                            elseif (str_starts_with($table_data, 'id_')) {
                                echo("<td><input name='value[$table_data]' required disabled></td>");
                            }
                            else echo("<td><input name='value[$table_data]' required></td>");
                        }
                    }
                    echo("<td><button class='data-input' type='submit' id='input-insert' form='table-form'>Добавить</button></td>");
                    mysqli_close($conn);
                    $connection_schema->close();
                    echo("</tr>");
                    echo("</tfoot>");
                    echo("</table>");
                    echo("</form>");
                }
                echo "</div>";
            }
            else {
                echo "<div class='auth'>";
                    echo '<form id="form-auth" action="auth.php" method="post">
                    <input type="text" name="input-login" id="input-login" placeholder="Логин" required><br>
                    <input type="password" name="input-password" id="input-password" placeholder="Пароль" required><br>
                    <input type="submit" name="input-sign-in" id="input-sign-in" value="Войти">
                </form>';
                echo "</div>";
            }
            ?>
    </main>
    <footer>
        
    </footer>    
</body>
</html>