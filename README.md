# App Mamy

## Description
it's an application using symfony 5 (microservice API) and nuxtjs (front), all with a Docker environment.

## Prerequisites
To run the application you need docker and docker-compose installed on your machine.

## Installation
For installation you must on each symfony microservice, create a .env.local file to override what is in the .env, you also need to create an .env file at the root folder from the .env.dist file.
In the .env.local you must define the link to the database like this:
```
DATABASE_URL="postgresql://postgres:changeme@app_db_postgres:5432/postgres?serverVersion=13&charset=utf8"
```
The database password can be changed by changing the POSTGRES_PASSWORD variable which is located in the .env at the root of the project.
To be able to be executed you must give the right permission to the docker.sh file:
```
$ sudo chmod +x docker.sh
```

## Usage
After configuring you can now launch the application by typing the following command in the terminal:
```
$ ./docker.sh deploy
```
This command downloads all dependencies and executes all commands necessary to launch the application.

After all that you must change the password for the admin by going to the link http://localhost/pass-reset/init-token
then access the login page at http://localhost/login with the user: admin@app.ad and your new password.
