{{-- Cangkang modal Alpine. $state adalah nama variabel boolean di x-data induknya,
     mis. <x-modal state="hapusOpen" title="Hapus Banjar">. Isi (body & tombol)
     diserahkan ke slot supaya tiap modal bebas memakai form sendiri.

     Pakai :scrollable="true" untuk modal panjang yang perlu digulir. --}}
@props(['state', 'title', 'scrollable' => false])

<div x-show="{{ $state }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-leaf-900/45 p-6">
    <div @click.outside="{{ $state }} = false"
        class="w-full max-w-[480px] rounded-2xl bg-white{{ $scrollable ? ' max-h-[88vh] overflow-y-auto' : '' }}">
        <div class="flex items-center justify-between border-b border-line px-[22px] py-5">
            <h3 class="font-display text-[17px] font-semibold text-leaf-900">{{ $title }}</h3>
            <button type="button" @click="{{ $state }} = false"
                class="h-[30px] w-[30px] rounded-full bg-paper text-base leading-none text-ink-soft">&times;</button>
        </div>

        {{ $slot }}
    </div>
</div>
