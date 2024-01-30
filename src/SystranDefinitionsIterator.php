<?php
declare(strict_types=1);
namespace Vocab;

class SystranDefinitionsIterator extends AbstractDefinitionsIterator { 
     
    public function __construct(array $targets)
    {
        parent::__construct($targets);        
    } 
 
    public function word_defined() : string
    {
       $target = parent::current();
  
       // strip out any tilde or anything that is not a-zaööü. Keep ' ' for  reflexive verbs like
       // 'sich vorstellen'
       return preg_replace('/[^A-ZÖÄÜa-zaöü ]/', '', $target['source']['lemma']); 
    }

    protected function get_current(mixed $target) : DefinitionInterface
    {
       return new SystranDefinition($target); 
    }
}
