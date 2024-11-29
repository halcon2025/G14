#!/bin/bash

# Crear archivo Rol_IAM_EKS.json
cat > Rol_IAM_EKS.json <<EOL
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": {
        "Service": "eks.amazonaws.com"
      },
      "Action": "sts:AssumeRole"
    }
  ]
}
EOL

echo "Creando el rol de IAM para el clúster EKS..."
aws iam create-role --role-name eks-cluster-role --assume-role-policy-document file://Rol_IAM_EKS.json

echo "Adjuntando la política AmazonEKSClusterPolicy al rol eks-cluster-role..."
aws iam attach-role-policy --role-name eks-cluster-role --policy-arn arn:aws:iam::aws:policy/AmazonEKSClusterPolicy

# Crear archivo Rol_IAM_Nodo.json
cat > Rol_IAM_Nodo.json <<EOL
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": {
        "Service": "ec2.amazonaws.com"
      },
      "Action": "sts:AssumeRole"
    }
  ]
}
EOL

echo "Creando el rol de IAM para los nodos del clúster..."
aws iam create-role --role-name eks-node-role --assume-role-policy-document file://Rol_IAM_Nodo.json

echo "Adjuntando políticas al rol eks-node-role..."
aws iam attach-role-policy --role-name eks-node-role --policy-arn arn:aws:iam::aws:policy/AmazonEKSWorkerNodePolicy
aws iam attach-role-policy --role-name eks-node-role --policy-arn arn:aws:iam::aws:policy/AmazonEC2ContainerRegistryReadOnly
aws iam attach-role-policy --role-name eks-node-role --policy-arn arn:aws:iam::aws:policy/AmazonEKS_CNI_Policy
aws iam attach-role-policy --role-name eks-node-role --policy-arn arn:aws:iam::aws:policy/AmazonEKSClusterPolicy

echo "Creando la política ClusterAutoscalerPolicy..."
aws iam create-policy --policy-name ClusterAutoscalerPolicy --policy-document '{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "autoscaling:DescribeAutoScalingGroups",
                "autoscaling:UpdateAutoScalingGroup",
                "autoscaling:SetDesiredCapacity",
                "autoscaling:TerminateInstanceInAutoScalingGroup",
                "autoscaling:DescribeTags"
            ],
            "Resource": "*"
        }
    ]
}'

echo "Adjuntando la política ClusterAutoscalerPolicy al rol eks-node-role..."
aws iam attach-role-policy --role-name eks-node-role --policy-arn arn:aws:iam::088902427698:policy/ClusterAutoscalerPolicy

echo "Configuración de roles completada exitosamente."