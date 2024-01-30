<?php
declare(strict_types=1);
namespace Vocab;

class DatabaseBase implements WordExistsInterface { 

   private static $word_exists_sql = "select 1 from words where word=:new_word";
   
   private \PDOStatement $word_exists_stmt;
   
   private string $new_word = '';

   public function __construct(\PDO $pdo)
   {
      $this->word_exists_stmt = $pdo->prepare(self::$word_exists_sql); 
      
      $this->word_exists_stmt->bindParam(':new_word', $this->new_word, \PDO::PARAM_STR); 
   }

   public function word_exists(string $word) : bool
   {
     $this->new_word = $word;

     $rc = $this->word_exists_stmt->execute();

     if ($rc === false) 
         
         throw new \ErrorException('SQL statement to test if word exist failed.');
     
     $rc = $this->word_exists_stmt->fetch(\PDO::FETCH_NUM);
         
     return ($rc === false) ? false : true;            
   }
}
