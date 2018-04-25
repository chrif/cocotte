# How to work with Traefik 

## Prerequisites

* You successfully ran the [`install` command](console.md#install).
* Your working directory is the root of your traefik installation.

When running the install command, Cocotte creates a directory named `traefik`. You can commit it to version control. This is yours to modify if and when necessary.

Let's say you installed Cocotte with the Traefik UI hostname `traefik.mydomain.com`.

## Develop locally

The local URL is the value of `APP_HOSTS` in `.env-override`. By default Cocotte sets it to the same value as your production URL with the `.local` extension. 

1. Edit your `/etc/hosts` file and point your local URL to `127.0.0.1`:
	```
	127.0.0.1 traefik.mydomain.local
	``` 
1. Deploy traefik locally:
	```
	./bin/dev
	```
	* You can visit your local Traefik UI at `http://traefik.mydomain.local`.
	* Note that the local version of Traefik that Cocotte puts in place is not using `https`.

## Deploy to production
1. When you are ready to publish your changes, run:  
	```
	./bin/prod
	```

## The commands

### ./bin/dev

Use this command to:

* Deploy Traefik locally.
* Restart Traefik.

### ./bin/logs [options]

Use this command to see the Traefik logs in production:

* Follow log output:
	```
	./bin/logs -f
	```
* Output all the logs:
	```
	./bin/logs -t
	```

### ./bin/prod

Use this command to:

* Deploy Traefik to production.
* Restart Traefik in production.

### ./bin/reset-dev

This command is only useful for debugging. Use this command to:

* Stop your local Traefik and remove it from your local Docker engine. You can put it back with [`./bin/dev`](#bindev).

### ./bin/reset-prod

This command is only useful for debugging. Use this command to:

* Stop Traefik in production and remove it from your Digital Ocean Docker engine. You can put it back with [`./bin/prod`](#binprod).

### ./bin/backup

_You will probably never need this command, but if your're curious, read on..._

This command is used by `./bin/reset-prod` before removing Traefik from your Digital Ocean Docker Engine. It is a backup of the _Let's Encrypt_ configuration created by Traefik when managing your SSL certificates. They can be restored with the [`./bin/restore`](#binrestore) command. By default, `./bin/prod` does not even run `./bin/restore`. It simply lets Traefik recreate new certificates. So why back them up? Mainly because _Let's Encrypt_ has [rate limits](https://letsencrypt.org/docs/rate-limits/) for the creation of new certificates/accounts. If for some reason you need to destroy and recreate Traefik in production many times in a short time span, it can be useful to reuse the same certificates. Note that only the `./bin/reset-prod` command destroys your certificates. If you're only running `./bin/prod`, your certificates are always reused. That's why you will probably never need this command.

Also see the [`ACME_ENABLED`](#acme_enabled) environment variable.

### ./bin/restore

_You will probably never need this command._ 

Should you need to redeploy Traefik after removing it completely with `./bin/reset-prod`, you could reuse the same certificates by restoring them prior to running `./bin/prod`. Read why in [`./bin/backup`](#binbackup).

## The environment variables in .env

