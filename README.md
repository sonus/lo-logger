# LO-LOGGER

Read from the aggregated log file that contains logs from several services and write it to a database and provide API endpoints to access them for a frontend application.


## Local Development Setup Guide

### Requirements

* Docker

## Installation

Start the application in your local environment by running the following command.

*Please NOTE:* This command would take some time when you run it for the first time since it creates vendors, DB, DB migration etc.

```sh
docker network create api-net
docker-compose up --build
```

You can now view the application swagger interface at [http://localhost:808/api](http://localhost:808/api)
## Access Database

You can connect to DB using the following credentials
* Hostname: `localhost`
* username: `root`
* password: `root`

## Development support Features

*Nginx Log Generator*
Nginx Log Generator is an underlying code that will generate real-time fake logs for easy local development.

To disable this open ``docker/config/supervisord.conf`` file and remove the following section.
```conf
- [program:genarate_log]
- command=/var/www/html/development/nginx-log-generator
- autorestart=true
```

### Troubleshoot

1. Already running local mysql

_You can either stop running MySQL instance or re-map the port configurations on following files: ``docker-compose.yml``,``.env.local``_
