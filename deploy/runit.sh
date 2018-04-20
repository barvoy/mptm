#!/bin/sh

ANSIBLE_OPTS=$*

echo "# running with $ANSIBLE_OPTS"

ansible ${ANSIBLE_OPTS} -i inventory all -m ping 

ansible-playbook -i inventory $ANSIBLE_OPTS playbook_main.yml
