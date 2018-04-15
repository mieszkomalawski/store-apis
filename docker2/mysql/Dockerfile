FROM mysql:latest
RUN deluser mysql
RUN useradd mysql
RUN mkdir -p /var/lib/mysql
RUN rm -rf /var/lib/mysql/*
RUN chmod -R 777 /var/lib/mysql