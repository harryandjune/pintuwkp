<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "admin_kendaraan") { header("location:../login.php"); exit(); }

$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Armada - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- FullCalendar CDN -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0f172a, #1e293b); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        
        #calendar-container {
            background: white;
            padding: 15px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        /* Customizing FullCalendar to Dark Theme */
        .fc { --fc-border-color: #eee; }
        .fc .fc-toolbar-title { font-size: 1rem; font-weight: bold; }
        .fc .fc-button-primary { background-color: #0f172a; border-color: #0f172a; font-size: 0.8rem; border-radius: 8px; }
        .fc .fc-button-primary:hover { background-color: #1e293b; }
        .fc-event { cursor: pointer; border-radius: 6px; padding: 2px 4px; font-size: 0.7rem; border: none !important; }
    </style>
</head>
<body>

    <div class="header-section shadow d-flex align-items-center">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Jadwal Armada</h4>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-2 mb-3">
            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Klik pada jadwal untuk detail perjalanan</small>
        </div>

        <div id="calendar-container">
            <div id='calendar'></div>
        </div>
    </div>

    <!-- Modal Detail Jadwal -->
    <div class="modal fade" id="carModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold mb-0">Detail Perjalanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-4 mb-3">
                        <h6 class="fw-bold text-primary mb-1" id="m-mobil"></h6>
                        <small class="text-muted d-block mb-2" id="m-plat"></small>
                        <hr class="my-2">
                        <small class="d-block"><strong>Peminjam:</strong> <span id="m-unit"></span></small>
                        <small class="d-block"><strong>PIC:</strong> <span id="m-pic"></span></small>
                    </div>
                    <div class="px-2">
                        <p class="mb-1 small"><strong>Tujuan:</strong></p>
                        <p id="m-tujuan" class="small mb-3"></p>
                        
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block">Sopir</small>
                                <span class="badge bg-warning text-dark" id="m-sopir"></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Status</small>
                                <span class="badge bg-success">DISETUJUI</span>
                            </div>
                        </div>
                    </div>
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
                events: 'get_events_kendaraan.php',
                eventClick: function(info) {
                    var props = info.event.extendedProps;
                    
                    document.getElementById('m-mobil').innerText = info.event.title;
                    document.getElementById('m-plat').innerText = "Plat Nomor: " + props.plat;
                    document.getElementById('m-unit').innerText = props.peminjam;
                    document.getElementById('m-pic').innerText = props.pic;
                    document.getElementById('m-tujuan').innerText = props.tujuan;
                    document.getElementById('m-sopir').innerText = props.sopir;
                    
                    var myModal = new bootstrap.Modal(document.getElementById('carModal'));
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