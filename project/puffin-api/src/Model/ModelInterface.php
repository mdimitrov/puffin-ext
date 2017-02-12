<?php
namespace Puffin\Model;

interface ModelInterface {

    public static function fromState(array $state);

    public function setFromAssoc(array $data);

    public function toAssoc();

}
