# Development

## Build the installer

1. Configure these dist files with 
	* .env.dist
	* docker-compose.override.yml.dist
1. Build Cocotte
	```
	docker-compose build cocotte
	```

## Running the installer
### Create machine and deploy traefik
* with `docker-compose.override.yml` (with project mount)
	```
	docker-compose run --rm install
	```
* test with `docker run` (no project mount, like prod)
	```
	(cd host; ../bin/install test)
	```
### Remove machine and networking for traefik
* with `docker-compose.override.yml` (with project mount)
	```
	docker-compose run --rm uninstall
	```
* test with `docker run` (no project mount, like prod)
	```
	(cd host; ../bin/uninstall test)
	```
### Run unit tests
```
docker-compose run --rm cmd phpunit --exclude-group=functional
```
### Symfony console
```
docker-compose run --rm cmd console
```
### Shell session
```
docker-compose run --rm cmd ash
```
## Running the wizard
* with `docker-compose.override.yml` (with project mount)
	```
	docker-compose run --rm wizard
	```
* with `docker run` (no project mount, like prod)
	```
	(cd host; ../bin/wizard)
	```
## Building the console documentation
```
docker-compose run --rm bare console doc > doc/console.md
```
