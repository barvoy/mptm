all: config.js

config.js: config_in.yml
	./run_this_when_cfg_changed.rb > config.js

s:
	php -S localhost:9091
