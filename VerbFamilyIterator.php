<?php
declare(strict_types=1);
namespace Vocab;

class VerbFamilyIterator implements \Iterator, \Countable {

   private int $main_verb_index;
   private array $matches; 
   private bool $iter_at_start;

   function __construct(array $matches, int $main_verb_index)
   { 
      $this->matches = $matches;

      $this->main_verb_index = $main_verb_index;
      
      $this->iter_at_start = true;
   }
      
   function count() : int
   {
      return count($this->matches);
   }

   function current() : SystranWord | false
   {
      $conjugator = function () : string  {

           $conj = $this->matches[$this->main_verb_index]['source']['inflection'];

           return str_replace(array("(", "/", ")"), array( "", ", ", ""), $conj); 
       };
      
      if ($this->iter_at_start) {
           
         return new SystranVerb($this->matches[$this->main_verb_index], $conjugator);

      } else {
         
         $ele = \current($this->matches);

         return new SystranRelatedVerbResult($ele);
      }
   }
   
   function next() : void
   {
     if ($this->iter_at_start) {
         
         $this->iter_at_start = false;
         \reset($this->matches);
         
     } else {
         
      \next($this->matches);    

      $index = key($this->matches);
         
      if ($index == $this->main_verb_index)

         \next($this->matches);
     }
   }
   
   function key() : mixed
   {
      return \key($this->matches);
   }

   function valid() : bool
   {
      // Check if current is false, which implies cursor is at end of array.
      $tmp = \current($this->matches);
      
      return ($tmp === false) ? false : true;
   }

   function rewind() : void
   {
      $this->iter_at_start = true;
   }
}
