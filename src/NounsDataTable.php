<?php
declare(strict_types=1);
namespace Vocab;

class NounsDataTable implements TableInsertInterface { 
     
   private \PDOStatement $insert_stmt; 

   private static string $insert_sql = "insert into nouns_data(gender, plural, word_id) values(:gender, :plural, :word_id)"; 

   private string $gender = '';
   private string $plural = '';
   private int $word_id = -1;

   private \PDO $pdo;
 
   public function __construct(\PDO $pdo)
   {
      $this->pdo = $pdo;

      $this->insert = $pdo->prepare(self::$insert_sql); 

      $this->insert->bindParam(':gender', $this->gender, \PDO::PARAM_STR);
 
      $this->insert->bindParam(':plural', $this->plural, \PDO::PARAM_STR); 

      $this->insert->bindParam(':word_id', $this->word_id, \PDO::PARAM_INT); 
   }

   public function insert(WordResultInterface $deface, int $word_id)
   {
       $this->gender = $deface->get_gender()->value;
       
       $this->plural = $deface->get_plural();

       $this->word_id = $word_id;
       
       $this->insert_stmt->execute();
      
       return (int) $this->pdo->lastIsertId();
   }
}
