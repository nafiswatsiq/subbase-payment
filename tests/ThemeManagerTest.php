<?php

namespace Nafiswatsiq\SubbasePayment\Tests;

require_once __DIR__ . '/TestCase.php';

use Nafiswatsiq\Subbase\Support\ThemeManager;

class ThemeManagerTest extends TestCase
{
    public function test_active_theme_defaults_to_default(): void
    {
        $themeManager = app(ThemeManager::class);
        $this->assertEquals('default', $themeManager->getActiveTheme());
    }

    public function test_can_set_active_theme(): void
    {
        $themeManager = app(ThemeManager::class);
        $themeManager->setActiveTheme('neo-brutalism');
        $this->assertEquals('neo-brutalism', $themeManager->getActiveTheme());
    }

    public function test_resolves_built_in_theme_view(): void
    {
        $themeManager = app(ThemeManager::class);
        $themeManager->setActiveTheme('neo-brutalism');

        $view = $themeManager->resolveView('subbase', 'components.plan-list');
        $this->assertEquals('subbase::themes.neo-brutalism.components.plan-list', $view);
    }

    public function test_falls_back_to_default_if_theme_view_missing(): void
    {
        $themeManager = app(ThemeManager::class);
        $themeManager->setActiveTheme('non-existent-theme');

        $view = $themeManager->resolveView('subbase', 'components.plan-list');
        $this->assertEquals('subbase::themes.default.components.plan-list', $view);
    }

    public function test_lists_all_built_in_themes(): void
    {
        $themeManager = app(ThemeManager::class);
        $themes = $themeManager->getAvailableThemes();

        $this->assertContains('default', $themes);
        $this->assertContains('neo-brutalism', $themes);
        $this->assertContains('glassmorphism', $themes);
        $this->assertContains('claymorphism', $themes);
        $this->assertContains('cyberpunk', $themes);
        $this->assertContains('maximalism', $themes);
    }

    public function test_theme_list_command(): void
    {
        $this->artisan('subbase:theme-list')
            ->expectsOutputToContain('Current active theme:')
            ->assertExitCode(0);
    }

    public function test_theme_install_command(): void
    {
        $this->artisan('subbase:theme-install', ['theme' => 'cyberpunk'])
            ->assertExitCode(0);

        $themeManager = app(ThemeManager::class);
        $this->assertEquals('cyberpunk', config('subbase.theme'));
    }
}
