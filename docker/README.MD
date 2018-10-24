To install infosys into a docker-container, run the following commands:

  docker build -t infosys .
  docker run --name infosys -v $(realpath $(pwd)/../):/var/www/infosys -v $(pwd)/infosys-fpm.conf:/etc/php/5.6/fpm/pool.d/infosys-fpm.conf -v $(pwd)/infosys-site.conf:/etc/nginx/sites-enabled/infosys-site.conf -v $(pwd)/config.ini:/var/www/infosys/include/config.ini -p 127.0.0.1:8080:80 -d infosys
