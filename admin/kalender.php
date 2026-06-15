<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] != "admin") {
    header("location:../login.php");
    exit();
}

$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Booking - PINTU WKP</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- FullCalendar CDN -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            padding-bottom: 100px;
        }

        .header-section {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        #calendar-container {
            background: white;
            padding: 15px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        /* Customize FullCalendar */
        .fc {
            --fc-border-color: #eee;
            --fc-button-bg-color: #1e293b;
            --fc-button-border-color: #1e293b;
        }

        .fc .fc-toolbar-title {
            font-size: 1.1rem;
            font-weight: bold;
        }

        .fc .fc-button {
            font-size: 0.8rem;
            border-radius: 10px;
        }

        .fc-event {
            border-radius: 5px;
            padding: 2px 5px;
            font-size: 0.75rem;
            border: none !important;
        }
    </style>
</head>

<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Sebaran Booking</h4>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-2 mb-3 d-flex justify-content-between align-items-center">
            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Klik event untuk detail</small>
            <div class="d-flex gap-2">
                <small><i class="bi bi-circle-fill text-info" style="font-size: 10px;"></i> GH</small>
                <small><i class="bi bi-circle-fill text-primary" style="font-size: 10px;"></i> MR</small>
            </div>
        </div>

        <div id="calendar-container">
            <div id='calendar'></div>
        </div>
    </div>

    <!-- Modal Detail Event -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Detail Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalBody" class="small"></p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                events: 'get_events.php',
                eventClick: function(info) {
                    var props = info.event.extendedProps;

                    // Set Judul Modal
                    document.getElementById('modalTitle').innerText = props.ruangan;

                    // Set Isi Modal
                    var html = "<div class='p-2'>" +
                        "<p class='mb-1'><strong>Institusi:</strong> " + props.pemohon + "</p>" +
                        "<p class='mb-1'><strong>PIC:</strong> " + props.pic + "</p>" +
                        "<p class='mb-1'><strong>Keperluan:</strong> " + props.keperluan + "</p>" +
                        "<hr class='my-2'>" +
                        "<p class='mb-0'><strong>Status:</strong> <span class='badge " +
                        (props.status === 'SELESAI' ? 'bg-secondary' : 'bg-success') +
                        "'>" + props.status + "</span></p>" +
                        "</div>";

                    document.getElementById('modalBody').innerHTML = html;

                    var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
                    myModal.show();
                },
                height: 'auto'
            });
            calendar.render();
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>