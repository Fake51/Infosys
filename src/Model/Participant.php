<?php
declare(strict_types = 1);

namespace App\Model;
use Doctrine\ORM\EntityManagerInterface;

class Participant
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function fetchList(array $fields, int $offset, int $limit) : array
    {
        $query = $this->entityManager->createQuery('
        SELECT p.id, c.name
        FROM App\\Entity\\Participant p
        JOIN p.country c
        ORDER BY p.id ASC
        ');

        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        return $query->getResult();
    }

    public function search(array $terms, array $fields, int $offset, int $limit) : array
    {
        $parsedFields = $this->replaceSpecialSearchFields($this->parseFields($fields));

        $index = 1;
        $parameters = [];

        foreach ($terms as $term) {
            $parameters['term' . $index++] = '%' . $term . '%';
        }

        $query = $this->entityManager->createQuery(
            sprintf('
                SELECT
                %s
                FROM App\\Entity\\Participant p
                LEFT JOIN p.country c
                LEFT JOIN p.participantTypes pt
                LEFT JOIN p.languages l
                WHERE %s
                GROUP BY p.id
                ORDER BY p.id ASC
                ',
                implode(', ', $parsedFields),
                $this->constructWhereClause(array_keys($parameters))
            )
        );

        $query->setParameters($parameters);

        $query->setMaxResults($limit);
        $query->setFirstResult($offset);

        return $query->getResult();
    }

    private function constructWhereClause(array $parameterNames) : string
    {
        $matches = [
            'p.name LIKE :%s',
            'p.email LIKE :%s',
            'p.address LIKE :%s',
            'p.postalcode LIKE :%s',
            'p.city LIKE :%s',
            'p.offeredSkills LIKE :%s',
            'c.name LIKE :%s',
            'l.name LIKE :%s',
        ];

        $parts = [];

        foreach ($matches as $match) {
            foreach ($parameterNames as $name) {
                $parts[] = sprintf($match, $name);
            }
        }

        return implode(' OR ', $parts);
    }

    private function parseFields(array $fields) : array
    {
        $map = [
            'participant' => 'p',
            'country' => 'c',
            'participantType' => 'pt',
            'language' => 'l',
        ];

        $parsedFields = [];

        // todo sanitize by whitelist

        foreach ($fields as $field) {
            $parts = explode('__', $field);

            if (2 !== count($parts) || !isset($map[$parts[0]])) {
                continue;
            }

            $parsedFields[] = sprintf('%s.%s AS %s__%s', $map[$parts[0]], $parts[1], $parts[0], $parts[1]);
        }

        return $parsedFields;
    }

    private function replaceSpecialSearchFields(array $fields) : array
    {
        $replacements = [
            'c.name' => 'GROUP_CONCAT(c.name)',
            'l.name' => 'GROUP_CONCAT(l.name)',
        ];

        return array_map(function ($field) use ($replacements) {
            $parts = explode(' ', $field);

            if (isset($replacements[$parts[0]])) {
                $field = str_replace($parts[0], $replacements[$parts[0]], $field);
            }

            return $field;
        }, $fields);
    }
}
