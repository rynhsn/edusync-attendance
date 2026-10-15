# WhatsApp Notifications for Attendance

Class attendance and RFID tap notifications use the Fonnte send-message API.
Attendance is saved even if WhatsApp is not configured or a message cannot be
sent. Class attendance reports successful API requests, while the RFID kiosk
response reports whether Fonnte accepted the notification request.

## Configure the application environment

Set the Fonnte API endpoint and token in the private application `.env` file.
Keep the token secret and never commit it:

```ini
WHATSAPP_API_URL = "https://api.fonnte.com/send"
WHATSAPP_API_TOKEN = your_fonnte_device_token
```

Get the token from the Fonnte dashboard for the connected sender device. The
API URL must be `https://api.fonnte.com/send`; a configured base URL of
`https://fonnte.com` is automatically normalized to that endpoint.

The app sends a personalized message to the student's linked parent/guardian
when class attendance is saved and after each valid RFID arrival/departure tap.
The RFID message includes the tap type, class, date, and time. A Meta-approved
template is not needed for this provider. The sending account/device must be
active in Fonnte. An accepted request means queued by Fonnte, not confirmed
delivery to WhatsApp.

## Add and link parent contacts

Create parent/guardian records and WhatsApp
numbers in **Master Data > Data Orang Tua**, then select the corresponding
contact in the student's add/edit form. One parent record can be selected for
multiple siblings. Indonesian numbers beginning with `08` are converted to
`62` when sending.

After configuring the Fonnte token, retry a previously saved class attendance
session using **Kirim Ulang WA**. RFID tap notifications are sent automatically
after a valid tap is recorded; the kiosk response includes the notification
request status.
