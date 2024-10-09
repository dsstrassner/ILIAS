<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

declare(strict_types=1);

require_once(__DIR__ . "/../../../Base.php");
require_once(__DIR__ . "/CommonFieldRendering.php");

use ILIAS\Data;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\UI\Implementation\Component as I;
use ILIAS\UI\Implementation\Component\Input\InputData;
use ILIAS\UI\Implementation\Component\SignalGenerator;

class SelectForTest extends ILIAS\UI\Implementation\Component\Input\Field\Select
{
    public function _isClientSideValueOk($value): bool
    {
        return $this->isClientSideValueOk($value);
    }
}

class SelectInputTest extends ILIAS_UI_TestBase
{
    use CommonFieldRendering;

    protected DefNamesource $name_source;

    public function setUp(): void
    {
        $this->name_source = new DefNamesource();
    }

    public function testOnlyValuesFromOptionsAreAcceptableClientSideValues(): void
    {
        $options = ["one" => "Eins", "two" => "Zwei", "three" => "Drei"];
        $select = new SelectForTest(
            $this->createMock(ILIAS\Data\Factory::class),
            $this->createMock(ILIAS\Refinery\Factory::class),
            "",
            $options,
            ""
        );

        $this->assertTrue($select->_isClientSideValueOk("one"));
        $this->assertTrue($select->_isClientSideValueOk("two"));
        $this->assertTrue($select->_isClientSideValueOk("three"));
        $this->assertFalse($select->_isClientSideValueOk("four"));
    }

    public function testEmptyStringIsAcceptableClientSideValueIfSelectIsNotRequired(): void
    {
        $options = [];
        $select = new SelectForTest(
            $this->createMock(ILIAS\Data\Factory::class),
            $this->createMock(ILIAS\Refinery\Factory::class),
            "",
            $options,
            ""
        );

        $this->assertTrue($select->_isClientSideValueOk(""));
    }

    public function testEmptyStringCreatesErrorIfSelectIsRequired(): void
    {
        $options = [];
        $select = $this->getFieldFactory()->select(
            "",
            $options,
            ""
        )
        ->withRequired(true)
        ->withNameFrom($this->name_source);

        $data = $this->createMock(InputData::class);
        $data->expects($this->once())
            ->method("getOr")
            ->willReturn(null);
        $select = $select->withInput(
            $data
        );

        $this->assertNotEquals(null, $select->getError());
    }

    public function testEmptyStringIsAnAcceptableClientSideValueEvenIfSelectIsRequired(): void
    {
        $options = [];
        $select = (new SelectForTest(
            $this->createMock(ILIAS\Data\Factory::class),
            $this->createMock(ILIAS\Refinery\Factory::class),
            "",
            $options,
            ""
        ))->withRequired(true);

        $this->assertTrue($select->_isClientSideValueOk(""));
    }

    public function testRender(): void
    {
        $f = $this->getFieldFactory();
        $label = "label";
        $byline = "byline";
        $options = ["one" => "One", "two" => "Two", "three" => "Three"];
        $select = $f->select($label, $options, $byline)->withNameFrom($this->name_source);
        $expected = $this->getFormWrappedHtml(
            'select-field-input',
            $label,
            '
            <select id="id_1" name="name_0">
                <option selected="selected" value="">-</option>
                <option value="one">One</option>
                <option value="two">Two</option>
                <option value="three">Three</option>
            </select>
            ',
            $byline
        );
        $this->assertEquals($expected, $this->render($select));
    }


    public function testRenderValue(): void
    {
        $f = $this->getFieldFactory();
        $label = "label";
        $byline = "byline";
        $options = ["one" => "One", "two" => "Two", "three" => "Three"];
        $select = $f->select($label, $options, $byline)->withNameFrom($this->name_source)->withValue("one");
        $expected = $this->getFormWrappedHtml(
            'select-field-input',
            $label,
            '
            <select id="id_1" name="name_0">
                <option value="">-</option>
                <option selected="selected" value="one">One</option>
                <option value="two">Two</option>
                <option value="three">Three</option>
            </select>
            ',
            $byline
        );
        $this->assertEquals($expected, $this->render($select));
    }

    public function testCommonRendering(): void
    {
        $f = $this->getFieldFactory();
        $label = "label";
        $select = $f->select($label, [], null)->withNameFrom($this->name_source);

        $this->testWithError($select);
        $this->testWithNoByline($select);
        $this->testWithRequired($select);
        $this->testWithDisabled($select);
    }


    public function testWithValueAndRequiredDoesNotContainNull(): void
    {
        $f = $this->getFieldFactory();
        $label = "label";
        $byline = "byline";
        $options = ["something_value" => "something"];
        $select = $f->select($label, $options, $byline)
                    ->withNameFrom($this->name_source);

        $html_without = $this->brutallyTrimHTML($this->getDefaultRenderer()->render($select));

        $this->assertTrue(str_contains($html_without, ">-</option>"));
        $this->assertTrue(str_contains($html_without, "value=\"\""));

        $select = $select->withRequired(true);
        $html_with_required = $this->brutallyTrimHTML($this->getDefaultRenderer()->render($select));

        $this->assertTrue(str_contains($html_with_required, ">ui_select_dropdown_label</option>"));
        $this->assertTrue(str_contains($html_with_required, "value=\"\""));

        $select = $select->withRequired(false)->withValue("something_value");
        $html_with_value = $this->brutallyTrimHTML($this->getDefaultRenderer()->render($select));

        $this->assertTrue(str_contains($html_with_value, ">-</option>"));
        $this->assertTrue(str_contains($html_with_value, "value=\"\""));

        $select = $select->withRequired(true);

        $html_with_value_and_required = $this->brutallyTrimHTML($this->getDefaultRenderer()->render($select));

        $this->assertFalse(str_contains($html_with_value_and_required, ">-</option>"));
        $this->assertFalse(str_contains($html_with_value_and_required, "value=\"\""));
    }
}
