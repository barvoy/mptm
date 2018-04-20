#!/bin/sh

ANSIBLE_OPTS=$*

echo "# running with $ANSIBLE_OPTS"

ansible-playbook -i inventory $ANSIBLE_OPTS sync.yml
