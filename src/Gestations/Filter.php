<?php

namespace SuperMae\Gestations;

use MongoDB\BSON\Regex;
use SuperMae\Gestations\Filter\AgeRange;

class Filter
{
    public $age;
    public $establishment;
    public $numberOfWeek;
    public $robsonGroup;

    public function __construct(AgeRange $ageRange, $establishment, $numberOfWeek, $robsonGroup)
    {
        $this->age = $ageRange;
        $this->establishment = $establishment;
        $this->numberOfWeek = (int) $numberOfWeek;
        $this->robsonGroup = (int) $robsonGroup;
    }

    public function toArray()
    {
        $filter = [
            'mother.numberOfWeek' => $this->numberOfWeek,
            'mother.robsonGroup' => $this->robsonGroup,
        ];

        if ($this->establishment) {
            $filter['establishment.name'] = new Regex(".*{$this->establishment}.*", 'i');
        }
        if ($this->age->isNotEmpty()) {
            $filter['mother.age'] = [
                '$gte' => $this->age->start,
                '$lte' => $this->age->end
            ];
        }

        return array_filter($filter, function ($item) {
            return !empty($item);
        });
    }

    public function isNotEmpty()
    {
        $empty = new static(
            new AgeRange(null, null),
            null,
            null,
            null
        );

        return $empty != $this;
    }
}
