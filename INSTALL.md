Installation
============

Requirements
------------
This app assumes you will be running it in Docker.

Upon installation, there will be two Docker containers. "receipts" runs PHP scripts on an Apache server. "receipt-cache" runs a Memcached server to store the receipts. If you want to change the container names, be sure to update the memcached server name in `Util::get_cache()` to match the name of the Memcached container.

The app is accessed at localhost:8080. If this port is not available, you will need to update the ports in the installation scripts or command line.

Installing via Script
--------------------
You can run the included "receipts.sh" script to automatically build and start the two Docker containers.

Installing via Command Line
-------------------
The following is the command used by "receipts.sh" and can be used directly in the terminal instead:

    docker build -f Apache.Dockerfile -t receipts-processor:latest . && docker build -f Memcached.Dockerfile -t receipt-cache:latest . && docker run -d -P --name receipt-cache receipts-cache:latest && docker run -d --name receipts --link receipt-cache:latest -p 8080:80 receipts-processor:latest

Usage
-----
After installing the app, the endpoints specified in the API definition can be reached at http://localhost:8080