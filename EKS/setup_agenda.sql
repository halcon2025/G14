-- Crear la base de datos
CREATE DATABASE agenda_db;

-- Conectar a la base de datos
\c agenda_db

-- Crear el usuario con contraseña
CREATE USER agenda_user WITH PASSWORD 'lab-password';

-- Otorgar permisos al usuario sobre la base de datos
GRANT ALL PRIVILEGES ON DATABASE agenda_db TO agenda_user;

-- Crear la tabla clientes
CREATE TABLE clientes (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    telefono VARCHAR(15),
    direccion VARCHAR(255),
    correo VARCHAR(100),
    foto VARCHAR(255)
);

-- Cambiar el propietario de la tabla
ALTER TABLE public.clientes OWNER TO agenda_user;

-- Mostrar las tablas de la base de datos
\dt
