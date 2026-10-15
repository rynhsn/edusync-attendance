# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

### Account registration and admin login

Registration asks for a name, username, password, and email. The user verifies
their email once with a one-time code, then logs in using their username and
password. Before running the authentication migration, set the initial Super
Admin address in `.env`:

```ini
auth.super_admin_email = admin@example.com
```

Configure Gmail SMTP in `.env` with the Google account that will send the codes.
Use a Google App Password (with 2-Step Verification enabled), not the account
password:

```ini
email.protocol = smtp
email.fromEmail = admin@example.com
email.fromName = EduSync
email.SMTPHost = smtp.gmail.com
email.SMTPUser = admin@example.com
email.SMTPPass = your-google-app-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```

Keep `.env` private and do not commit SMTP credentials. Run `php spark migrate`
with a database account that can create tables. The account used by the web
application needs `SELECT`, `INSERT`, and `UPDATE` access to `auth_users`.
The migration seeds the configured email as the Super Admin; other registered
email addresses receive the regular `user` role. Registration verification
codes expire after 10 minutes and can only be used once. Passwords must be at
least 8 characters; usernames must be 3–32 letters, numbers, dots, underscores,
or hyphens.

Super Admin assigns the `user`, `kepala_sekolah`, and `bendahara` roles from
the Data User page. The User role has dashboard and profile access by default;
Kepala Sekolah and Bendahara can view attendance reports and class-attendance
details by default. Super Admin can grant operational button permissions to
any role from Pengaturan Akses Tombol. Role changes take effect on the
account's next protected request. Bendahara currently sees attendance
summaries only; the application does not yet contain financial records or a
finance module.

Super Admin can show or hide items in the Super Admin sidebar from
`/sidebar-settings`. This changes sidebar visibility only; existing role
authorization still protects each page.

Super Admin can grant permissions for operational buttons per role or
individual account from `/button-settings`, including attendance
entry/edit/delete, student management, academic master data, grades, and
assessments. Per-user settings override that account's role setting for actions
that are explicitly configured; unset actions inherit the role's permission.
These permissions control both button visibility and the corresponding action
endpoints, and the role must also have access to the associated menu. Super
Admin always retains all operational permissions. User/role administration and
system access configuration remain Super Admin-only.

Super Admin can add custom sidebar menus and submenus at `/menu-settings`,
including their Bootstrap icon, display order, internal URL, and parent menu.
Submenus can be placed under built-in sections such as Settings or under a
custom menu, then moved to a different section. Changing sidebar navigation
does not create a page or grant access to it; the target route must already
exist and continue to enforce its own role authorization.

### Academic data and classroom attendance

After signing in as Super Admin, add subjects, teachers, lesson periods,
extracurricular activities, kokurikuler activities, and an academic year from
their Master Data pages. Kokurikuler activities have their own CRUD page at
`/kokurikuler`; access to its create, edit, and delete actions can be managed
per role or per user from `/button-settings`. Student kokurikuler assessments
are entered at `/academic-assessments/kokurikuler`, using the same A-D scale as
extracurricular assessments, and are included in the student report. Create the
assessment storage table with `create_kokurikuler_assessments.sql` or run its
migration. Class attendance can then be
recorded at `/class-attendance/create`: select the class, subject, teacher,
period, academic year, and date, then set each active student's status. The
class attendance list and student-level details are available at
`/class-attendance`. Attendance status options are present, sick, excused, and
absent; a class/subject/period/year combination can only be recorded once per
date.

Before entering grades, assign subjects to teachers at `/teacher-subjects`.
The `Input Nilai` page lets an authorized role select a class, academic year,
subject, and a teacher assigned to that subject, then save UTS, UAS, and
Praktik scores (0–100) for each active student in the class. Scores are stored
separately per student, class, subject, teacher, and academic year/semester;
blank components are allowed until a score is available. From the grade list,
use `Lihat Detail` on a student to view the saved score components and their
class, subject, teacher, and semester context.

### Public SekolahKu website

Super Admin manages public articles through **Website Sekolah → Kelola News**.
Articles are stored in the `school_website_news` table in the EduSync database;
new installations start with no articles. **Lihat News** opens the public
SekolahKu homepage at `/news`, and the article listing is at `/news/news`.
The attendance kiosk remains available at `/`; public pages stay under the
`/news/` prefix. The main EduSync application does not connect to the separate
SekolahKu CMS database.

Super Admin can update the public contact page from **Website Sekolah →
Pengaturan Kontak** in the EduSync panel. Changes to the school address,
telephone, email, and working hours appear on `/news/contact`; this does not
open a separate CMS admin.

The same **Website Sekolah** group includes **Pengaturan Beranda**,
**Pengaturan Profil**, and **Pengaturan Program**. These pages update the
homepage hero, statistics, principal welcome, school profile, and visible
program cards using tables in the EduSync database. The website-settings,
program, and news tables are created empty; no settings, programs, or articles
are copied from the SekolahKu database or inserted as seed data.

The public **SPMB Online** button opens `/news/spmb`. The registration form
collects student, contact, address, previous-school, and parent/guardian data.
It does not request an admission path, report-card scores, parent income,
geotag selfie, SPTJM, or path-specific certificates.
Required documents are limited to the family card, birth certificate, guardian
identity, previous-school certificate, and recent photo; each upload is
limited to 2 MB. Submitted values and private document metadata are stored in
EduSync, while files are kept under `writable/uploads/spmb`. Applicants receive
a PDF registration receipt after submission. Super Admin can review
applications and download their documents from **Website Sekolah → Pendaftar
SPMB**.
CSRF checks remain enabled; tokens stay stable across submissions so a long
registration form is not invalidated by another open form.

Run `php spark migrate` with a database account authorized to create and alter
the website, SPMB, and custom-sidebar tables. The runtime database account
needs `SELECT`, `INSERT`, `UPDATE`, and `DELETE` on the tables it uses,
including `custom_sidebar_items`, and the web server must be allowed to write
to the `writable/uploads/spmb` directory. The runtime account also needs
`SELECT`, `INSERT`, `UPDATE`, and `DELETE` access to `role_action_settings`
and `user_action_settings` for per-role and per-user button permissions.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
