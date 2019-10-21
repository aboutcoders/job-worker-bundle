# AbcJobWorkerBundle

A symfony bundle to process jobs managed by AbcJobServerBundle using [php-enqueue](https://github.com/php-enqueue/enqueue-dev) as transport layer.

**Note: This project is still in an experimental phase!**

## Installation

```bash
composer require abc/job-worker-bundle
```

## Demo Docker Project

Please take a look at [job-docker-compose](https://gitlab.com/hasc/job-docker-compose) and start a demo application using docker-compose in a couple of minutes.

## Configuration Reference

```yaml
abc_job_worker:
    server_baseUrl: 'http://domain.tld/job'
```

## Getting Started

### Prerequisites
1. A Symfony application with AbcJobServerBundle installed
2. Enqueue transport is configured

### Create a job processor

A job processor must implement the interface `ProcessorInterface`.

```php
interface ProcessorInterface
{
    public function process(?string $input, Context $context);
}
```

### Register a job processor

A job processor must be registered using the tag `abc.job.processor`. You must define the tag attribute `jobName` with it which defines the name of the job that must be processed with this processor.

```yaml
App\Job\SayHelloProcessor:
    tags:
        - { name: 'abc.job.processor', jobName: 'say_hello'}
```

### Configure job routes

A route must be configured for every job. A route consist of three parameters: `name` specifies the the name of the job, `queue` specifies the name of the queue the job is sent to, `replyTo` specifies the name of the queue where the job sends it's replies.

Routes are configured by a class that implements the interface `RouteProviderInterface`.

```php
<?php

namespace App;

use Abc\Job\RouteProviderInterface;

class JobRoutes implements RouteProviderInterface
{
    public static function getRoutes()
    {
        return [
            [
                'name' => 'job_A',
                'queue' => 'queue_A',
                'replyTo' => 'reply_default',
            ],
            [
                'name' => 'job_B',
                'queue' => 'queue_B',
                'replyTo' => 'reply_default',
            ],
            [
                'name' => 'job_C',
                'queue' => 'queue_B',
                'replyTo' => 'reply_C',
            ],
        ];
    }
}
```

A route provider must be registered using the tag `abc.job.route_provider`.

```yaml
App\JobRoutes:
    tags:
        - { name: 'abc.job.route_provider'}
```

### Process jobs

The bundle uses [php-enqueue](https://github.com/php-enqueue/enqueue-dev) as transport layer, thus the worker is started by using the enqueue consume command.

The following command will consume jobs from the default queue `queue_A`.

```bash
bin/console enqueue:transport:consume job queue_A
```

Before consumptions starts the routes the job server is informed about all routes configured in the worker and will route the jobs according to the provided configuration.

## License

The MIT License (MIT). Please see [License File](./LICENSE) for more information.
