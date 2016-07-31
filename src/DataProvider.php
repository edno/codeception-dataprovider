<?php
/**
 * Before suite hook that provide dataprovider annotation
 * for datadriven tests using non-static data source
 */
namespace Codeception\Extension;

use Codeception\Util\Annotation;
use Codeception\Step\Comment;
use Codeception\Test\Cest as CestFormat;
use Codeception\Exception\TestParseException;

class DataProvider extends \Codeception\Platform\Extension
{
  // list events to listen to
  public static $events = array(
    //run before any test`
    'suite.before' => 'before'
  );

  public function before(\Codeception\Event\SuiteEvent $se)
  {
    $suite = $se->getSuite();
    $tests = $suite->tests();
    foreach ($tests as $id => $test) {
        if (get_class($test) == 'Codeception\Test\Cest') {
            $testClass = $test->getTestClass();
            $testMethod = $test->getTestMethod();
            $testFile = $test->getMetadata()->getFilename();
            $testActor = $test->getMetadata()->getCurrent('actor');
            $dataMethod = Annotation::forMethod($testClass, $testMethod)->fetch('dataprovider');
            if (false === empty($dataMethod)) {
                if (false === is_callable([$testClass, $dataMethod])) {
                    throw new TestParseException(
                        $testFile, "DataProvider {$dataMethod} for ${testClass}->${testMethod} is invalid or not callable"
                        . PHP_EOL .
                        "Make sure this is a public static function."
                    );
                }
                try {
                    $dataProvider = new \PHPUnit_Framework_TestSuite_DataProvider();
                    $examples = $testClass::$dataMethod();
                    foreach ($examples as $example) {
                        if ($example === null) {
                            throw new TestParseException(
                                $testFile, "Values return by DataProvider {$dataMethod} for ${testClass}->${testMethod} is invalid"
                            );
                        }
                        $dataTest = new CestFormat($testClass, $testMethod, $testFile);
                        $dataTest->getMetadata()->setServices([
                            'di'         => $test->getMetadata()->getService('di'),
                            'dispatcher' => $test->getMetadata()->getService('dispatcher'),
                            'modules'    => $test->getMetadata()->getService('modules')
                        ]);
                        $dataTest->getMetadata()->setCurrent(['actor' => $testActor, 'example' => $example]);
                        $step = new Comment('', $dataTest->getMetadata()->getCurrent('example'));
                        $dataTest->getScenario()->setFeature($dataTest->getSpecFromMethod() . ' | '. $step->getArgumentsAsString(100));
                        $groups = Annotation::forMethod($testClass, $testMethod)->fetchAll('group');
                        $dataProvider->addTest($dataTest, $groups);
                    }
                    $tests[$id] = $dataProvider;
                } catch(\Exception $e) {
                    throw new TestParseException(
                        $testFile, "DataProvider {$dataMethod} for ${testClass}->${testMethod} is invalid or not callable"
                        . PHP_EOL .
                        "Make sure this is a public static function."
                    );
                }
            }
        }
    }
    $suite->setTests($tests);
  }

}
