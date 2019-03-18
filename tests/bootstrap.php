<?php

exec('bin/console doctrine:database:create --if-not-exists');
exec('bin/console doctrine:schema:update --force');
exec('bin/console hautelook:fixtures:load -n');

require __DIR__.'/../vendor/autoload.php';
