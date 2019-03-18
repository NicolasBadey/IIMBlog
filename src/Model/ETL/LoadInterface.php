<?php
namespace App\Model\ETL;

interface LoadInterface
{
    public static function getAlias(): string;
    public function getMappingProperties() :array;
    public function setLiveMode(bool $live): void;
    public function preLoad(): void;
    public function postLoad(): void;
    public function bulkLoad(array $data): array;
    public function singleLoad(array $data, bool $createIndexIdNotExists): array;
}
