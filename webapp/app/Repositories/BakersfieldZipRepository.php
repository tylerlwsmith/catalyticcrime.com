<?php

namespace App\Repositories;

class BakersfieldZipRepository
{
    protected $zips = [
        '93301',
        '93304',
        '93305',
        '93306',
        '93307',
        '93308',
        '93309',
        '93311',
        '93312',
        '93313',
        '93314'
    ];

    public function all()
    {
        return $this->zips;
    }

    public function random()
    {
        return collect($this->zips)->random();
    }
}
