<?php

namespace Anh\TiedContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AnhTiedContentBundle extends Bundle
{
    const VERSION = 'v2.1.0';
    const TITLE = 'AnhTiedContentBundle';
    const DESCRIPTION = 'Bundle for tied content management';

    public static function getRequiredBundles()
    {
        return array(
            'Anh\ContentBundle\AnhContentBundle',
        );
    }
}
