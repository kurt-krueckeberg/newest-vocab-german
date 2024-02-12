<?php
declare(strict_types=1);
namespace Vocab;

class Database extends DatabaseBase implements InserterInterface { 

   private \PDO $pdo;

   private string $locale;

   private InserterInterface $inserter;

   private array $tables;

   private $word_prim_keys = []; // maps words to primary keys
   private $conjugated_tenses_prim_keys = []; // maps words to primary keys
   
   public function __construct(Config $config)
   {
     $cred = $config->get_db_credentials();
     
     $this->pdo = new \PDO($cred["dsn"], $cred["user"], $cred["password"]); 
       
     parent::__construct($this->pdo);
       
     $this->locale = $config->get_locale();

     $this->inserter = new WordResultInserter($this);
   }
     
   public function insert_noun(WordInterface $deface) : int
   {
     $id = $this->insert_word($deface);
  
     $nounsDataTbl = $this->get_table('NounsDataTable');
  
     $id = $nounsDataTbl->insert($deface, $id); 
  
     return $id;
   }

   public function insert_verb(WordInterface $wrface) : int // returns words.id
   {
     $word_id = $this->insert_word($wrface);
     
     $conjTensesTbl = $this->get_table('ConjugatedTensesTable');

     $conj_id = $conjTensesTbl->insert($wrface, $word_id);
     
     $this->conjugated_tenses_prim_keys[$wrface->word_defined()] = $conj_id;  

     $conjugatedVerbsTbl = $this->get_table('ConjugatedVerbsTable');

     $conjugatedVerbsTbl->insert($conj_id, $word_id);
     
     return $word_id;
   }

   public function insert_related_verb(WordInterface $wrface) : int
   {
      $word_id = $this->insert_word($wrface);

      /* 
      foreach($wrface as $key => $verbResult) {

         echo "Verb to be inserted: " . $verbResult->word_defined() . ".\n"; 
         
         $id = $this->insert_word($verbResult); 

         $related[] = $id;
      }
      */

      $conjugatedVerbsTbl = $this->get_table('ConjugatedVerbsTable');
       
      $conj_id = $this->conjugated_tenses_prim_keys[$wrface->get_main_verb()]; 
  
      $conjugatedVerbsTbl->insert($conj_id, $word_id);
      
      return $conj_id;
   }

   public function insert_word(WordInterface $wrface) : int
   {
     $word_tbl = $this->get_table('WordTable');

     $defns_tbl = $this->get_table('DefnsTable');

     $expr_tbl = $this->get_table('ExpressionsTable');

     $word_id = $word_tbl->insert($wrface); 

     $this->word_prim_keys[$wrface->word_defined()] = $word_id;
     
     foreach($wrface->definitions() as $defn) {

        $defn_id = $defns_tbl->insert($defn->definition(), $word_id);

        foreach ($defn->expressions() as $expr) {

          $expr_tbl->insert($expr, $defn_id);
        } 
     }

    return $word_id; 
   }

   public function save_lookup(WordInterface $wdResultFace)   
   {
      $wdResultFace->accept($this->inserter); 
     
      return true;
   } 
   
   private function get_table(string $table_name) : mixed
   {
      $className = "Vocab\\$table_name";
  
      if (isset($this->tables[$className]) === false) {

         $instance = new $className($this->pdo);

         $this->tables[$className] = $instance;
     } 

      return $this->tables[$className];
   }

   function fetch_word($word) : WordInterface | false
   {
      $fetch_word = $this->get_table('FetchWord');

      $wrface = $fetch_word($word); 

      return $wrface;
   }

   function save_samples(string $word, \Iterator $sentences_iter) : bool
   {
      $samplesTbl = new SamplesTable($this->pdo);

      $prim_key = $this->word_prim_keys[$word];

      foreach ($sentences_iter as $sentence) {
          
        $rc = $samplesTbl->insert($sentence, $prim_key);  
      }
      
      return true;
   }
}
