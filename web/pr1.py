import os
import subprocess
import time
import boto3
from botocore.exceptions import NoCredentialsError, PartialCredentialsError

# Verificar si boto3 está instalado; si no, instalarlo
try:
    import boto3
except ImportError:
    subprocess.check_call(['pip', 'install', 'boto3'])

# Inicializar cliente de AWS
try:
    cloudformation = boto3.client('cloudformation')
    iam = boto3.client('iam')
    ec2 = boto3.client('ec2')
    ssm = boto3.client('ssm')
except (NoCredentialsError, PartialCredentialsError):
    print("Credenciales de AWS no configuradas. Asegúrate de tener acceso a AWS.")
    exit(1)

# 1. Crear la pila de CloudFormation (comentado para la prueba)
# try:
#     cloudformation.create_stack(
#         StackName='PILVPC10',
#         TemplateBody='file://script_VPC_443.yaml',
#         Parameters=[
#             {
#                 'ParameterKey': 'VPCName',
#                 'ParameterValue': 'VPC10'
#             }
#         ]
#     )
#     print("Pila de CloudFormation creada exitosamente.")
# except Exception as e:
#     print("Error al crear la pila de CloudFormation:", e)

# Esperar 30 segundos para la creación de la pila
print("Esperando 30 segundos para la creación de la pila...")
time.sleep(30)

# 2. Asigna la política AdministratorAccess al rol LabRole
try:
    iam.attach_role_policy(
        RoleName='LabRole',
        PolicyArn='arn:aws:iam::aws:policy/AdministratorAccess'
    )
    print("Política AdministratorAccess asignada a LabRole.")
except Exception as e:
    print("Error al asignar la política a LabRole:", e)

# 3. Obtiene el ID de la instancia Bastion Host
try:
    instances = ec2.describe_instances(
        Filters=[{'Name': 'tag:Name', 'Values': ['Bastion Host']}]
    )
    instance_id = instances['Reservations'][0]['Instances'][0]['InstanceId']
    print(f"ID de la instancia Bastion Host obtenido: {instance_id}")
except Exception as e:
    print("Error al obtener el ID de la instancia Bastion Host:", e)
    exit(1)

# 4. Obtiene la asociación de perfil de instancia actual
try:
    associations = ec2.describe_iam_instance_profile_associations(
        Filters=[{'Name': 'instance-id', 'Values': [instance_id]}]
    )
    association_id = associations['IamInstanceProfileAssociations'][0]['AssociationId']
    print(f"Asociación de perfil de instancia obtenida: {association_id}")
except Exception as e:
    print("Error al obtener la asociación de perfil de instancia:", e)
    exit(1)

# 5. Desasocia el perfil de instancia actual
try:
    ec2.disassociate_iam_instance_profile(AssociationId=association_id)
    print("Perfil de instancia desasociado exitosamente.")
except Exception as e:
    print("Error al desasociar el perfil de instancia:", e)

# 6. Asocia el nuevo perfil de instancia LabInstanceProfile
try:
    ec2.associate_iam_instance_profile(
        InstanceId=instance_id,
        IamInstanceProfile={'Name': 'LabInstanceProfile'}
    )
    print("Nuevo perfil de instancia asociado exitosamente.")
except Exception as e:
    print("Error al asociar el nuevo perfil de instancia:", e)

# 7. Espera hasta que la instancia esté en estado "Online" en SSM
print("Esperando a que la instancia esté 'Online' en SSM...")
ssm_status = ""
while ssm_status != "Online":
    try:
        instance_info = ssm.describe_instance_information(
            Filters=[{'Key': 'InstanceIds', 'Values': [instance_id]}]
        )
        ssm_status = instance_info['InstanceInformationList'][0]['PingStatus']
        if ssm_status != "Online":
            time.sleep(10)
    except Exception as e:
        print("Esperando estado 'Online' en SSM:", e)
        time.sleep(10)
print("La instancia está 'Online' en SSM.")

# 8. Ejecuta los comandos en la instancia Bastion Host usando SSM
try:
    ssm.send_command(
        DocumentName="AWS-RunShellScript",
        Targets=[{"Key": "instanceids", "Values": [instance_id]}],
        Parameters={"commands": [
            "aws iam create-user --user-name anconur",
            "aws iam create-group --group-name GRUPO_ADMIN",
            "aws iam attach-group-policy --group-name GRUPO_ADMIN --policy-arn arn:aws:iam::aws:policy/AdministratorAccess",
            "aws iam add-user-to-group --group-name GRUPO_ADMIN --user-name anconur"
        ]}
    )
    print("Comandos ejecutados en la instancia Bastion Host.")
except Exception as e:
    print("Error al ejecutar los comandos en la instancia Bastion Host:", e)

# Comando para reiniciar el agente SSM en el Bastion Host
try:
    ssm.send_command(
        DocumentName="AWS-RunShellScript",
        Targets=[{"Key": "instanceids", "Values": [instance_id]}],
        Parameters={"commands": ["sudo systemctl restart amazon-ssm-agent"]}
    )
    print("Agente SSM reiniciado en la instancia Bastion Host.")
except Exception as e:
    print("Error al reiniciar el agente SSM:", e)
