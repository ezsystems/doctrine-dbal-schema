<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\API;

use Doctrine\DBAL\Schema\Schema;

/**
 * Export the given database Schema object to the custom Yaml format.
 */
interface SchemaExporter
{
    /**
     * Export \Doctrine\DBAL\Schema object to the custom Yaml format.
     *
     * @param \Doctrine\DBAL\Schema\Schema $schemaDefinition
     *
     * @return string
     */
    public function export(Schema $schemaDefinition): string;
}
