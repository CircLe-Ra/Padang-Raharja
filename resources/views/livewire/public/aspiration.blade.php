<?php

use App\Models\Aspiration;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Layout('components.layouts.public')]
#[\Livewire\Attributes\Title('Halaman Aspirasi Masyarakat')]
class extends Component {
    use WithPagination, WithFileUploads;

    public array $faqs = [
        [
            'question' => 'Apa itu Halaman Aspirasi Masyarakat?',
            'answer' => 'Halaman ini adalah platform digital untuk warga menyampaikan keluhan, saran, atau ide terkait pembangunan wilayah, layanan publik, atau masalah sosial. Aspirasi akan ditindaklanjuti oleh pihak berwenang secara transparan.'
        ],
        [
            'question' => 'Bagaimana cara mengajukan aspirasi?',
            'answer' => '1. Klik tombol "Ajukan Aspirasi Baru"<br>
                         2. Isi formulir dengan:<br>
                          - Pilih kategori (infrastruktur, kesehatan, dll.)<br>
                          - Judul singkat (contoh: "Permintaan perbaikan jalan di RT 05")<br>
                          - Deskripsi lengkap (Masukan nama, jelaskan lokasi, kondisi, dan harapan)<br>
                          - Upload bukti pendukung (foto/dokumen)<br>
                         3. Submit formulir. Anda akan mendapat notifikasi konfirmasi.'
        ],
        [
            'question' => 'Bisakah saya mengajukan aspirasi secara anonim?',
            'answer' => '✅ <strong>Ya</strong>. Centang opsi "Sampaikan sebagai anonim" saat mengisi formulir. Namun, jika membutuhkan follow-up, disarankan mencantumkan kontak (email/telepon).'
        ],
        [
            'question' => 'Berapa lama waktu proses aspirasi?',
            'answer' => '- <strong>Tahap review</strong>: 1-3 hari kerja (aspirasi diverifikasi oleh admin)<br>
                        - <strong>Tindak lanjut</strong>: 7-14 hari kerja (tergantung kompleksitas masalah)<br>
                        Anda bisa pantau status di kolom "Status" (Menunggu/Dalam Review/Selesai/Ditolak)'
        ],
        [
            'question' => 'Bagaimana cara melacak perkembangan aspirasi saya?',
            'answer' => '- Jika terdaftar sebagai pengguna, cek di <strong>dashboard akun</strong> Anda<br>
                        - Jika anonim, gunakan <strong>nomor tiket</strong> yang dikirim via email/SMS<br>
                        - Fitur <strong>"Cari Aspirasi"</strong> bisa digunakan dengan memasukkan judul/kategori'
        ],
        [
            'question' => 'Mengapa aspirasi saya ditolak?',
            'answer' => 'Aspirasi mungkin ditolak jika:<br>
                        - Tidak memenuhi syarat (contoh: mengandung SARA, hoaks, atau duplikat)<br>
                        - Data tidak lengkap (lokasi tidak jelas, deskripsi terlalu singkat)<br>
                        - Diluar kewenangan instansi terkait<br>
                        🔔 Anda akan mendapat notifikasi beserta alasan penolakan'
        ],
        [
            'question' => 'Apa perbedaan status "Publik" dan "Privat"?',
            'answer' => '- <strong>Publik</strong>: Aspirasi bisa dilihat semua orang di halaman daftar<br>
                        - <strong>Privat</strong>: Hanya admin dan Anda yang bisa melihat detailnya<br>
                        🔒 Status bisa diubah saat mengajukan atau edit aspirasi (jika bukan anonim)'
        ],
        [
            'question' => 'Siapa yang bisa menanggapi aspirasi?',
            'answer' => '- <strong>Admin/Staff</strong>: Memberikan tanggapan resmi dan mengubah status<br>
                        - <strong>Masyarakat</strong>: Bisa memberi komentar jika aspirasi berstatus "Publik"<br>
                        Tanggapan resmi akan muncul di bagian "Riwayat Diskusi"'
        ],
        [
            'question' => 'Apa syarat aspirasi yang bisa diajukan?',
            'answer' => '- Bersifat <strong>konstruktif</strong> dan <strong>spesifik</strong><br>
                        - Dilengkapi bukti pendukung (foto/link dokumen) jika ada<br>
                        - Tidak mengandung unsur pelecehan atau ujaran kebencian'
        ],
        [
            'question' => 'Bagaimana jika aspirasi saya mendesak?',
            'answer' => 'Gunakan kategori <strong>"Penting/Darurat"</strong> dan tambahkan penjelasan di deskripsi. Tim prioritas akan meninjau lebih cepat.'
        ]
    ];

    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public $show = 10;
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $search = '';
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $status = '';

