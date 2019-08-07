<?php
declare(strict_types=1);

namespace Maba\DatabaseInconsistencyFinder\Tests\Functional\Service;

use Maba\DatabaseInconsistencyFinder\Database\QueryExecutor;
use Maba\DatabaseInconsistencyFinder\Entity\Interval;
use Maba\DatabaseInconsistencyFinder\Service\FastConsistencyValidator;
use Maba\DatabaseInconsistencyFinder\Tests\Functional\FunctionalTestCase;

class FastConsistencyValidatorTest extends FunctionalTestCase
{
    /**
     * @var FastConsistencyValidator
     */
    private $fastConsistencyValidator;

    public function setUp()
    {
        parent::setUp();

        $this->fastConsistencyValidator = new FastConsistencyValidator(
            $this->referencesConfiguration,
            new QueryExecutor()
        );
    }

    public function testValidateConsistency()
    {
        $this->prepareWithValidData();

        $result = $this->fastConsistencyValidator->validateConsistency(
            new Interval()
        );
        $this->assertTrue($result);

        $result = $this->fastConsistencyValidator->validateConsistency(
            (new Interval())->setFrom(19)->setUntil(78)
        );
        $this->assertTrue($result);
    }

    public function testValidateConsistencyWithInvalidReference()
    {
        $this->prepareWithInvalidReference();

        $result = $this->fastConsistencyValidator->validateConsistency(
            new Interval()
        );
        $this->assertFalse($result);
    }

    public function testValidateConsistencyWithInvalidReferenceCount()
    {
        $this->prepareWithInvalidReferenceCount();

        $result = $this->fastConsistencyValidator->validateConsistency(
            new Interval()
        );
        $this->assertFalse($result);
    }

    public function testValidateConsistencyWithOrphan()
    {
        $this->prepareWithOrphan();

        $result = $this->fastConsistencyValidator->validateConsistency(
            new Interval()
        );
        $this->assertFalse($result);
    }
}
