#!/bin/bash

dir=$(cd `dirname $0` && pwd)

template=`sudo pveam available | grep debian-11-standard | head -1 | awk -F ' +' '{print $2}'`

sudo pveam download local "$template"

id=1010
name=proxmox-test

if sudo pct list | grep $id > /dev/null 2>&1; then
  echo "Container $id $name already exists, Rebooting..."
  cat /vagrant/tests/machines/$name/lxc.conf | sudo tee /etc/pve/lxc/$fullid.conf
  sudo pct reboot $id
else
  echo "Container $id $name does not exist. Creating container..."
  sudo pct create $id "local:vztmpl/$template" --storage local-lvm --hostname $name --ostype debian --memory 512 --cores 1 --onboot 1
  sudo pct start $id
fi
