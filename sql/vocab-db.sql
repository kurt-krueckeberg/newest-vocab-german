create database if not exists vocab;

use vocab; 

# -- I'm not sure what POS the Systran dictionary
# -- look up will return
create table if not exists words (
  id int not null auto_increment primary key,
  word varchar(30) not null,
  unique(word),
  pos varchar(10) not null 
);

# -- The plural form of the noun
# -- The gender. 'u' if gender is not known, an unexplained 
# -- Systran behavior.
create table if not exists nouns_data (
  id int not null auto_increment primary key,
  gender enum('m', 'f', 'n', 'u') not null,
  plural varchar(25) not null,
  word_id int not null,
  unique(word_id),
  foreign key(word_id) references words(id) on delete cascade on update cascade
);

# -- This table locates the conjugation. Prefix and reflexive
# -- verbs share the same conjugation.
create table if not exists verb_conjs (
  word_id int not null primary key,
  conjugation varchar(70) not null,
  foreign key(word_id) references words(id) on delete cascade on update cascade
);

# -- This is not returned even though it is desired
# -- to have.
# -- create table if not exists verbs (
# --  id int not null auto_increment primary key,
# --  type enum('strong', 'weak') 
# -- );

# -- Definitions of a word
create table if not exists defns (
  id int not null auto_increment primary key,
  defn varchar(45) not null,
  word_id int not null,
  foreign key(word_id) references words(id) on delete cascade on update cascade
);

# -- Example expressions for a particular word
create table if not exists exprs (
  id int not null auto_increment primary key,
  source varchar(85) not null,
  target varchar(85) not null,
  defn_id int not null,
  foreign key(defn_id) references defns(id) on delete cascade on update cascade
);

# -- Example sentences for a particular word
create table if not exists samples (
  id int not null auto_increment primary key,
  sample text not null,
  trans text not null,
  word_id int not null,
  foreign key(word_id) references words(id) on delete cascade on update cascade
);

# -- My confidence in knowing the word
create table if not exists confidence (
  id int not null auto_increment primary key,
  rating int,
  word_id int not null,
  foreign key(word_id) references words(id) on delete cascade on update cascade
);
