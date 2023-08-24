
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 2px
        }
        .flex {
            display: flex;
            width: 100%;
            flex-wrap: wrap;
        }
        .box {
            border: 2px solid black;
            text-align: center;
            padding: 0 5px;
            margin: 5px;
            /* width:370px; */
            width:360px;
            
            float:left;
            min-height:170px
        }
        .box .top {
            border-bottom: 1px solid black
        }
        h3 {
            font-size: 14px;
            margin-bottom: 3px;
        }
        .text-left {
            text-align:left
        }
        .clear {
            clear:both;
        }
        table.table tr td {
            /* padding: 10px; */
            height: 45px;
        }
        @media print {
            .pagebreak {
                clear: both;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="">
        @php 
            $page_break = 1;
            $lipat = 4;
        @endphp 
        @foreach($entry->bookStock as $book)
            @for($i = 1;$i <= $book->qty; $i++)
                <div class="box">
                    <div class="top">
                        <h3>PERPUSTAKAAN</h3>
                        <h3>SEKOLAH KRISTEN BASIC</h3>
                    </div>
                    <div class="bottom">
                        <h3 style="margin-top: 10px; margin-bottom: 15px">DAFTAR PEMINJAMAN & PENGEMBALIAN BUKU</h3>
                        <div class="text-left">
                            <!-- <table width="100%"> -->
                            <table>
                                <tr>
                                    <td>
                                        Judul Buku 
                                    </td>
                                    <td>:</td>
                                    <td>
                                        {{$book->book->book_name}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Lokasi Buku
                                    </td>
                                    <td>:</td>
                                    <td>
                                        @if($entry->book_location_name != '') {{$entry->book_location_name}} @else - @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Tahun
                                    </td>
                                    <td>:</td>
                                    <td>
                                        {{date('Y')}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                       <table class="table" border width="100%">
                           <thead>
                               <th>Nama Anggota</th>
                               <th>Tgl Pinjam</th>
                               <th>Wajib Kembali</th>
                               <th>Tgl Kembali</th>
                               <th>Denda (hari)</th>
                           </thead>
                           <tbody>
                               <tr><td></td><td></td><td></td><td></td><td></td></tr>
                               <tr><td></td><td></td><td></td><td></td><td></td></tr>
                               <tr><td></td><td></td><td></td><td></td><td></td></tr>
                               <tr><td></td><td></td><td></td><td></td><td></td></tr>
                               <tr><td></td><td></td><td></td><td></td><td></td></tr>
                           </tbody>
                       </table>
                    </div>
                </div>
                @if($page_break % $lipat == 0)<div class="pagebreak"> </div>@endif
                @php $page_break++; @endphp
            @endfor
        @endforeach
        <div class="clear"></div>
    </div>
</body>
</html>