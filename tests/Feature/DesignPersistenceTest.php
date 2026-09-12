<?php

namespace Tests\Feature\PublicModule;

use App\Services\CurrentTenant;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ViewErrorBag;
use Modules\Account\Models\User;
use Modules\Customization\Services\ThemeConfigService;
use Modules\Public\Http\Requests\SaveDesignRequest;
use Modules\Public\Services\LandingPageService;
use Modules\Public\Services\ThemeRegistry;
use Modules\Tenant\Services\TenantConfigService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DesignPersistenceTest extends TestCase
{
    public function createApplication()
    {
        $app = require dirname(__DIR__, 4).'/bootstrap/app.php';
        $app->afterBootstrapping(LoadConfiguration::class, function ($app) {
            $sqlite = ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false];
            foreach (array_keys($app['config']->get('database.connections')) as $connection) {
                $app['config']->set("database.connections.$connection", $sqlite);
            }
            $app['config']->set(['database.default' => 'sqlite', 'cache.default' => 'array', 'session.driver' => 'array']);
        });
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    private function createConfigTables(): void
    {
        Schema::connection('sys_core')->create('sys_tenant_config', function (Blueprint $table) {
            $table->id('config_id');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('group');
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type');
            $table->timestamps();
        });
        Schema::connection('sys_log')->create('sys_activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->string('event')->nullable();
            $table->text('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_portal_preferences_survive_reload_and_remain_isolated_between_tenants(): void
    {
        $this->createConfigTables();
        $service = $this->app->make(ThemeConfigService::class);
        $this->assertSame('grid', $service->load('portal', 41)['layout']); // Populate cache before saving.
        $service->save('portal', [...ThemeConfigService::defaults('portal'), 'layout' => 'compact', 'grouping' => 'columns', 'primaryColor' => '#b42318', 'backgroundMode' => 'texture'], 41);
        $service->save('portal', [...ThemeConfigService::defaults('portal'), 'layout' => 'bento'], 42);
        $fresh = new ThemeConfigService(new TenantConfigService);
        $this->assertSame('compact', $fresh->load('portal', 41)['layout']);
        $this->assertSame('columns', $fresh->load('portal', 41)['grouping']);
        $this->assertSame('#b42318', $fresh->load('portal', 41)['primaryColor']);
        $this->assertSame('texture', $fresh->load('portal', 41)['backgroundMode']);
        $this->assertSame('bento', $fresh->load('portal', 42)['layout']);
        $this->assertSame('grid', $fresh->load('portal', null)['layout']);
        $this->assertSame(2, DB::connection('sys_core')->table('sys_tenant_config')->count());
    }

    public function test_shared_theme_storage_preserves_auth_main_and_existing_portal_settings(): void
    {
        $this->createConfigTables();
        $service = $this->app->make(ThemeConfigService::class);
        $config = new TenantConfigService;
        $config->set(41, 'theme_portal', 'preferences', [...ThemeConfigService::defaults('portal'), 'order' => ['sys', 'account']], 'json');
        $service->save('auth', ['primary' => 'blue'], 41);
        $service->save('tabler', ['primary' => 'green'], 41);
        $this->app->make(CurrentTenant::class)->set(41);
        $service->save('portal', [...$service->load('portal'), 'order' => ['account'], 'title' => null, 'tenant_id' => 42]);
        $this->assertSame('blue', $service->load('auth', 42)['primary']);
        $this->assertSame('green', $service->load('tabler', 41)['primary']);
        $this->assertSame(['account', 'sys'], $service->load('portal')['order']);
        $this->assertSame('', $service->load('portal')['title']);
        $this->assertArrayNotHasKey('tenant_id', $config->get(41, 'theme_portal', 'preferences'));
        $this->assertSame([], $config->get(42, 'theme_portal', 'preferences', []));
    }

    public function test_portal_save_requires_a_resolved_tenant(): void
    {
        $this->app->make(CurrentTenant::class)->clear();
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Pilih tenant terlebih dahulu.');
        $this->app->make(ThemeConfigService::class)->save('portal', ThemeConfigService::defaults('portal'));
    }

    public function test_invalid_section_settings_are_rejected_but_generated_design_is_accepted(): void
    {
        $rules = (new SaveDesignRequest)->rules();
        $valid = ['template' => 'modern', 'sectionVariants' => ['hero' => 'hero_1'], 'sectionColors' => ['hero' => ['bg' => '#ffffff', 'image' => null]], 'sectionSettings' => ['hero' => ['title' => 'Halo', 'limit_data' => null]]];
        $this->assertFalse(Validator::make($valid, $rules)->fails());
        foreach ([['sectionVariants' => ['hero' => 'hero_999']], ['sectionColors' => ['hero' => ['bg' => 'url(evil)']]], ['sectionSettings' => ['hero' => ['active' => 'yes']]], ['sectionSettings' => ['unknown' => []]], ['sectionSettings' => ['hero' => ['unexpected' => true]]]] as $patch) {
            $this->assertTrue(Validator::make(array_replace($valid, $patch), $rules)->fails());
        }
    }

    public function test_design_authorization_requires_a_resolved_tenant_and_cms_permission(): void
    {
        $request = new SaveDesignRequest;
        $this->app->make(CurrentTenant::class)->set(41);
        $this->assertFalse($request->authorize());
        $user = \Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('public.cms.settings.update', [])->andReturn(false, true);
        $this->actingAs($user);
        $this->assertFalse($request->authorize());
        $this->assertTrue($request->authorize());
        $this->app->make(CurrentTenant::class)->clear();
        $this->assertFalse($request->authorize());
    }

    public function test_portal_color_validation_and_editor_markup_use_the_same_contract(): void
    {
        $preferences = ThemeConfigService::defaults('portal');
        $rules = $this->app->make(ThemeConfigService::class)->portalRules();
        $this->assertFalse(Validator::make($preferences, $rules)->fails());
        $this->assertTrue(Validator::make([...$preferences, 'primaryColor' => 'red;display:none'], $rules)->fails());
        $this->assertTrue(Validator::make([...$preferences, 'backgroundMode' => 'remote-script'], $rules)->fails());
        view()->share('errors', new ViewErrorBag);
        $html = view('customization::pages.customization.partials.portal-settings', ['portalPreferences' => $preferences])->render();
        $this->assertStringContainsString('data-color="primaryColor"', $html);
        $this->assertStringContainsString('data-color="backgroundColor"', $html);
        $this->assertStringContainsString('data-save-tenant', $html);
    }

    public function test_section_failure_rolls_back_the_published_design_and_template(): void
    {
        Schema::create('cms_landing_page_settings', function (Blueprint $table) {
            $table->id('setting_id');
            $table->unsignedBigInteger('tenant_id');
            $table->text('design')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        $this->app->make(CurrentTenant::class)->set(41);
        DB::table('cms_landing_page_settings')->insert(['tenant_id' => 41, 'design' => json_encode(['template' => 'modern'])]);
        $service = \Mockery::mock(LandingPageService::class, [new TenantConfigService, new ThemeRegistry])->makePartial();
        $service->shouldReceive('saveSectionSettings')->once()->andThrow(new \RuntimeException('section failure'));
        try {
            $service->saveDesign('editorial', ['radius' => 'square'], ['hero' => ['title' => 'Changed']]);
            $this->fail('Expected failure');
        } catch (\RuntimeException $e) {
            $this->assertSame('section failure', $e->getMessage());
        }
        $this->assertSame(['template' => 'modern'], json_decode(DB::table('cms_landing_page_settings')->value('design'), true));
    }
}
