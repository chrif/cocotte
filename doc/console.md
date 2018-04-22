Console API Reference
=====================

* [`install`](#install)
  > Create a Docker machine on Digital Ocean and install the Traefik reverse proxy on it.
* [`static-site`](#static-site)
  > Create a static website and deploy it to your Docker Machine.
* [`uninstall`](#uninstall)
  > Destroy the Docker machine on Digital Ocean and remove the Traefik subdomain.
* [`wizard`](#wizard)
  > Interactively build a simple 'install' command for Cocotte.

`install`
---------

### Usage

* `docker run -it --rm chrif/cocotte install [options]`

Create a Docker machine on Digital Ocean and install the Traefik reverse proxy on it.

Example:
```
  docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte install \
    --digital-ocean-api-token="xxxx" \
    --traefik-ui-hostname="traefik.mydomain.com" \
    --traefik-ui-password="password" \
    --traefik-ui-username="username";
```
This command requires 2 volumes:
  * "$(pwd)":/host
  * /var/run/docker.sock:/var/run/docker.sock:ro


### Options

#### `--digital-ocean-api-token`

##### Digital Ocean API Token
If you don't have a Digital Ocean account yet, get one with a 10$ credit at
https://digitalocean.com/?refcode=c25ed78e51c5 🔗
Then generate a token at https://cloud.digitalocean.com/settings/api/tokens 🔗
Cocotte will make a call to Digital Ocean's API to validate the token.


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `NULL`

#### `--machine-name`

##### Machine Name
This is both the name used for docker-machine commands and by Digital Ocean
for the droplet name. Must match /^[a-zA-Z0-9][a-zA-Z0-9\-\.]*$/


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `'cocotte'`

#### `--traefik-ui-hostname`

##### Traefik UI hostname
This the fully qualified domain name for your Traefik UI.
It has to be with a subdomain like in 'traefik.mydomain.com', in which case
'mydomain.com' must point to the name servers of Digital Ocean, and Cocotte
will create and configure the 'traefik' subdomain for you.
Cocotte validates that the name servers of the domain you enter are Digital
Ocean's. How to point to Digital Ocean name servers: https://goo.gl/SJnw2c 🔗
Please note that when a domain is newly registered, or the name servers are
changed, you can expect a propagation time up to 24 hours.


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `NULL`

#### `--traefik-ui-username`

##### Traefik UI username
Alphanumeric characters. Must match /^[a-zA-Z0-9]+$/


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `NULL`

#### `--traefik-ui-password`

##### Traefik UI password
Alphanumeric and some special characters. Must match /^[a-zA-Z0-9_@#%?&*+=!-]+$/


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `NULL`

`static-site`
-------------

### Usage

* `docker run -it --rm chrif/cocotte static-site [options]`

Create a static website and deploy it to your Docker Machine.

Example:
```
  docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte static-site \
    --digital-ocean-api-token="xxxx" \
    --namespace="static-site" \
    --hostname="static-site.mydomain.com";
```
This command requires 2 volumes:
  * "$(pwd)":/host
  * /var/run/docker.sock:/var/run/docker.sock:ro


### Options

#### `--namespace`

##### Static site namespace
Allowed characters are lowercase letters, digits and -. Must match /^[a-z0-9-]+$/


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `NULL`

#### `--hostname`

##### Static site hostname
This the fully qualified domain name for your website.
It has to be with a subdomain like in 'mywebsite.mydomain.com', in which case
'mydomain.com' must point to the name servers of Digital Ocean, and Cocotte
will create and configure the 'mywebsite' subdomain for you.
Cocotte validates that the name servers of the domain you enter are Digital
Ocean's. How to point to Digital Ocean name servers: https://goo.gl/SJnw2c 🔗
Please note that when a domain is newly registered, or the name servers are
changed, you can expect a propagation time up to 24 hours.


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `NULL`

#### `--digital-ocean-api-token`

##### Digital Ocean API Token
If you don't have a Digital Ocean account yet, get one with a 10$ credit at
https://digitalocean.com/?refcode=c25ed78e51c5 🔗
Then generate a token at https://cloud.digitalocean.com/settings/api/tokens 🔗
Cocotte will make a call to Digital Ocean's API to validate the token.


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `NULL`

#### `--machine-name`

##### Machine Name
This is both the name used for docker-machine commands and by Digital Ocean
for the droplet name. Must match /^[a-zA-Z0-9][a-zA-Z0-9\-\.]*$/


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `'cocotte'`

#### `--skip-networking`

Do not configure networking. Cannot be true if skip-deploy is true.

* Accept value: no
* Is value required: no
* Is multiple: no
* Default: `false`

#### `--skip-deploy`

Do not deploy to prod after creation.

* Accept value: no
* Is value required: no
* Is multiple: no
* Default: `false`

`uninstall`
-----------

### Usage

* `docker run -it --rm chrif/cocotte uninstall [options]`

Destroy the Docker machine on Digital Ocean and remove the Traefik subdomain.

Example:
```
  docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte uninstall \
    --digital-ocean-api-token="xxxx" \
    --traefik-ui-hostname="traefik.mydomain.com";
```
This command requires 2 volumes:
  * "$(pwd)":/host
  * /var/run/docker.sock:/var/run/docker.sock:ro


### Options

#### `--digital-ocean-api-token`

##### Digital Ocean API Token
If you don't have a Digital Ocean account yet, get one with a 10$ credit at
https://digitalocean.com/?refcode=c25ed78e51c5 🔗
Then generate a token at https://cloud.digitalocean.com/settings/api/tokens 🔗
Cocotte will make a call to Digital Ocean's API to validate the token.


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `NULL`

#### `--machine-name`

##### Machine Name
This is both the name used for docker-machine commands and by Digital Ocean
for the droplet name. Must match /^[a-zA-Z0-9][a-zA-Z0-9\-\.]*$/


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `'cocotte'`

#### `--traefik-ui-hostname`

##### Traefik UI hostname
This the fully qualified domain name for your Traefik UI.
It has to be with a subdomain like in 'traefik.mydomain.com', in which case
'mydomain.com' must point to the name servers of Digital Ocean, and Cocotte
will create and configure the 'traefik' subdomain for you.
Cocotte validates that the name servers of the domain you enter are Digital
Ocean's. How to point to Digital Ocean name servers: https://goo.gl/SJnw2c 🔗
Please note that when a domain is newly registered, or the name servers are
changed, you can expect a propagation time up to 24 hours.


* Accept value: yes
* Is value required: yes
* Is multiple: no
* Default: `NULL`

`wizard`
--------

### Usage

* `docker run -it --rm chrif/cocotte wizard`

Interactively build a simple 'install' command for Cocotte.

Example:
```
  docker run -it --rm chrif/cocotte wizard
```

