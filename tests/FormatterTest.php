<?php

use PHPUnit\Framework\TestCase;
/**
 * @author: Mattakorn Limkool
 *
 */
class FormatterTest extends TestCase
{

    public function testDateDb(){
        $formatter = new \obbz\yii2\i18n\CoreFormatter();
        $this->assertEquals(
            $formatter->asDbDate('20/12/2017'),
            '2017-12-20'
        );
    }
}