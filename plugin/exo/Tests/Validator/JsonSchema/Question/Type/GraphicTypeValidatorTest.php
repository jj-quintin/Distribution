<?php

namespace UJM\ExoBundle\Tests\Validator\JsonSchema\Question\Type;

use UJM\ExoBundle\Library\Testing\Json\JsonSchemaTestCase;
use UJM\ExoBundle\Validator\JsonSchema\Question\Type\GraphicTypeValidator;

class GraphicTypeValidatorTest extends JsonSchemaTestCase
{
    /**
     * @var GraphicTypeValidator
     */
    private $validator;

    protected function setUp()
    {
        parent::setUp();

        $this->validator = $this->injectJsonSchemaMock(
            new GraphicTypeValidator()
        );
    }

    public function testNoSolutionWithPositiveScoreThrowsError()
    {
    }

    public function testIncoherentIdsInSolutionThrowErrors()
    {

    }
}
