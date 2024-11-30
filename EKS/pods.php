<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pod Info</title>
    <style>
        /* Estilo general */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background: linear-gradient(to bottom, #e0f7ff, #cceeff);
            border-radius: 15px;
            padding: 20px 40px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 2.5rem;
            color: #0066cc;
        }

        p {
            font-size: 1.2rem;
            color: #333;
            margin: 10px 0;
        }

        .timestamp {
            font-style: italic;
            color: #666;
        }

        .refresh-btn {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            color: white;
            background-color: #007acc;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .refresh-btn:hover {
            background-color: #005c99;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pod Information</h1>
        <?php
        // Leer el ID del Pod desde /etc/hostname
        $pod_id = @file_get_contents('/etc/hostname') ?: 'Unknown Pod';
        $timestamp = date('Y-m-d H:i:s');
        ?>
        <p><strong>Pod ID:</strong> <?php echo htmlspecialchars($pod_id); ?></p>
        <p class="timestamp"><strong>Timestamp:</strong> <?php echo $timestamp; ?></p>
        <button class="refresh-btn" onclick="refreshPage()">Refresh</button>
    </div>
    <script>
        // Agrega un efecto al recargar la página
        function refreshPage() {
            document.querySelector('.container').style.opacity = '0';
            setTimeout(() => {
                location.reload();
            }, 300);
        }
    </script>
</body>
</html>