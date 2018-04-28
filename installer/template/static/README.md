# How to work with the static site template

The examples assume a static site created with namespace `mysite` and hostname `mysite.mydomain.com`. It is also assumed that Traefik is running locally.

## Develop locally

The local URL is the value of `APP_HOSTS` in `.env-override`. By default Cocotte sets it to the same value as your production URL with the `.local` extension.

1. Edit your `/etc/hosts` file and point your local URL to `127.0.0.1`:
	```
	127.0.0.1 mysite.mydomain.local
	``` 
1. Deploy your site locally:
	```
	./bin/dev
	```
1. The changes you make to your files in the `web` folder are live at `http://mysite.mydomain.local`.
	* Note that the local version of the static site template is not using `https`.

## Deploy to production
1. When you are ready to publish your changes, run:  
	```
	./bin/prod
	```
	* Your changes are deployed to `https://mysite.mydomain.com`.

## The commands

### dev

```
./bin/dev
```
Use this command to:

* Deploy your local site.
* Restart your local site.
* Update your local site when you make changes that are not live automatically.

### logs

Use this command to see the logs of your web server in production:

* Follow log output:
	```
	./bin/logs -f
	```
* Output all the logs:
	```
	./bin/logs -t
	```

### prod

```
./bin/prod
```

Use this command to:

* Deploy your production site.
* Restart your production site.

### reset-dev

```
./bin/reset-dev
```

This command is only useful for debugging. Use this command to:

* Stop your local site and remove it from your local Docker engine. You can put it back with [`./bin/dev`](#dev).

### reset-prod

```
./bin/reset-prod
```

This command is only useful for debugging. Use this command to:

* Stop your production site and remove it from your Digital Ocean Docker engine. You can put it back with [`./bin/prod`](#prod).

## Configuration

### The environment variables in `.env`

This file is automatically used to populate environment at runtime when running [commands](#the-commands).

* `APP_HOSTS`
	* This is the hostname of your application in production. It is used by Traefik to route requests and generate SSL certificates. Be aware that it cannot be simply changed here. Digital Ocean networking would also have to be updated. Therefore consider this value read-only unless you know what you're doing.
* `MACHINE_NAME`
	* This is the name used by Docker Machine for Cocotte. This is a read-only value.
* `MACHINE_STORAGE_PATH`
	* This is the path where your cloud machine credentials are stored by Docker Machine. This is a read-only value.

### The environment variables in `.env-override`

This file is automatically used to override values from `.env` for the local development [commands](#the-commands).

* `APP_HOSTS`
	* This is the hostname of your application in development.
