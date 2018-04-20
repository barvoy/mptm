all: app/config.js

app/config.js: app/config_in.yml
	./scripts/run_this_when_cfg_changed.rb app/config_in.yml  > app/config.js

s:
	(cd app && php -S localhost:9091)
d:
	(cd deploy/ && ./runit.sh)
s:
	(cd deploy/ && ./sync.sh)
rs:
	rsync -a root@mptm:/var/www/mptm.barvoy.com/ rapp/

clean:
	rm -rf deploy/*.retry

