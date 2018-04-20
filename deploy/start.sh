#!/bin/sh

ANSIBLE_OPTS=
#ANSIBLE_OPTS=-vvvv # verbose

ansible ${ANSIBLE_OPTS} -i inventory all -m ping 

ansible-playbook -i inventory playbook.yml
