<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Marquee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.marquee/1.5.0/jquery.marquee.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f0f0f0;
        }

        .marquee-container {
            width: 80%;
            height: 80%;
            overflow: hidden;
            position: relative;
        }

        .marquee-content {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .marquee-item {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            color: #fff;
            background: #007aff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 10px;
            height: calc(100% / 10);
            /* Ensure each item takes up 1/10th of the container height */
        }
    </style>
</head>

<body>
    <div class="marquee-container">
        <div class="marquee-content" id="marqueeContent">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="marquee-item">Slide <?= $i ?></div>
            <?php endfor; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.marquee@1.6.1/jquery.marquee.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#marqueeContent').marquee({
                duration: 30000, // Durasi scroll dalam milidetik
                gap: 0,
                delayBeforeStart: 0,
                direction: 'up',
                duplicated: true,
                pauseOnHover: true
            });
        });
    </script>
</body>

</html>