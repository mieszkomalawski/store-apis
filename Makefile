test:
	-cd docker2 && make install
	-cd docker2 && make start
	-vendor/bin/phpunit tests