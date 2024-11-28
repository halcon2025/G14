from flask import Flask, jsonify, render_template_string
import psycopg2
import requests
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# Configuración de conexión a la base de datos
def get_db_connection():
    return psycopg2.connect(
        host="dbpostgres.chzd0gyptzf0.us-east-1.rds.amazonaws.com",
        database="productsdb",
        user="postgres",
        password="lab-password"
    )

# Obtener la IP pública usando un servicio externo confiable
def get_public_ip():
    try:
        response = requests.get('https://api64.ipify.org?format=json', timeout=5)
        data = response.json()
        return data.get("ip", "127.0.0.1")
    except Exception as e:
        return "127.0.0.1"

PUBLIC_IP = get_public_ip()

# Ruta principal para el puerto 8080
@app.route('/', methods=['GET'])
def home():
    html_content = f"""
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Backend Flask en EC2</title>
        <style>
            body {{
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background: linear-gradient(45deg, #4facfe, #00f2fe);
                font-family: Arial, sans-serif;
                color: #fff;
                text-align: center;
            }}
            h1 {{
                font-size: 2.5rem;
                margin-bottom: 20px;
            }}
            p {{
                font-size: 1.2rem;
                margin-bottom: 10px;
            }}
            ul {{
                list-style: none;
                padding: 0;
            }}
            li {{
                font-size: 1.1rem;
            }}
            a {{
                color: #ffde59;
                text-decoration: none;
                font-weight: bold;
            }}
            a:hover {{
                text-decoration: underline;
            }}
            .container {{
                background: rgba(0, 0, 0, 0.5);
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            }}
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Backend Flask en EC2</h1>
            <p>El servidor está funcionando correctamente.</p>
            <p>Para probar la API, por favor dirígete a:</p>
            <ul>
                <li><a href="http://{PUBLIC_IP}:5000/products" target="_blank">http://{PUBLIC_IP}:5000/products</a> para ver los productos disponibles.</li>
            </ul>
        </div>
    </body>
    </html>
    """
    return render_template_string(html_content)

# Ruta para obtener los productos en el puerto 5000
@app.route('/products', methods=['GET'])
def get_products():
    try:
        conn = get_db_connection()
        cur = conn.cursor()
        cur.execute('SELECT name, description FROM products;')
        products = cur.fetchall()
        cur.close()
        conn.close()
        return jsonify([{'name': row[0], 'description': row[1]} for row in products])
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# Configurar Flask para funcionar en los puertos 8080 y 5000
if __name__ == '__main__':
    from multiprocessing import Process

    # Función para ejecutar la aplicación en el puerto 8080
    def run_on_port_8080():
        app.run(host='0.0.0.0', port=8080)

    # Función para ejecutar la aplicación en el puerto 5000
    def run_on_port_5000():
        app.run(host='0.0.0.0', port=5000)

    # Crear procesos para ambos puertos
    p1 = Process(target=run_on_port_8080)
    p2 = Process(target=run_on_port_5000)

    # Iniciar procesos
    p1.start()
    p2.start()

    # Esperar a que los procesos terminen
    p1.join()
    p2.join()
