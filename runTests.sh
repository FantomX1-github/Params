#!/usr/bin/env bash

set -e
# set -x

sh runCodeSniffer.sh

php vendor/bin/phpunit -c test/phpunit.xml

php ./phpstan.phar analyze -c ./phpstan.neon -l 7 lib

set +e

php vendor/bin/infection --configuration=infection.json.dist --log-verbosity=0 --only-covered --min-covered-msi=90

infection_exit_code=$?

set -e

if [ "$infection_exit_code" -ne "0" ]; then echo "Infection failed"; cat infection-log.txt;  exit "$infection_exit_code"; fi


examples=()
examples+=('lib/ParamsExample/1_basic_usage_acceptable_input.php')
examples+=('lib/ParamsExample/2_basic_usage_bad_input.php')
examples+=('lib/ParamsExample/3_errors_returned_acceptable_input.php')
examples+=('lib/ParamsExample/4_errors_returned_bad_input.php')
examples+=('lib/ParamsExample/5_other_validator.php')
examples+=('lib/ParamsExample/6_open_api_descriptions.php')


for example in "${examples[@]}"
do
   :
   php $example
   example_exit_code=$?

   if [ "$example_exit_code" -ne "0" ]; then echo "Example [] failed";  exit "$example_exit_code"; fi
done


echo "Tests completed without problem"

# rerun unit tests to get the stats again, to save scrolling...
php vendor/bin/phpunit -c test/phpunit.xml
