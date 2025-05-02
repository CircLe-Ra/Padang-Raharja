<flux:modal class="min-w-[22rem]" :$attributes>
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Konfirmasi</flux:heading>
            <flux:text class="mt-2">
                <p>Kamu yakin akan menghapus data ini?</p>
                <p>Data yang sudah dihapus tidak dapat dikembalikan.</p>
            </flux:text>
        </div>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Batal</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="danger" wire:click="confirmDelete">Hapus</flux:button>
        </div>
    </div>
</flux:modal>
