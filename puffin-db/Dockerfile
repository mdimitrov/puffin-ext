FROM mariadb

RUN mkdir -p /docker-entrypoint-initdb.d
COPY ./init-puffin-db.sql /docker-entrypoint-initdb.d/init-puffin-db.sql
