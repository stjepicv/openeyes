<?php

    /**
     * Class OphDrPrescription_ItemTaperTest
     * @property OphDrPrescription_ItemTaper $instance
     */
class OphDrPrescription_ItemTaperTest extends CDbTestCase
{
    protected $fixtures = array(
        'ophdrprescription_item_tapers' => OphDrPrescription_ItemTaper::class,
        'ophdrprescription_items' => OphDrPrescription_Item::class,
    );
        
    private $instance;
        
    public function setUp()
    {
        parent::setUp();
        $this->instance = $this->ophdrprescription_item_tapers('prescription_item_taper1');
    }
        
    public function tearDown()
    {
        parent::tearDown();
        unset($this->instance);
    }

    /**
     * @covers OphDrPrescription_ItemTaper::fpTenDose
     */
    public function testFpTenDose()
    {
        $expected = strtoupper('DOSE: '
            . (is_numeric($this->instance->dose) ? "{$this->instance->dose} {$this->instance->item->drug->dose_unit}" : $this->instance->dose)
            . ', ' . $this->instance->item->route->name
            . ($this->instance->item->route_option ? ' (' . $this->instance->item->route_option->name . ')' : null));
        $actual = $this->instance->fpTenDose();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers OphDrPrescription_ItemTaper::fpTenFrequency
     */
    public function testFpTenFrequency()
    {
        $expected = strtoupper("FREQUENCY: {$this->instance->frequency->long_name} FOR {$this->instance->duration->name}");
        $actual = $this->instance->fpTenFrequency();

        $this->assertEquals($expected, $actual);

        $this->instance = $this->ophdrprescription_item_tapers('prescription_item_taper8');
        $duration = strtolower($this->instance->duration->name);
        $expected = strtoupper("FREQUENCY: {$this->instance->frequency->long_name} {$duration}");
        $actual = $this->instance->fpTenFrequency();

        $this->assertEquals($expected, $actual);

    }
}
