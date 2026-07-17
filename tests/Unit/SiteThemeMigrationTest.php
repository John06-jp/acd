<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SiteThemeMigrationTest extends TestCase
{
    public function test_required_data_screens_use_shared_table_theme(): void
    {
        $views = [
            'resources/views/students/students.blade.php',
            'resources/views/employees/index.blade.php',
            'resources/views/view_accounts/list.blade.php',
            'resources/views/attendance_logs/index.blade.php',
            'resources/views/admin/feedbacks.blade.php',
            'resources/views/feedbacks/index.blade.php',
            'resources/views/attendance_logs/partials/patron_reports_body.blade.php',
        ];

        foreach ($views as $view) {
            $this->assertStringContainsString('site-table', file_get_contents(base_path($view)), $view);
        }
    }

    public function test_shared_theme_defines_all_button_variants_and_table_states(): void
    {
        $css = file_get_contents(public_path('css/site-theme.css'));

        foreach (['primary', 'secondary', 'success', 'warning', 'danger', 'neutral'] as $variant) {
            $this->assertStringContainsString(".site-btn-{$variant}", $css);
        }

        foreach (['header', 'body', 'stripe', 'hover', 'selected', 'radius', 'cell-padding'] as $token) {
            $this->assertStringContainsString("--site-table-{$token}", $css);
        }
    }
}
