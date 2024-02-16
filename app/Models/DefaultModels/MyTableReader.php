<?php

namespace App\Models\DefaultModels;


use XBase\Record\RecordFactory;
use XBase\TableReader;


class MyTableReader extends TableReader
{
 public function getTotalCount(){

     return $this->getHeader()->recordCount;
 }
}
