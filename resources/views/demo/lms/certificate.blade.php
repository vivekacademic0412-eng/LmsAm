<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
body{
    font-family: DejaVu Sans, sans-serif;
    margin:0;
    padding:0;
}

.certificate{
    width:100%;
    border:12px solid #1e40af;
    padding:40px;
    text-align:center;
    box-sizing:border-box;
}

.logo{
    margin-bottom:20px;
}

.logo img{
    width:120px;
}

.title{
    font-size:42px;
    font-weight:bold;
    color:#1e40af;
}

.subtitle{
    font-size:18px;
    letter-spacing:4px;
    margin-bottom:30px;
}

.student-name{
    font-size:36px;
    font-weight:bold;
    margin:25px 0;
}

.course-name{
    font-size:26px;
    color:#2563eb;
    font-weight:bold;
    margin-top:20px;
}

.footer{
    margin-top:60px;
    width:100%;
}

.footer-table{
    width:100%;
}

.footer-table td{
    text-align:center;
    padding-top:20px;
}

.line{
    border-top:1px solid #000;
    width:180px;
    margin:auto;
    margin-bottom:8px;
}
</style>
</head>

<body>

<div class="certificate">

    <div class="logo">
        <img src="{{ public_path('logo.png') }}">
    </div>

    <div class="title">
        CERTIFICATE
    </div>

    <div class="subtitle">
        OF COMPLETION
    </div>

    <p>This certificate is proudly awarded to</p>

    <div class="student-name">
        {{ auth()->user()->name }}
    </div>

    <p>For successfully completing</p>

    <div class="course-name">
        {{ $course->title }}
    </div>

    <table class="footer-table">
        <tr>
            <td>
                <div class="line"></div>
                Date<br>
                {{ now()->format('d M Y') }}
            </td>

            <td>
                <div class="line"></div>
                Certificate ID<br>
                CERT-{{ auth()->id() }}-{{ $course->id }}
            </td>

            <td>
                <div class="line"></div>
                Authorized Signature
            </td>
        </tr>
    </table>

</div>

</body>
</html>