<?php
declare(strict_types=1);
namespace Vocab;

abstract class AbstractDefinitionsIterator extends \ArrayIterator {

   function __construct(array $targets)
   { 
      parent::__construct($targets);
   }

   abstract protected function get_current(array $target) : DefinitionInterface | false;

   function current() : DefinitionInterface | false
   {
      $ele =  parent::current();

      return ($ele === false) ? $ele : $this->get_current($ele);
   }
}
