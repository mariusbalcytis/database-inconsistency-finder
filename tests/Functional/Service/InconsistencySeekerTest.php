<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Tests\Functional\Service;

use Maba\DatabaseInconsistencyFinder\Database\QueryExecutor;
use Maba\DatabaseInconsistencyFinder\Entity\InconsistenciesResult;
use Maba\DatabaseInconsistencyFinder\Entity\Interval;
use Maba\DatabaseInconsistencyFinder\Service\InconsistencySeeker;
use Maba\DatabaseInconsistencyFinder\Tests\Functional\FunctionalTestCase;

class InconsistencySeekerTest extends FunctionalTestCase
{
    /**
     * @var InconsistencySeeker
     */
    private $inconsistencySeeker;

    public function setUp()
    {
        parent::setUp();

        $this->inconsistencySeeker = new InconsistencySeeker(
            $this->referencesConfiguration,
            new QueryExecutor()
        );
    }

    public function testSeekForInconsistencies()
    {
        $this->prepareWithValidData();

        $result = $this->inconsistencySeeker->seekForInconsistencies(
            new Interval()
        );
        $this->assertFalse($result->areInconsistenciesFound());

        $result = $this->inconsistencySeeker->seekForInconsistencies(
            (new Interval())->setFrom(19)->setUntil(78)
        );
        $this->assertFalse($result->areInconsistenciesFound());
    }

    public function testSeekForInconsistenciesWithInvalidReference()
    {
        $this->prepareWithInvalidReference();

        $result = $this->inconsistencySeeker->seekForInconsistencies(
            new Interval()
        );
        $this->assertEquals(
            (new InconsistenciesResult())
                ->addMissingReferenceCount(301, 1)
                ->addMissingReferenceCount(302, 1),
            $result
        );
    }

    public function testSeekForInconsistenciesWithInvalidReferenceCount()
    {
        $this->prepareWithInvalidReferenceCount();

        $result = $this->inconsistencySeeker->seekForInconsistencies(
            new Interval()
        );
        $this->assertEquals(
            (new InconsistenciesResult())
                ->addInvalidReferenceCount(1, 1),
            $result
        );
    }

    public function testSeekForInconsistenciesWithOrphan()
    {
        $this->prepareWithOrphan();

        $result = $this->inconsistencySeeker->seekForInconsistencies(
            new Interval()
        );
        $this->assertEquals(
            (new InconsistenciesResult())
                ->addOrphanedRecordId(201),
            $result
        );
    }
}
