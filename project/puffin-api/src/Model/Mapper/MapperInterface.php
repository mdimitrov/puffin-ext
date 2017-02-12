<?php
namespace Puffin\Model\Mapper;

use Puffin\Model\ModelInterface;

interface MapperInterface {

    public function findById($id);

    public function findAll();

    public function findAllAssoc();

    public function save(ModelInterface $model);

    public function mapRowToObject(array $row);

}
