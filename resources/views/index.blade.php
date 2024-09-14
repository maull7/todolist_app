<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>To do list</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Custom CSS */
        body {
            background-color: #f5f5f5;
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        .card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-body {
            background-color: #ffffff;
        }

        .badge {
            font-size: 0.75rem;
        }

        .list-group-item {
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-button {
            cursor: pointer;
            border: none;
            background: none;
            font-size: 0.75rem;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Tambah list tugas
        </button>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Ingin list apa hari ini ?</h5>

            </div>
            <div class="card-body">
                <ul id="task-list" class="list-group mt-3">
                    <!-- Daftar tugas akan diisi di sini -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambahkan tugas</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="task-form">
                        <div class="mb-3">
                            <label for="NamaTugas" class="form-label">Nama tugas</label>
                            <input type="text" class="form-control" name="tugas" id="NamaTugas" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="Waktu" class="form-label">Tenggat Waktu</label>
                            <input type="date" class="form-control" name="tenggat_waktu" id="Waktu"
                                placeholder="name@example.com">
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah Tugas</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            function loadTasks() {
                $.ajax({
                    url: '{{ route('task.index') }}', // Pastikan URL sesuai dengan rute Anda
                    type: 'GET',
                    success: function(response) {
                        $('#task-list').empty(); // Kosongkan daftar tugas

                        // Tambahkan tugas dari response
                        $.each(response, function(index, task) {
                            $('#task-list').append(
                                '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                '<div>' +
                                '<strong>' + task.tugas + '</strong><br>' + // Nama tugas
                                '<small class="text-muted">Tenggat: ' + task.tenggat_waktu +
                                '</small>' + // Tenggat waktu
                                '</div>' +
                                '<div>' +
                                '<span class="badge bg-primary rounded-pill">' + task
                                .status + '</span>' +
                                (task.status === 'belum' ?
                                    '<button class="status-button btn btn-sm btn-outline-primary ms-2" data-id="' +
                                    task.id + '">Tandai selesai</button>' : ''
                                ) +
                                '</div>' +
                                '</li>'
                            );
                        });
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan! ' + xhr.responseText);
                    }
                });
            }
            // Memuat data tugas saat halaman dimuat
            loadTasks();

            // Menambahkan tugas baru
            $('#task-form').on('submit', function(event) {
                event.preventDefault(); // Mencegah submit biasa

                $.ajax({
                    url: '{{ route('task.store') }}', // Pastikan route sudah benar
                    type: 'POST',
                    data: {
                        tugas: $('#NamaTugas').val(),
                        tenggat_waktu: $('#Waktu').val(),
                        _token: $('meta[name="csrf-token"]').attr('content') // Sertakan token CSRF
                    },
                    success: function(response) {
                        swal({
                            title: "Berhasil",
                            text: "Kamu berhasil menambah tugas baru",
                            icon: "success",
                        });
                        $('#exampleModal').modal('hide'); // Menyembunyikan modal
                        $('#task-form')[0].reset(); // Mengosongkan form

                        // Memuat ulang data tugas untuk menampilkan data terbaru
                        loadTasks();
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan! ' + xhr.responseText);
                    }
                });
            });

            // Mengubah status tugas ketika tombol diklik
            $(document).on('click', '.status-button', function() {
                var taskId = $(this).data('id'); // Ambil ID tugas dari atribut data-id

                $.ajax({
                    url: '{{ route('task.updateStatus') }}', // Pastikan route sudah benar
                    type: 'POST',
                    data: {
                        id: taskId,
                        _token: $('meta[name="csrf-token"]').attr('content') // Sertakan token CSRF
                    },
                    success: function(response) {
                        swal({
                            title: "Selamat",
                            text: "Selamat telah mengerjakan tugas anda",
                            icon: "success",
                        });
                        // Memuat ulang data tugas untuk menampilkan data terbaru
                        loadTasks();
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan! ' + xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>
