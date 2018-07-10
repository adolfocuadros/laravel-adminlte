#!/bin/sh

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.

sudo rm -rf /usr/lib/php/20170718/zray.so
sudo rm -rf /etc/php/7.2/fpm/conf.d/zray.ini