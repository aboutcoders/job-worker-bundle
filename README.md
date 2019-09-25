# AbcWorkerBundle

A symfony bundle to process managed by AbcJobBundle using [php-enqueue](https://github.com/php-enqueue/enqueue-dev) as transport layer.

We're working on the first stable release. Please excuse the poor documentation. We're also looking for contributors that support the project.

## Demo Docker Project

Please take a look at [job-docker-compose](https://gitlab.com/hasc/job-docker-compose) and start a demo application using docker-compose in a couple of minutes.

## Getting Started

### Prerequisites
1. Configure a Symfony application with AbcJobBundle
2. Configure the enqueue transport layer corresponding to the configuration in AbcJobBundle

### Create a job processor

A job processor must implement the interface `ProcessorInterface`.

```php
interface ProcessorInterface
{
    public function process(?string $input, Context $context);
}
```

### Register a job processor

There are two options to register a job processor.

#### Register a job processor using a tag

You can register a job processor using the tag `abc.job.processor`. You must define the tag attribute `jobName` with it which defines the name of the job that must be processed with this processor.

```yaml
App\Job\SayHelloProcessor:
	tags:
		- { name: 'abc.job.processor', jobName: 'say_hello'}
```

#### Register a job subscriber processor

There is also a `JobSubscriberInterface` to bind a processor to a job name.

```php
namespace App\Job;

class GenericProcessor implements ProcessorInterface, JobSubscriberInterface
    {
        public static function getSubscribedJob()
        {
            return ['say_hello', 'say_goodbye'];
        }
    }
```

Tag the service in the container with `abc.job_subscriber` tag:

```yaml
services:
  App\Job\GenericProcessor:
    tags:
        - { name: 'abc.job.subscriber' }
```

### Process jobs

The bundle uses [php-enqueue](https://github.com/php-enqueue/enqueue-dev) as transport layer, thus the worker is started by using the enqueue consume command.

The following command will consume jobs from the default queue `abc.job`.

```bash
bin/console enqueue:transport:consume job abc.job
```

## ToDo
* CompilePass for JobSubscriberInterface
* How to limit jobs
* How to consume from multiple queues (just test it)
