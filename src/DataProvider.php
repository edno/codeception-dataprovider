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
    public static $events = ['suite.before' => 'before'];

    public function before(\Codeception\Event\SuiteEvent $se)
    {
        $suite = $se->getSuite();
        $tests = $suite->tests();
        foreach ($tests as $id => $test) {
            if (is_a($test, 'Codeception\Test\Cest')) {

                $testClass = $test->getTestClass();
                $testClassName = get_class($testClass);
                $testMethod = $test->getTestMethod();
                $testFile = $test->getMetadata()->getFilename();
                $testActor = $test->getMetadata()->getCurrent('actor');

                $dataMethod = Annotation::forMethod($testClass, $testMethod)->fetch('dataprovider');

                try {
                    if (empty($dataMethod)) {
                        continue;
                    }
                    
                    if (false === is_callable([$testClass, $dataMethod])) {
                        throw new \Exception();
                    }

                    $dataProvider = new \PHPUnit_Framework_TestSuite_DataProvider();
                    $examples = $testClassName::$dataMethod();
                    foreach ($examples as $example) {
                        if ($example === null) {
                            throw new TestParseException(
                                $testFile, "Invalid values format returned by DataProvider {$dataMethod} for ${testClassName}->${testMethod}."
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
                        $dataTest->getScenario()->setFeature($dataTest->getSpecFromMethod() . ' | ' . $step->getArgumentsAsString(100));
                        $groups = Annotation::forMethod($testClass, $testMethod)->fetchAll('group');
                        $dataProvider->addTest($dataTest, $groups);
                    }
                    $tests[$id] = $dataProvider;

                } catch (\Exception $e) {
                    throw new TestParseException(
                        $testFile, "DataProvider {$dataMethod} for ${testClassName}->${testMethod} is invalid or not callable."
                        . PHP_EOL .
                        "Make sure this is a public static function."
                    );
                }
            }
        }
        $suite->setTests($tests);
    }

}
