# How to work with the static site template

Prerequisites:

* You successfully ran the [`install` command](console.md#install).
* You are [running Traefik locally](traefik.md#running-traefik-locally).
* You successfully created a static site with the [`static-site` command](console.md#static-site).
* Your working directory is the root of your static site.

Let's say you created a static site with namespace `mysite` and hostname `mysite.mydomain.com`. You now have a directory on your computer named `mysite`. `cd` to this directory.

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
1. The changes you make to your files in the `web` folder are live at http://mysite.mydomain.local

## Deploy to production
1. When you are ready to publish your changes, run:  
	```
	./bin/prod
	```

## The commands

### ./bin/dev

Use this command to:

* Deploy your local site.
* Restart your local site.
* Update your local site when you make changes that are not live automatically.

### ./bin/logs [options]

Use this command to see the logs of your web server in production:

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

* Deploy your production site.
* Restart your production site.

### ./bin/reset-dev

Use this command to:

* Stop your local site and remove it from your local Docker engine. You can put it back with `./bin/dev`.

### ./bin/reset-prod

Use this command to:

* Stop your production site and remove it from your Digital Ocean Docker engine. You can put it back with `./bin/prod`.

## Reference

* Docker Compose
* Docker