    public bool $showFormModal = false;
    public bool $showResponseModal = false;
    public bool $showDeleteModal = false;

    public $aspirationId;
    public string $title = '';
    public string $description = '';
    public string $category = '';
    public string $location = '';
    public string $name = '';
    public string $contact = '+62';
    public bool $isAnonymous = false;
    public bool $isPublic = true;
    public string $response = '';
    public array $images = [];

    public bool $personalDataStatus;

    public Collection $categories;

    public function mount(): void
    {
        $this->categories = collect([
            'infrastructure' => 'Infrastruktur',
            'health' => 'Kesehatan',
            'education' => 'Pendidikan',
            'environment' => 'Lingkungan',
            'public_service' => 'Pelayanan Publik',
            'other' => 'Lainnya',
        ]);

        $this->isAnonymous =  auth()->guest();

        $this->personalDataStatus = \App\Models\PersonalData::where('user_id', auth()->user()->id)->count() ?? false;
    }

    public function resetBagAndField(): void
    {
        $this->resetValidation();
        $this->reset(['aspirationId', 'title', 'description', 'category', 'location', 'name', 'contact', 'isPublic', 'response', 'images']);
        $this->dispatch('pond-reset');
    }

    public function store(): void
    {
            $validated = $this->validate([
                'name' => [$this->isAnonymous ? 'required' : 'nullable', 'string', 'max:100'],
                'contact' => [$this->isAnonymous ? 'required' : 'nullable', 'string', 'max:100'],
                'title' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string'],
                'category' => ['required', 'in:infrastructure,health,education,environment,public_service,other'],
                'location' => ['required', 'string', 'max:100'],
                'isAnonymous' => ['required', 'boolean'],
                'isPublic' => ['required', 'boolean'],
                'images' => ['required', 'array', 'max:5'],
                'images.*' => ['required', 'image', 'max:2048'],
            ]);
        try{
            $user_id = $this->isAnonymous ? null : auth()->user()->id;
            $aspiration = Aspiration::create([
                'user_id' => $user_id,
                'name' => $validated['name'],
                'contact' => $validated['contact'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'location' => $validated['location'],
                'is_anonymous' => $validated['isAnonymous'],
                'is_public' => $validated['isPublic'],
            ]);

            foreach ($validated['images'] as $image) {
                $aspiration->images()->create([
                    'aspiration_id' => $aspiration->id,
                    'image' => $image->store('aspiration'),
                ]);
            }

            $this->resetBagAndField();
            Flux::modal('aspiration-modal')->close();
            $this->dispatch('toast', message: 'Aspirasi berhasil diajukan');
            if (!$this->isAnonymous) {
                $this->redirect(route('toView'), navigate: true);
            }
        } catch (Exception $e) {
            Flux::modal('aspiration-modal')->close();
            $this->dispatch('toast', type: 'error', message: 'Aspirasi gagal diajukan ' . $e->getMessage());
//            dd($e->getMessage());
        }


    }


}; ?>

<div class="mt-2">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-6">
        <div class="lg:col-span-4">
            <x-accordion
                :items="$faqs"
                multiple="true"
                active-key="0"
            />
        </div>
        <div class="lg:col-span-2">
            <div class="p-6 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-900">
                <flux:heading size="xl" level="2">Cari Aspirasi</flux:heading>
                <flux:subheading class="mb-4">Selahkah masuk nomor tiket aspirasi yang telah dikirimkan melalui
                    Email\WhatsApp.
                </flux:subheading>
                <div class="mt-4 space-y-4">
                    <form wire:submit="findAspirations">
                        <flux:input wire:model="findAspiration" placeholder="Cari Aspirasi..."/>
                        <flux:button class="mt-4 w-full" variant="primary" type="submit">Cari</flux:button>
                    </form>
                </div>
            </div>
            <flux:separator class="my-6" text="Atau"/>
            <flux:modal.trigger name="aspiration-modal">
                <flux:button class=" w-full" variant="primary" wire:click="showFormModal = true">Ajukan Aspirasi Baru
                </flux:button>
            </flux:modal.trigger>
            @guest
                <div class="card-wrapper h-[325px] mt-6">
                    <div class="card-content p-6 text-justify">
                        <flux:heading size="lg" level="2" class="flex items-center mb-2 ">
                            <flux:icon.information-circle class="me-3"/>
                            Informasi Penting
                        </flux:heading>
                        <flux:text class="text-base">Anda dapat mengajukan aspirasi tanpa perlu login terlebih dahulu.
                            Namun, dengan login, aspirasi Anda akan diproses lebih cepat karena kami dapat memverifikasi
                            data diri Anda secara lengkap. <a href="{{ route('login') }}" wire:navigate
                                                              class="text-blue-500 hover:underline">Login Sekarang</a>
                            untuk pengajuan yang lebih prioritas.
                        </flux:text>
                        <flux:text class="text-base">Belum memiliki akun? Segera <a href="{{ route('register') }}"  wire:navigate class="text-blue-500 hover:underline">daftarkan diri anda</a> untuk mendapatkan kemudahan dalam pengajuan aspirasi dan prioritas peninjauan. Proses pendaftaran cepat dan gratis!
                        </flux:text>
                    </div>
                </div>
            @endguest
            @auth
                @if(!$this->personalDataStatus)
                    <div class="card-wrapper h-[325px] mt-6">
                        <div class="card-content p-6 text-justify">
                            <flux:heading size="lg" level="2" class="flex items-center mb-2 ">
                                <flux:icon.information-circle class="me-3"/>
                                Informasi Penting
                            </flux:heading>
                            <flux:text class="text-base">Kamu belum melengkapi data diri. Silahkan kunjungi halaman <a href="{{ route('personal-data') }}" wire:navigate class="text-blue-500 hover:underline">Data Pribadi</a> agar kamu dapat melengkapi data diri anda.
                            </flux:text>
                            <flux:text class="text-base mt-3">Diharapkan kamu melengkapi data diri sebelum melakukan pengajuan aspirasi. Terimasih atas kepercayaan anda kepada kami.
                            </flux:text>
                        </div>
                    </div>
                @endif
            @endauth

