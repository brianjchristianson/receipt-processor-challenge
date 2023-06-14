FROM textlab/ubuntu-essential
RUN DEBIAN_FRONTEND=noninteractive apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install memcached

EXPOSE 11211

CMD ["/usr/bin/memcached", "-u", "memcache", "-v"]
