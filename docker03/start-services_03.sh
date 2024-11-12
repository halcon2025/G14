#!/bin/bash
# Iniciar PostgreSQL
service postgresql start
# Iniciar Apache
service apache2 start
# Iniciar SSH
/usr/sbin/sshd -D
