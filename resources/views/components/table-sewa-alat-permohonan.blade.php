<div class="col-span-2 overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
    <div x-data="{
        showModalPermohonan: false,
        expanded: false,
        showModalBatalPermohonan: false,
        data: { id: null, namaAlat: null, tanggalSewa: null, unit: null, status: null, total: null, expedisi: null, resi: null },
        action: null,
        download: null,
    }" class="flex flex-col h-full p-6 text-gray-900 dark:text-gray-100">
        <h2 class="flex items-center mb-4 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Permohonan Anda
        </h2>

        @if ($permohonan->isEmpty())
            <div class="grid flex-1 place-content-center">
                <img src="{{ asset('images/alat-tidak-tersedia.svg') }}" alt="" width="200">
                <p>Belum ada permohonan</p>
            </div>
        @else
            <div class="w-full -mr-6 overflow-x-auto">
                <table class="w-full overflow-hidden rounded table-auto text-slate-600 dark:text-slate-400">
                    <thead class="border-b bg-slate-100 dark:bg-slate-900 border-b-slate-300 dark:border-b-slate-500">
                        <tr>
                            <th class="p-3 text-left">Barang</th>
                            <th class="p-3 text-left">Tanggal Sewa</th>
                            <th class="p-3 text-left">Unit</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permohonan as $item)
                            @php
                                $alat_dipilih = $alats->firstWhere('id', $item->alat_id);

                                $date1 = new DateTime($item->sewa_mulai);
                                $date2 = new DateTime($item->sewa_berakhir);
                                $diff = $date1->diff($date2)->days;
                                $lama_sewa = $diff == 0 ? 1 : $diff;

                                
                                $total = 'Rp' . number_format($item->alat->harga * ($lama_sewa * $item->banyak_unit), 0, ',', '.');
                                $tagihan = 'Rp' . number_format($item->alat->harga * ($lama_sewa * $item->banyak_unit) + $item->id, 0, ',', '.');
                                $invoice = 'INV#'.sprintf('%04d',$item->id);
                                $batasbayar =  date("d/m/Y H:i",time() + 1800);
                            @endphp
                            <tr class="transition duration-200 border-b hover:cursor-pointer border-b-slate-300 dark:border-b-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700"
                                @click="
                                            showModalPermohonan = true;
                                            expanded = false;
                                            data.id = `{{ $item->id }}`;
                                            data.namaAlat = `{{ $item->alat->nama }}`;
                                            data.tanggalSewa = `{{ $item->sewa_mulai }} s/d {{ $item->sewa_berakhir }}`;
                                            data.unit = `{{ $item->banyak_unit }}`;
                                            data.status = `{{ $item->status }}`;
                                            data.total = `{{ $total }}`;
                                            data.tagihan = `{{ $tagihan }}`;
                                            data.invoice = `{{ $invoice }}`;
                                            data.batasbayar = `{{ $batasbayar }}`;
                                            action = `{{ route('sewa-alat.destroy', ['sewa_alat' => $item]) }}`;
                                            download = `{{ route('sewa-alat.download-permohonan', ['sewa_alat' => $item]) }}`;

                                            @if ($item->expedisi != null && $item->resi != null) data.expedisi = `{{ $item->expedisi }}`;
                                                data.resi = `{{ $item->resi }}`; @endif
                                        ">
                                <td class="p-3 align-top max-w-[200px]">
                                    {{ $item->alat->nama }}
                                </td>
                                <td class="p-3 align-top">
                                    <span>{{ \Carbon\Carbon::parse($item->sewa_mulai)->format('d/m/Y') }}</span>
                                    -
                                    <span>{{ \Carbon\Carbon::parse($item->sewa_berakhir)->format('d/m/Y') }}</span>
                                </td>
                                <td class="p-3 align-top">
                                    {{ $item->banyak_unit }} unit
                                </td>
                                <td class="p-3 align-top">
                                    <span class="font-bold text-yellow-500">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="p-3 font-bold align-top dark:text-white">
                                    {{ $total }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Modal -->
        <div class="fixed inset-0 z-30 overflow-auto bg-black bg-opacity-50" x-show="showModalPermohonan"
            x-transition.opacity x-cloak>

            <!-- Modal inner -->
            <div class="w-full max-w-sm p-6 mx-auto mt-20 mb-10 text-left bg-white rounded-lg shadow-lg dark:bg-slate-800"
                @click.away="showModalPermohonan = false" x-transition>
                <!-- Title / Close-->
                <div class="flex items-center justify-between mb-5">
                    <h5 class="mr-3 font-bold max-w-none">Detail Permohonan</h5>

                    <button type="button" class="z-50 cursor-pointer" @click="showModalPermohonan = false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- content -->
                <div class="modal-content">
                    <dl class="grid grid-cols-2 gap-y-3">
                        <dt class="text-sm text-slate-500">Nama Alat</dt>
                        <dd x-text="data.namaAlat"></dd>

                        <dt class="text-sm text-slate-500">Tanggal Sewa</dt>
                        <dd x-text="data.tanggalSewa"></dd>

                        <dt class="text-sm text-slate-500">Jumlah Unit</dt>
                        <dd x-text="data.unit"></dd>

                        <dt class="text-sm text-slate-500">Status</dt>
                        <dd x-text="data.status"></dd>

                        <dt class="text-sm text-slate-500">Total Biaya</dt>
                        <dd x-text="data.total"></dd>

                        <dt class="text-sm text-slate-500">File Permohonan</dt>
                        <dd>
                            <a :href="download" class="font-bold text-green-500 hover:text-green-700">
                                Download
                                <i class="fa-solid fa-file-arrow-down"></i>
                            </a>
                        </dd>

                        <div x-show="data.expedisi">
                            <dt class="text-sm text-slate-500">Pengiriman</dt>
                            <dd>
                                <p x-text="data.expedisi"></p>
                                <p x-text="data.resi"></p>
                            </dd>
                        </div>
                    </dl>

                    <div x-show="data.status = `Belum Lunas`" @click="expanded = ! expanded"
                    class="p-3 mt-5 text-yellow-800 bg-yellow-200 rounded hover:cursor-pointer">
                    <h3 class="block font-bold">Panduan
                        Pembayaran:</h3>
                    <p x-show="expanded" x-collapse>
                        Untuk memudahkan proses pembayaran, berikut adalah panduan pembayaran kami:
                        <br><br>
                        <b>1. Metode Pembayaran:</b><br>
                        Transfer Bank:<br>
                        Nama Bank: Mandiri<br>
                        Nomor Rekening: 137001521521<br>
                        Nama Pemilik Rekening: Wuri Handayani<br><br>
                        <b>2. Detail Tagihan:</b><br>
                        Nomor Invoice/Faktur:<br>
                        <span x-text="data.invoice"></span><br>
                        Jumlah Pembayaran:<br>
                        <span x-text="data.tagihan"></span><br><br>
                        <b>3. Batas Waktu Pembayaran:</b><br>
                        Pembayaran diharapkan diterima paling lambat pada <span x-text="data.batasbayar"></span>.<br> Jika pembayaran tidak diterima pada waktu yang ditentukan, pesaan akan dibatalkan secara otomatis.<br><br>
                        <b>4. Konfirmasi Pembayaran:</b><br>
                        Mohon konfirmasikan pembayaran Anda dengan mengirimkan bukti transfer atau konfirmasi pembayaran ke stageof.yogya@bmkg.go.id.<br><br>
                        <b>5. Hubungi Kami:</b><br>
                        Jika Anda memiliki pertanyaan atau memerlukan bantuan lebih lanjut, jangan ragu untuk menghubungi tim kami melalui [Nomor Telepon] atau stageof.yogya@bmkg.go.id.<br>
                        Terima kasih atas kerjasama Anda! Kami berharap dapat terus memberikan layanan terbaik untuk Anda.</p>
                </div>

                    <button type="button" @click="showModalPermohonan = false; showModalBatalPermohonan = true"
                        class="w-full p-3 mt-5 text-center text-white uppercase bg-red-400 rounded hover:bg-red-500 ms-auto">Batal
                        Permohonan</button>
                </div>
            </div>
        </div>
        <!-- /Modal -->

        <!-- Modal Confirm Delete -->
        <div class="fixed inset-0 z-30 overflow-auto bg-black bg-opacity-50" x-show="showModalBatalPermohonan"
            x-transition.opacity x-cloak>

            <!-- Modal inner -->
            <div class="w-full max-w-sm p-6 mx-auto mt-20 mb-10 text-left bg-white rounded-lg shadow-lg dark:bg-slate-800"
                @click.away="showModalBatalPermohonan = false" x-transition>
                <!-- Title / Close-->
                <div class="flex items-center justify-between mb-5">
                    <h5 class="mr-3 font-bold max-w-none">Batalkan Permohonan</h5>

                    <button type="button" class="z-50 cursor-pointer" @click="showModalBatalPermohonan = false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- content -->
                <div class="modal-content">
                    <p>Anda yakin akan membatalkan permohonan?</p>

                    <form :action="action" method="post" class="flex gap-3 w-full *:flex-1 mt-5">
                        @csrf @method('delete')

                        <button type="button" class="p-3 rouded"
                            @click="showModalBatalPermohonan = false">Tidak</button>
                        <button type="submit"
                            class="p-3 text-center text-white bg-red-400 rounded hover:bg-red-500">Ya,
                            Batalkan</butt>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Modal Confirm Delete -->
    </div>
</div>
