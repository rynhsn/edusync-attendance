# WhatsApp Attendance Notifications

Attendance WhatsApp notifications are sent when the attendance is recorded.
No Windows Task Scheduler or hosting cron job is needed for these
notifications.

## Configure Fonnte

Set the Fonnte endpoint and token in the private application `.env` file.
Keep the token secret and never commit it:

```ini
WHATSAPP_API_URL = "https://api.fonnte.com/send"
WHATSAPP_API_TOKEN = your_fonnte_device_token
```

The application sends to `https://api.fonnte.com/send`. Indonesian phone
numbers beginning with `08` are converted to `62`.

## Configure parent contacts

Create parent/guardian records with WhatsApp numbers in **Master Data > Data
Orang Tua**, then link each student to the correct parent record. One parent
contact can be linked to multiple students by searching and selecting names
on the parent form. The relationship uses `master_siswa.orang_tua_id`, so
student names are sourced from student records instead of duplicated in the
parent table.

## Notification behavior

- Saving a class attendance session sends one message per parent for that
  class, subject, and lesson period, containing only linked students marked
  **Sakit**, **Izin**, or **Alpa**. Students marked **Hadir** are not included.
- Scanning an RFID card during arrival sends a student arrival notification.
  RFID taps recorded as departure do not send a WhatsApp message.
- If sending fails, the attendance remains saved. The **Kirim Ulang WA** button
  on the class attendance session retries failed notifications; notifications
  already accepted by Fonnte are skipped.
- The **Data Absensi Kelas** page also has date and class filters. After
  selecting one date and one class, **Kirim Rekap WhatsApp Kelas** sends one
  manual daily summary to each linked parent contact. The summary includes
  each student's RFID arrival and departure taps and every class-attendance
  subject/period recorded for that date. This is an additional manual send;
  it does not replace or schedule the immediate notifications above.
- A Fonnte accepted response means the message was queued, not confirmed as
  delivered to WhatsApp.

The existing `whatsapp_daily_attendance_log` table is reused for retry
tracking. For a database where that table already exists, run
[`update_whatsapp_daily_attendance_log_for_class.sql`](../update_whatsapp_daily_attendance_log_for_class.sql)
once to add the class-session ID to its unique key. Existing daily log entries
are retained. For a fresh database, use the updated
[`create_whatsapp_daily_attendance_log.sql`](../create_whatsapp_daily_attendance_log.sql).
On deployments using CodeIgniter migrations, run the migration
`AddClassSessionToDailyAttendanceWhatsAppLog` after the log table exists.

Older Windows Task Scheduler entries for `spark whatsapp:attendance-daily` no
longer send daily summaries. Disable or delete that task; notifications are
now sent directly when attendance is recorded.
