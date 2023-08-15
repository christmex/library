
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
            font-size: 10px;
            margin: 0;
            padding: 2px
        }
        .flex {
            display: flex;
            width: 100%;
            flex-wrap: nowrap;

            flex-direction: column;
            /* justify-content: space-around;
            align-content: space-around; */
            min-height:170px;
        }
        .flex > div{
            flex: 1
        }
        .flex > .bottom{
            margin-top: auto;
        }
        .box {
            border: 2px solid black;
            text-align: center;
            padding: 0 5px;
            margin: 1px;
            width:160px;

            float:left;
            /* min-height:100% */
            min-height:170px;
        }
        .box .top {
            border-bottom: 2px solid black;
            flex: 0;
        }
        h3 {
            font-size: 10px;
            margin-bottom: 3px;
        }
        .clear {
            clear:both;
        }
        @media print {
            .pagebreak {
                clear: both;
                page-break-after: always;
            }
            a
            {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <a href="/books">Kembali</a>
    <div class="">
        @php 
            $page_break = 1;
            $lipat = 20;
        @endphp 
            @for($i = 1;$i <= $qty; $i++)
                <div class="box">
                    <div class="flex">
                        <div class="top">
                            <h3>PERPUSTAKAAN</h3>
                            <p>SEKOLAH KRISTEN BASIC</p>
                        </div>
                        <div class="middle">
                            <h2 style="color:red">{{$entry->book->book_name}}</h2>
                        </div>
                        <div class="bottom">
                            <h3>@if($entry->bookLocation->book_location_name != '') {{$entry->bookLocation->book_location_name}} @else - @endif</h3>
                            <h3>@if($entry->bookLocation->book_location_label != '') {{$entry->bookLocation->book_location_label}} @else - @endif</h3>
                        </div>
                    </div>
                    
                </div>
                @if($page_break % $lipat == 0)<div class="pagebreak"> </div>@endif
                @php $page_break++; @endphp
            @endfor
        <div class="clear"></div>
    </div>
</body>
</html>