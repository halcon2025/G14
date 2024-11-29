<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABCX Digital Products Portal</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #b3e5c8, #e8f5e9);
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background-color: #1b5e20;
            color: white;
            padding: 20px;
            text-align: center;
        }
        header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        header p {
            margin: 5px 0 0;
            font-size: 1.2em;
        }
        main {
            padding: 20px;
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #1b5e20;
            font-size: 1.5em;
        }
        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        form label {
            font-size: 1em;
            margin-bottom: 10px;
        }
        form input[type="file"] {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #1b5e20;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        form button:hover {
            background-color: #66bb6a;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            margin: 5px 0;
        }
        ul li a {
            color: #1b5e20;
            text-decoration: none;
            font-size: 1em;
        }
        ul li a:hover {
            text-decoration: underline;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #1b5e20;
            color: white;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header>
        <h1>ABCX Digital Products Portal</h1>
        <p>Gestión de archivos digitales de manera eficiente y segura</p>
    </header>
    <main>
        <h2>Subir un archivo al portal</h2>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <label for="file">Selecciona un archivo:</label>
            <input type="file" name="file" id="file" required>
            <button type="submit">Subir archivo</button>
        </form>
        <h2>Archivos disponibles</h2>
        <ul>
            <?php
            require 'vendor/autoload.php';
            use Aws\S3\S3Client;

            $s3 = new S3Client(['region' => 'us-east-1', 'version' => 'latest']);
            $bucket = 'abcx-digital-products';

            try {
                $objects = $s3->listObjects(['Bucket' => $bucket]);
                if (!empty($objects['Contents'])) {
                    foreach ($objects['Contents'] as $object) {
                        echo "<li><a href='https://$bucket.s3.amazonaws.com/{$object['Key']}' target='_blank'>{$object['Key']}</a></li>";
                    }
                } else {
                    echo "<li>No hay archivos disponibles.</li>";
                }
            } catch (Exception $e) {
                echo "<p style='color:red;'>Error al cargar los archivos: " . $e->getMessage() . "</p>";
            }
            ?>
        </ul>
    </main>
    <footer>
        &copy; 2024 ABCX. Todos los derechos reservados.
    </footer>
</body>
</html>
