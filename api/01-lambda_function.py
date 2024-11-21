import json
import boto3
from decimal import Decimal

# Inicializar el cliente DynamoDB
dynamodb = boto3.resource('dynamodb')
table = dynamodb.Table('Inventarios')

# Serializador para manejar tipos Decimal
class DecimalEncoder(json.JSONEncoder):
    def default(self, obj):
        if isinstance(obj, Decimal):
            return float(obj)
        return super(DecimalEncoder, self).default(obj)

def lambda_handler(event, context):
    try:
        # Identificar el método HTTP
        http_method = event['httpMethod']

        # Manejar POST
        if http_method == 'POST':
            if 'body' not in event or not event['body']:
                return {
                    "statusCode": 400,
                    "body": json.dumps({"error": "El cuerpo de la solicitud no está presente o es inválido."})
                }

            body = json.loads(event['body'])
            item_id = body.get("id")
            item_name = body.get("name")
            quantity = body.get("quantity")

            if not item_id or not item_name or not quantity:
                return {
                    "statusCode": 400,
                    "body": json.dumps({"error": "Faltan campos obligatorios."})
                }

            table.put_item(Item={"id": item_id, "name": item_name, "quantity": quantity})

            return {
                "statusCode": 200,
                "body": json.dumps({"message": "Item agregado", "item_id": item_id})
            }

        # Manejar GET
        elif http_method == 'GET':
            if not event.get('queryStringParameters') or 'id' not in event['queryStringParameters']:
                return {
                    "statusCode": 400,
                    "body": json.dumps({"error": "El parámetro 'id' es obligatorio."})
                }

            item_id = event['queryStringParameters']['id']
            response = table.get_item(Key={"id": item_id})
            item = response.get('Item')

            if not item:
                return {
                    "statusCode": 404,
                    "body": json.dumps({"error": f"El item con id '{item_id}' no existe."})
                }

            return {
                "statusCode": 200,
                "body": json.dumps(item, cls=DecimalEncoder)
            }

        # Método no soportado
        else:
            return {
                "statusCode": 405,
                "body": json.dumps({"error": f"Método '{http_method}' no soportado."})
            }

    except Exception as e:
        return {
            "statusCode": 500,
            "body": json.dumps({"error": "Error interno", "details": str(e)})
        }
