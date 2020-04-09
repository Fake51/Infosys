<?php
declare(strict_types = 1);

namespace App\Service;
use App\Model\Participant as ParticipantModel;

class Participant
{

    public function __construct(ParticipantModel $model)
    {
        $this->model = $model;
    }

    public function fetchList(array $fields, int $offset, int $limit) : array
    {
        return $this->model->fetchList($fields, $offset, $limit);
    }

    public function search(string $terms, array $fields, int $offset, int $limit) : array
    {
        if ("" === $terms) {
            return [];
        }

        return $this->model->search(explode(' ', $terms), $fields, $offset, $limit);
    }
}
