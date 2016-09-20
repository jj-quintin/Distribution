<?php

namespace UJM\ExoBundle\Tests\Validator\JsonSchema\Question\Type;

use UJM\ExoBundle\Library\Testing\Json\JsonSchemaTestCase;
use UJM\ExoBundle\Validator\JsonSchema\Question\Type\ClozeTypeValidator;

class ClozeTypeValidatorTest extends JsonSchemaTestCase
{
    /**
     * @var ClozeTypeValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();

        $this->validator = $this->injectJsonSchemaMock(
            new ClozeTypeValidator()
        );
    }

    public function testNoSolutionWithPositiveScoreThrowsError()
    {
    }

    public function testIncoherentIdsInSolutionThrowErrors()
    {

    }
}
