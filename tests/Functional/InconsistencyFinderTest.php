<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Tests\Functional;

use Maba\DatabaseInconsistencyFinder\Entity\InconsistenciesResult;
use Maba\DatabaseInconsistencyFinder\Service\Factory;

class InconsistencyFinderTest extends FunctionalTestCase
{
    public function testFind()
    {
        $this->prepareWithValidData();

        $inconsistencyFinder = (new Factory(10, 25))
            ->createInconsistencyFinder($this->referencesConfiguration)
        ;

        $result = $inconsistencyFinder->find();
        $this->assertFalse($result->areInconsistenciesFound());
    }

    public function testSeekForInconsistenciesWithInvalidReference()
    {
        $this->prepareWithInvalidReference();

        $inconsistencyFinder = (new Factory(10, 25))
            ->createInconsistencyFinder($this->referencesConfiguration)
        ;

        $result = $inconsistencyFinder->find();
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

        $inconsistencyFinder = (new Factory(10, 25))
            ->createInconsistencyFinder($this->referencesConfiguration)
        ;

        $result = $inconsistencyFinder->find();
        $this->assertEquals(
            (new InconsistenciesResult())
                ->addInvalidReferenceCount(1, 1),
            $result
        );
    }

    public function testSeekForInconsistenciesWithOrphan()
    {
        $this->prepareWithOrphan();

        $inconsistencyFinder = (new Factory(10, 25))
            ->createInconsistencyFinder($this->referencesConfiguration)
        ;

        $result = $inconsistencyFinder->find();
        $this->assertEquals(
            (new InconsistenciesResult())
                ->addOrphanedRecordId(201),
            $result
        );
    }
}
