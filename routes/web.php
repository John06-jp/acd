<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeveloperDashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeIdCardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IdCardController;
use App\Http\Controllers\PendingEmployeeController;
use App\Http\Controllers\PendingStudentController;
use App\Http\Controllers\ProspectusController;
use App\Http\Controllers\Sf2ReportController;
use App\Http\Controllers\SiteCustomizationController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [PendingStudentController::class, 'create'])->name('patron.register');
Route::post('/register', [PendingStudentController::class, 'store'])->name('pending.store');
Route::post('/register-employee', [PendingEmployeeController::class, 'store'])->name('pendingEmployee.store');

Route::get('/index', fn () => redirect()->route('home'));

// Attendance kiosk (public)
Route::get('/attendance', [AttendanceController::class, 'showScanner'])->name('attendance.scan');
Route::post('/attendance', [AttendanceController::class, 'scan'])->name('attendance.process');
Route::post('/attendance/section', [AttendanceController::class, 'processSection'])->name('attendance.section');
Route::post('/attendance-feedback', [FeedController::class, 'store'])->name('attendance.feedback.store');

// Admin + Staff
Route::middleware(['auth', 'can:isAdminOrStaff'])->group(function () {
    Route::get('/admin/pending', [StudentController::class, 'pending'])->name('students.pending');
    Route::post('/admin/pending/{id}/approve', [StudentController::class, 'approve'])->name('students.approve');
    Route::post('/admin/pending/{id}/reject', [StudentController::class, 'reject'])->name('students.reject');
    Route::get('/pending', [PendingStudentController::class, 'index'])->name('pending.index');

    Route::get('/pending/employees', [PendingEmployeeController::class, 'index'])->name('pending.employees');
    Route::post('/pending/employees/approve/{id}', [PendingEmployeeController::class, 'approve'])->name('employees.approve');
    Route::post('/pending/employees/reject/{id}', [PendingEmployeeController::class, 'reject'])->name('employees.reject');

    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/bulk-download-ids', [EmployeeController::class, 'bulkDownloadIds'])->name('employees.bulk.ids');
        Route::get('/export', [EmployeeController::class, 'export'])->name('employees.export');
        Route::get('/add', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/add', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/update/{id}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/delete/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/id/front/{id}', [IdCardController::class, 'front'])->name('employees.id.front');
        Route::get('/id/back/{id}', [IdCardController::class, 'back'])->name('employees.id.back');
        Route::get('/id/download/{id}', [IdCardController::class, 'downloadZip'])->name('employees.id.download');
    });

    Route::prefix('employees/idcard')->group(function () {
        Route::get('/front/{id}', [EmployeeIdCardController::class, 'front'])->name('employees.idcard.front');
        Route::get('/back/{id}', [EmployeeIdCardController::class, 'back'])->name('employees.idcard.back');
        Route::get('/download/{id}', [EmployeeIdCardController::class, 'download'])->name('employees.idcard.download');
    });

    Route::get('/attendance/change-video', [AttendanceController::class, 'showChangeVideo'])->name('attendance.changeVideo');
    Route::post('/attendance/upload-video', [AttendanceController::class, 'uploadVideo'])->name('attendance.uploadVideo');
    Route::get('/attendance/logout-feedback', [AttendanceController::class, 'feedbackSettings'])->name('attendance.feedback.settings');
    Route::post('/attendance/logout-feedback', [AttendanceController::class, 'updateFeedbackSettings'])->name('attendance.feedback.settings.update');
    Route::get('/attendance/section-picker', [AttendanceController::class, 'sectionSettings'])->name('attendance.section.settings');
    Route::post('/attendance/section-picker', [AttendanceController::class, 'updateSectionSettings'])->name('attendance.section.settings.update');

    Route::get('/admin/feedbacks', [FeedController::class, 'index'])->name('feedback.index');

    Route::get('/sms-blast', [SmsController::class, 'index'])->name('sms.page');
    Route::post('/sms/send', [SmsController::class, 'send'])->name('sms.send');
    Route::get('/sms/scan-message', [SmsController::class, 'scanMessage'])->name('sms.scanMessage');
    Route::post('/sms/scan-message', [SmsController::class, 'updateScanMessage'])->name('sms.scanMessage.update');
    Route::get('/sms/count', [SmsController::class, 'count'])->name('sms.count');

    Route::get('/attendance-logs', [AttendanceLogController::class, 'index'])->name('attendance_logs.index');
    Route::get('/attendance-logs/reports', [AttendanceLogController::class, 'reportsHub'])->name('attendance_logs.reports.hub');
    Route::get('/attendance-logs/reports/dashboard', [AttendanceLogController::class, 'reportsDashboard'])->name('attendance_logs.reports.dashboard');
    Route::get('/attendance-logs/reports/export', [AttendanceLogController::class, 'reportsExportCsv'])->name('attendance_logs.reports.export');
    Route::get('/attendance-logs/export/excel', [AttendanceLogController::class, 'exportExcel'])->name('attendance_logs.export.excel');
    Route::get('/attendance-logs/export/pdf', [AttendanceLogController::class, 'exportPdf'])->name('attendance_logs.export.pdf');

    Route::prefix('sf2')->name('sf2.')->group(function () {
        Route::get('/', [Sf2ReportController::class, 'index'])->name('index');
        Route::get('/create', [Sf2ReportController::class, 'create'])->name('create');
        Route::post('/', [Sf2ReportController::class, 'store'])->name('store');
        Route::get('/{sf2}', [Sf2ReportController::class, 'show'])->name('show');
        Route::get('/{sf2}/edit', [Sf2ReportController::class, 'edit'])->name('edit');
        Route::put('/{sf2}', [Sf2ReportController::class, 'update'])->name('update');
        Route::delete('/{sf2}', [Sf2ReportController::class, 'destroy'])->name('destroy');
        Route::get('/{sf2}/pdf', [Sf2ReportController::class, 'pdf'])->name('pdf');
        Route::get('/{sf2}/excel', [Sf2ReportController::class, 'excel'])->name('excel');
    });

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/report', [StudentController::class, 'index'])->name('students.report');
    Route::get('/students/bulk-download-ids', [StudentController::class, 'bulkDownloadIds'])->name('students.bulk.ids');
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
});

