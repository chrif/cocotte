# Docker Machine

Cocotte uses Docker Machine to provision the Digital Ocean droplet and to deploy to it through SSH. 

__Machine storage path__

In order to do so, security credentials are stored on your computer when the droplet is created, and reused later to connect to it. The location of these credentials is called the _machine storage path_. The default storage path used by Docker Machine when no one is specified is something like `~/.docker/machine`. Cocotte instead uses a custom path.

__Machine name__

The machine Cocotte creates has a name which defaults to `cocotte` and which is also the name given to your Digital Ocean droplet.

## The `machine` directory and the storage path

In order to make Cocotte as reliable as possible, a custom storage path named `machine` is used relative to where you run Cocotte. This results in your Digital Ocean droplet being the only machine found by Docker Machine in this path. This also results in the need to specify this path for every `docker-machine` commands used by Cocotte like so:

```
docker-machine --storage-path=machine ls
```

### How to not specify the path for every `docker-machine` commands

At the root of every application Cocotte creates is a file named `.env` and it contains an environment variable named `MACHINE_STORAGE_PATH`. Simply export it to your shell. Then your `docker-machine` commands will look like:

```
docker-machine ls
```

It works because `MACHINE_STORAGE_PATH` is actually used by Docker Machine itself as a way to override the default machine storage path globally.

