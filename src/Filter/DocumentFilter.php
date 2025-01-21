<?php

namespace App\Filter;

// use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\PropertyInfo\Type;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;

class DocumentFilter extends AbstractFilter
{
    public function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        // Logique de filtrage ici
        if ($property === 'document') {
            // Exemple de logique pour ajouter un filtre sur le document
            $alias = $queryBuilder->getRootAliases()[0]; // Récupère l'alias de la table principale
            $queryBuilder
                ->leftJoin("$alias.document", 'd') // Rejoindre la table des documents
                ->andWhere('d.id = :documentId')
                ->setParameter('documentId', $value); // Filtrer sur l'ID du document
        }
    }

    public function getDescription(string $resourceClass): array
    {
        if (\App\Entity\Image::class === $resourceClass) {
            return [
                'document' => [
                    'property' => 'document',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'Filter images by document ID',
                ],
            ];
        }

        return [];
    }
}