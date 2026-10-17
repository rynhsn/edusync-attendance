<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'SchoolkuController::home');
$routes->get('news', 'SchoolkuController::home');
$routes->get('news/', 'SchoolkuController::home');
$routes->get('news/sitemap.xml', 'SchoolkuController::sitemap');
$routes->get('news/news', 'SchoolkuController::news');
$routes->get('news/news/search', 'SchoolkuController::searchNews');
$routes->get('news/news/(:segment)', 'SchoolkuController::newsDetail/$1');
$routes->get('news/downloads', 'SchoolkuController::downloads');
$routes->get('news/downloads/file/(:num)', 'SchoolkuController::downloadFile/$1');
$routes->get('news/contact', 'SchoolkuController::contact');
$routes->post('news/contact/send', 'SchoolkuController::sendContactMessage', ['filter' => 'csrf']);
$routes->get('news/spmb', 'SpmbController::form');
$routes->post('news/spmb/submit', 'SpmbController::submit', ['filter' => 'csrf']);
$routes->get('news/pages/(:segment)', 'SchoolkuController::page/$1');
$routes->get('website-news', 'SchoolWebsiteNewsController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('website-news/new', 'SchoolWebsiteNewsController::create', ['filter' => 'actionAccess:website-news.manage']);
$routes->get('website-news/edit/(:num)', 'SchoolWebsiteNewsController::edit/$1', ['filter' => 'actionAccess:website-news.manage']);
$routes->post('website-news/save', 'SchoolWebsiteNewsController::save', ['filter' => ['actionAccess:website-news.manage', 'csrf']]);
$routes->post('website-news/delete/(:num)', 'SchoolWebsiteNewsController::delete/$1', ['filter' => ['actionAccess:website-news.delete', 'csrf']]);
$routes->get('website-messages', 'SchoolWebsiteMessagesController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('website-messages/read/(:num)', 'SchoolWebsiteMessagesController::markRead/$1', ['filter' => ['roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator', 'csrf']]);
$routes->get('notifications', 'NotificationsController::index', ['filter' => 'adminAuth']);
$routes->get('notifications/open/(:num)', 'NotificationsController::open/$1', ['filter' => 'adminAuth']);
$routes->get('school-contact-settings', 'SchoolContactSettingsController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('school-contact-settings', 'SchoolContactSettingsController::save', ['filter' => ['actionAccess:school-contact-settings.save', 'csrf']]);
$routes->get('website-settings/home', 'SchoolkuWebsiteSettingsController::home', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('website-settings/home', 'SchoolkuWebsiteSettingsController::saveHome', ['filter' => ['actionAccess:website-home-settings.save', 'csrf']]);
$routes->get('website-settings/spmb-schedule', 'SchoolkuWebsiteSettingsController::spmbSchedule', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('website-settings/spmb-schedule', 'SchoolkuWebsiteSettingsController::saveSpmbSchedule', ['filter' => ['actionAccess:website-spmb-schedule.save', 'csrf']]);
$routes->get('website-settings/profile', 'SchoolkuWebsiteSettingsController::profile', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('website-settings/profile', 'SchoolkuWebsiteSettingsController::saveProfile', ['filter' => ['actionAccess:website-profile-settings.save', 'csrf']]);
$routes->get('website-settings/programs', 'SchoolkuWebsiteSettingsController::programs', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('website-settings/programs/section', 'SchoolkuWebsiteSettingsController::saveProgramsSection', ['filter' => ['actionAccess:website-programs.section', 'csrf']]);
$routes->post('website-settings/programs/save', 'SchoolkuWebsiteSettingsController::saveProgram', ['filter' => ['actionAccess:website-programs.manage', 'csrf']]);
$routes->post('website-settings/programs/delete/(:num)', 'SchoolkuWebsiteSettingsController::deleteProgram/$1', ['filter' => ['actionAccess:website-programs.delete', 'csrf']]);
$routes->get('website-content', 'SchoolWebsiteContentController::index/achievements', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('website-content/(:segment)', 'SchoolWebsiteContentController::index/$1', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('website-content/(:segment)/save', 'SchoolWebsiteContentController::save/$1', ['filter' => ['actionAccess:website-content.manage', 'csrf']]);
$routes->post('website-content/(:segment)/delete/(:num)', 'SchoolWebsiteContentController::delete/$1/$2', ['filter' => ['actionAccess:website-content.delete', 'csrf']]);
$routes->get('spmb/applications', 'SpmbController::applications', ['filter' => 'adminAuth']);
$routes->get('spmb/applications/(:num)/proof', 'SpmbController::proof/$1', ['filter' => 'adminAuth']);
$routes->get('spmb/applications/(:num)/documents/(:segment)', 'SpmbController::document/$1/$2', ['filter' => 'adminAuth']);

// Email OTP authentication
$routes->get('login', 'AuthController::login');
$routes->get('register', 'AuthController::register');
$routes->get('auth/verify', 'AuthController::verify');
$routes->get('auth/access-denied', 'AuthController::accessDenied');
$routes->post('auth/register', 'AuthController::registerAccount', ['filter' => 'csrf']);
$routes->post('auth/login', 'AuthController::authenticate', ['filter' => 'csrf']);
$routes->post('auth/resend-code', 'AuthController::resendVerificationCode', ['filter' => 'csrf']);
$routes->post('auth/verify-code', 'AuthController::verifyCode', ['filter' => 'csrf']);
$routes->post('auth/logout', 'AuthController::logout', ['filter' => 'csrf']);
$routes->post('language', 'LanguageController::set', ['filter' => 'csrf']);

// Authenticated users can manage their own profile, including regular users.
$routes->get('profile', 'ProfileController::index', ['filter' => 'authenticated']);
$routes->post('profile', 'ProfileController::update', ['filter' => ['authenticated', 'csrf']]);
$routes->post('profile/password', 'ProfileController::changePassword', ['filter' => ['authenticated', 'csrf']]);
$routes->get('profile/photo', 'ProfileController::photo', ['filter' => 'authenticated']);

// Attendance kiosk remains public; attendance management is restricted.
$routes->get('attendance', 'AttendanceController::attendance');
$routes->get('attendance/manage', 'AttendanceController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('attendance/kiosk', 'AttendanceController::attendance');
$routes->group('attendance', function ($routes) {
    $routes->post('tap', 'AttendanceController::tap');
    $routes->get('show', 'AttendanceController::show');
});

// panel
$routes->get('panel', 'PanelController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);

// attendance reports
$routes->get('report/attendance', 'AttendanceController::report', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('report/attendance/pdf', 'AttendanceController::downloadReportPdf', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('extracurricular-attendance', 'ExtracurricularAttendanceController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('report/extracurricular-attendance', 'ExtracurricularAttendanceController::report', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('report/extracurricular-attendance/pdf', 'ExtracurricularAttendanceController::downloadMonthlyStudentReportPdf', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('extracurricular-attendance/save', 'ExtracurricularAttendanceController::save', ['filter' => ['actionAccess:extracurricular-attendance.save', 'csrf']]);
$routes->get('class-attendance-report', 'ClassAttendanceController::monthlyReport', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('class-attendance-report/pdf', 'ClassAttendanceController::downloadMonthlyReportPdf', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('users', 'UsersController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('users/role/(:num)', 'UsersController::setRole/$1', ['filter' => ['adminAuth', 'csrf']]);
$routes->get('user-passwords', 'UsersController::passwords', ['filter' => 'adminAuth']);
$routes->post('user-passwords/reset/(:num)', 'UsersController::resetPassword/$1', ['filter' => ['adminAuth', 'csrf']]);
$routes->get('sidebar-settings', 'SidebarSettingsController::index', ['filter' => 'adminAuth']);
$routes->post('sidebar-settings', 'SidebarSettingsController::save', ['filter' => ['adminAuth', 'csrf']]);
$routes->get('button-settings', 'ButtonSettingsController::index', ['filter' => 'adminAuth']);
$routes->post('button-settings', 'ButtonSettingsController::save', ['filter' => ['adminAuth', 'csrf']]);
$routes->get('menu-settings', 'MenuSettingsController::index', ['filter' => 'adminAuth']);
$routes->post('menu-settings/save', 'MenuSettingsController::save', ['filter' => ['adminAuth', 'csrf']]);
$routes->post('menu-settings/icon', 'MenuSettingsController::saveIcon', ['filter' => ['adminAuth', 'csrf']]);
$routes->post('menu-settings/delete/(:num)', 'MenuSettingsController::delete/$1', ['filter' => ['adminAuth', 'csrf']]);
$routes->get('online-users', 'OnlineUsersController::index', ['filter' => 'adminAuth']);
$routes->get('teacher-subjects', 'TeacherSubjectController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('teacher-subjects', 'TeacherSubjectController::save', ['filter' => ['actionAccess:teacher-subjects.save', 'csrf']]);

// Master data spreadsheet import and export
foreach ([
    'major' => 'major.manage',
    'class' => 'class.manage',
    'subjects' => 'subjects.create',
    'teachers' => 'teachers.create',
    'parents' => 'parents.create',
    'lesson-periods' => 'lesson-periods.create',
    'academic-years' => 'academic-years.create',
] as $masterDataType => $importAction) {
    $routes->get($masterDataType . '/import-template', 'MasterDataSpreadsheetController::template/' . $masterDataType, ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
    $routes->get($masterDataType . '/export', 'MasterDataSpreadsheetController::export/' . $masterDataType, ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
    $routes->post($masterDataType . '/import', 'MasterDataSpreadsheetController::import/' . $masterDataType, ['filter' => ['actionAccess:' . $importAction, 'csrf']]);
}

// Academic master data
$routes->get('subjects', 'AcademicDataController::index/subjects', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('subjects', 'AcademicDataController::create/subjects', ['filter' => ['actionAccess:subjects.create', 'csrf']]);
$routes->post('subjects/update/(:num)', 'AcademicDataController::update/subjects/$1', ['filter' => ['actionAccess:subjects.edit', 'csrf']]);
$routes->post('subjects/delete/(:num)', 'AcademicDataController::delete/subjects/$1', ['filter' => ['actionAccess:subjects.delete', 'csrf']]);
$routes->get('teachers', 'AcademicDataController::index/teachers', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('teachers', 'AcademicDataController::create/teachers', ['filter' => ['actionAccess:teachers.create', 'csrf']]);
$routes->post('teachers/update/(:num)', 'AcademicDataController::update/teachers/$1', ['filter' => ['actionAccess:teachers.edit', 'csrf']]);
$routes->post('teachers/delete/(:num)', 'AcademicDataController::delete/teachers/$1', ['filter' => ['actionAccess:teachers.delete', 'csrf']]);
$routes->get('parents', 'AcademicDataController::index/parents', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('parents', 'AcademicDataController::create/parents', ['filter' => ['actionAccess:parents.create', 'csrf']]);
$routes->post('parents/update/(:num)', 'AcademicDataController::update/parents/$1', ['filter' => ['actionAccess:parents.edit', 'csrf']]);
$routes->post('parents/delete/(:num)', 'AcademicDataController::delete/parents/$1', ['filter' => ['actionAccess:parents.delete', 'csrf']]);
$routes->get('lesson-periods', 'AcademicDataController::index/lesson-periods', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('lesson-periods', 'AcademicDataController::create/lesson-periods', ['filter' => ['actionAccess:lesson-periods.create', 'csrf']]);
$routes->post('lesson-periods/update/(:num)', 'AcademicDataController::update/lesson-periods/$1', ['filter' => ['actionAccess:lesson-periods.edit', 'csrf']]);
$routes->post('lesson-periods/delete/(:num)', 'AcademicDataController::delete/lesson-periods/$1', ['filter' => ['actionAccess:lesson-periods.delete', 'csrf']]);
$routes->get('extracurriculars', 'AcademicDataController::index/extracurriculars', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('extracurriculars', 'AcademicDataController::create/extracurriculars', ['filter' => ['actionAccess:extracurriculars.create', 'csrf']]);
$routes->post('extracurriculars/update/(:num)', 'AcademicDataController::update/extracurriculars/$1', ['filter' => ['actionAccess:extracurriculars.edit', 'csrf']]);
$routes->post('extracurriculars/delete/(:num)', 'AcademicDataController::delete/extracurriculars/$1', ['filter' => ['actionAccess:extracurriculars.delete', 'csrf']]);
$routes->get('kokurikuler', 'AcademicDataController::index/kokurikuler', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('kokurikuler', 'AcademicDataController::create/kokurikuler', ['filter' => ['actionAccess:kokurikuler.create', 'csrf']]);
$routes->post('kokurikuler/update/(:num)', 'AcademicDataController::update/kokurikuler/$1', ['filter' => ['actionAccess:kokurikuler.edit', 'csrf']]);
$routes->post('kokurikuler/delete/(:num)', 'AcademicDataController::delete/kokurikuler/$1', ['filter' => ['actionAccess:kokurikuler.delete', 'csrf']]);
$routes->get('academic-years', 'AcademicDataController::index/academic-years', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('academic-years', 'AcademicDataController::create/academic-years', ['filter' => ['actionAccess:academic-years.create', 'csrf']]);
$routes->post('academic-years/update/(:num)', 'AcademicDataController::update/academic-years/$1', ['filter' => ['actionAccess:academic-years.edit', 'csrf']]);
$routes->post('academic-years/delete/(:num)', 'AcademicDataController::delete/academic-years/$1', ['filter' => ['actionAccess:academic-years.delete', 'csrf']]);
$routes->get('grades/student/(:num)', 'GradeController::student/$1', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('grades', 'GradeController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('grades', 'GradeController::save', ['filter' => ['roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator', 'actionAccess:grades.save', 'csrf']]);
$routes->get('report/grades', 'GradeReportController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('report/grades/pdf', 'GradeReportController::download', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('academic-assessments/(:segment)', 'AcademicAssessmentController::index/$1', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('academic-assessments/(:segment)', 'AcademicAssessmentController::save/$1', ['filter' => ['roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator', 'actionAccess:academic-assessments.save', 'csrf']]);

// Teacher-led class attendance
$routes->get('class-attendance', 'ClassAttendanceController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('class-attendance/create', 'ClassAttendanceController::create', ['filter' => 'actionAccess:class-attendance.create']);
$routes->get('class-attendance/(:num)/edit', 'ClassAttendanceController::edit/$1', ['filter' => 'actionAccess:class-attendance.edit']);
$routes->get('class-attendance/(:num)/pdf', 'ClassAttendanceController::downloadPdf/$1', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('class-attendance/(:num)', 'ClassAttendanceController::show/$1', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('class-attendance', 'ClassAttendanceController::store', ['filter' => ['actionAccess:class-attendance.create', 'csrf']]);
$routes->post('class-attendance/send-daily-whatsapp', 'ClassAttendanceController::sendDailyWhatsAppReport', ['filter' => ['actionAccess:class-attendance.whatsapp', 'csrf']]);
$routes->post('class-attendance/(:num)/send-whatsapp', 'ClassAttendanceController::sendAbsenceWhatsApp/$1', ['filter' => ['actionAccess:class-attendance.whatsapp', 'csrf']]);
$routes->post('class-attendance/update/(:num)', 'ClassAttendanceController::update/$1', ['filter' => ['actionAccess:class-attendance.edit', 'csrf']]);
$routes->post('class-attendance/delete/(:num)', 'ClassAttendanceController::delete/$1', ['filter' => ['actionAccess:class-attendance.delete', 'csrf']]);

$routes->get('student', 'StudentController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('student/', 'StudentController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('student/show', 'StudentController::show', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('student/export', 'StudentController::export', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('student/import-template', 'StudentController::importTemplate', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('student/import', 'StudentController::import', ['filter' => ['actionAccess:student.import', 'csrf']]);
$routes->post('student/create', 'StudentController::create', ['filter' => ['actionAccess:student.create', 'csrf']]);
$routes->post('student/update/(:num)', 'StudentController::update/$1', ['filter' => ['actionAccess:student.edit', 'csrf']]);
$routes->post('student/delete/(:num)', 'StudentController::delete/$1', ['filter' => ['actionAccess:student.delete', 'csrf']]);
$routes->post('student/status/(:num)', 'StudentController::setStatus/$1', ['filter' => ['actionAccess:student.status', 'csrf']]);

//group class
$routes->get('class', 'ClassController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('class/', 'ClassController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('class/show', 'ClassController::show', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('class/save', 'ClassController::saveForm', ['filter' => ['actionAccess:class.manage', 'csrf']]);
$routes->post('class/delete/(:num)', 'ClassController::deleteForm/$1', ['filter' => ['actionAccess:class.delete', 'csrf']]);
$routes->post('class/create', 'ClassController::create', ['filter' => ['actionAccess:class.create', 'csrf']]);
$routes->put('class/update/(:num)', 'ClassController::update/$1', ['filter' => 'actionAccess:class.edit']);
$routes->delete('class/delete/(:num)', 'ClassController::delete/$1', ['filter' => 'actionAccess:class.delete']);

// group major
$routes->get('major', 'MajorController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('major/', 'MajorController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('major/show', 'MajorController::show', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('major/save', 'MajorController::saveForm', ['filter' => ['actionAccess:major.manage', 'csrf']]);
$routes->post('major/delete/(:num)', 'MajorController::deleteForm/$1', ['filter' => ['actionAccess:major.delete', 'csrf']]);
$routes->post('major/create', 'MajorController::create', ['filter' => ['actionAccess:major.create', 'csrf']]);
$routes->put('major/update/(:num)', 'MajorController::update/$1', ['filter' => 'actionAccess:major.edit']);
$routes->delete('major/delete/(:num)', 'MajorController::delete/$1', ['filter' => 'actionAccess:major.delete']);

// group time setting
$routes->get('timesetting', 'TimeSettingController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('timesetting/', 'TimeSettingController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('timesetting/show', 'TimeSettingController::show', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('timesetting/create', 'TimeSettingController::create', ['filter' => ['actionAccess:timesetting.manage', 'csrf']]);
$routes->put('timesetting/update/(:num)', 'TimeSettingController::update/$1', ['filter' => 'actionAccess:timesetting.manage']);
$routes->delete('timesetting/delete/(:num)', 'TimeSettingController::delete/$1', ['filter' => 'actionAccess:timesetting.manage']);

// group attendance status
$routes->get('attendance-status', 'AttendanceStatusController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->get('attendance-status/', 'AttendanceStatusController::index', ['filter' => 'roleAccess:super_admin,user,kepala_sekolah,bendahara,kurikulum,operator']);
$routes->post('attendance-status/save', 'AttendanceStatusController::save', ['filter' => ['actionAccess:attendance-status.manage', 'csrf']]);
$routes->post('attendance-status/delete/(:segment)/(:num)', 'AttendanceStatusController::deleteStatus/$1/$2', ['filter' => ['actionAccess:attendance-status.manage', 'csrf']]);