        </div>
        <flux:modal name="aspiration-modal" class="md:w-96" @close="resetBagAndField" variant="flyout">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Ajukan Aspirasi Baru</flux:heading>
                    <flux:text class="mt-2">Ajukan aspirasi baru disini.</flux:text>
                </div>
                <form wire:submit="store" class="space-y-4">
                    <flux:checkbox.group wire:model="subscription" label="Tentang Aspirasi">
                        <div
                            class="border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-900 p-4 space-y-4">
                            <flux:checkbox disabled wire:model="isAnonymous" label="Anonim" description="Jika anda mengajukan aspirasi tanpa login, maka data anda akan dianggap anonim."/>
                            <flux:checkbox wire:model="isPublic" label="Publik" description="Jika anda menandai ini, maka aspirasi akan ditampilkan ke semua orang."/>
                        </div>
                    </flux:checkbox.group>
                    @guest
                        <flux:input label="Nama" wire:model="name" autocomplete="off"/>
                        <flux:input label="Nomor Telepon" wire:model="contact" type="phone" mask="+99 999-9999-9999" value="62" />
                    @endguest
                    @auth
                        @if(!$this->personalDataStatus)
                            <flux:input label="Nama" wire:model="name" autocomplete="off" required/>
                            <flux:input label="Nomor Telepon" wire:model="contact" type="phone" required mask="+99 999-9999-9999" value="62" />
                        @endif
                    @endauth
                    <flux:select
                        wire:model.live="category"
                        label="Kategori">
                        <flux:select.option value="">Pilih?</flux:select.option>
                        @foreach($this->categories as $key => $category)
                            <flux:select.option value="{{ $key }}">{{ $category }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input label="Judul Aspirasi" wire:model="title" autocomplete="off"/>
                    <flux:input label="Lokasi" wire:model="location" autocomplete="off"/>
                    <flux:textarea rows="6" label="Isi Aspirasi" wire:model="description" type="textarea" autocomplete="off"/>
                    <x-filepond label="Foto / Dokumentasi" wire:model="images" multiple/>
                    <div class="flex">
                        <flux:spacer/>
                        <flux:button type="submit" variant="primary">Simpan</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </div>

    <x-confirm wire:model.self="showDeleteModal"/>
</div>
