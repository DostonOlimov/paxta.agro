<?php

namespace App\Models\DefaultModels;


use XBase\Record\RecordFactory;
use XBase\TableReader;


class MyTableReader extends TableReader
{
 public function getNumber(){

     for($i=0;$i<$this->getHeader()->recordCount;$i++){
         $record = RecordFactory::create($this->table, $this->encoder, $i, $this->getStream()
             ->read($this->getHeader()->recordByteLength));
     }

     return $record;
 }
}
