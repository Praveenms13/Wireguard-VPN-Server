<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Praveen's VPN</title>
    <style>
         *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #161616;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        /* block-$ */
        
        .block-effect {
            font-size: calc(8px + 6vw);
        }
        
        .block-reveal {
            --t: calc(var(--td) + var(--d));
            color: transparent;
            padding: 4px;
            position: relative;
            overflow: hidden;
            animation: revealBlock 0s var(--t) forwards;
        }
        
        .block-reveal::after {
            content: '';
            width: 0%;
            height: 100%;
            padding-bottom: 4px;
            position: absolute;
            top: 0;
            left: 0;
            background: var(--bc);
            animation: revealingIn var(--td) var(--d) forwards, revealingOut var(--td) var(--t) forwards;
        }
        /* animations */
        
        @keyframes revealBlock {
            100% {
                color: white;
            }
        }
        
        @keyframes revealingIn {
            0% {
                width: 0;
            }
            100% {
                width: 100%;
            }
        }
        
        @keyframes revealingOut {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(100%);
            }
        }
    </style>
</head>
<body>
<h1 class="block-effect" style="--td: 2s">
        <div class="block-reveal" style="--bc: #B9A92C; --d: .1s">VPN</div>
        <div class="block-reveal" style="--bc: #B9A92C; --d: .1s">Dashboard</div>
        <div class="block-reveal" style="--bc: #FF5722; --d: .5s">Under Development</div>
    </h1>
</body>
</html>