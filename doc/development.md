# Development

## Setup
Configure these dist files at the root
* `.env.dist` 
	* copy to `.env`
	* some values need to be customized
* `docker-compose.override.yml.dist`
	* copy to `docker-compose.override.yml`

## Build
```
docker-compose build --pull cocotte
```

## Useful commands
### Install
* with `docker-compose.override.yml` (with project mount)
	```
	docker-compose run --rm install
	```
* test with `docker run` (no project mount, like prod)
	```
	(cd host; ../bin/install test)
	```
### Uninstall
* with `docker-compose.override.yml` (with project mount)
	```
	docker-compose run --rm uninstall
	```
* test with `docker run` (no project mount, like prod)
	```
	(cd host; ../bin/uninstall test)
	```
## Wizard
* with `docker-compose.override.yml` (with project mount)
	```
	docker-compose run --rm wizard
	```
* with `docker run` (no project mount, like prod)
	```
	(cd host; ../bin/wizard)
	```
### Unit tests
```
docker-compose run --rm cmd phpunit --testsuite=unit 
```
### Symfony console
```
docker-compose run --rm cmd console
```
### Shell session
```
docker-compose run --rm cmd ash
```
## Building the documentation
```
./bin/build-doc
```
## Create a release

```
git checkout master
git pull
git fetch -p -P && git tag <version> && git push --tags
```
