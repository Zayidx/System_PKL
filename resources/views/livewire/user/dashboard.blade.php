<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Selamat Datang!</h4>
        </div>
        <div class="card-body">
            <p>Halo, <strong>{{ $user->username }}</strong>! Senang melihat Anda kembali.</p>
            
            <p>Berikut adalah detail akun Anda yang tersimpan di sistem:</p>
            <ul>
                <li><strong>Nama Lengkap:</strong> {{ $user->username }}</li>
                <li><strong>Alamat Email:</strong> {{ $user->email }}</li>
            </ul>

            <div class="mt-4 alert alert-danger">
                <h4 class="alert-heading">Peringatan Keamanan!</h4>
                <p>
                    <strong>Untuk Tujuan Pembelajaran:</strong> Di bawah ini adalah password Anda yang sudah di-hash (dienkripsi).
                    Menampilkan informasi ini di aplikasi sungguhan adalah **risiko keamanan yang sangat besar**. Jangan pernah lakukan ini di proyek nyata.
                </p>
                <hr>
                <p class="mb-0">
                    <strong>Password Hash:</strong> <small style="word-break: break-all;">{{ $user->password }}</small>
                </p>
            </div>
        </div>
    </div>
</div>
