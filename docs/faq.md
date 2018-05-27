# Frequently asked questions

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [Why is there no cluster with an orchestrator like Swarm or Kubernetes?](#why-is-there-no-cluster-with-an-orchestrator-like-swarm-or-kubernetes)
- [Why not pushing images to a Docker Registry and pulling them in production?](#why-not-pushing-images-to-a-docker-registry-and-pulling-them-in-production)
- [Command fails with an error message beginning with `Failed to validate name servers`](#command-fails-with-an-error-message-beginning-with-failed-to-validate-name-servers)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Why is there no cluster with an orchestrator like Swarm or Kubernetes?

This is left to a future, alternate version of Cocotte for users who are not just beginning to learn about containers and cloud hosting and who are also willing to spend more money for their infrastructure. It would use Kubernetes. 

An orchestrator is designed for high-availability clusters and Cocotte is meant to be as cheap as possible a solution to get started with container/cloud *development*, not for scaling high traffic applications.

Therefore, the cheapest solution is *one* instance of the smallest VM, and a great way to leverage and start learning with such a setup is to simply deploy with `docker-compose up` in a `docker-machine env` shell session.

## Why not pushing images to a Docker Registry and pulling them in production?

This is a continuation of the previous answer about an orchestrator...

Because Cocotte does not setup a cluster, the need for an image registry is not as important. With Cocotte, images are intended for only one remote Docker engine, and they might contain closed source code: the free Docker Hub plan would expose that code. 

We could host our own Docker Registry on the same VM as the only Docker Engine pulling them, but it would also add the overhead of installing and securing the registry, as opposed to a `docker-machine env` shell session which requires no installation and is already secure.

There are third-parties offering free Docker Registry hosting, but their business model is most likely to have a free version just annoying enough so that people end-up paying.

For all these reasons, the introduction of an image registry is left to a more advanced version of Cocotte.

## Command fails with an error message beginning with `Failed to validate name servers`

Cocotte uses a third-party library to validate that the name servers of your domain are Digital Ocean's. If you are confident that your domain is valid but the library fails to validate your domain, you can disable DNS validation with the `--skip-dns-validation` option.