// Developer administrator only
Route::middleware(['auth', 'can:isAdminDeveloper'])->prefix('developer')->group(function () {
    Route::get('/dashboard', DeveloperDashboardController::class)->name('developer.dashboard');

    Route::prefix('site-customization')->name('site-customization.')->group(function () {
        Route::get('/', [SiteCustomizationController::class, 'index'])->name('index');
        Route::get('/history', [SiteCustomizationController::class, 'history'])->name('history');
        Route::put('/', [SiteCustomizationController::class, 'updateAll'])->name('update-all');
        Route::put('/section/{group}', [SiteCustomizationController::class, 'updateSection'])->name('update-section');
        Route::post('/images/{key}', [SiteCustomizationController::class, 'upload'])->name('images.upload');
        Route::delete('/images/{key}', [SiteCustomizationController::class, 'removeImage'])->name('images.remove');
        Route::delete('/section/{group}', [SiteCustomizationController::class, 'resetSection'])->name('reset-section');
        Route::delete('/', [SiteCustomizationController::class, 'resetAll'])->name('reset-all');
        Route::post('/revisions/{batch}/restore', [SiteCustomizationController::class, 'restore'])->name('restore');
    });
});

// Library admin only
Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/register-student', [StudentController::class, 'create'])->name('students.create');
    Route::post('/register-student', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/import-template', [StudentController::class, 'downloadImportTemplate'])->name('students.import.template');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/employees/import-template', [EmployeeController::class, 'downloadImportTemplate'])->name('employees.import.template');
    Route::post('/employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

    Route::get('/idcard/download/{id}', [IdCardController::class, 'download'])->name('idcard.download');
    Route::get('/idcard/front/{id}', [IdCardController::class, 'front'])->name('idcard.front');
    Route::get('/idcard/back/{id}', [IdCardController::class, 'back'])->name('idcard.back');

    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::get('/files/view/{id}', [FileController::class, 'view'])->name('files.view');
    Route::get('/files/download/{id}', [FileController::class, 'download'])->name('files.download');
    Route::delete('/files/delete/{id}', [FileController::class, 'delete'])->name('files.delete');

    Route::prefix('prospectus')->name('prospectus.')->group(function () {
        Route::get('/', [ProspectusController::class, 'index'])->name('index');
        Route::post('/store-program', [ProspectusController::class, 'storeProgram'])->name('storeProgram');
        Route::get('/{program}/years', [ProspectusController::class, 'getProgramYears'])->name('getProgramYears');
    });
    Route::post('/prospectus/{year}/course', [ProspectusController::class, 'storeCourse'])->name('prospectus.storeCourse');
    Route::put('/prospectus/course/{course}', [ProspectusController::class, 'updateCourse'])->name('prospectus.updateCourse');
    Route::delete('/prospectus/course/{course}', [ProspectusController::class, 'destroyCourse'])->name('prospectus.destroyCourse');
    Route::put('/prospectus/program/{program}', [ProspectusController::class, 'updateProgram'])->name('prospectus.updateProgram');
    Route::delete('/prospectus/program/{program}', [ProspectusController::class, 'destroyProgram'])->name('prospectus.destroyProgram');

});

// Account management is available to library administrators only.
Route::middleware(['auth', 'can:isAdmin'])->group(function () {
    Route::get('/view-users', [UserController::class, 'index'])->name('users.index');
    Route::get('/edit-user/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/update-user/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/delete-user/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/create-user', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
});
