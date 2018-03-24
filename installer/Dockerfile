FROM ubuntu:trusty

RUN sudo apt-get update
RUN sudo apt-get install -y curl

 # install docker-machine
RUN curl -L https://github.com/docker/machine/releases/download/v0.13.0/docker-machine-`uname -s`-`uname -m` >/tmp/docker-machine
RUN sudo install /tmp/docker-machine /usr/local/bin/docker-machine

WORKDIR /root
COPY . ./

ENTRYPOINT ["sh"]
CMD ["create"]

ENV MACHINE_NAME cocotte
ENV MACHINE_STORAGE_PATH /host/machine
