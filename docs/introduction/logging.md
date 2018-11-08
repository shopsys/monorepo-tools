# Logging
Shopsys Framework is a Symfony based application that uses [Monolog](https://github.com/Seldaek/monolog) with [symfony/monolog-bundle](https://github.com/symfony/monolog-bundle) as a logging tool.
Currently, this tool is used to manage the log of cron processes, slow responses, unexpected exceptions and application errors.

## Logging Using Streams
Based on [The Twelve-Factor App](https://12factor.net/logs) principle we decided to use output streams for logging rather than files so debugging and monitoring can be done better and faster.

By default, there are 2 output streams for logging:
- `STDERR` should be used only for error logs
- `STDOUT` should be used any other info and debug data

In the php-fpm container we used a named pipe as a gateway between the application and stdout.
This approach prevents logs from being output to console during console commands.

## How to check the logs
To see the logs from all containers including the Postgres database, web server etc. simply run:
```
docker-compose logs
```
The output will consist of log messages on separate lines, starting with the container name:
```
shopsys-framework-redis | 1:M 23 Jul 11:32:57.000 * Ready to accept connections
shopsys-framework-php-fpm | [2018-07-23 11:39:12] security.INFO: Populated the TokenStorage with an anonymous Token. [] []
shopsys-framework-php-fpm | [2018-07-23 11:42:41] slow.NOTICE: 3.14159265359 Shopsys\ShopBundle\Controller\Front\ProductController::listByCategoryAction /test-category/ [] []
```

*Note: If you're only interested in some specific messages you can use `grep`, eg. `docker-compose logs | grep slow.NOTICE` for slow responses.*

## Conclusion
Logging into streams is one of the prerequisites for scalable application.
With implementation of orchestration tool like [Kubernetes](https://kubernetes.io/) we will be able to store logs in a centralized way.
This will help us find problems faster and also monitor the production environment.
