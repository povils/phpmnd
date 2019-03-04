<?php

define('test', 3);

$variable = '4';

class TEST_1
{
   const TEST_1 = 3;

   private $variable = 6;

   public function test($input = 4) {
      if ($input > 2) {
         return 15;
      }

      for ($i = 3; $i <= 10; $i++) {
         switch($input) {
            case 5:
               break 2;
         }
      }

      round($input, 4);
      round($input, $input > 7);

      $array = [
         'name' => 'Name',
         'age' => 13,
         'adult' => $input > 18,
          18,
          123 => 1234,
          '1234' => 1234,
      ];

      if ($input > 1);
      if (PHP_VERSION_ID < 50600);

      $value = $input * 15;
   }

   const TEST_2 = 20 * 21;

   public function returnString() {
      return 'string';
   }

   public function returnNegative() {
      return -1;
   }

   public function ignoreValues()
   {
       return [
           intval(100),
           floatval(3.14),
           strval('10')
       ];
   }
  
   public function returnFromKey() {
        $a = [];
        return $a[1234];
   }
}
