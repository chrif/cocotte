# Traefik

The examples assume you installed Cocotte with the Traefik UI hostname `traefik.mydomain.com`.

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
	* Your changes are deployed and your Traefik UI is available at `https://traefik.mydomain.com`.

## The commands

### dev

```
./bin/dev
```

Use this command to:

* Deploy Traefik locally.
* Restart Traefik locally.

### logs

Use this command to see the Traefik logs in production:

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

* Deploy Traefik to production.
* Restart Traefik in production.

### reset-dev

```
./bin/reset-dev
```

This command is only useful for debugging. Use this command to:

* Stop your local Traefik and remove it from your local Docker engine. You can put it back with [`./bin/dev`](#dev).

### reset-prod

```
./bin/reset-prod
```

This command is only useful for debugging. Use this command to:

* Stop Traefik in production and remove it from your Digital Ocean Docker engine. You can put it back with [`./bin/prod`](#prod).

### backup

```
./bin/backup
```

_You will probably never need this command, but if your're curious, read on..._

This command is used by `./bin/reset-prod` before removing Traefik from your Digital Ocean Docker Engine. It is a backup of the _Let's Encrypt_ configuration created by Traefik when managing your SSL certificates. They can be restored with the [`./bin/restore`](#restore) command. By default, `./bin/prod` does not even run `./bin/restore`. It simply lets Traefik recreate new certificates. So why back them up? Mainly because _Let's Encrypt_ has [rate limits](https://letsencrypt.org/docs/rate-limits/) for the creation of new certificates/accounts. If for some reason you need to destroy and recreate Traefik in production many times in a short time span, it can be useful to reuse the same certificates. Note that only the `./bin/reset-prod` command destroys your certificates. If you're only running `./bin/prod`, your certificates are always reused. That's why you will probably never need this command.

Also see the [`ACME_ENABLED`](#disabling-acme) environment variable.

### restore

```
./bin/restore
```

_You will probably never need this command._ 

Should you need to redeploy Traefik after removing it completely with `./bin/reset-prod`, you could reuse the same certificates by restoring them prior to running `./bin/prod`. Read why in [`./bin/backup`](#backup).

## Configuration

### The environment variables in `.env`

This file is automatically used to populate environment at runtime when running [commands](#the-commands).

* `APP_HOSTS`
	* This is the hostname of your application in production. It is used by Traefik to expose its web UI. Be aware that it cannot be simply changed here. Digital Ocean networking would also have to be updated. Therefore consider this value read-only unless you know what you're doing.
* `APP_AUTH_BASIC`
	* This is the encoded credentials for securing the Traefik web UI with basic access authentication. You can change these credentials with this command:
		```
		htpasswd -b -n myusername mypassword
		```
		Make sure to put the generated string between single quotes:
		```
		APP_AUTH_BASIC='<generated string>'
		```
* `ACME_EMAIL`
	* This is optional. Let's Encrypt certificates expire every 90 days and it will send you reminders to this email when a certificate is about to expire. This value has to be set when Traefik is creating the Let's Encrypt account, which happens during Cocotte installation. Setting this value after account creation has no effect. Since Traefik automatically renew certificates, this can be left empty. Traefik actually documents it as required, but it is definitely not.
* `MACHINE_NAME`
	* This is the name used by Docker Machine for Cocotte. This is a read-only value.
* `MACHINE_STORAGE_PATH`
	* This is the path where your cloud machine credentials are stored by Docker Machine. This is a read-only value.

### The environment variables in `.env-override`

This file is automatically used to override values from `.env` for the local development [commands](#the-commands).

* `APP_HOSTS`
	* This is the hostname of your application in development.

### Disabling ACME

If you need to repeatedly deploy to production for debugging purposes and are concerned about _Let's Encrypt_ [rate limits](https://letsencrypt.org/docs/rate-limits/), you can temporarily disable ACME (Automated Certificate Management Environment). Add `ACME_ENABLED=false` to the file `.env`, and [deploy](#prod). Your browser will give you a warning that there is no valid SSL certificate.

## Reference

* [Traefik docs](https://docs.traefik.io/)

