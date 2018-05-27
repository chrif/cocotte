# Development

## Setup

1. Configure these dist files at the root
	* `.env.dist` 
		* copy to `.env`
		* some values need to be customized
	* `docker-compose.override.yml.dist`
		* copy to `docker-compose.override.yml`
1. Build
	```
	docker-compose build --pull cocotte
	```
1. Install dependencies locally
	```
	docker-compose run --rm cmd composer install
	```

## Useful commands
__Install__
* with `docker-compose.override.yml` (with project mount)
	```
	docker-compose run --rm install
	```
* test with `docker run` (no project mount, like prod)
	```
	./bin/install test
	```
__Uninstall__
* with `docker-compose.override.yml` (with project mount)
	```
	docker-compose run --rm uninstall
	```
* test with `docker run` (no project mount, like prod)
	```
	./bin/uninstall test
	```
__Wizard__
* with `docker-compose.override.yml` (with project mount)
	```
	docker-compose run --rm wizard
	```
* with `docker run` (no project mount, like prod)
	```
	./bin/wizard
	```
__Unit tests__
```
docker-compose run --rm cmd phpunit --testsuite=unit 
```
__Symfony console__
```
docker-compose run --rm cmd console
```
__Shell session__
```
docker-compose run --rm cmd ash
```
__Prune tags__
```
git fetch -p -P
```

## Building the documentation
```
./bin/build-doc
```
## Create a release
1. Unset `ACME_ENABLED=false` and `SYSTEM_TEST_VERBOSITY=16` in Travis repo settings.
2. Tag release
	```
	git checkout master
	git pull
	./bin/release <version>
	```
