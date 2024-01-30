<?php
declare(strict_types=1);
namespace Vocab;

abstract class AbstractDefinitionsIterator extends \ArrayIterator {

   function __construct(array $targets)
   { 
      parent::__construct($targets);
   }

   abstract protected function get_current(array $target);

   function current() : DefinitionInterface
   {
      $ele =  parent::current();

      return $this->get_current($ele);
   }
}
