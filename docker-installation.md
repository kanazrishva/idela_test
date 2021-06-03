Idela data visualization
------------------------

Installation Guidance
---------------------
**Requirement(s)**
- Docker

**Installation**
---------------
- Build containers: `$ docker-compose build`
- Run the containers: `$ docker-compose up -d`
- Copy environment file: `$ cp .env.docker .env`
- Install composer dependancies: `$ docker-compose exec app composer install`
- Generate application key: `$ docker-compose exec app php artisan key:generate`
- Install npm dependencies: `$ docker-compose exec app npm install`
- Compile assets: `$ docker-compose exec app npm run dev`
- Create symbolic link to storage: `$ docker-compose exec app php artisan storage:link`
- Run migration: `$ docker-compose exec app php artisan migrate` & voila!

***Addiotional***
- Stop the containers: `$ docker-compose down --remove-orphans`
- if you have sql dump of the database & you wish to import it, first list the running containers (`$ docker container ls`) find the container name that has ***db*** in it and copy the name. Run the following command to import replacing {container name} with the copied container name: `$ cat path/to/dump.sql | docker exec -i {container name} mysql -u idela --password=idela idela`
