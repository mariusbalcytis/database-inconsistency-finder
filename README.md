# Database inconsistency finder

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Library that helps to find orphaned records (if they should be removed when nothing points to them),
references to non-existing records and invalid number of references to those records.

## When could this be helpful?

- you don't use foreign keys and could have invalid references. This could happen due to different reasons:
  - you have several different databases, for example when sharding or using microservices;
  - you don't use foreign keys for easier database structure migrations;
  - you have application that just does not use foreign keys;
- you want to find orphaned records. For example, you can have Files in a database table and if nothing
points to this record, we want to delete the file itself.

Normally, these restrictions would be guaranteed by your application. Unfortunately, stuff happens and
there might be some inconsistencies that just occur time to time.

## Installation

```bash
composer require maba/database-inconsistency-finder
```

## Configuration and usage

```php

$connection = DriverManager::getConnection(['url' => 'mysql://user:secret@localhost/mydb']);
$connection1 = DriverManager::getConnection(['url' => 'mysql://user:secret@db.example.org/otherdb']);

$referencesConfiguration = (new ReferencesConfiguration())
   ->setReferencedColumn(
       (new ReferencedColumn())
           ->setConnection($connection)
           ->setTableName('files')
           ->setIdColumnName('id')
           ->setReferenceNumberColumnName('reference_count')
   )
   ->addTableReferences(
       (new TableReferences())
           ->setConnection($connection)
           ->setTableName('profiles')
           ->setColumnNames(['avatar_file_id', 'cv_file_id'])
   )
   ->addTableReferences(
       (new TableReferences())
           ->setConnection($connection1)
           ->setTableName('documents')
           ->setColumnNames(['file_id'])
   )
;

$inconsistencyFinder = (new Factory())
    ->createInconsistencyFinder($referencesConfiguration)
;

$result = $inconsistencyFinder->find();

if ($result->areInconsistenciesFound()) {
    var_dump(
        $result->getOrphanedRecordIds(),
        $result->getMissingReferenceCounts(),
        $result->getInvalidReferenceCounts()
    );
}

```

Currently all work is done synchronously. You can configure this by implementing
`JobDistributorFactoryInterface` and related `JobDistributorInterface`. In this case create service tree yourself,
do not use the `Factory` class.

## Internals

Consistency validation is performed in the following manner:
- ID range is queried from the database (from-to IDs in the main table)
- range is divided into separate intervals for job distribution
- each job is given to concrete worker
- worker validates consistency by using `SUM` query to the database, which is relatively fast
- if inconsistencies are found in the interval, it's split into even smaller intervals
- with each smaller interval `SUM` query is repeated
- for those intervals where inconsistencies are found, inconsistency seeking algorithm is ran

### Inconsistency seeking algorithm

- all IDs and their corresponding reference counts are fetched from the database
- all related tables are iterated over and all IDs are fetched from there
- fetched data is looped to find any inconsistencies

These actions are performed in-memory, so it's essential that interval in this stage would be already quite small.

### Consistency validation

Consistency is validated by issuing `SUM` queries to database. To avoid false positives, we select not the sum
of reference counts, but sum of CRC32 of referenced IDs (and sum them that many times how many times they were 
referenced).

## Semantic versioning

This library follows [semantic versioning](http://semver.org/spec/v2.0.0.html).

See [Symfony BC rules](http://symfony.com/doc/current/contributing/code/bc.html) for basic
information about what can be changed and what not in the API.

## Running tests

```
composer update
cd docker
docker-compose up -d
docker exec -it database_inconsistency_finder_test_php bin/phpunit
docker-compose down
```

## Contributing

Feel free to create issues and give pull requests.

You can fix any code style issues using this command:
```
composer fix-cs
```

[ico-version]: https://img.shields.io/packagist/v/maba/database-inconsistency-finder.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/maba/database-inconsistency-finder/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/maba/database-inconsistency-finder.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/maba/database-inconsistency-finder.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/maba/database-inconsistency-finder.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/maba/database-inconsistency-finder
[link-travis]: https://travis-ci.org/maba/database-inconsistency-finder
[link-scrutinizer]: https://scrutinizer-ci.com/g/maba/database-inconsistency-finder/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/maba/database-inconsistency-finder
[link-downloads]: https://packagist.org/packages/maba/database-inconsistency-finder
