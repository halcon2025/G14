<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #fff;
            text-align: center;
        }
        h2 {
            color: #ffeb3b;
        }
        form, table {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
            margin: auto;
            width: 50%;
        }
        input[type="text"], input[type="email"], input[type="submit"] {
            padding: 10px;
            margin: 5px;
            width: 80%;
            border-radius: 5px;
            border: none;
        }
        input[type="submit"] {
            background-color: #ffeb3b;
            color: #333;
            font-weight: bold;
            cursor: pointer;
        }
        a {
            color: #ffeb3b;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>Gestión de Clientes</h2>

<!-- Formulario para agregar clientes -->
<form action="index.php" method="post">
    <h3>Agregar Cliente</h3>
    Nombre: <input type="text" name="nombre" required><br>
    Email: <input type="email" name="email" required><br>
    <input type="submit" name="add" value="Agregar">
</form>

<?php
// Conexión a la base de datos PostgreSQL
$host = "localhost";
$dbname = "app_db";
$user = "app_user";
$password = "password";
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if (!$conn) {
    echo "<p>Error: No se pudo conectar a la base de datos.</p>";
    exit;
}

// Agregar cliente
if (isset($_POST['add'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $result = pg_query_params($conn, "INSERT INTO clientes (nombre, email) VALUES ($1, $2)", array($nombre, $email));
    if ($result) {
        echo "<p>Cliente agregado exitosamente.</p>";
    } else {
        echo "<p>Error al agregar el cliente.</p>";
    }
}

// Editar cliente
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $result = pg_query_params($conn, "UPDATE clientes SET nombre=$1, email=$2 WHERE id=$3", array($nombre, $email, $id));
    if ($result) {
        echo "<p>Cliente actualizado exitosamente.</p>";
    } else {
        echo "<p>Error al actualizar el cliente.</p>";
    }
}

// Mostrar lista de clientes
$result = pg_query($conn, "SELECT * FROM clientes ORDER BY id ASC");

echo "<h3>Lista de Clientes</h3>";
echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 50%; margin: auto;'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Acciones</th></tr>";

while ($row = pg_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['nombre']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>
            <form action='index.php' method='post' style='display: inline;'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <input type='text' name='nombre' value='{$row['nombre']}' required>
                <input type='email' name='email' value='{$row['email']}' required>
                <input type='submit' name='edit' value='Guardar'>
            </form>
          </td>";
    echo "</tr>";
}
echo "</table>";

// Cierra la conexión
pg_close($conn);
?>

</body>
</html>
