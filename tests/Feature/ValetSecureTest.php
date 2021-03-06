<?php

namespace Tests\Feature;

use App\Actions\ValetSecure;
use App\Shell\Shell;
use Exception;
use Illuminate\Support\Facades\Config;
use Tests\Feature\Fakes\FakeProcess;
use Tests\TestCase;

class ValetSecureTest extends TestCase
{
    private $shell;

    public function setUp(): void
    {
        parent::setUp();
        $this->shell = $this->mock(Shell::class);
    }

    /** @test */
    function it_runs_valet_link()
    {
        Config::set('lambo.store.valet_secure', true);

        $this->shell->shouldReceive('execInProject')
            ->with('valet secure')
            ->once()
            ->andReturn(FakeProcess::success());

        app(ValetSecure::class)();
    }

    /** @test */
    function it_throws_an_exception_if_the_after_script_fails()
    {
        Config::set('lambo.store.valet_secure', true);

        $this->shell->shouldReceive('execInProject')
            ->with('valet secure')
            ->once()
            ->andReturn(FakeProcess::fail('valet secure'));

        $this->expectException(Exception::class);

        app(ValetSecure::class)();
    }
}
