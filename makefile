IP=$(shell ifconfig en0 | grep inet | grep -v inet6 | awk '{ print $$2 }')

all: app/config.json

app/config.json: app/config_in.yml
	./scripts/run_this_when_cfg_changed.rb app/config_in.yml  > app/config.json

S:
	qrcode-terminal http://${IP}:9091
	(cd app && php -S ${IP}:9091)
SL:
	qrcode-terminal http://localhost:9091
	(cd app && php -S localhost:9091)

d:
	(cd deploy/ && ./runit.sh)
s:
	(cd deploy/ && ./sync.sh)
rs:
	rsync -a root@mptm:/var/www/mptm.barvoy.com/ rapp/

lint:
	/bin/ls -1 */*.php | xargs -n 1 php -l

clean:
	rm -rf deploy/*.retry

