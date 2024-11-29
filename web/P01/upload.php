<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABCX - Subir Archivo</title>
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
        main {
            padding: 20px;
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        main h2 {
            color: #1b5e20;
            font-size: 1.5em;
        }
        main p {
            font-size: 1em;
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #1b5e20;
            font-weight: bold;
            font-size: 1.1em;
        }
        a:hover {
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
    </header>
    <main>
        <?php
        require 'vendor/autoload.php';
        use Aws\S3\S3Client;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $file = $_FILES['file'];

            $s3 = new S3Client([
                'region' => 'us-east-1',
                'version' => 'latest'
            ]);

            $bucket = 'abcx-digital-products';

            try {
                $result = $s3->putObject([
                    'Bucket' => $bucket,
                    'Key' => $file['name'],
                    'SourceFile' => $file['tmp_name'],
                ]);

                echo "<h2>Archivo subido con éxito</h2>";
                echo "<p>El archivo se ha subido correctamente a S3. Puedes descargarlo desde el siguiente enlace:</p>";
                echo "<a href='{$result['ObjectURL']}' target='_blank'>Descargar Archivo</a>";
            } catch (Exception $e) {
                echo "<h2 style='color:red;'>Error al subir el archivo</h2>";
                echo "<p>Ocurrió un problema al intentar subir el archivo: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<h2 style='color:red;'>Acceso Inválido</h2>";
            echo "<p>Esta página solo está disponible para subir archivos mediante el formulario.</p>";
        }
        ?>
        <p><a href="index.php">Volver al portal</a></p>
    </main>
    <footer>
        &copy; 2024 ABCX. Todos los derechos reservados.
    </footer>
</body>
</html>
