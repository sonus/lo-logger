# LO-LOGGER

Read from aggregated log file that contains logs from several services


## Local Development Setup Guide

### Requriements

* Docker

## Installation

You start application on your local environment by running the following command.

*Please NOTE:* This command would take sometime when you run it on the first time since its creating vendors, db, db migration etc.

```sh
docker-compose up --build
```

You can now view the application swagger interface at [http://localhost:808/api](http://localhost:808/api)
## Access Database

You can connect to local db port under
* Hostname: `localhost`
* username: `root`
* password: `root`

## Development support Features

*Nginx Log Generator*
Nginx Log Generator is an underlying code that will generate realtime fake logs for easy local development.

To disable this open ``docker/config/supervisord.conf`` file and remove the following section.
```conf
- [program:genarate_log]
- command=/var/www/html/development/nginx-log-generator
- autorestart=true
```

### Troubleshoot

1. Already running local mysql

_You can either stop running mysql instance or re-map the port configurations on following files: ``docker-compose.yml``,``.env.local``_
