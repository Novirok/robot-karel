<?php
session_start();

// Inicializace proměnných, pokud neexistují
if (!isset($_SESSION['karel_x'])) {
    $_SESSION['karel_x'] = 0; // Počáteční pozice X
    $_SESSION['karel_y'] = 0; // Počáteční pozice Y
    $_SESSION['direction'] = 'right'; // Počáteční směr
    $_SESSION['grid'] = array_fill(0, 10, array_fill(0, 10, false)); // Inicializace prázdného pole
}

// Zpracování příkazů
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commands = explode("\n", trim($_POST['command'])); // Rozdělení příkazů na jednotlivé řádky
    
    foreach ($commands as $command) {
        $command = trim($command); // Odstranění bílých znaků kolem příkazu
        switch (strtolower($command)) {
            case 'krok':
                moveKarel();
                break;
            case 'polož':
                placeColor();
                break;
            case 'vlevobok':
                turnLeft();
                break;
            case 'reset':
                resetKarel();
                break;
            default:
                break;
        }
    }
}

// Funkce pro pohyb Karla
function moveKarel() {
    if ($_SESSION['direction'] == 'right' && $_SESSION['karel_x'] < 9) {
        $_SESSION['karel_x']++;
    } elseif ($_SESSION['direction'] == 'down' && $_SESSION['karel_y'] < 9) {
        $_SESSION['karel_y']++;
    } elseif ($_SESSION['direction'] == 'left' && $_SESSION['karel_x'] > 0) {
        $_SESSION['karel_x']--;
    } elseif ($_SESSION['direction'] == 'up' && $_SESSION['karel_y'] > 0) {
        $_SESSION['karel_y']--;
    }
}

// Funkce pro otočení Karla doleva
function turnLeft() {
    $directions = ['right', 'down', 'left', 'up'];
    $currentIndex = array_search($_SESSION['direction'], $directions);
    $_SESSION['direction'] = $directions[($currentIndex + 1) % 4];
}

// Funkce pro resetování Karla
function resetKarel() {
    $_SESSION['karel_x'] = 0;
    $_SESSION['karel_y'] = 0;
    $_SESSION['direction'] = 'right';
    $_SESSION['grid'] = array_fill(0, 10, array_fill(0, 10, false)); // Reset celé mřížky
}

// Funkce pro zobrazení směru Karla jako šipky
function getKarelDirection() {
    switch ($_SESSION['direction']) {
        case 'right': return '→';
        case 'down': return '↓';
        case 'left': return '←';
        case 'up': return '↑';
        default: return '→';
    }
}

// Funkce pro "položení" červené barvy
function placeColor() {
    $x = $_SESSION['karel_x'];
    $y = $_SESSION['karel_y'];
    $_SESSION['grid'][$y][$x] = true; // Položí červenou barvu na aktuální pozici
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karel Bot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            margin: 20px;
            text-align: center;
        }

        .layout {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
        }

        .controls {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .controls textarea {
            width: 150px;
            height: 150px;
            margin-bottom: 10px;
            resize: none;
        }

        .grid-container {
            margin-top: 10px;
        }

        #karel-grid {
            border-collapse: collapse;
            margin: 0 auto;
        }

        #karel-grid td {
            width: 30px;
            height: 30px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            font-size: 20px;
            font-family: Arial, sans-serif;
        }

        #karel-grid .red {
            background-color: red;
        }

        .instructions {
            margin-top: 20px;
            text-align: left;
            font-size: 14px;
            max-width: 600px;
        }

        .instructions p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Karel Bot</h1>
        <div class="layout">
            <div class="controls">
                <label for="command-input">Příkazy:</label>
                <form method="POST" action="">
                    <textarea id="command-input" name="command" placeholder="Zadejte příkazy (každý na nový řádek)"></textarea><br>
                    <button type="submit">Spustit</button>
                </form>
            </div>
            <div class="grid-container">
                <table id="karel-grid">
                    <?php
                    $gridSize = 10; // Velikost tabulky
                    for ($i = 0; $i < $gridSize; $i++) {
                        echo "<tr>";
                        for ($j = 0; $j < $gridSize; $j++) {
                            $class = "";
                            if ($i == $_SESSION['karel_y'] && $j == $_SESSION['karel_x']) {
                                $class = "karel";
                            }
                            if ($_SESSION['grid'][$i][$j]) {
                                $class .= " red"; // Pokud je na tomto místě červená barva
                            }
                            echo "<td class='$class'>" . (($i == $_SESSION['karel_y'] && $j == $_SESSION['karel_x']) ? getKarelDirection() : "") . "</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
        <div class="instructions">
            <p><strong>Příkaz "krok":</strong> Posune Karla o jedno pole vpřed ve směru, kterým je otočen.</p>
            <p><strong>Příkaz "polož":</strong> Položí červenou barvu do aktuálního pole.</p>
            <p><strong>Příkaz "vlevobok":</strong> Otočí Karla o 90° doleva.</p>
            <p><strong>Příkaz "reset":</strong> Resetuje celé pole, vrátí Karla zpět na startovní pozici.</p>
        </div>
    </div>
</body>
</html>
