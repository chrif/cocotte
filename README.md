<p style="margin:0;padding:0" id="cocotte" align="center">
<img width="300" height="263" src="docs/images/logo.png" alt="Cocotte" title="Cocotte" />
</p>

[![Build Status](https://badge.chrif.org/cocotte/travis)](https://travis-ci.org/chrif/cocotte) 
[![Code Coverage](https://badge.chrif.org/cocotte/codecov)](https://codecov.io/gh/chrif/cocotte)
[![Maintainability](https://badge.chrif.org/cocotte/codeclimate)](https://codeclimate.com/github/chrif/cocotte/maintainability)
[![Scrutinizer Code Quality](https://badge.chrif.org/cocotte/scrutinizer)](https://scrutinizer-ci.com/g/chrif/cocotte/?branch=master)
[![PayPal](docs/images/paypal-badge.svg)](https://www.paypal.me/Fecteau)
[![Join the chat at https://slack.cocotte.rocks](https://badge.chrif.org/cocotte/slack)](https://slack.cocotte.rocks)
[![Twitter](https://badge.chrif.org/cocotte/twitter)](https://twitter.com/intent/follow?screen_name=CocotteRocks)

Cocotte aims at easing the learning curve for web developers starting with cloud hosting and application containerization. You get a basic and [affordable](#pricing) installation, allowing you to focus on your project(s) first, and learn about your infrastructure later:

* [Docker](https://www.docker.com/) containers for all processes.
* [Digital Ocean](https://www.digitalocean.com/) as the cloud provider.
* [Traefik](https://traefik.io/) as the reverse proxy for hostname routing and SSL certificates automation.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
## Table of Contents

- [In pseudo-code](#in-pseudo-code)
- [In details](#in-details)
- [Pricing](#pricing)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Reference](#reference)
- [Contributing](#contributing)
- [Support](#support)
- [Feedback](#feedback)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## In pseudo-code

```
$ cocotte install
$ cocotte add --name="my-symfony-app" --host="app1.mydomain.com"
$ cocotte add --name="my-static-website" --host="app2.mydomain.com"
$ cocotte add --name="my-django-app" --host="app3.mydomain.com"
$ cocotte add --name="my-nodejs-app" --host="app4.mydomain.com"

$ curl https://app1.mydomain.com
Served by Nginx
$ curl https://app2.mydomain.com
Served by a different Nginx
$ curl https://app3.mydomain.com
Served by Apache HTTP Server
$ curl https://app4.mydomain.com
Served by Node.js 
```

These applications/websites are:
* all on the same virtual machine in the cloud
* all listening to the same ports
* each using a valid SSL certificate
* each running on a different operating system

The cost for all this is only 5$ a month, assuming the cheapest cloud machine, and that all these apps can share 1GB memory and 20GB disk, which they can, thanks to application containerization.

## In details

When learning about cloud hosting and containers, one has let's say 3 options:
* Read the manuals which is the best but can be time-consuming.
* Read blog posts/tutorials that are often outdated or incomplete.
* Use a third-party management solution where you end up not learning much and potentially locked in.

Meet Cocotte, a 4th option.

Cocotte is a free cloud installer for hosting multiple containerized applications, each accessible through https with a different hostname, but all hosted on one cloud machine at 5$ a month. This is the only cost for the infrastructure that Cocotte puts in place.

As opposed to a solution that would lock you in for further maintenance, Cocotte is just a one-time installer. Once the cloud machine is provisioned, you don't need Cocotte anymore. So you'll need to read the manuals eventually, but when you do, you'll have full control over every aspect of your installation.

Optionally, and in the hope to get you started even faster, Cocotte has a template feature for adding new Docker applications to your infrastructure. It generates deployment scripts for a https web server, in a folder ready for source control. The only template available right now is a static website with Nginx but more templates are coming soon. The intended way of developing and deploying with these application models is [documented](docs/templates.md).

Cocotte is fully tested on [Travis CI](https://travis-ci.org/chrif/cocotte), so there is no surprises with tutorials that used to work when they were written but are now outdated. The build itself is the tutorial, and if it passes, you can be confident that Cocotte works.

## Pricing

Trying out Cocotte is completely free if you don't have a Digital Ocean account and create one with [this link](https://m.do.co/c/c25ed78e51c5) which gives you a 10$ credit (2 months of hosting).

If you already have a Digital Ocean account, then you probably know about cloud pricing. For those who don't, you are charged $0.007/hour by Digital Ocean for the machine that Cocotte creates. So just testing Cocotte and then destroying the machine costs less than 1 cent. Keeping the machine online for a month costs 5$. 

[Read about Digital Ocean pricing](https://www.digitalocean.com/pricing/).

## Requirements

* A Mac or Linux operating system.
* Docker Community Edition (including Docker Compose and Docker Machine).
* A Digital Ocean account.
* A domain name using the name servers of Digital Ocean.

## Installation

* Install [Docker Community Edition](https://www.docker.com/community-edition) (including Docker Compose and Docker Machine).
* If you don't have a Digital Ocean account, create one with [this link](https://m.do.co/c/c25ed78e51c5) and you will get a 10$ credit, allowing you to try Cocotte for free.
* Generate a [Digital Ocean API token](https://cloud.digitalocean.com/settings/api/tokens) with write permissions.
* Make sur you have a domain whose name servers are set to:
	 * ns1.digitalocean.com
	 * ns2.digitalocean.com
	 * ns3.digitalocean.com

## Usage

* Run the [`wizard`](installer/docs/console.md#wizard).
	```
	$ docker run -it --rm chrif/cocotte wizard
	```
* Display help for the [`install`](installer/docs/console.md#install) command.
	```
	$ docker run -it --rm chrif/cocotte install --help
	```
* [More usages](installer/docs/console.md)

## Reference

* [Console API Reference](installer/docs/console.md)
* [Developing and deploying applications](docs/templates.md)
* [The `machine` directory](docs/machine.md)
* [Frequently asked questions](docs/faq.md)

## Contributing

Pull requests are welcome. Take a look at the [development documentation](docs/development.md).

## Support

* Join the [Cocotte Slack channel](https://slack.cocotte.rocks).
* Use [Stack Overflow](https://stackoverflow.com/questions/tagged/cocotte) (using the `cocotte` tag).
* [Open an issue on GitHub](https://github.com/chrif/cocotte/issues).
* [Send an email](mailto:cocotte.rocks@gmail.com).

## Feedback

Thank you for trying Cocotte! If you have bugs to report or improvements to suggest, [open an issue on GitHub](https://github.com/chrif/cocotte/issues).
