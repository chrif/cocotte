# Development

## Build the installer

1. Configure these dist files with 
	* .env.dist
	* docker-compose.override.yml.dist
	* installer/template/traefik/docker-compose.override.yml.dist
	* installer/template/traefik/.env-override.dist
	* installer/template/traefik/.env.dist
1. Build installer
	```
	docker-compose build installer
	```

## Run the installer
### Create machine and deploy traefik
* with `docker-compose.override.yml` (with bind mount)
	```
	docker-compose run --rm installer
	```
* with `docker run` (no mount, like prod)
	```
	(cd host; ../bin/installer test)
	```
### Remove machine and networking for traefik
* with `docker-compose.override.yml` (with bind mount)
	```
	docker-compose run --rm installer remove
	```
* with `docker run` (no mount, like prod)
	```
	(cd host; ../bin/installer test remove)
	```
### Run PHP test suite
```
docker-compose run --rm installer test
```
### Symfony console
```
docker-compose run --rm installer console
```
### Shell session
```
docker-compose run --rm installer ash
```
