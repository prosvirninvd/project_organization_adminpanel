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
    <h1>Админка</h1>
    </header>
    <main>
        <div class="menu">
            <p>Таблицы</p>
            <?php
        require_once('config.php');
    $connection_schema = mysqli_connect(host, user, password, schema);
    if (!$connection_schema) exit();
    $result = mysqli_query($connection_schema,"select table_name from ".schema.".tables where table_schema = '".database."';");
    echo("<ul class='table-list'>");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li><a href='index.php?table=$row[table_name]'>$row[table_name]</a></li>";
    }
    echo('</ul>');
    mysqli_close($connection_schema);
    ?>
        </div>
        <div class="table-output">
            <?php
                if (!empty($_GET['table'])) {
                    $table_name = $_GET['table'];
                    $connection_schema = mysqli_connect(host, user, password, schema);
                    if (!$connection_schema) exit();
                    $result = mysqli_query($connection_schema, "select column_name from ".schema.".columns where table_schema = '".database."' and table_name = '$table_name';");
                    echo("<form action='#' method='post' id='table-form'>");
                    echo("<input name='table-name' value='$table_name' readonly />");
                    echo("<table>");
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
                    
                    mysqli_close($conn);
                    echo("</tbody>");
                    echo("<tfoot>");
                    echo("<tr>");
                    $result = mysqli_query($connection_schema, "select column_name from ".schema.".columns where table_schema = '".database."' and table_name = '$table_name';");
                    $table_body = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    foreach ($table_body as $table_row) {
                        foreach ($table_row as $table_data) {
                            if (str_starts_with($table_data, 'id_')) {
                                echo("<td><input name='value[$table_data]' required disabled></td>");
                            }
                            else echo("<td><input name='value[$table_data]' required></td>");
                        }
                    }
                    echo("<td><button class='data-input' type='submit' id='input-insert' form='table-form'>Добавить</button></td>");
                    $connection_schema->close();
                    echo("</tr>");
                    echo("</tfoot>");
                    echo("</table>");
                    echo("</form>");
                }
                
            
            ?>
        </div>
    </main>
    <footer>
        
    </footer>    
</body>
</html>