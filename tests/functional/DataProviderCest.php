<?php

use \Codeception\Example;

class DataProviderCest
{
    /**
     * @example ["/api/rest", 200]
     */
     public function withExample(FunctionalTester $I, Example $example)
     {
         $I->assertEquals("/api/rest", $example[0]);
         $I->assertEquals(200, $example[1]);
     }

     public function withoutExample()
     {
     }

     /**
      * @dataprovider __myDataSource
      */
      public function withExampleProvider(FunctionalTester $I, Example $example)
      {
            $expected = ["", "foo", "bar", "re"];
            $I->assertInternalType('integer', $example[0]);
            $I->assertEquals($expected[$example[0]], $example[1]);
      }

      public static function __myDataSource()
      {
          return [[1, "foo"],[2, "bar"],[3, "re"]];
      }
}
