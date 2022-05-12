# Project Detail
Technologies: PHP + Laravel (api), Mysql

# Follow these instructions to setup the project
Clone the project 

Create .env file and make the neccessary changes (at the bottom there are some configs for docker-compose)

`cp .env.example .env` 

For the first project setup run  (if on windows copy each command inside `./setup.h` file and exectue)

`sh ./setup.sh` 

# Other important commands 
To access application bash 

`docker-compose exec app bash` 

# SMTP
We will be sending mails, so update variables related to smtp credentials in .env

# Filesystem
If you are running application in development/local env then use public filsystem i.e set `FILESYSTEM_DRIVER=public` in .env 

But for the cloud env, use s3 filesystem i.e set `FILESYSTEM_DRIVER=s3` and also update other variable related to s3 credentials in .env
