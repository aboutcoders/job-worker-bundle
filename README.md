# AbcJobWorkerBundle

[![Build Status](https://travis-ci.org/aboutcoders/job-worker-bundle.png?branch=master)](https://travis-ci.org/aboutcoders/job-worker-bundle)

A symfony bundle to process jobs managed by AbcJobServerBundle using [php-enqueue](https://github.com/php-enqueue/enqueue-dev) as transport layer.

**Note: This project is still in experimental!**

## Demo

You can find a demo [here](https://gitlab.com/hasc/abc-job-demo/).

## Installation

```bash
composer require abc/job-worker-bundle
```

## Configuration Reference

```yaml
abc_job_worker:
    server_baseUrl: 'http://domain.tld/job'
```

## Getting Started

### Prerequisites
1. A Symfony application with AbcJobServerBundle installed
2. Enqueue transport is configured matching the configuration of AbcJobServerBundle

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

A route must be configured for every job. A route consist of three parameters: `name` specifies the the name of the job, `queue` specifies the name of the queue the job is sent to, `replyTo` specifies the name of the queue where the reply of a job is sent to.

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

There are two commands to process a job: `abc:process:queue` and `abc:process:job`. 

### Command `abc:process:queue`

The command `abc:process:queue` processes jobs one or more queues. It will process all jobs that have been registered.

```bash
bin/console abc:process:queue --help
```

You can provide a single queue name or an array of queues as argument.

### Command `abc:process:job`

The command `abc:process:job` processes one or more specific jobs, that have to be specified by name. You can provide a single job name or an array of job names as argument.

```bash
bin/console abc:process:job --help
```

### Command `abc:routes:register`

The command `abc:routes:register` registers routes defined by a route provider on the server. Existing routes are overwritten but not deleted.

```bash
bin/console abc:process:job --help
```

## Configuration Reference
   
```yaml
abc_job_worker:
    server_baseUrl: # Required (e.g. http://domain.tld/api/
    default_queue: default
    default_replyTo: reply
```


## License

The MIT License (MIT). Please see [License File](./LICENSE) for more information.
