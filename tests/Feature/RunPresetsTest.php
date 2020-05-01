<?php

namespace Tests\Feature;

use App\Actions\RunPresets;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RunPresetsTest extends TestCase
{
    /** @test */
    function it_parses_multiple_parameters()
    {
        Config::set('lambo.store.presets', 'telescope:abc,def');
        $action = app(RunPresets::class);

        $presetsPassed = $action->presetsPassed();

        $this->assertCount(1, $presetsPassed);

        $presetPassed = reset($presetsPassed);

        $this->assertEquals('telescope', $presetPassed->preset);
        $this->assertCount(2, $presetPassed->parameters);
        $this->assertContains('abc', $presetPassed->parameters);
        $this->assertContains('def', $presetPassed->parameters);
    }

    /** @test */
    function it_parses_single_parameter()
    {
        Config::set('lambo.store.presets', 'telescope:abc');
        $action = app(RunPresets::class);

        $presetsPassed = $action->presetsPassed();

        $this->assertCount(1, $presetsPassed);

        $presetPassed = reset($presetsPassed);

        $this->assertEquals('telescope', $presetPassed->preset);
        $this->assertCount(1, $presetPassed->parameters);
        $this->assertContains('abc', $presetPassed->parameters);
    }

    /** @test */
    function it_handles_multiple_presets()
    {
        Config::set('lambo.store.presets', 'telescope|nova');
        $action = app(RunPresets::class);

        $presetsPassed = $action->presetsPassed();

        $this->assertCount(2, $presetsPassed);

        $firstPreset = reset($presetsPassed);
        $secondPreset = next($presetsPassed);

        $this->assertEquals('telescope', $firstPreset->preset);
        $this->assertCount(0, $firstPreset->parameters);
        $this->assertEquals('nova', $secondPreset->preset);
        $this->assertCount(0, $secondPreset->parameters);
    }

    /** @test */
    function it_handles_multiple_parameters_on_multiple_presets()
    {
        Config::set('lambo.store.presets', 'telescope:abc,def|nova:ghi,jkl');
        $action = app(RunPresets::class);

        $presetsPassed = $action->presetsPassed();

        $this->assertCount(2, $presetsPassed);

        $firstPreset = reset($presetsPassed);
        $secondPreset = next($presetsPassed);

        $this->assertEquals('telescope', $firstPreset->preset);
        $this->assertCount(2, $firstPreset->parameters);
        $this->assertContains('abc', $firstPreset->parameters);
        $this->assertContains('def', $firstPreset->parameters);
        $this->assertEquals('nova', $secondPreset->preset);
        $this->assertContains('ghi', $secondPreset->parameters);
        $this->assertContains('jkl', $secondPreset->parameters);
    }

    /** @test */
    function it_generates_preset_class_names_from_short_strings()
    {
        $action = app(RunPresets::class);

        $this->assertEquals('MyFavoriteEver', $action->getPresetClassName('my-favorite-ever'));
    }

    /** @test */
    function it_news_premade_presets_from_passed_preset_objects()
    {
        $action = app(RunPresets::class);

        $passedPreset = (object) ['preset' => 'telescope', 'parameters' => []];

        $this->assertTrue($action->getPresetInstance($passedPreset) instanceof \App\Presets\Premade\Telescope);
    }

    // @todo decide: Are the parameters passed when newing up the preset? assume so?
    // @todo new tests:
    // it news local presets
    // it news composer-required presets
    // it errors if a preset is requested which doesn't exist
    // it passes parameters to presets
}
