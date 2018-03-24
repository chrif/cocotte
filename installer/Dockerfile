FROM chrif/docker

 # install docker-machine
RUN curl -L https://github.com/docker/machine/releases/download/v0.13.0/docker-machine-`uname -s`-`uname -m` >/tmp/docker-machine && \
    install /tmp/docker-machine /usr/local/bin/docker-machine && \
    rm -rf /tmp/* /var/cache/apk/*

WORKDIR /root
COPY . ./

ENTRYPOINT ["sh"]
CMD ["create"]

ENV MACHINE_NAME cocotte
ENV MACHINE_STORAGE_PATH /host/machine
