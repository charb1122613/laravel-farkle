<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Farkle</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ranchers&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/dice.css'])
    @vite(['resources/js/how-to.js'])
</head>
<body>
    <div class="container">
        <div class="header-container">
            <div class="header-links">
                <a href="#">Return to Portfolio</a>
                <button id="btn-open-how-to">How to Play</button>
            </div>
            <div id="main-title" class="flex justify-center items-center h-64">
                <svg viewBox="0 0 500 100">
                    <path id="text-arch" d="M 50,200 A 220,220 0 0,1 450,200" fill="none" />

                    <text fill="black">
                        <textPath href="#text-arch" startOffset="50%" text-anchor="middle">
                            Farkle!
                        </textPath>
                    </text>
                </svg>
            </div>
        </div>

        <livewire:new-roller/>

        <div class="line-container">
            <div class="vertical-line"></div>
        </div>

        <livewire:scoreboard/>

        <x-scoring-sidebar />

        <x-how-to-play />
    </div>

    <script src=""></script>
</body>
</html>
