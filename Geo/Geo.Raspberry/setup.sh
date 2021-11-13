#!/bin/bash

# This script must be run once to configure the raspberry to use all sensors required to run this project

tmp=$(mktemp)

cat /boot/config.txt | grep -v "dtoverlay=w1-gpio" > "$tmp"

sudo sed -i '4 i dtoverlay=w1-gpio' "$tmp"

sudo mv "$tmp" /boot/config.txt

sudo apt install python3-pip
sudo pip3 install w1thermsensor
sudo pip3 install requests

sudo modprobe w1-gpio
sudo modprobe w1-therm

