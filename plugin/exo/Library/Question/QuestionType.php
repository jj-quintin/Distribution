<?php

namespace UJM\ExoBundle\Library\Question;

use UJM\ExoBundle\Entity\InteractionGraphic;
use UJM\ExoBundle\Entity\InteractionHole;
use UJM\ExoBundle\Entity\InteractionMatching;
use UJM\ExoBundle\Entity\InteractionOpen;
use UJM\ExoBundle\Entity\InteractionQCM;

/**
 * References the types of Question managed by the bundle.
 */
final class QuestionType
{
    /**
     * The user have to choose one (or many) proposition(s) in a set.
     *
     * @var string
     */
    const CHOICE = 'application/x.choice+json';

    /**
     * The user have to fill hole(s) in a text.
     *
     * @var string
     */
    const CLOZE = 'application/x.cloze+json';

    /**
     * The user have to find element(s) on an image.
     *
     * @var string
     */
    const GRAPHIC = 'application/x.graphic+json';

    /**
     * The user have to associate elements together.
     *
     * @var string
     */
    const MATCH = 'application/x.match+json';

    /**
     * The user have to write his answer.
     *
     * @var string
     */
    const OPEN = 'application/x.short+json';

    /**
     * Get the list of managed question types.
     *
     * @return array
     */
    public static function getList()
    {
        return [
            static::CHOICE,
            static::CLOZE,
            static::GRAPHIC,
            static::MATCH,
            static::OPEN,
        ];
    }

    /**
     * Get the correct type from Interaction name
     * For retro-compatibility purpose (will be removed in next release).
     *
     * @deprecated
     *
     * @param string $name
     *
     * @return string
     */
    public static function getFromName($name)
    {
        $mapping = [
            InteractionQCM::TYPE => static::CHOICE,
            InteractionHole::TYPE => static::CLOZE,
            InteractionGraphic::TYPE => static::GRAPHIC,
            InteractionMatching::TYPE => static::MATCH,
            InteractionOpen::TYPE => static::OPEN,
        ];

        $type = null;
        if (!empty($mapping[$name])) {
            $type = $mapping[$name];
        }

        return $type;
    }

    public static function isSupported($type)
    {
        return in_array($type, static::getList());
    }
}
