FROM mysql:8.0.36-debian
ADD db-migrations /docker-entrypoint-initdb.d