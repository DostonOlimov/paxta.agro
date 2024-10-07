@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

<div id="invoice-cheque" class="py-4 col-12 {{ $classes ?? '' }}" style=" font-family: Times New Roman;">

        <h3 style="text-align: center">O'zbekiston Respublikasi Qishloq xo'jaligi vazirligi huzuridagi Agrosanoat majmui ustidan nazorat qilish<br>
            Inspeksiyasi  qoshidagi “Qishloq xo‘jaligi mahsulotlari sifatini baholash markazi” davlat muassasasi</h3>
    <h2 class="text-center">SIFAT SERTIFIKATI</h2>
    <h3 class="text-center">filiali Paxta mahsulotlari sinov laboratoriyasi</h3>
    <h3 class="text-left">Berildi: {{$test->date}}</h3>
    <h3 class="text-center">{{$test->crops->name->name}} - {{$test->crops->kodtnved}}</h3>
    <h3 class="text-left">Ishlab chiqaruvchi - {{$test->organization->name}}</h3>
    <h3 class="text-left">Manzil - {{$test->organization->full_address}}</h3>
    <h3 class="text-left">filiali Paxta mahsulotlari sinov laboratoriyasi</h3>
    </div>

<script>
    function printCheque() {
        $('#invoice-cheque').print({
            NoPrintSelector: '.no-print',
            title: '',
        })
    }
</script>
