echo 'TAD Development Platform' ;

while true; do
  echo 'List of actions';
  echo '[1] Start application';
  echo '[2] Start application w/ database refresh';
  echo '[3] Seed fake data';
  echo '[4] Get into workspace';
  echo 'E[x]it';
  read -p 'Option: ' option
  case $option in
    1 )
      docker-compose down && docker-compose up -d
      docker-compose exec workspace bash -c "php composer.phar update"
      docker-compose exec workspace bash -c "php artisan migrate"
      break;;
    2 )
      docker-compose down -v && docker-compose up -d
      docker-compose exec workspace bash -c "php composer.phar update"
      docker-compose exec workspace bash -c "php artisan migrate"
      break;;
    3 )
      docker-compose exec workspace bash -c "php artisan db:seed --class='GeneralDatabaseSeeder'"
      docker-compose exec workspace bash -c "php artisan db:seed --class='FakeDatabaseSeeder'"
      break;;
    4 )
      docker-compose exec workspace bash
      break;;
    [Xx] )
      exit;;
  esac
done
