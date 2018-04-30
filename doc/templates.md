# Developing and deploying applications

## Traefik
* When running the [`install`](console.md#install) command, Cocotte creates a directory named `traefik`. You can commit it to version control. This is yours to modify if and when necessary.
* Cocotte expects exactly one Traefik container running in your Docker Engine as this is required to route the requests of all your other applications.
* This is a reverse proxy whose job is to listen to ports needed by more than one container, and route each request to its intended container based on the hostname of the request. 
* This particular reverse proxy uses Let's Encrypt to automatically generate and renew SSL certificates per hostname.
* Cocotte makes it work out of the box, but should you want to further develop for this application, then check out its [README](../installer/template/traefik/README.md).

## Application templates

### Static site
* When running the [`static-site`](console.md#static-site) command, Cocotte creates a directory named after the namespace your chose for your site.
* You can create as many of them as you want.
* To develop locally, first make sure Traefik is [running locally](../installer/template/traefik/README.md#develop-locally), then hop to the [README](../installer/template/static/README.md).
	
### Symfony (coming soon)

## Useful commands

* `docker ps` lists all your running containers.
* `docker-machine -s machine ssh cocotte` logs as root into your cloud machine.
* `docker stats` shows you memory and CPU usage for your containers.

## Reference manuals

* [Dockerfile](https://docs.docker.com/engine/reference/builder/)
* [Compose file](https://docs.docker.com/compose/compose-file/)
* [Compose CLI](https://docs.docker.com/compose/reference/overview/)
* [Machine CLI](https://docs.docker.com/machine/reference/)

## What should I learn first?

You should learn about persistence first. This is what changes the most when going from the centralized server model to containerized services. Because containers are meant to be short-lived, persisting the data they create (like inserts in a database) is done differently. When you destroy and recreate a container used as a database service, you are destroying the OS and the database engine, but the database files need to be isolated from that process. The simplest way to do this is with _volumes_. Volumes are to containers what shared folders are to a VM. As your knowledge and usage of containers progress, persisting data will remain one of the most challenging task. This is why you should get a good grasp right from the start, and read the Docker guide about [data management](https://docs.docker.com/storage/).
